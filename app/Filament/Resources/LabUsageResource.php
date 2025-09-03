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
    protected static ?int $navigationSort = 3; // angka kecil = lebih depan

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
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->id())
                    ->dehydrated(true), // ini yang masuk DB
                Forms\Components\TextInput::make('user_name')
                    ->required()
                    ->label('Name')
                    ->dehydrated(false)
                    ->default(fn () => auth()->user()->name)
                    ->disabled()
                    ->formatStateUsing(fn ($state, $record) => $record?->user?->name ?? auth()->user()->name),
                Forms\Components\Select::make('num_lab')
                    ->label('Number Lab')
                    ->required()
                    ->options(function () {
                        $all = range(1, 6);

                        // Ambil semua num_lab yang sudah dipakai HARI INI saja
                        $used = LabUsage::whereDate('created_at', now()->toDateString())
                            ->pluck('num_lab')
                            ->toArray();

                        // Hitung lab yang masih tersedia
                        $available = array_diff($all, $used);

                        // Tampilkan label "Lab X"
                        return collect($available)
                            ->mapWithKeys(fn ($num) => [$num => "Lab {$num}"])
                            ->toArray();
                    })
                    ->placeholder('Select Lab Number'),
                Forms\Components\TextInput::make('lab_function')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('end_state')
                    ->maxLength(255),
                Forms\Components\TextInput::make('notes')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->placeholder("Invalid or deleted user")
                    ->searchable(),
                Tables\Columns\TextColumn::make('num_lab')
                    ->formatStateUsing(fn ($state) => "Lab {$state}")
                    ->sortable(),
                Tables\Columns\TextColumn::make('lab_function')
                    ->label('Lab Function')
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_state')
                    ->label('End State')
                    ->placeholder('No End State')
                    ->searchable(),
                Tables\Columns\TextColumn::make('notes')
                    ->placeholder('No Notes')
                    ->searchable(),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Borrowed At')
                    ->dateTime()
                    ->sortable(),
                    // ->toggleable(isToggledHiddenByDefault: true),
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

