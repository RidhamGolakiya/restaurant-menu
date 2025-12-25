<?php

namespace App\Filament\Restaurant\Resources\TableResource\Pages;

use Filament\Actions\Action;
use Filament\Infolists\Infolist;
use Filament\Infolists\Components\Group;
use Filament\Resources\Pages\ViewRecord;
use Filament\Infolists\Components\Section;
use Filament\Infolists\Components\TextEntry;
use App\Filament\Restaurant\Resources\TableResource;
use Filament\Resources\Pages\Concerns\HasRelationManagers;

class ViewTable extends ViewRecord
{
    use HasRelationManagers;

    protected static string $resource = TableResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Action::make('back')
                ->label('Back')
                ->url(TableResource::getUrl('index')),
        ];
    }

    public function infolist(Infolist $infolist): Infolist
    {
        return $infolist
            ->schema([
                Section::make('Table Details')
                    ->schema([
                        Group::make([
                            TextEntry::make('name')
                                ->label('Table Name:'),
                            TextEntry::make('capacity')
                                ->label('Capacity:'),
                            TextEntry::make('restaurant.name')
                                ->label('Restaurant Name:'),
                        ])->columns(3),
                    ]),
            ]);
    }
}
