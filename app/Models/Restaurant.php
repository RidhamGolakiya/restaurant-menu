<?php

namespace App\Models;

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
    ];

    protected $casts = [
        'theme_config' => 'array',
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

    public function user()
    {
        return $this->hasOne(User::class, 'restaurant_id');
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