<?php

namespace App\Filament\Resources\PenggunaanLabResource\Pages;

use App\Filament\Resources\PenggunaanLabResource;
use Filament\Actions;
use Filament\Resources\Pages\ListRecords;

class ListPenggunaanLabs extends ListRecords
{
    protected static string $resource = PenggunaanLabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\CreateAction::make(),
        ];
    }
}
