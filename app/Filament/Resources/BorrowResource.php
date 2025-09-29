<?php

namespace App\Filament\Resources;

use Carbon\Carbon;
use Filament\Forms;
use Filament\Tables;
use App\Models\Borrow;
use Filament\Forms\Form;
use App\Models\Inventory;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use App\Exports\BorrowCustomExport;
use Filament\Tables\Filters\Filter;
use Filament\Notifications\Notification;
use Filament\Tables\Actions\ActionGroup;
use Filament\Forms\Components\DatePicker;
use Illuminate\Database\Eloquent\Builder;
use Filament\Notifications\Actions\Action;
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
    protected static ?int $navigationSort = 6; // angka kecil = lebih depan

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
    //     return Borrow::where('status', 'Pending')->count() > 5 ? 'warning' : 'primary';
    // }


    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Hidden::make('user_id')
                    ->default(fn () => auth()->id())
                    ->dehydrated(fn () => ! auth()->user()->hasRole('super_admin')),
                Forms\Components\TextInput::make('user_name')
                    ->label('Name')
                    ->default(fn () => auth()->user()->name)
                    ->disabled()
                    ->dehydrated(false)
                    ->formatStateUsing(fn ($state, $record) => $record?->user?->name ?? auth()->user()->name)
                    ->visible(fn () => ! auth()->user()->hasRole('super_admin')),
                Forms\Components\Select::make('user_id')
                    ->label('User')
                    ->relationship('user', 'name') // pastikan ada relasi `user()`
                    ->searchable()
                    ->preload()
                    ->visible(fn () => auth()->user()->hasRole('super_admin')),                  
                Forms\Components\Select::make('item_id')
                    ->label('Item Name')
                    ->relationship(
                        name: 'item',
                        titleAttribute: 'item_name',
                        modifyQueryUsing: fn ($query) => $query->where('status', 'Available'),
                    )                    
                    ->required()
                    ->searchable()
                    ->preload(),
                Forms\Components\Select::make('location_id')
                    ->label('Location')
                    ->required()
                    ->searchable()
                    ->relationship(name: 'location', titleAttribute: 'name')
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
                                    $fail("Borrowed quantity cannot exceed Available stock. ({$inventory->quantity}).");
                                }
                            }
                        },
                    ] : []),
                Forms\Components\Hidden::make('status')
                    ->default('Pending')
                    ->visibleOn('create'),
                Forms\Components\DatePicker::make('borrow_time')
                    ->required()
                    ->readOnly()
                    ->native(false)
                    ->displayFormat('j M, Y') 
                    ->default(now()),
                Forms\Components\Select::make('status')
                    ->options([
                        'Pending'   => 'Pending',
                        'Active'    => 'Active',
                        'Finished'  => 'Finished',
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
                Tables\Columns\TextColumn::make('item.item_name')
                    ->placeholder('Invalid or deleted item')
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('quantity')
                    ->placeholder('No Quantity')
                    ->numeric()
                    ->sortable(),
                Tables\Columns\TextColumn::make('location.name')
                    ->placeholder('Invalid or deleted labusage')
                    ->label('Location')
                    // ->formatStateUsing(fn ($state) => "Lab {$state}")
                    ->searchable()
                    ->sortable(),
                Tables\Columns\TextColumn::make('borrow_time')
                    ->color(fn ($record) =>
                        $record->status !== 'Finished'
                        && Carbon::parse($record->borrow_time)->lt(now()->subDay())
                            ? 'danger'
                            : null
                    )
                    ->placeholder('No Borrow Time')
                    ->date('j M, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('return_time')
                    ->placeholder('Not Returned Yet')
                    ->date('j M, Y')
                    ->sortable(),
                Tables\Columns\TextColumn::make('status')
                    ->placeholder('Invalid Status')
                    ->badge()
                    ->colors([
                        'warning' => 'Pending',
                        'success' => 'Active',
                        'primary'    => 'Finished',
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
                ActionGroup::make([
                    Tables\Actions\EditAction::make(),
                    Tables\Actions\Action::make('Update Status')
                        ->action(function ($record) {
                            $next = match ($record->status) {
                                'Pending' => 'Active',
                                'Active'  => 'Finished',
                                default   => $record->status, // kalau sudah finished, biarin
                            };
                        
                            $record->update(['status' => $next]);
                        
                            // === Kirim notifikasi ke user yang pinjam ===
                            $status = ucfirst($record->status);
                            $itemName = $record->item?->item_name;
                            $user = $record->user;
                        
                            Notification::make()
                                ->title('Borrow status updated!')
                                ->success()
                                ->body("Status borrow <strong>{$itemName}</strong> now: <span style='color: green;'>{$status}</span>")
                                ->actions([
                                    Action::make('view')
                                        ->button()
                                        ->markAsRead()
                                        ->url(BorrowResource::getUrl('index', ['record' => $record])),
                                ])
                                ->sendToDatabase($user);
                        })
                        ->tooltip(fn ($record) => match ($record->status) {
                            'Pending' => 'Set to Active',
                            'Active'  => 'Set to Finished',
                            default   => 'Status is Finished',
                        })
                        ->requiresConfirmation()
                        ->visible(fn (Borrow $record) => 
                            auth()->user()->hasRole('super_admin')
                        )
                        ->icon('heroicon-o-check-circle')
                        ->color('success'),
                    Tables\Actions\DeleteAction::make(),
                    ])
                    ->button()
                    ->label('Actions')
                ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                    ExportBulkAction::make()
                        ->visible(fn () => auth()->user()->can('export_borrow'))
                        ->exports([
                            BorrowCustomExport::make('selected')
                                // ->fromTable()
                                ->modifyQueryUsing(fn ($q) => $q->with('item')),
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
