<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use Filament\Actions;
use Filament\Resources\Components\Tab;
use Filament\Resources\Pages\ListRecords;
use App\Filament\Resources\BorrowResource;

class ListBorrows extends ListRecords
{
    protected static string $resource = BorrowResource::class;

    protected function getHeaderActions(): array
    {
        return [
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
