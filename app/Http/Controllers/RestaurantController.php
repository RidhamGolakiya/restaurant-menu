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

    public function index($slug, $any = null)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();

        if (!$restaurant->is_active) {
            $settings = getGlobalSettings();
            
            if (isset($settings['support_whatsapp']) && is_string($settings['support_whatsapp'])) {
                $settings['support_whatsapp'] = json_decode($settings['support_whatsapp'], true);
            }
            
            return view('restaurant.offline', compact('settings'));
        }

        $date = request()->query('date') ?? now($restaurant->timezone)->toDateString();
        $dayName = Carbon::parse($date, $restaurant->timezone)->format('l');

        $restaurantHours = RestaurantTimingSlot::where('restaurant_id', $restaurant->id)->get();

        $otherRestaurants = Restaurant::where('id', '!=', $restaurant->id)->get();
        
        $baseCategories = \App\Models\BaseCategory::where('restaurant_id', $restaurant->id)
            ->orderBy('sort_order')
            ->with(['menuCategories' => function ($query) {
                $query->whereHas('menuItems')->orderBy('sort_order'); // Only load categories that have items and sort them
            }, 'menuCategories.media', 'menuCategories.menuItems.media']) // Load media for products and categories
            ->whereHas('menuCategories.menuItems') // Only load base categories that have items
            ->get();

        // Fallback for categories without base category? 
        // Or we assume all categories should have a base now?
        // Let's also fetch orphan menu categories just in case, or if the user wants to show them separately.
        // For now, let's assume we want to show everything. 
        // If specific requirement is "Menu category create via Base category", we might want to prioritize that structure.
        
        $menuCategories = $restaurant->menuCategories()->with('menuItems')->get(); // Keep this for now for compatibility or other themes

        $menus = Menu::where('restaurant_id', $restaurant->id)->get();

        $settings = $restaurant->user->settings()->get();
        $currencyId = optional($settings->where('key', 'currency_id')->first())->value;
        $currency = $restaurant->currency ?? Currency::find($currencyId);

        if ($restaurant->theme === 'modern') {
            return view('restaurant.themes.modern', compact('restaurant', 'date', 'restaurantHours', 'otherRestaurants', 'menuCategories', 'baseCategories', 'menus', 'settings', 'currency'));
        }

        if ($restaurant->theme === 'theme_3') {
            $categories = $menuCategories; // Reusing variable
            return view('restaurant.themes.theme_3.index', compact('restaurant', 'categories', 'baseCategories', 'settings', 'currency'));
        }

        if ($restaurant->theme === 'theme_4') {
            // Prepare data for React application
            $cats = $restaurant->menuCategories()->with('media', 'baseCategory')->get()->map(function ($cat) {
                return [
                    'id' => (string) $cat->id,
                    'name' => $cat->name,
                    'group' => $cat->baseCategory ? $cat->baseCategory->name : 'Other',
                    'image' => $cat->getFirstMediaUrl('category_image'),
                    'description' => $cat->description,
                ];
            });

            $foodItems = \App\Models\Menu::where('restaurant_id', $restaurant->id)
                ->with('media')
                ->get()
                ->map(function ($item) {
                    return [
                        'id' => (string) $item->id,
                        'categoryId' => (string) $item->category_id,
                        'name' => $item->name,
                        'price' => (float) $item->price,
                        'rating' => 5.0, // Hardcoded for now
                        'image' => $item->getFirstMediaUrl('menu_image'),
                        'description' => $item->description,
                        'ingredients' => $item->ingredients ? array_map('trim', explode(',', $item->ingredients)) : [],
                        'bestSelling' => (bool) $item->is_best_seller,
                        'special' => (bool) $item->today_special,
                        'tag' => $item->today_special ? 'Chef\'s Special' : null,
                    ];
                });

            $specials = $foodItems->filter(fn($f) => $f['special'])->values();
            
            $reviews = $restaurant->reviews()
                ->where('is_visible', true)
                ->orderBy('time', 'desc')
                ->take(10)
                ->get()
                ->map(function ($review) {
                    return [
                        'id' => $review->id,
                        'author_name' => $review->author_name,
                        'author_url' => $review->author_url,
                        'profile_photo_url' => $review->profile_photo_url,
                        'rating' => $review->rating,
                        'text' => $review->text,
                        'relative_time_description' => $review->relative_time_description,
                        'time' => $review->time ? $review->time->timestamp : null,
                    ];
                });

            // Construct the RESTAURANT_DATA object
            $restaurantData = [
                'restaurant' => [
                    'name' => $restaurant->name,
                    'slug' => $restaurant->slug,
                    'logo' => $restaurant->getFirstMediaUrl('logo'),
                    'phone' => $restaurant->phone,
                    'address' => $restaurant->address,
                    'city' => $restaurant->city,
                    'zip_code' => $restaurant->zip_code,
                    'social_links' => $restaurant->social_links,
                    'overview' => $restaurant->overview,
                    'created_at' => $restaurant->created_at->format('Y'), // Added creation year
                    'established_text' => $restaurant->theme_config['established_text'] ?? null,
                    'currency' => $currency->symbol ?? '$', // Handle currency symbol
                    'zomato_link' => $restaurant->zomato_link,
                    'swiggy_link' => $restaurant->swiggy_link,
                    'photos' => $restaurant->getMedia('photos')->map(fn($media) => $media->getUrl())->toArray(),
                ],
                'opening_hours' => $restaurantHours->map(function ($slot) {
                    return [
                        'day' => $slot->day_name,
                        'open' => \Carbon\Carbon::parse($slot->open_time)->format('g:i A'),
                        'close' => \Carbon\Carbon::parse($slot->close_time)->format('g:i A'),
                    ];
                }),
                'categories' => $cats,
                'foods' => $foodItems,
                'specials' => $specials,
                'offers' => [], // Placeholder for now
                'reviews' => $reviews,
                'settings' => $settings->pluck('value', 'key')->all(),
            ];

            return view('restaurant.themes.theme_4.index', compact('restaurant', 'restaurantData', 'settings'));
        }

        return view('restaurant.home', compact('restaurant', 'date', 'restaurantHours', 'otherRestaurants', 'menuCategories', 'menus', 'settings', 'currency'));
    }

    public function category($slug, $categorySlug)
    {
        $restaurant = Restaurant::where('slug', $slug)->firstOrFail();
        $category = \App\Models\MenuCategory::where('restaurant_id', $restaurant->id)->where('slug', $categorySlug)->firstOrFail();
        $products = \App\Models\Menu::where('category_id', $category->id)->with('media')->get();
        // Theme check
        if ($restaurant->theme === 'theme_3') {
             return view('restaurant.themes.theme_3.category', compact('restaurant', 'category', 'products'));
        }

        if ($restaurant->theme === 'modern') {
             $settings = $restaurant->user->settings()->get();
             $currencyId = optional($settings->where('key', 'currency_id')->first())->value;
             $currency = $restaurant->currency ?? Currency::find($currencyId);

             return view('restaurant.themes.modern.category', compact('restaurant', 'category', 'products', 'settings', 'currency'));
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
