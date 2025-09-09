<?php

namespace App\Filament\Resources;

use Filament\Forms;
use Filament\Tables;
use App\Models\Borrow;
use App\Models\LabUsage;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Filament\Tables\Actions\Action;
use Filament\Tables\Filters\Filter;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use pxlrbt\FilamentExcel\Exports\ExcelExport;
use App\Filament\Resources\LabUsageResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use pxlrbt\FilamentExcel\Actions\Tables\ExportBulkAction;
use App\Filament\Resources\LabUsageResource\RelationManagers;


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
        $query = static::getModel()::query();

        // Kalau bukan super_admin, filter data sesuai user
        if (!auth()->user()->hasRole('super_admin')) {
            $query->where('user_id', auth()->id());
        }

        return $query->count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Select::make('user_id')
                    ->label('Teacher Name')
                        ->relationship(
                            name: 'user',
                            titleAttribute: 'name',
                            modifyQueryUsing: fn ($query) => $query->whereHas('roles', function ($q) {
                                $q->whereIn('name', ['super_admin', 'guru']);
                            })
                        )
                    ->searchable()
                    ->preload()
                    ->default(auth()->id())
                    ->required()
                    ->disabled(fn (string $context) => $context === 'edit'),
                Forms\Components\Select::make('num_lab')
                    ->label('Number Lab')
                    ->required()
                    ->options(function ($get, $record) {
                        $all = range(1, 6);
                    
                        // Ambil semua num_lab yang sudah dipakai HARI INI
                        $used = LabUsage::whereDate('created_at', now()->toDateString())
                            ->pluck('num_lab')
                            ->toArray();
                    
                        // Tambahkan value lama record supaya tetap muncul
                        if ($record?->num_lab) {
                            $used = array_diff($used, [$record->num_lab]);
                        }
                    
                        $available = array_diff($all, $used);
                    
                        // Tampilkan label "Lab X"
                        return collect($available)
                            ->mapWithKeys(fn ($num) => [$num => "Lab {$num}"])
                            ->toArray();
                    })
                    ->placeholder('Select Lab Number'),
                Forms\Components\Select::make('class_name')
                    ->label('Class')
                    ->options([
                        'X RPL'  => 'X RPL',
                        'X DKV'  => 'X DKV',
                        'X TKJ'  => 'X TKJ',
                        'XI RPL' => 'XI RPL',
                        'XI DKV' => 'XI DKV',
                        'XI TKJ' => 'XI TKJ',
                        'XII RPL'=> 'XII RPL',
                        'XII DKV'=> 'XII DKV',
                        'XII TKJ'=> 'XII TKJ',
                    ])
                    ->required(),
                Forms\Components\TextInput::make('num_students')
                    ->label('Number of Students')
                    ->numeric()
                    ->maxValue(50)
                    ->nullable(),
                Forms\Components\TextInput::make('lab_function')
                    ->nullable()
                    ->maxLength(255),
                Forms\Components\TextInput::make('end_state')
                    ->maxLength(255)
                    // ->required(fn (string $context) => $context === 'edit'),
                    ->nullable(),
                Forms\Components\TextInput::make('notes')
                    ->columnSpanFull()
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('user.name')
                    ->label('Teacher')
                    ->placeholder("Invalid or deleted user")
                    ->searchable(),
                Tables\Columns\TextColumn::make('num_lab')
                    ->label('Status')
                    ->badge()
                    ->color(fn ($record) => $record->status === 'complete' ? 'success' : 'danger')
                    ->formatStateUsing(function ($record) {
                        $labNumber = $record->num_lab ? "Lab {$record->num_lab}" : 'No Lab';
                        $class = $record->class_name ?? 'No Class';
                        $numStudents = $record->num_students ? "Students: {$record->num_students}" : 'No child';
                        $status = ucfirst($record->status ?? 'incomplete');

                        return ("{$status} | {$labNumber} | {$class} | {$numStudents}");
                    }),
                Tables\Columns\TextColumn::make('lab_function')
                    ->label('Lab Function')
                    ->placeholder('Not Filled')
                    ->searchable(),
                Tables\Columns\TextColumn::make('end_state')
                    ->label('End State')
                    ->searchable()
                    ->placeholder('Pending'),
                Tables\Columns\TextColumn::make('notes')
                    ->placeholder('No Notes')
                    ->searchable()
                    ->tooltip(function ($record) {
                        return $record->notes;
                    }),
                Tables\Columns\TextColumn::make('created_at')
                    ->label('Used At')
                    ->dateTime()
                    ->sortable(),
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
                Tables\Filters\SelectFilter::make('num_lab')
                    ->label('Lab Number')
                    ->options([
                        1 => 'Lab 1',
                        2 => 'Lab 2',
                        3 => 'Lab 3',
                        4 => 'Lab 4',
                        5 => 'Lab 5',
                        6 => 'Lab 6',
                    ]),
            ])
            ->actions([
                Tables\Actions\EditAction::make()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),
                Action::make('Done')
                    ->color('success')
                    ->icon('heroicon-o-check-circle')
                    ->form([
                        Select::make('class_name')
                            ->label('Class')
                            ->options([
                                'X RPL'  => 'X RPL',
                                'X DKV'  => 'X DKV',
                                'X TKJ'  => 'X TKJ',
                                'XI RPL' => 'XI RPL',
                                'XI DKV' => 'XI DKV',
                                'XI TKJ' => 'XI TKJ',
                                'XII RPL'=> 'XII RPL',
                                'XII DKV'=> 'XII DKV',
                                'XII TKJ'=> 'XII TKJ',
                            ])
                            ->default(fn ($record) => $record->class_name)
                            ->required(),
                            
                        TextInput::make('num_students')
                            ->label('Number of students')
                            ->numeric()
                            ->default(fn ($record) => $record->num_students)
                            ->required(),
                            
                        TextInput::make('lab_function')
                            ->label('Lab Function')
                            ->default(fn ($record) => $record->lab_function)
                            ->required(),
                            
                        TextInput::make('end_state')
                            ->label('End State')
                            ->default(fn ($record) => $record->end_state)
                            ->required(),
                            
                        Textarea::make('notes')
                            ->label('Notes')
                            ->default(fn ($record) => $record->notes)
                            ->nullable(),
                    ])
                    ->action(function (array $data, LabUsage $record): void {
                        $record->update($data);
                    })
                    ->visible(fn (LabUsage $record) => 
                        auth()->user()->hasRole('super_admin') || $record->status === 'incomplete'
                    )
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
            ])
            ->defaultSort('created_at', 'desc');
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