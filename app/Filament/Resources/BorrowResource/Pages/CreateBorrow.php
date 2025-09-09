<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Filament\Resources\BorrowResource;
use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use Filament\Notifications\Notification;
use App\Models\User;
use Filament\Notifications\Actions\Action;
use App\Models\Borrow;

class CreateBorrow extends CreateRecord
{
    protected static string $resource = BorrowResource::class;

    protected function afterCreate(): void
    {
        // Ambil semua user dengan role super_admin
        $superAdmins = User::role('super_admin')->get();

        $itemName = $this->record->item?->item_name;
        $borrow = $this->record;

        Notification::make()
            ->title('Ada yang meminjam barang!')
            ->info()
            ->body("Barang {$itemName} dipinjam oleh " . auth()->user()->name)
            ->actions([
                Action::make('view')
                ->button()
                ->markAsRead()
                ->url(BorrowResource::getUrl('edit', ['record' => $borrow])),
                // Action::make('approve')
                //     ->button()
                //     ->action(function () use ($borrow) {
                //         Borrow::find($borrow->id)?->update(['status' => 'active']);
                //     }), // kalau mau coba pake livewire, tapi malas pake dispatch()
                ])
            ->sendToDatabase($superAdmins);
    }
}
