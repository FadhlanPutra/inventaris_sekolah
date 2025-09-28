<?php

namespace App\Filament\Resources\BorrowResource\Pages;

use App\Models\User;
use Filament\Actions;
use App\Models\Borrow;
use Filament\Notifications\Notification;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\BorrowResource;
use Filament\Notifications\Actions\Action;

class CreateBorrow extends CreateRecord
{
    protected static string $resource = BorrowResource::class;

    protected function afterCreate(): void
    {
        // Ambil semua user dengan role super_admin
        $superAdmins = User::role('super_admin')->get();

        $itemName = $this->record->item?->item_name;
        $borrow = $this->record;
        $user = $this->record->user?->name;

        Notification::make()
            ->title('Someone borrowed an item!')
            ->info()
            ->body("Item <strong style='color:blue'>{$itemName}</strong> borrowed by <span style='color:blue'>{$user}</span>")
            ->actions([
                Action::make('view')
                ->button()
                ->markAsRead()
                ->url(BorrowResource::getUrl('edit', ['record' => $borrow])),
                // Action::make('approve')
                //     ->button()
                //     ->action(function () use ($borrow) {
                //         Borrow::find($borrow->id)?->update(['status' => 'Active']);
                //     }), // kalau mau coba pake livewire, tapi malas pake dispatch()
                ])
            ->sendToDatabase($superAdmins);
    }
}
