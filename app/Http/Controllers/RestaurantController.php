<?php

namespace App\Http\Controllers;

use App\Models\Currency;
use App\Models\Customer;
use App\Models\Menu;
use App\Models\Reservation;
use App\Models\Restaurant;
use App\Models\RestaurantTimingSlot;
use App\Models\Setting;
use App\Models\Table;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class RestaurantController extends Controller
{

    public function index($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        $date = request()->query('date') ?? now($restaurant->timezone)->toDateString();
        $dayName = Carbon::parse($date, $restaurant->timezone)->format('l');

        $restaurantHours = RestaurantTimingSlot::where('restaurant_id', $restaurant->id)->get();

        $otherRestaurants = Restaurant::where('id', '!=', $restaurant->id)->get();

        $menuCategories = $restaurant->menuCategories()->with('menuItems')->get();

        $menus = Menu::where('restaurant_id', $restaurant->id)->get();

        $settings = $restaurant->user->settings()->get();
        $currencyId = optional($settings->where('key', 'currency_id')->first())->value;
        $currency = $restaurant->currency ?? Currency::find($currencyId);

        if ($restaurant->theme === 'modern') {
            return view('restaurant.themes.modern', compact('restaurant', 'date', 'restaurantHours', 'otherRestaurants', 'menuCategories', 'menus', 'settings', 'currency'));
        }

        if ($restaurant->theme === 'theme_3') {
            $categories = $menuCategories; // Reusing variable
            return view('restaurant.themes.theme_3.index', compact('restaurant', 'categories', 'settings', 'currency'));
        }

        return view('restaurant.home', compact('restaurant', 'date', 'restaurantHours', 'otherRestaurants', 'menuCategories', 'menus', 'settings', 'currency'));
    }

    public function category($slug, $categorySlug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $category = \App\Models\MenuCategory::where('restaurant_id', $restaurant->id)->where('slug', $categorySlug)->firstOrFail();
        $products = \App\Models\Menu::where('category_id', $category->id)->get();
        // Theme check
        if ($restaurant->theme === 'theme_3') {
             return view('restaurant.themes.theme_3.category', compact('restaurant', 'category', 'products'));
        }
        abort(404); // Or default view
    }

    public function product($slug, $productSlug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $product = \App\Models\Menu::where('restaurant_id', $restaurant->id)->where('slug', $productSlug)->with('category')->firstOrFail();
        
         if ($restaurant->theme === 'theme_3') {
             return view('restaurant.themes.theme_3.product', compact('restaurant', 'product'));
        }
        abort(404);
    }

    public function bestSellerFood($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $products = \App\Models\Menu::where('restaurant_id', $restaurant->id)->where('is_best_food', true)->with('category')->get();
        if ($restaurant->theme === 'theme_3') {
             return view('restaurant.themes.theme_3.best_seller', compact('restaurant', 'products') + ['title' => 'Best Seller Food']);
        }
        abort(404);
    }

    public function bestSellerDrink($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $products = \App\Models\Menu::where('restaurant_id', $restaurant->id)->where('is_best_drink', true)->with('category')->get();
        if ($restaurant->theme === 'theme_3') {
             return view('restaurant.themes.theme_3.best_seller', compact('restaurant', 'products') + ['title' => 'Best Seller Drinks']);
        }
        abort(404);
    }

    public function getTimeSlots($slug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        $date = request()->query('date') ?? now($restaurant->timezone)->toDateString();
        $dayName = Carbon::parse($date, $restaurant->timezone)->format('l');

        $slots = RestaurantTimingSlot::where('restaurant_id', $restaurant->id)
            ->where('day_name', $dayName)
            ->get();

        $slotDurationSetting = Setting::where('user_id', $restaurant->id)
            ->where('key', 'max_booking_time_per_table')
            ->value('value');

        $slotDuration = (int) $slotDurationSetting > 0 ? (int) $slotDurationSetting : 1;
        $now = now($restaurant->timezone);

        $existingReservations = Reservation::where('restaurant_id', $restaurant->id)
            ->whereDate('start_time', $date)
            ->get();

        $availableHours = [];

        foreach ($slots as $slot) {
            $open = Carbon::parse($slot->open_time, $restaurant->timezone);
            $close = Carbon::parse($slot->close_time, $restaurant->timezone);

            for ($time = $open->copy(); $time->lt($close); $time->addHours($slotDuration)) {
                $hourFormatted = $time->format('g:i A');
                $hourTime = Carbon::parse($date . ' ' . $hourFormatted, $restaurant->timezone);

                if (Carbon::parse($date, $restaurant->timezone)->isToday() && $hourTime->lt($now)) {
                    continue;
                }

                $isAvailable = true;
                foreach ($existingReservations as $reservation) {
                    $startTime = Carbon::parse($reservation->start_time, $restaurant->timezone);
                    $endTime = Carbon::parse($reservation->end_time, $restaurant->timezone);

                    if ($hourTime->eq($startTime) || ($hourTime->gt($startTime) && $hourTime->lt($endTime))) {
                        $isAvailable = false;
                        break;
                    }
                }

                if ($isAvailable) {
                    $availableHours[] = $hourFormatted;
                }
            }
        }

        $availableHours = collect($availableHours)->unique()->sort()->values();

        return response()->json(['slots' => $availableHours]);
    }


    public function reservation(Request $request, $slug)
    {
        $restaurantId = Restaurant::where('slug', $slug)->firstOrFail()->id;

        $validatedData = $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email',
            'phone' => 'required|string|max:15',
            'date' => 'required|date',
            'time' => 'required',
            'persons' => 'required|integer|min:1',
        ]);

        try {
            DB::beginTransaction();

            $customer = Customer::where('phone', $validatedData['phone'])->first();

            if (!$customer) {
                $customer = Customer::create([
                    'name' => $validatedData['name'],
                    'phone' => $validatedData['phone'],
                    'email' => $validatedData['email'],
                    'restaurant_id' => $restaurantId,
                ]);
            }

            // Directly check table availability if customer exists
            if ($customer->exists) {
                $availableTable = Table::where('restaurant_id', $restaurantId)
                    ->where('capacity', '>=', $validatedData['persons'])
                    ->whereDoesntHave('reservations', function ($query) use ($validatedData) {
                        $query->where(function ($q) use ($validatedData) {
                            $q->where(function ($q2) use ($validatedData) {
                                $q2->where('start_time', '<', $validatedData['time']);
                            });
                        });
                    })
                    ->orderBy('capacity', 'asc')
                    ->first();

                if (! $availableTable) {
                    return response()->json([
                        'message' => 'No available tables for this number of persons.',
                        'status' => 'error',
                    ], 500);
                }

                $settings = Setting::where('user_id', $restaurantId)
                    ->where('key', 'max_booking_time_per_table')
                    ->first();

                $bookHours = $settings ? $settings->value : 2;
                $startTime = Carbon::parse($validatedData['date'] . ' ' . $validatedData['time'])->format('Y-m-d H:i:s');
                $endTime = Carbon::parse($startTime)->addHours((int) $bookHours)->format('Y-m-d H:i:s');

                $reservation = Reservation::create([
                    'restaurant_id' => $restaurantId,
                    'customer_id' => $customer->id,
                    'table_id' => $availableTable->id,
                    'no_of_person' => $validatedData['persons'],
                    'start_time' => $startTime,
                    'end_time' => $endTime,
                    'status' => 1,
                ]);

                $reservation->reservation_unique_id = $reservation->generateUniqueReservationId($restaurantId);
                $reservation->save();

                DB::commit();
                return response()->json([
                    'message' => 'Thank you! Your reservation is confirmed.',
                    'status' => 'success',
                ], 200);
            }
        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json([
                'message' => 'Reservation creation failed.',
                'status' => 'error',
                'error' => $e->getMessage(),
            ], 500);
        }
    }
    public function checkAvailability(Request $request)
    {
        $validated = $request->validate([
            'date' => 'required|date',
            'time' => 'required|string',
            'persons' => 'required|integer|min:1',
        ]);

        $restaurantId = auth()->user()->restaurant_id ?? 1; // Or pass restaurant_id in request if multi-restaurant

        $settings = Setting::where('user_id', $restaurantId)
            ->where('key', 'max_booking_time_per_table')
            ->first();
        $bookHours = $settings ? $settings->value : 2;

        $startTime = Carbon::parse($validated['date'] . ' ' . $validated['time']);
        $endTime = (clone $startTime)->addHours((int) $bookHours);

        $availableTable = Table::where('restaurant_id', $restaurantId)
            ->where('capacity', '>=', $validated['persons'])
            ->whereDoesntHave('reservations', function ($query) use ($startTime, $endTime) {
                $query->where(function ($q) use ($startTime, $endTime) {
                    $q->where('start_time', '<', $endTime)
                        ->where('end_time', '>', $startTime);
                });
            })
            ->orderBy('capacity', 'asc')
            ->first();

        if (!$availableTable) {
            return response()->json([
                'message' => 'No available tables for this number of persons.',
                'status' => 'error',
            ], 500);
        }

        return response()->json([
            'available' => true,
        ]);
    }
}
