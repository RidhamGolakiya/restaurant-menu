<?php

namespace App\Models;

use Filament\Forms\Components\Group;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\Toggle;
use Illuminate\Database\Eloquent\Model;
use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;

class Menu extends Model implements HasMedia
{
    use InteractsWithMedia;

    protected $table = 'menus';

    protected $fillable = [
        'restaurant_id',
        'name',
        'slug',
        'category_id',
        'price',
        'currency_id',
        'today_special',
        'ingredients',
        'description',
        'dietary_type',
        'is_jain',
        'is_vegan',
        'is_best_seller',
        'is_best_food',
        'is_best_drink',
    ];

    const MENU_IMAGE = 'menu_image';

    public function category()
    {
        return $this->belongsTo(MenuCategory::class, 'category_id');
    }

    public function currency()
    {
        return $this->belongsTo(Currency::class, 'currency_id');
    }

    public static function formSchema()
    {
        return [
            Hidden::make('restaurant_id')->default(auth()->user()->restaurant_id),
            SpatieMediaLibraryFileUpload::make('menu_image')
                ->label('Image')
                ->image()
                ->disk(config('app.media_disk'))
                ->collection(self::MENU_IMAGE),
            Group::make([
                Select::make('category_id')
                    ->relationship('category', 'name', fn ($query) => $query->where('restaurant_id', auth()->user()->restaurant_id))
                    ->required()
                    ->label('Category: ')
                    ->searchable()
                    ->preload()
                    ->native(false),

                TextInput::make('name')
                    ->label('Name: ')
                    ->placeholder('Name')
                    ->required()
                    ->live(onBlur: true)
                    ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                    ->maxLength(255),
                TextInput::make('slug')
                    ->required()
                    ->unique(ignoreRecord: true),
                Select::make('dietary_type')
                    ->options([
                        'veg' => 'Veg',
                        'non_veg' => 'Non Veg'
                    ])
                    ->default('veg')
                    ->required(),

                TextInput::make('price')
                    ->label('Price: ')
                    ->placeholder('Price')
                    ->default(0)
                    ->minValue(0)
                    ->numeric(),

            ])->columns(2),

            Textarea::make('ingredients')
                ->label('Ingredients: ')
                ->placeholder('Ingredients')
                ->rows(3)
                ->maxLength(255),
            Textarea::make('description')
                ->label('Short Description: ')
                ->placeholder('Description')
                ->rows(2),
            Group::make([
                Toggle::make('today_special')
                    ->label('Today Special'),
                Toggle::make('is_jain')->label('Jain Available'),
                // Toggle::make('is_vegan')->label('Vegan Available'),
                Toggle::make('is_best_seller')->label('Best Selling'),
                Toggle::make('is_best_food')->label('Best Food'),
                Toggle::make('is_best_drink')->label('Best Drink'),
            ])->columns(2),
        ];
    }
}
