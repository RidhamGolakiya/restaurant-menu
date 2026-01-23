<?php

namespace App\Models;

use Filament\Models\Contracts\HasName;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Spatie\MediaLibrary\MediaCollections\Models\Concerns\HasUuid;

class Restaurant extends Model implements HasMedia
{
    use HasUuid, InteractsWithMedia;




    protected $table = 'restaurants';

    protected $fillable = [
        'name',
        'phone',
        'address',
        'address_2',
        'city',
        'state',
        'zip_code',
        'google_map_link',
        'google_place_id',
        'google_data_id',
        'country_id',
        'uuid',
        'restaurant_website_link',
        'overview',
        'timezone',
        'slug',
        'theme_mode',
        'primary_color',
        'secondary_color',
        'accent_color',
        'theme_config',
        'plan_id',
        'plan_status',
        'plan_expiry',
        'is_active',
        'theme',
        'currency_id',
        'show_on_landing_page',
        'type',
        'social_links',
    ];

    public function currency()
    {
        return $this->belongsTo(Currency::class);
    }

    protected $casts = [
        'theme_config' => 'array',
        'show_on_landing_page' => 'boolean',
        'social_links' => 'array',
    ];

    public static array $types = [
        'Cafe' => 'Cafe',
        'Restaurant' => 'Restaurant',
        'Hotel' => 'Hotel',
        'Bar' => 'Bar',
        'Fast Food' => 'Fast Food',
        'Other' => 'Other',
    ];

    const HERO_IMAGE = 'hero-images';
    const PHOTOS = 'photos';
    const LOGO = 'logo';

    public static $rules = [
        'photos' => 'max:2048',
        'logo' => 'max:2048',
        'hero_images' => 'max:2048',
    ];

    public function menuCategories()
    {
        return $this->hasMany(MenuCategory::class, 'restaurant_id', 'id');
    }

    public function country()
    {
        return $this->belongsTo(Country::class, 'country_id');
    }

    public function plan()
    {
        return $this->belongsTo(Plan::class);
    }

    public function planRequests()
    {
        return $this->hasMany(PlanRequest::class);
    }

    public function user()
    {
        return $this->hasOne(User::class, 'restaurant_id');
    }

    public function baseCategories()
    {
        return $this->hasMany(BaseCategory::class, 'restaurant_id');
    }

    public function timingSlots()
    {
        return $this->hasMany(RestaurantTimingSlot::class);
    }

    public function reviews()
    {
        return $this->hasMany(Review::class);
    }

    public function getPhotosAttribute()
    {
        return $this->getMedia(self::PHOTOS);
    }

    /**
     * Get the theme configuration for the restaurant
     */
    public function getThemeConfig(): array
    {
        $defaultColors = [
            'primary' => '#da3743', // Default primary color from your CSS
            'secondary' => '#247f9e', // Default secondary color from your CSS
            'accent' => '#f59e0b', // Default accent color from your CSS
        ];

        switch ($this->theme_mode) {
            case 'black_and_white':
                return [
                    'primary' => '#000000',
                    'secondary' => '#ffffff',
                    'accent' => '#000000',
                    'text_color' => '#000000',
                    'background_color' => '#ffffff',
                ];
            case 'custom':
                return [
                    'primary' => $this->primary_color ?? $defaultColors['primary'],
                    'secondary' => $this->secondary_color ?? $defaultColors['secondary'],
                    'accent' => $this->accent_color ?? $defaultColors['accent'],
                    'text_color' => '#000000', // Default text color
                    'background_color' => '#ffffff', // Default background color
                ];
            default:
                return $defaultColors;
        }
    }
}