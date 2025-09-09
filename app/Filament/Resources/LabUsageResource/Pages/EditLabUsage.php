<?php

namespace App\Filament\Resources\LabUsageResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use App\Filament\Resources\LabUsageResource;

class EditLabUsage extends EditRecord
{
    protected static string $resource = LabUsageResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
