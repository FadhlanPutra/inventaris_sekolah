<?php

namespace App\Filament\Resources;

use App\Filament\Resources\PenggunaanLabResource\Pages;
use App\Filament\Resources\PenggunaanLabResource\RelationManagers;
use App\Models\PenggunaanLab;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\SoftDeletingScope;

class PenggunaanLabResource extends Resource
{
    protected static ?string $model = PenggunaanLab::class;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('full_name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('no_lab')
                    ->required()
                    ->numeric(),
                Forms\Components\TextInput::make('fungsi_lab')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('kondisi_akhir')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('catatan')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('full_name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('no_lab')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('fungsi_lab')
                    ->searchable(),
                Tables\Columns\TextColumn::make('kondisi_akhir')
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
            'index' => Pages\ListPenggunaanLabs::route('/'),
            'create' => Pages\CreatePenggunaanLab::route('/create'),
            'edit' => Pages\EditPenggunaanLab::route('/{record}/edit'),
        ];
    }
}
