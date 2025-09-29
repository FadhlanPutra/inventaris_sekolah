<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Grade extends Model
{
    use ClearsResponseCache, LogsActivity;

    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/grades',
    ];

    protected $fillable = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('Grade')
            ->logOnly(['name'])
            ->setDescriptionForEvent(fn (string $eventName) => "Grade has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

}
