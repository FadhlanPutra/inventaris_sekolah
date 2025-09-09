<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use Filament\Actions;
use Filament\Forms\Form;
use App\Models\Inventory;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use EightyNine\ExcelImport\ExcelImportAction;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Filament\Resources\InventoryResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\InventoryResource\RelationManagers;


class InventoryResource extends Resource
{
    protected static ?string $model = Inventory::class;

    protected static ?string $navigationIcon = 'heroicon-o-cube'; // ikon biasa
    protected static ?string $activeNavigationIcon = 'heroicon-s-cube'; // ikon ketika aktif

    // 2. Label navigasi
    protected static ?string $navigationLabel = 'Inventory';

    // 3. Posisi di menu (urutan)
    protected static ?int $navigationSort = 2; // angka kecil = lebih depan

    protected function getHeaderActions(): array
    {
        return [
            ExcelImportAction::make()
                ->color('primary'),
            Actions\CreateAction::make(),
        ];
    }

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
                Forms\Components\TextInput::make('item_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\Select::make('category')
                    ->label('Category')
                    ->relationship(name: 'category', titleAttribute: 'name')
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->placeholder(1)
                    ->minValue(0),
                Forms\Components\Select::make('status')
                    ->label('Borrowable')
                    ->hint('Can it be borrowed?')
                    ->options([
                        'available' => 'Available',
                        'unavailable' => 'Unavailable',
                    ])
                    ->required()
                    ->default('borrowable'),
                    // ->disabledOn('create'),
                Forms\Components\Textarea::make('desc')
                    ->columnSpanFull(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('item_name')
                    ->placeholder('No Item Name')
                    ->label('Item Name')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('category.name')->label('Category')
                    ->placeholder('Invalid or deleted category')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->placeholder('No Quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->placeholder('No Status')
                    ->sortable()
                    ->searchable(),
                Tables\Columns\TextColumn::make('desc')
                    ->placeholder('No Description')
                    ->tooltip(function ($record) {
                        return $record->desc;
                    })
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
                        ->visible(fn () => auth()->user()->hasRole('super_admin'))
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
            'index' => Pages\ListInventory::route('/'),
            'create' => Pages\CreateInventory::route('/create'),
            'edit' => Pages\EditInventory::route('/{record}/edit'),
        ];
    }

    
}
