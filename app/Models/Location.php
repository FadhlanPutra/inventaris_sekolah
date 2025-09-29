<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;

class Location extends Model
{
    use ClearsResponseCache, LogsActivity;
    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/locations',
    ];
    protected $fillable = [
        'name',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('location')
            ->logOnly(['name'])
            ->setDescriptionForEvent(fn (string $eventName) => "Location has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function inventories()
    {
        return $this->hasMany(Inventory::class);
    }
}
