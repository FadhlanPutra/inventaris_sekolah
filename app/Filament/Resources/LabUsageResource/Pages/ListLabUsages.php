<?php

namespace App\Filament\Resources\LabUsageResource\Pages;

use Filament\Actions;
use App\Models\LabUsage;
use App\Exports\LabUsageCustomExport;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\LabUsageResource;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction as ExcelExportAction;

class ListLabUsages extends ListRecords
{
    protected static string $resource = LabUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([

            // Export All (pakai pxlrbt export action)
            ExcelExportAction::make('exportAll')
                ->label('Export All')
                ->icon('heroicon-o-arrow-down-tray')
                ->exports([
                    LabUsageCustomExport::make()
                        ->fromModel(LabUsage::class)
                        ->modifyQueryUsing(fn ($q) => $q->with('user', 'location')),
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
                    LabUsageCustomExport::make()
                        ->fromModel(LabUsage::class)
                        ->modifyQueryUsing(fn ($query) => 
                            $query->with(['user'])
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
        ->visible(auth()->user()->can('export_lab::usage'))
        ->outlined(false),
            Actions\CreateAction::make(),
        ];
    }
}
