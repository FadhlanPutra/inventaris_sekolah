<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BorrowResource;
use App\Models\Borrow;
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
                    ExcelExport::make()->fromModel(Borrow::class),
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
                        ->fromModel(Borrow::class)
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
            Actions\CreateAction::make(),
        ];
    }

    public function getTabs(): array
    {
        // hanya super_admin yang bisa lihat tabs
        if (! auth()->user()?->hasRole('super_admin')) {
            return [];
        }

        return [
            'All' => Tab::make()
                ->badge(fn () => BorrowResource::getEloquentQuery()->count())
                ->modifyQueryUsing(fn ($query) => $query),

            'Pending' => Tab::make()
                ->badge(fn () => BorrowResource::getEloquentQuery()->where('status', 'pending')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'pending')),

            'Active' => Tab::make()
                ->badge(fn () => BorrowResource::getEloquentQuery()->where('status', 'active')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'active')),

            'Finished' => Tab::make()
                ->badge(fn () => BorrowResource::getEloquentQuery()->where('status', 'finish')->count())
                ->modifyQueryUsing(fn ($query) => $query->where('status', 'finish')),
        ];
    }
}
