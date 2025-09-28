<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use App\Traits\ClearsResponseCache;
use Illuminate\Support\Facades\Log;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

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

    protected static function booted()
    {
        parent::booted();

        static::saving(function (Inventory $inventory) {
                        // Debug: Log data sebelum disimpan
                        Log::info('Model Inventory saving:', [
                            'item_name' => $inventory->item_name,
                            'category_id' => $inventory->category_id,
                            'quantity' => $inventory->quantity,
                            'status' => $inventory->status,
                            'desc' => $inventory->desc,
                        ]);


            // Normalisasi nama
            $name = trim($inventory->item_name);

            // Cek duplikat case-insensitive
            $exists = static::query()
                ->when($inventory->exists, fn ($q) => $q->where('id', '!=', $inventory->id))
                ->whereRaw('LOWER(item_name) = ?', [strtolower($name)])
                ->exists();

            if ($exists) {
                throw new \Exception("Item with name '{$name}' has already been taken");
            }

            // Simpan nama dengan format konsisten (opsional: semua lowercase)
            $inventory->item_name = $name;

            // Logic status otomatis
            if ($inventory->quantity <= 0) {
                $inventory->status = 'Unavailable';
            } else {
                // Bisa pilih auto set available kalau quantity > 0
                // $inventory->status = 'Available';
            }

            Log::info('Model Inventory after processing:', [
                'item_name' => $inventory->item_name,
                'category_id' => $inventory->category_id,
                'quantity' => $inventory->quantity,
                'status' => $inventory->status,
                'desc' => $inventory->desc,
            ]);
        });
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
