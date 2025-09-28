<?php

namespace App\Filament\Resources\MaintenanceResource\Pages;

use App\Exports\MaintenanceCustomExport;
use Filament\Actions;
use App\Models\Maintenance;
use Filament\Resources\Pages\ListRecords;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Filament\Resources\MaintenanceResource;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction as ExcelExportAction;



class ListMaintenances extends ListRecords
{
    protected static string $resource = MaintenanceResource::class;

   protected function getHeaderActions(): array
{
    return [
        Actions\ActionGroup::make([

            // Export All (pakai pxlrbt export action)
            ExcelExportAction::make('exportAll')
                ->label('Export All')
                ->icon('heroicon-o-arrow-down-tray')
                ->exports([
                    MaintenanceCustomExport::make()
                        ->fromModel(Maintenance::class)
                        ->modifyQueryUsing(fn ($q) => $q->with('inventory')),
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
                    MaintenanceCustomExport::make()
                        ->fromModel(Maintenance::class)
                        ->modifyQueryUsing(fn ($query) => 
                            $query->with(['inventory'])
                                  ->where('created_at', '>=', now()->subMonth())
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
        ->visible(auth()->user()->can('export_maintenance'))
        ->outlined(false),

         Actions\CreateAction::make(),
    ];
}
}
