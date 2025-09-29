<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\User;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Resources\Resource;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;
use Filament\Tables\Filters\SelectFilter;
use Illuminate\Database\Eloquent\Builder;
use App\Filament\Resources\UserResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\UserResource\RelationManagers;

class UserResource extends Resource
{
    protected static ?string $model = User::class;

    protected static ?string $navigationIcon = 'heroicon-o-users'; // ikon biasa
    protected static ?string $activeNavigationIcon = 'heroicon-s-users'; // ikon ketika aktif

    // 2. Label navigasi
    protected static ?string $navigationLabel = 'Users';

    // 3. Posisi di menu (urutan)
    protected static ?int $navigationSort = 8; // angka kecil = lebih depan

    // 4. Grup navigasi
    protected static ?string $navigationGroup = 'Users';

    // 5. Tambahkan badge jumlah
    public static function getNavigationBadge(): ?string
    {
        // return Borrow::where('status', 'Pending')->count();
        return static::getModel()::count();
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\TextInput::make('name')
                    ->required()
                    ->maxLength(255),
                Forms\Components\TextInput::make('email')
                    ->email()
                    ->unique(ignoreRecord: true)
                    ->required()
                    ->maxLength(255)    
                    ->helperText(fn (string $operation): ?string => 
                        $operation === 'edit'
                            ? 'Updating this email will reset its verification status until it is verified again.'
                            : null
                    ),
                Forms\Components\TextInput::make('password')
                    ->password()
                    ->revealable()
                    ->minLength(8)
                    ->dehydrateStateUsing(fn ($state) => Hash::make($state))
                    ->dehydrated(fn ($state) => filled($state))
                    ->required(fn (string $context): bool => $context === 'create'),
                Forms\Components\Select::make('roles')
                    ->relationship('roles', 'name')
                    ->required()
                    ->preload()
                    ->searchable(),
            ]);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                Tables\Columns\TextColumn::make('name')
                    ->placeholder('No Name')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email')
                    ->placeholder('No Email')
                    ->searchable(),
                Tables\Columns\TextColumn::make('email_verified_at')
                    ->date('j M, Y')
                    ->placeholder('Not Verified')
                    ->sortable(),
                Tables\Columns\TextColumn::make('roles.name')
                    ->placeholder('No Role'),
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
                SelectFilter::make('roles')
                    ->label('Role')
                    ->relationship('roles', 'name')
                    ->options([
                        'super_admin' => 'Super Admin',
                        'guru'        => 'Guru',
                        'siswa'       => 'Siswa',
                    ])
                    ->multiple()
                    ->searchable()
                    ->preload(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),

                Tables\Actions\Action::make('updateRole')
                    ->label('Update Role')
                    ->form([
                        Forms\Components\Select::make('role')
                            ->label('Role')
                            ->options([
                                'super_admin' => 'Super Admin',
                                'guru'        => 'Guru',
                                'siswa'       => 'Siswa',
                            ])
                            ->required(),
                    ])
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => 'Update Role for "'.$record->name.'"')
                    ->modalDescription(fn (User $record) => 'Are you sure you want to do this for user "'.$record->email.'"?')
                    ->action(function (User $record, array $data): void {
                        // sync role ke user
                        $record->syncRoles([$data['role']]);
                    })
                    ->icon('heroicon-o-pencil-square')
                    ->color('warning'),

                Tables\Actions\Action::make('resendVerification')
                    ->label('Resend verification email')
                    ->requiresConfirmation()
                    ->modalHeading(fn (User $record) => 'Resend verification for "'.$record->name.'"')
                    ->modalDescription(fn (User $record) => 'An email will be sent to '.$record->email)
                    ->action(function (User $record): void {
                        $record->sendEmailVerificationNotification();
                    
                        Notification::make()
                            ->title('The verification email was sent successfully')
                            ->body('Email sent to '.$record->email)
                            ->success()
                            ->send();
                    })
                    ->icon('heroicon-o-envelope')
                    ->visible(fn (User $record): bool => is_null($record->email_verified_at))
                    ->color('success'),
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
            'index' => Pages\ListUsers::route('/'),
            'create' => Pages\CreateUser::route('/create'),
            'edit' => Pages\EditUser::route('/{record}/edit'),
        ];
    }
}
