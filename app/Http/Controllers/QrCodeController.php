<?php

namespace App\Http\Controllers;

use App\Models\QrCode;
use App\Models\QrCodeAnalytic;
use Illuminate\Http\Request;
use Jenssegers\Agent\Agent;

class QrCodeController extends Controller
{
    public function scan(Request $request, $uuid)
    {
        $qrCode = QrCode::where('uuid', $uuid)->firstOrFail();

        // Increment scan count
        $qrCode->increment('scans_count');

        // Capture Analytics
        $agent = new Agent();
        
        // Get location from IP (using a service or basic IP)
        // For now, we'll just store the IP. Real geo-ip requires a database or API.
        $ip = $request->ip();
        
        QrCodeAnalytic::create([
            'qr_code_id' => $qrCode->id,
            'ip_address' => $ip,
            'device_type' => $agent->deviceType(), // mobile, desktop, tablet, robot
            'os' => $agent->platform(),
            'browser' => $agent->browser(),
            // 'city' => ..., // Requires GeoIP
            // 'country' => ..., // Requires GeoIP
            'scanned_at' => now(),
        ]);

        // Always redirect to restaurant public page
        if ($qrCode->restaurant && $qrCode->restaurant->slug) {
             return redirect()->route('restaurant.index', ['slug' => $qrCode->restaurant->slug]); // Corrected route name based on web.php: restaurant.index
        }
        
        return abort(404, 'Destination not found');
    }
}
