<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCode extends Model
{
    protected $fillable = [
        'uuid',
        'restaurant_id',
        'name',
        'scans_count',
        'settings',
    ];

    protected $casts = [
        'settings' => 'array',
    ];


    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function analytics()
    {
        return $this->hasMany(QrCodeAnalytic::class);
    }

    public function getRouteKeyName()
    {
        return 'uuid';
    }

    public function getQrImageAttribute()
    {
        $settings = $this->settings ?? [];
        $color = $this->hexToRgb($settings['color'] ?? '#000000');
        $bgColor = $this->hexToRgb($settings['background_color'] ?? '#ffffff');
        $style = $settings['style'] ?? 'square';
        $eyeStyle = $settings['eye_style'] ?? 'square';
        $logo = isset($settings['logo_url']) ? storage_path('app/public/' . $settings['logo_url']) : null;

        $qr = \SimpleSoftwareIO\QrCode\Facades\QrCode::size(500) // Increased size for better scannability
            ->format('png')
            ->margin(2) // Added margin for better scanning
            ->color($color[0], $color[1], $color[2])
            ->backgroundColor($bgColor[0], $bgColor[1], $bgColor[2]);

        // Apply styles only if they're valid options
        // For better scannability, avoid complex styles like 'dot' that can interfere with QR recognition
        if ($style !== 'square' && $style !== 'dot') {
            $qr = $qr->style($style);
        } elseif ($style === 'dot') {
            // If dot style is selected, increase the size and margin for better scannability
            $qr = $qr->size(600); // Further increase size for dot style
        }
        if ($eyeStyle !== 'square') {
            $qr = $qr->eye($eyeStyle);
        }

        // Add logo if it exists
        if ($logo && file_exists($logo)) {
            // Merge the logo in the center, 20% of QR code size, with padding
            $qr->merge($logo, .2, true);
        }
        
        // Ensure route exists and is correct. We use the public scan route.
        $url = route('qr.scan', $this->uuid);

        return $qr->generate($url);
    }
    
    private function hexToRgb($hex) {
        $hex = str_replace("#", "", $hex);
        if(strlen($hex) == 3) {
           $r = hexdec(substr($hex,0,1).substr($hex,0,1));
           $g = hexdec(substr($hex,1,1).substr($hex,1,1));
           $b = hexdec(substr($hex,2,1).substr($hex,2,1));
        } else {
           $r = hexdec(substr($hex,0,2));
           $g = hexdec(substr($hex,2,2));
           $b = hexdec(substr($hex,4,2));
        }
        return [$r, $g, $b];
    }
}
