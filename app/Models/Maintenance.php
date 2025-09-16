<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Maintenance extends Model
{
    use ClearsResponseCache, LogsActivity;

    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/maintenances',
    ];

    protected $fillable = [
        'inventory_id',
        'item_name',
        'issue',
        'condition_before',
        'condition_after',
        'add_notes',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('maintenance')
            ->logOnly([
                'inventory_id',
                'item_name',
                'issue',
                'condition_before',
                'condition_after',
                'add_notes',
            ])
            ->setDescriptionForEvent(fn(string $eventName) => "Maintenance record has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
