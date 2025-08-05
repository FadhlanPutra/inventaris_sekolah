<?php

namespace App\Filament\Resources;

use App\Filament\Resources\MaintenanceResource\Pages;
use App\Filament\Resources\MaintenanceResource\RelationManagers;
use App\Models\Maintenance;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;

class MaintenanceResource extends Resource
{
    protected static ?string $model = Maintenance::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('inventaris_id')
                    ->label('Nama_barang')
                    ->relationship(name: 'inventaris', titleAttribute: 'nama_barang')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('kondisi')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kerusakan')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kondisi_sebelum')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kondisi_sesudah')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('catatan')
                    ->required()
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('inventaris.nama_barang')
                    ->label('nama_barang')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kondisi')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kerusakan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kondisi_sebelum')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kondisi_sesudah')
                    ->searchable(),
                Tables\Columns\TextColumn::make('catatan')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
                Tables\Columns\TextColumn::make('updated_at')
                    ->dateTime()
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: true),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->exports([
                            ExcelExport::make('table')
                                ->fromTable()
                                ->withFilename(fn ($resource, $livewire, $model) =>
                                    sprintf('%s-%s', $model::query()->getModel()->getTable(), now()->format('Ymd'))
                                ),
                        ]),
                ]),
            ]);
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListMaintenances::route('/'),
            'create' => Pages\CreateMaintenance::route('/create'),
            'edit' => Pages\EditMaintenance::route('/{record}/edit'),
        ];
    }
}
