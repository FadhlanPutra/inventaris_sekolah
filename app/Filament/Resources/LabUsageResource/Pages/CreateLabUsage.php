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
        $superAdmins = User::role('super_admin')->get();

        $numLab = $this->record->num_lab;
        $borrow = $this->record;

        Notification::make()
            ->title('Someone used an lab!')
            ->info()
            ->body("lab {$numLab} used by " . auth()->user()->name)
            ->actions([
                Action::make('view')
                ->button()
                ->markAsRead()
                ->url(LabUsageResource::getUrl('edit', ['record' => $borrow])),
                ])
            ->sendToDatabase($superAdmins);
    }
}
