<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Spatie\Activitylog\LogOptions;
use Spatie\Activitylog\Traits\LogsActivity;

class Category extends Model
{
    use ClearsResponseCache, LogsActivity;

    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/categories',
    ];

    protected $fillable = ['name'];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('category')
            ->logOnly(['name'])
            ->setDescriptionForEvent(fn (string $eventName) => "Category has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function inventaris(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
