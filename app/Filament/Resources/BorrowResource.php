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

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

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
                    Forms\Components\Select::make('penggunaanlab_id')
                    ->label('Location')
                    ->relationship(name: 'penggunaanlab', titleAttribute: 'no_lab')
                    ->searchable()
                    ->preload(),
                Forms\Components\TextInput::make('quantity')
                    ->required()
                    ->numeric(),
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
                Tables\Columns\TextColumn::make('location')
                    ->searchable(),
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
