<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Borrow;
use Filament\Forms\Form;
use App\Models\Inventory;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Filament\Resources\BorrowResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\BorrowResource\RelationManagers;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    // 1. Ikon default dan ikon saat aktif
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down'; // ikon biasa
    protected static ?string $activeNavigationIcon = 'heroicon-s-archive-box'; // ikon ketika aktif

    // 2. Label navigasi
    protected static ?string $navigationLabel = 'Borrow';

    // 3. Posisi di menu (urutan)
    protected static ?int $navigationSort = 4; // angka kecil = lebih depan

    // 4. Grup navigasi
    // protected static ?string $navigationGroup = 'Manajemen';

    // 5. Tambahkan badge jumlah
    public static function getNavigationBadge(): ?string
    {
        $query = static::getModel()::query();
        
        // Kalau bukan super_admin, filter data sesuai user
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        }
    
        return $query->count();
    }

    // 6. Warna badge kondisional
    // public static function getNavigationBadgeColor(): ?string
    // {
    //     return Borrow::where('status', 'pending')->count() > 5 ? 'warning' : 'primary';
    // }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->id())
                    ->dehydrated(true), // ini yang masuk DB
                Forms\Components\TextInput::make('user_name')
                    ->label('Name')
                    ->default(fn () => auth()->user()->name)
                    ->disabled()
                    ->dehydrated(false) // tidak masuk DB
                    ->formatStateUsing(fn ($state, $record) => $record?->user?->name ?? auth()->user()->name),
                Forms\Components\DateTimePicker::make('borrow_time')
                    ->required()
                    ->readOnly()
                    ->default(now()),
                Forms\Components\Select::make('item_id')
                    ->label('Name Item')
                    ->relationship(
                        name: 'item',
                        titleAttribute: 'item_name',
                        modifyQueryUsing: fn ($query) => $query->where('status', 'available'),
                    )                    
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('labusage_id')
                    ->label('Location')
                    ->hint('Only shows labs used today')
                    ->options(
                        \App\Models\LabUsage::whereDate('created_at', now()) // hanya yang dibuat hari ini
                            ->pluck('num_lab', 'id')
                            ->map(fn ($num) => "Lab {$num}")
                    )
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->placeholder(1)
                    ->minValue(1)
                    ->rules(fn (callable $get, $context) => $context === 'create' ? [
                        function (string $attribute, $value, \Closure $fail) use ($get) {
                            $inventoryId = $get('item_id'); // ambil item_id dari Select
                            if ($inventoryId) {
                                $inventory = Inventory::find($inventoryId);
                                if ($inventory && $value > $inventory->quantity) {
                                    $fail("Jumlah peminjaman tidak boleh lebih dari stok ({$inventory->quantity}).");
                                }
                            }
                        },
                    ] : []),
                Forms\Components\Hidden::make('status')
                    ->default('pending')
                    ->visibleOn('create'),
                Forms\Components\Select::make('status')
                    ->options([
                        'pending'   => 'Pending',
                        'active'    => 'Active',
                        'finished'  => 'Finished',
                    ])
                    ->visibleOn('edit')
                    ->required(),
                    
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->placeholder("Invalid or deleted user")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('borrow_time')
                    ->placeholder('No Borrow Time')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_time')
                    ->placeholder('Not Returned Yet')
                    ->dateTime()
                    ->sortable(),
                Tables\Columns\TextColumn::make('labusage.num_lab')
                    ->placeholder('Invalid or deleted labusage')
                    ->label('Location')
                    ->formatStateUsing(fn ($state) => "Lab {$state}")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('item.item_name')
                    ->placeholder('Invalid or deleted item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->placeholder('No Quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->placeholder('Invalid Status')
                    ->badge()
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'primary'    => 'finished',
                    ])
                    ->formatStateUsing(fn ($state) => ucfirst($state))
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
                    ->label('Range')
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
                    }),
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
            'index' => Pages\ListBorrows::route('/'),
            'create' => Pages\CreateBorrow::route('/create'),
            'edit' => Pages\EditBorrow::route('/{record}/edit'),
        ];
    }

    public static function getEloquentQuery(): Builder
    {
        $query = parent::getEloquentQuery();

        // Kalau admin → tampilkan semua
        if (auth()->user()->hasRole('super_admin')) {
            return $query;
        }

        // Kalau bukan admin → tampilkan data user itu sendiri
        return $query->where('user_id', auth()->id());
    }
}
