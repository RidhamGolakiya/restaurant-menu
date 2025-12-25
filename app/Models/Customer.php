<?php

namespace App\Models;

use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Ysfkaya\FilamentPhoneInput\Forms\PhoneInput;

class Customer extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'customers';

    protected $fillable = [
        'name',
        'phone',
        'email',
        'country_code',
        'restaurant_id',
    ];

    const CUSTOMER_IMAGE = 'customer-images';

    public function reservations()
    {
        return $this->hasMany(Reservation::class);
    }

    public static function getForm(): array
    {
        return [
            TextInput::make('name')
                ->label('Name:')
                ->placeholder('Name')
                ->required(),
            TextInput::make('email')
                ->label('Email:')
                ->placeholder('Email')
                ->email()
                ->unique('customers', 'email', ignoreRecord: true),
            PhoneInput::make('phone')
                ->label('Phone Number:')
                ->placeholder('Phone Number')
                ->required()
                ->formatStateUsing(function ($state, $record) {
                    if($record) {
                        return formatPhoneNumber($record->country_code, $record->phone);
                    }

                    return $state;
                }),
            SpatieMediaLibraryFileUpload::make('customer-images')
                ->label('Image:')
                ->image()
                ->disk(config('app.media_disk'))
                ->collection(self::CUSTOMER_IMAGE),
        ];
    }
}
