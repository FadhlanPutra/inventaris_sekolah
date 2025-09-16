<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Inventory extends Model
{
    use ClearsResponseCache, LogsActivity;

    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/inventories',
    ];

    protected $table = 'inventories';

    protected $fillable = [
        'item_name',
        'category_id',
        'quantity',
        'status',
        'desc',
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('inventory')
            ->logOnly(['item_name', 'category_id', 'quantity', 'status', 'desc'])
            ->setDescriptionForEvent(fn (string $eventName) => "Inventory has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

    public function maintenances(): HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

    public function borrow(): HasMany
    {
        return $this->hasMany(Borrow::class);
    }
}
