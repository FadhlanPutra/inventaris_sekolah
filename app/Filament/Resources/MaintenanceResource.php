<?php

namespace App\Filament\Resources;

use App\Exports\MaintenanceCustomExport;
use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use App\Models\Maintenance;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\MaintenanceResource\Pages;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\MaintenanceResource\RelationManagers;

class MaintenanceResource extends Resource
{
    protected static ?string $model = Maintenance::class;

    protected static ?string $navigationIcon = 'heroicon-o-wrench-screwdriver'; // ikon biasa
    protected static ?string $activeNavigationIcon = 'heroicon-s-wrench-screwdriver'; // ikon ketika aktif

    // 2. Label navigasi
    protected static ?string $navigationLabel = 'Maintenance';

    // 3. Posisi di menu (urutan)
    protected static ?int $navigationSort = 7; // angka kecil = lebih depan

    // 5. Tambahkan badge jumlah
    public static function getNavigationBadge(): ?string
    {
        // return Borrow::where('status', 'Pending')->count();
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('inventory_id')
                    ->label('Item Name')
                    ->relationship(
                        name: 'inventory',
                        titleAttribute: 'item_name',
                        modifyQueryUsing: function (Builder $query, string $operation) {
                            // Kondisi: hanya inventory dengan quantity > 0
                            $query->where('quantity', '>', 0);
                            // Opsional: jika ingin berbeda behavior di form edit
                            // if ($operation === 'edit') {
                            //     // misalnya, bagi edit kamu tetap mau semua inventory, atau tetap filter
                            //     $query->where('quantity', '>', 0);
                            // }
                        }
                    )                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('issue')
                    ->label('Issue')
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
        ->description(auth()->user()->can('export_maintenance') ? 'Select data and click "Bulk Actions" to export to Excel.' : null)
            ->columns([
                Tables\Columns\TextColumn::make('inventory.item_name')
                    ->label('Item Name')
                    ->placeholder('Invalid or deleted item')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('issue')
                    ->placeholder('No Issue')
                    ->label('Issue')
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition_before')
                    ->placeholder('No Condition Before')
                    ->searchable(),
                Tables\Columns\TextColumn::make('condition_after')
                    ->placeholder('No Condition After')
                    ->searchable(),
                Tables\Columns\TextColumn::make('add_notes')
                    ->label('Notes')
                    ->placeholder('No Notes')
                    ->tooltip(function ($record) {
                        return $record->add_notes;
                    })
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Start At')
                    ->date('j M, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
                Tables\Columns\TextColumn::make('updated_at')
                    ->label('Finished At')
                    ->date('j M, Y')
                    ->sortable()
                    ->toggleable(isToggledHiddenByDefault: false),
            ])
            ->filters([
                Filter::make('created_at')
                    ->form([
                        DatePicker::make('created_from'),
                        DatePicker::make('created_until'),
                    ])
                    ->query(function (Builder $query, array $data): Builder {
                        return $query
                            ->when(
                                $data['created_from'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '>=', $date),
                            )
                            ->when(
                                $data['created_until'],
                                fn (Builder $query, $date): Builder => $query->whereDate('created_at', '<=', $date),
                            );
                    })
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->visible(fn () => auth()->user()->can('export_maintenance'))
                        ->exports([
                            MaintenanceCustomExport::make('selected')
                                // ->fromTable()
                                ->modifyQueryUsing(fn ($q) => $q->with('inventory')),
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
