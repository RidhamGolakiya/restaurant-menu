<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Table extends Model
{
    protected $table = 'tables';

    protected $fillable = [
        'restaurant_id',
        'name',
        'capacity',
        'color',
    ];

    protected $hidden = [
        'created_at',
        'updated_at',
    ];

    public function restaurant()
    {
        return $this->belongsTo(Restaurant::class);
    }

    public function reservations()
    {
        return $this->hasMany(Reservation::class, 'table_id');
    }

    public static function getFormSchema()
    {
        return [
            \Filament\Forms\Components\TextInput::make('name')
                ->label('Table Name:')
                ->placeholder('Table Name')
                ->required()
                ->unique('tables', 'name', ignoreRecord: true)
                ->maxLength(255),
            \Filament\Forms\Components\TextInput::make('capacity')
                ->label('Capacity:')
                ->placeholder('Capacity')
                ->required()
                ->minValue(1)
                ->numeric(),
            \Filament\Forms\Components\ColorPicker::make('color')
                ->label('Color:')
                ->placeholder('Color')
                ->default('#deb887'),
        ];
    }
}
