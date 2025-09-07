<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Borrow;
use Filament\Forms\Form;
use App\Models\Inventory;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\BorrowResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
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
        // return Borrow::where('status', 'pending')->count();
        return static::getModel()::count();
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
                // Forms\Components\DatePicker::make('return_time')
                //     ->nullable(),
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
                    ->options(
                        \App\Models\LabUsage::pluck('num_lab', 'id')
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
                    ->colors([
                        'warning' => 'pending',
                        'success' => 'active',
                        'primary'    => 'finished',
                    ])
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
}
