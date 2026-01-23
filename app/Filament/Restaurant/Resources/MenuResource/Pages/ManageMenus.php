<?php

namespace App\Filament\Restaurant\Resources\MenuResource\Pages;

use App\Filament\Restaurant\Resources\MenuResource;
use App\Models\Menu;
use App\Models\MenuCategory;
use EightyNine\ExcelImport\ExcelImportAction;
use Filament\Actions;
use Filament\Forms\Components\Actions\Action;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\ManageRecords;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Str;

class ManageMenus extends ManageRecords
{
    protected static string $resource = MenuResource::class;

    protected $i = 1;

    protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make('import_menus')
                ->label('Import')
                ->sampleFileExcel(
                    url: asset('files/sample.xlsx'),
                    sampleButtonLabel: 'Download Sample File',
                    customiseActionUsing: fn (Action $action) => $action->color('success')
                        ->icon('heroicon-m-arrow-down-tray')
                        ->requiresConfirmation(),
                )
                ->acceptedFileTypes(['application/vnd.openxmlformats-officedocument.spreadsheetml.sheet'])
                ->closeModalByClickingAway(true)
                ->color('primary')
                ->processCollectionUsing(function (string $modelClass, Collection $collection, $action) {

                    $restaurantId = auth()->user()->restaurant_id;

                    $arrayData = $collection->toArray();

                    $cleanedArray = array_map(function ($item) use ($restaurantId) {
                        if (! empty($item['category_name'])) {
                            $categoryName = trim($item['category_name']);

                            $category = MenuCategory::where('restaurant_id', $restaurantId)->whereRaw('LOWER(name) = ?', [Str::lower($categoryName)])->first();

                            if (! $category) {
                                $category = MenuCategory::create([
                                    'name' => $categoryName,
                                    'restaurant_id' => $restaurantId,
                                ]);
                            }

                            $menuColumns = Schema::getColumnListing('menus');

                            $filtered = array_filter(
                                $item,
                                function ($value, $key) use ($menuColumns) {
                                    return in_array($key, $menuColumns) || $key === 'category_name';
                                },
                                ARRAY_FILTER_USE_BOTH
                            );

                            if (isset($filtered['price'])) {
                                if (! preg_match('/^\d+(\.\d{1,2})?$/', trim($filtered['price']))) {
                                    Notification::make()
                                        ->title('Import Failed')
                                        ->body("Invalid price value '{$filtered['price']}' found. Please fix the file and try again.")
                                        ->danger()
                                        ->send();
                            
                                    return null;
                                }
                            }

                            $filtered['category_id'] = $category->id;
                            $filtered['restaurant_id'] = (int) $restaurantId;
                            unset($filtered['category_name']);

                            return $filtered;
                        }

                        return null;
                    }, $arrayData);

                    $cleanedArray = array_filter($cleanedArray);
                    Menu::insert($cleanedArray);
                })
                ->icon('heroicon-o-document-arrow-up'),
            Actions\CreateAction::make()
                ->label('New Menu')
                ->createAnother(false)
                ->modalWidth('lg')
                ->successNotificationTitle('Menu Created Successfully')
        ];
    }
}
