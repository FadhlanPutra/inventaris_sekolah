<?php

namespace App\Filament\Resources;

use App\Filament\Resources\LabUsageResource\Pages;
use App\Filament\Resources\LabUsageResource\RelationManagers;
use App\Models\LabUsage;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class LabUsageResource extends Resource
{
    protected static ?string $model = LabUsage::class;

    protected static ?string $navigationIcon = 'heroicon-o-computer-desktop'; // ikon biasa
    protected static ?string $activeNavigationIcon = 'heroicon-s-computer-desktop'; // ikon ketika aktif

    // 2. Label navigasi
    protected static ?string $navigationLabel = 'Lab Usage';

    // 3. Posisi di menu (urutan)
    protected static ?int $navigationSort = 4; // angka kecil = lebih depan

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
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('num_lab')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('lab_function')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('end_state')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('notes')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('num_lab')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('lab_function')
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_state')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
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
            'index' => Pages\ListLabUsages::route('/'),
            'create' => Pages\CreateLabUsage::route('/create'),
            'edit' => Pages\EditLabUsage::route('/{record}/edit'),
        ];
    }
}

