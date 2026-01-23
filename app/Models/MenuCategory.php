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
        'base_category_id',
        'sort_order',
    ];

    public function menuItems()
    {
        return $this->hasMany(Menu::class, 'category_id');
    }

    public static function formSchema(): array
    {
        return [
            \Filament\Forms\Components\Select::make('base_category_id')
                ->label('Base Category')
                ->relationship('baseCategory', 'name', modifyQueryUsing: fn ($query) => $query->where('restaurant_id', auth()->user()->restaurant_id))
                ->searchable()
                ->preload()
                ->createOptionForm([
                    \Filament\Forms\Components\TextInput::make('name')
                        ->required()
                        ->maxLength(255)
                        ->live(onBlur: true)
                        ->afterStateUpdated(fn ($state, callable $set) => $set('slug', \Illuminate\Support\Str::slug($state))),
                    \Filament\Forms\Components\TextInput::make('slug')
                        ->required()
                        ->maxLength(255),
                    \Filament\Forms\Components\Hidden::make('restaurant_id')
                        ->default(auth()->user()->restaurant_id),
                ])
                ->createOptionUsing(function (array $data) {
                    return \App\Models\BaseCategory::create($data)->id;
                }),
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

    public function baseCategory()
    {
        return $this->belongsTo(BaseCategory::class, 'base_category_id');
    }
}
