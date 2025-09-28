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

    protected static function booted()
    {
        static::saving(function ($model) {
            // Normalisasi dulu kalau mau case insensitive
            $name = trim($model->name);

            $exists = static::query()
                ->when($model->exists, fn ($q) => $q->where('id', '!=', $model->id))
                ->whereRaw('LOWER(name) = ?', [strtolower($name)]) // case-insensitive check
                ->exists();

            if ($exists) {
                throw new \Exception("Category with name '{$name}' has already been taken");
            }

            $model->name = $name;
        });
    }
}
