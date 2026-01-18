<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class QrCodeAnalytic extends Model
{
    protected $fillable = [
        'qr_code_id',
        'ip_address',
        'device_type',
        'os',
        'browser',
        'city',
        'country',
        'scanned_at',
    ];

    protected $casts = [
        'scanned_at' => 'datetime',
    ];

    public function qrCode()
    {
        return $this->belongsTo(QrCode::class);
    }
}
