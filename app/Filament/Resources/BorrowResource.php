<?php

namespace App\Filament\Resources;

use App\Filament\Resources\BorrowResource\Pages;
use App\Filament\Resources\BorrowResource\RelationManagers;
use App\Models\Borrow;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class BorrowResource extends Resource
{
    protected static ?string $model = Borrow::class;

    // 1. Ikon default dan ikon saat aktif
    protected static ?string $navigationIcon = 'heroicon-o-archive-box-arrow-down'; // ikon biasa
    protected static ?string $activeNavigationIcon = 'heroicon-s-archive-box'; // ikon ketika aktif

    // 2. Label navigasi
    protected static ?string $navigationLabel = 'Borrow';

    // 3. Posisi di menu (urutan)
    protected static ?int $navigationSort = 3; // angka kecil = lebih depan

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
                Forms\Components\Select::make('user_id')
                    ->label('Name')
                    ->relationship(name: 'user', titleAttribute: 'name')
                    ->searchable()
                    ->preload(),
                Forms\Components\DatePicker::make('borrow_time')
                    ->required()
                    ->default(now()),
                Forms\Components\DatePicker::make('return_time')
                    ->nullable(),
                Forms\Components\Select::make('labusage_id')
                    ->label('Location')
                    ->relationship(name: 'labusage', titleAttribute: 'num_lab')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric()
                    ->minValue(1),
                Forms\Components\Select::make('status')
                    ->options([
                        'available' => 'Available',
                        'unavailable' => 'Unavailable',
                    ])
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user_id')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('borrow_time')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_time')
                    ->date()
                    ->sortable(),
                Tables\Columns\TextColumn::make('labusage.num_lab')
                    ->label('Location')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
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
