<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use Filament\Actions;
use App\Models\Borrow;
use App\Exports\BorrowCustomExport;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BorrowResource;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use pxlrbt\FilamentExcel\Actions\Pages\ExportAction as ExcelExportAction;

class ListBorrows extends ListRecords
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\ActionGroup::make([

            // Export All (pakai pxlrbt export action)
            ExcelExportAction::make('exportAll')
                ->label('Export All')
                ->icon('heroicon-o-arrow-down-tray')
                ->exports([
                    BorrowCustomExport::make()
                        ->fromModel(Borrow::class)
                        ->modifyQueryUsing(fn ($q) => $q->with('labusage', 'user', 'item')),
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
                    BorrowCustomExport::make()
                        ->fromModel(Borrow::class)
                        ->modifyQueryUsing(fn ($query) => 
                            $query->with(['labusage', 'user', 'item'])
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
        ->visible(auth()->user()->can('export_borrow'))
        ->outlined(false),
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        // // hanya super_admin yang bisa lihat tabs
        // if (! auth()->user()?->hasRole('super_admin')) {
        //     return [];
        // }

        return [
            'All' => Tab::make()
                ->badge(fn () => BorrowResource::getEloquentQuery()->count())
                ->modifyQueryUsing(fn ($query) => $query),

            'Pending' => Tab::make()
                ->badge(fn () => BorrowResource::getEloquentQuery()->where('status', 'Pending')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Pending')),

            'Active' => Tab::make()
                ->badge(fn () => BorrowResource::getEloquentQuery()->where('status', 'Active')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Active')),

            'Finished' => Tab::make()
                ->badge(fn () => BorrowResource::getEloquentQuery()->where('status', 'Finished')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'Finished')),
        ];
    }
}
