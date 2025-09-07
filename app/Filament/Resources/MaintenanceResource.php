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

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver'; // ikon biasa
    protected static ?string $activeNavigationIcon = 'heroicon-s-wrench-screwdriver'; // ikon ketika aktif

    // 2. Label navigasi
    protected static ?string $navigationLabel = 'Maintenance';

    // 3. Posisi di menu (urutan)
    protected static ?int $navigationSort = 5; // angka kecil = lebih depan

    // 5. Tambahkan badge jumlah
    public static function getNavigationBadge(): ?string
    {
        // return Borrow::where('status', 'pending')->count();
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('inventory_id')
                    ->label('Item Name')
                    ->relationship(name: 'inventory', titleAttribute: 'item_name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('condition')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('breaking')
                    ->label('Damage')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('condition_before')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('condition_after')
                    ->maxLength(255),
                Forms\Components\TextInput::make('add_notes')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->description('Select data and click "Bulk Actions" to export to Excel.')
            ->columns([
                Tables\Columns\TextColumn::make('inventory.item_name')
                    ->label('Item Name')
                    ->placeholder('Invalid or deleted item')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition')
                    ->placeholder('No Condition')
                    ->searchable(),
                Tables\Columns\TextColumn::make('breaking')
                    ->placeholder('No Damage')
                    ->label('Damage')
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition_before')
                    ->placeholder('No Condition Before')
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition_after')
                    ->placeholder('No Condition After')
                    ->searchable(),
                Tables\Columns\TextColumn::make('add_notes')
                    ->placeholder('No Notes')
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
