<?php

namespace App\Models;

use App\Models\Grade;
use Spatie\Activitylog\LogOptions;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use Spatie\Activitylog\Traits\LogsActivity;
use App\Filament\Resources\LabUsageResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabUsage extends Model
{
    use ClearsResponseCache, LogsActivity;

    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/lab-usages',
    ];

    protected $table = 'lab_usages';

    protected $fillable = [
        'user_id',
        'status',
        'location_id',
        'grade_id',
        'num_students',
        'lab_function',
        'end_state',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('lab_usage')
            ->logOnly(['user_id', 'location_id', 'lab_function', 'end_state', 'notes', 'num_students', 'grade_id', 'status'])
            ->setDescriptionForEvent(fn (string $eventName) => "Lab usage has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }
    public function grade(): BelongsTo
    {
        return $this->belongsTo(Grade::class);
    }

    public function location(): BelongsTo
    {
        return $this->belongsTo(Location::class, 'location_id');
    }

    protected static function booted()
    {
        static::saving(function ($labUsage) {
            // Daftar field penting
            $requiredFields = ['location_id', 'grade_id', 'num_students', 'lab_function', 'end_state'];

            // Cek apakah semua field terisi
            $allFilled = collect($requiredFields)->every(fn ($field) => !empty($labUsage->$field));

            // Update status otomatis
            $labUsage->status = $allFilled ? 'Complete' : 'Incomplete';
        });

        static::updated(function ($labUsage) {
            // Kalau status berubah dan sekarang complete → kirim notifikasi ke user
            if ($labUsage->isDirty('status') && $labUsage->status === 'Complete') {
                $user = $labUsage->user;

                if ($user) {
                    Notification::make()
                        ->title('Lab Usage Completed')
                        ->success()
                        ->body("Your lab usage in <strong>Lab {$labUsage->location->name}</strong> has been marked as <span style='color:green;'>Complete</span>.")
                        ->actions([
                            Action::make('view')
                                ->button()
                                ->markAsRead()
                                ->url(LabUsageResource::getUrl('index')),
                        ])
                        ->sendToDatabase($user);
                }
            }
        });
    }
}
