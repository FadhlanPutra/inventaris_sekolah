<?php

namespace App\Filament\Resources\LabUsageResource\Pages;

use App\Filament\Resources\LabUsageResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

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
