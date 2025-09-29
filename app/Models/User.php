<?php

namespace App\Models;

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
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class User extends Authenticatable implements FilamentUser, HasAvatar
{
    use HasFactory, Notifiable, HasRoles, ClearsResponseCache, LogsActivity;

    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/users',
    ];

    protected $fillable = [
        'name',
        'email',
        'password',
        'avatar_url',
        'role',
        'has_seen_tour',
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
        ];
    }

    protected static function booted()
    {
        static::updating(function (User $user) {
            if ($user->isDirty('email')) {
                $user->email_verified_at = null;
            }
        });

         static::creating(function (User $user) {
            if (!$user->hasRole('siswa')) {
            }
        });

        static::created(function (User $user) {
            if (!$user->hasRole('siswa')) {
                $user->assignRole('siswa');
            }
        });
    }

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
        return true;
    }

    public function getRoleNameAttribute(): string
    {
        return $this->roles->pluck('name')->implode(', ');
    }

    public function getFilamentAvatarUrl(): ?string 
    {
        $avatarColumn = config('filament-edit-profile.avatar_column', 'avatar_url');

        if (!$this->$avatarColumn) {
            return null;
        }

        $disk = config('filament-edit-profile.disk', 'public');

        try {
            return Storage::disk($disk)->url($this->$avatarColumn);
        } catch (\Exception $e) {
            return null;
        }
    }

    // Logging konfigurasi
    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('user')
            ->logOnly(['name', 'email', 'role_name', 'avatar_url', 'theme_color', 'theme'])
            ->setDescriptionForEvent(fn(string $eventName) => "User has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }
}
