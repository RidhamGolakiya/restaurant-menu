<?php

namespace App\Models;

use Filament\Forms\Components\Hidden;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Validation\Rule;

class MenuCategory extends Model
{
    protected $table = 'menu_categories';

    protected $fillable = [
        'restaurant_id',
        'name',
        'description',
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
                ->rule(function ($record) {
                    return Rule::unique('menu_categories', 'name')
                        ->where('restaurant_id', auth()->user()->restaurant_id)
                        ->ignore($record?->id);
                }),
            Hidden::make('restaurant_id')
                ->default(auth()->user()->restaurant_id),
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
