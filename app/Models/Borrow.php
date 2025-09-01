<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\ClearsResponseCache;

class Borrow extends Model
{
    use ClearsResponseCache;
    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/borrows',
    ];
    protected $fillable = ['user_id', 'item_id', 'borrow_time', 'return_time', 'labusage_id', 'quantity', 'status'];

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
        if ($borrow->isDirty('status') && $borrow->status === 'finished') {
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
