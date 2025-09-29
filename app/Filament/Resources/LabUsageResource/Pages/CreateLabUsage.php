<?php

namespace App\Filament\Resources\LabUsageResource\Pages;

use App\Models\User;
use Filament\Actions;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\LabUsageResource;

class CreateLabUsage extends CreateRecord
{
    protected static string $resource = LabUsageResource::class;

        protected function afterCreate(): void
    {
        // Ambil semua user dengan role super_admin
        // $superAdmins = User::role('super_admin')->get();
        // pastikan data terbaru dan relasi ter-load
        $record = $this->record->fresh();

        $borrow = $this->record;
        $user = $record->user; // null atau User model

        Notification::make()
            ->title('Lab Usage Notification')
            ->info()
            ->body("<strong>Lab {$record->num_lab}</strong> has been reserved for your class <strong>{$record->grade->id}</strong>.")
            ->actions([
                Action::make('view')
                ->button()
                ->markAsRead()
                ->url(LabUsageResource::getUrl('index', ['record' => $borrow])),
                ])
            ->sendToDatabase($user);
    }
}
