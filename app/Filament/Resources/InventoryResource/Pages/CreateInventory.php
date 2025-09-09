<?php

namespace App\Filament\Resources\InventoryResource\Pages;

use Filament\Actions;
use Filament\Resources\Pages\CreateRecord;
use App\Filament\Resources\InventoryResource;

class CreateInventory extends CreateRecord
{
    protected static string $resource = InventoryResource::class;
}
