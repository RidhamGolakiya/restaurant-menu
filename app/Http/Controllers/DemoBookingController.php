<?php

namespace App\Http\Controllers;

use App\Models\DemoBooking;
use Illuminate\Http\Request;

class DemoBookingController extends Controller
{
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'restaurant_name' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'phone' => 'nullable|string|max:20',
        ]);

        DemoBooking::create($validated);

        return redirect()->back()->with('success', 'Thank you! Your demo request has been received. We will contact you shortly.');
    }
}
