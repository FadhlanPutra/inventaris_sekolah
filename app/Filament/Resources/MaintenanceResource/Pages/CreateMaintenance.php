<?php

namespace App\Filament\Resources\MaintenanceResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\MaintenanceResource;

class CreateMaintenance extends CreateRecord
{
    protected static string $resource = MaintenanceResource::class;
}
