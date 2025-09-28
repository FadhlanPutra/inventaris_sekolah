<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Filament\Notifications\Notification;
use Filament\Notifications\Actions\Action;
use App\Filament\Resources\LabUsageResource;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

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
        'num_lab',
        'class_name',
        'num_students',
        'lab_function',
        'end_state',
        'notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('lab_usage')
            ->logOnly(['user_id', 'num_lab', 'lab_function', 'end_state', 'notes', 'num_students', 'class_name', 'status'])
            ->setDescriptionForEvent(fn (string $eventName) => "Lab usage has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected static function booted()
    {
        static::saving(function ($labUsage) {
            // Daftar field penting
            $requiredFields = ['num_lab', 'class_name', 'num_students', 'lab_function', 'end_state'];

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
                        ->body("Your lab usage in <strong>Lab {$labUsage->num_lab}</strong> has been marked as <span style='color:green;'>Complete</span>.")
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
