<?php

namespace App\Filament\Resources\PenggunaanLabResource\Pages;

use App\Filament\Resources\PenggunaanLabResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;

class EditPenggunaanLab extends EditRecord
{
    protected static string $resource = PenggunaanLabResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make(),
        ];
    }
}
