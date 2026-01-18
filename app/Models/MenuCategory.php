<?php

namespace App\Models;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

use Spatie\MediaLibrary\HasMedia;
use Spatie\MediaLibrary\InteractsWithMedia;
use Filament\Forms\Components\SpatieMediaLibraryFileUpload;

class MenuCategory extends Model implements HasMedia
{
    use InteractsWithMedia;
    protected $table = 'menu_categories';

    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
        'slug',
    ];

    public function menuItems()
    {
        return $this->hasMany(Menu::class, 'category_id');
    }

    public static function formSchema(): array
    {
        return [
            TextInput::make('name')
                ->label('Name:')
                ->placeholder('Name')
                ->required()
                ->live(onBlur: true)
                ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state)))
                ->rule(function ($record) {
                    return Rule::unique('menu_categories', 'name')
                        ->where('restaurant_id', auth()->user()->restaurant_id)
                        ->ignore($record?->id);
                }),
            TextInput::make('slug')
                ->required()
                ->unique(ignoreRecord: true),
            Hidden::make('restaurant_id')
                ->default(auth()->user()->restaurant_id),
            SpatieMediaLibraryFileUpload::make('image')
                ->label('Category Image')
                ->collection('category_image'),
            Textarea::make('description')
                ->label('Description:')
                ->placeholder('Description')
                ->rows(3),
        ];
    }

    public function menus()
    {
        return $this->hasMany(Menu::class, 'category_id', 'id');
    }
}
