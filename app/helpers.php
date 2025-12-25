<?php

use App\Models\Currency;
use App\Models\Restaurant;
use App\Models\Setting;
use Illuminate\Support\Fluent;
use libphonenumber\PhoneNumberFormat;
use libphonenumber\PhoneNumberUtil;

if (! function_exists('getUserSettings')) {

    function getUserSettings()
    {
        $settings = Setting::where('user_id', auth()->user()->id)->pluck('value', 'key')->toArray();

        return $settings;
    }
}

if (! function_exists('restaurantTableHours')) {

    function restaurantTableHours($restaurantID)
    {
        $restaurant = Restaurant::where('uuid', $restaurantID)->firstOrFail();

        $settings = $restaurant->user->settings()
            ->where('key', 'max_booking_time_per_table')
            ->first();

        return $settings ? $settings->value : 2; // Default to 2 hours if not set
    }
}
if (! function_exists('currencyIcon')) {
    function currencyIcon()
    {
        $settings = getUserSettings();

        $currencyIcon = Currency::find($settings['currency_id'] ?? '')?->icon ?? '';

        return $currencyIcon ?? '';
    }
}

if (! function_exists('getPerTableTime')) {
    function getPerTableTime()
    {
        $settings = getUserSettings();

        return $settings['max_booking_time_per_table'] ?? 2;
    }
}

if (! function_exists('phoneNumberSeparator')) {
    function phoneNumberSeparator($phone): Fluent
    {
        try {
            if (! str_starts_with($phone, '+')) {
                $phone = '+'.$phone;
            }

            $phoneUtil = PhoneNumberUtil::getInstance();

            $numberProto = $phoneUtil->parse($phone);

            $countryCode = $numberProto->getCountryCode();

            $phoneNumber = $numberProto->getNationalNumber();

            return new Fluent(['country_code' => $countryCode, 'phone' => $phoneNumber]);
        } catch (\libphonenumber\NumberParseException $e) {
            return new Fluent(['country_code' => null, 'phone' => $phone]);
        }
    }
}

if (! function_exists('formatPhoneNumber')) {
    function formatPhoneNumber($countryCode, $phone)
    {
        try {
            $phone = '+'.$countryCode.$phone;

            $phoneUtil = PhoneNumberUtil::getInstance();

            $numberProto = $phoneUtil->parse($phone);

            return $phoneUtil->format($numberProto, PhoneNumberFormat::INTERNATIONAL);
        } catch (\Exception $e) {
            return $phone;
        }
    }
}

if (! function_exists('formatDuration')) {
    function formatDuration($duration)
    {
        $duration = abs($duration);
        if ($duration < 60) {
            return $duration.' s';
        } elseif ($duration < 3600) {
            return floor($duration / 60).':'.str_pad($duration % 60, 2, '0', STR_PAD_LEFT).' mins';
        } else {
            return floor($duration / 3600).':'.
                   str_pad(floor(($duration % 3600) / 60), 2, '0', STR_PAD_LEFT).':'.
                   str_pad($duration % 60, 2, '0', STR_PAD_LEFT).' hrs';
        }
    }
}

function getTimeZone(): array
{
    $timezoneArr = json_decode(file_get_contents(public_path('timezone/timezone.json')), true);
    $timezones = [];

    foreach ($timezoneArr as $utcData) {
        foreach ($utcData['utc'] as $item) {
            $timezones[$item] = $item;
        }
    }

    return $timezones;
}
