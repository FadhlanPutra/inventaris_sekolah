<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;

use Filament\Panel;
use Illuminate\Support\Str;
use App\Traits\ClearsResponseCache;
use Illuminate\Support\Facades\Auth;
use Spatie\Permission\Traits\HasRoles;
use Illuminate\Support\Facades\Storage;
use Illuminate\Notifications\Notifiable;
use Filament\Models\Contracts\HasAvatar;
use Filament\Models\Contracts\FilamentUser;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Filament\Models\Concerns\SendsFilamentPasswordResetNotification;

class User extends Authenticatable implements FilamentUser, HasAvatar, MustVerifyEmail
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasRoles, ClearsResponseCache;
    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/users',
    ];

    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'role',
        'has_seen_tour',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    /**
     * Get the user's initials
     */
    public function initials(): string
    {
        return Str::of($this->name)
            ->explode(' ')
            ->take(2)
            ->map(fn ($word) => Str::substr($word, 0, 1))
            ->implode('');
    }
    
        public function canAccessPanel(Panel $panel): bool
    {
        return true; // beri akses semua user
    }

    public function getFilamentAvatarUrl(): ?string 
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');

        // Pastikan tidak ada infinite loop
        if (!$this->$avatarColumn) {
            return null;
        }
        
        // Gunakan disk yang sama dengan konfigurasi package
        $disk = config('filament-edit-profile.disk', 'public');

        try {
            return Storage::disk($disk)->url($this->$avatarColumn);
        } catch (\Exception $e) {
            return null;
        }
    }
}