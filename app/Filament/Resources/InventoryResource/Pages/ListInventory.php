<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use Filament\Actions;
use App\Models\Inventory;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\InventoryResource;
use EightyNine\ExcelImport\ExcelImportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction as ExcelExportAction;

class ListInventory extends ListRecords
{
    protected static string $resource = InventoryResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([

            // Export All (pakai pxlrbt export action)
            ExcelExportAction::make('exportAll')
                ->label('Export All')
                ->icon('heroicon-o-arrow-down-tray')
                ->exports([
                    ExcelExport::make()->fromModel(Inventory::class),
                ])
                ->extraAttributes([
                    // styling item di dropdown: no bg, margin, padding, gap, dll
                    'style' => 'background-color:transparent !important; margin:6px 0 !important; padding:8px 12px !important; border-radius:8px !important; display:flex; align-items:center; gap:8px;'
                ]),

            // Export Last Month
            ExcelExportAction::make('exportLastMonth')
                ->label('Export Last 1 Month')
                ->icon('heroicon-o-calendar')
                ->exports([
                    ExcelExport::make()
                        ->fromModel(Inventory::class)
                        ->modifyQueryUsing(fn ($query) =>
                            $query->where('created_at', '>=', now()->subMonth())
                        ),
                ])
                ->extraAttributes([
                    'style' => 'background-color:transparent !important; margin:6px 0 !important; padding:8px 12px !important; border-radius:8px !important; display:flex; align-items:center; gap:8px;'
                ]),
        ])
        ->label('Export')
        ->icon('heroicon-o-arrow-down-tray')
        ->color('primary')  // tombol utama biru
        ->button()
        ->outlined(false),
             ExcelImportAction::make()
                ->color('primary'),
            Actions\CreateAction::make(),
        ];
    }
}
