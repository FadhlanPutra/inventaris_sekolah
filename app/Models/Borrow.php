<?php

namespace App\Models;

use Spatie\Activitylog\LogOptions;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Spatie\Activitylog\Traits\LogsActivity;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Borrow extends Model
{
    use ClearsResponseCache, LogsActivity;
    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/borrows',
    ];
    protected $fillable = [
        'user_id', 
        'item_id', 
        'quantity', 
        'labusage_id', 
        'borrow_time', 
        'return_time', 
        'status'
    ];

    public function getActivitylogOptions(): LogOptions
    {
        return LogOptions::defaults()
            ->useLogName('borrow')
            ->logOnly(['user_id', 'item_id', 'borrow_time', 'return_time', 'quantity', 'status'])
            ->setDescriptionForEvent(fn (string $eventName) => "Borrow record has been {$eventName}")
            ->dontSubmitEmptyLogs();
    }

    public function labusage(): BelongsTo
    {
        return $this->belongsTo(LabUsage::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function item(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }

    protected static function booted()
    {
        // Saat create → kurangi stok
        static::created(function ($borrow) {
            if ($borrow->item) {
                $borrow->item->decrement('quantity', $borrow->quantity);
            }
        });

        // Saat update
        static::updating(function ($borrow) {
            // Jika status berubah jadi finished → isi return_time
            if ($borrow->isDirty('status') && $borrow->status === 'Finished') {
                $borrow->return_time = now();

                // Kembalikan stok ke inventory
                if ($borrow->item) {
                    $borrow->item->increment('quantity', $borrow->quantity);
                }
            }

            // Jika quantity berubah → sesuaikan stok (hanya berlaku saat edit quantity, biasanya tidak terjadi karena field disable)
            if ($borrow->isDirty('quantity')) {
                $old = $borrow->getOriginal('quantity');
                $new = $borrow->quantity;
                $diff = $new - $old;

                if ($diff > 0) {
                    $borrow->item->decrement('quantity', $diff);
                } elseif ($diff < 0) {
                    $borrow->item->increment('quantity', abs($diff));
                }
            }
        });

        // Saat delete → kembalikan stok
        static::deleted(function ($borrow) {
            if ($borrow->item) {
                $borrow->item->increment('quantity', $borrow->quantity);
            }
        });
    }
}
