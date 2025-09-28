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

    protected static function booted()
    {
        // Saat mau buat maintenance baru
        static::creating(function (Maintenance $maintenance) {
            $inventory = $maintenance->inventory; 
            if (! $inventory) {
                throw new \Exception("Inventory item not found."); 
            }
            if ($inventory->quantity <= 0) {
                throw new \Exception("Cannot create maintenance: inventory quantity is zero."); 
            }
            // Kurangi inventory
            $inventory->quantity = $inventory->quantity - 1;  // atau sesuai logika
            $inventory->save();
        });

        // Saat update maintenance
        static::updating(function (Maintenance $maintenance) {
            // Jika inventory_id tidak berubah, mungkin tidak perlu cek stok baru
            // Tapi kalau inventori item nya berubah, atau stok harus dikembalikan dari versi lama dulu, dll.
            $originalInventoryId = $maintenance->getOriginal('inventory_id');
            $newInventoryId = $maintenance->inventory_id;

            if ($originalInventoryId !== $newInventoryId) {
                // Kembalikan pada inventory lama
                $oldInventory = \App\Models\Inventory::find($originalInventoryId);
                if ($oldInventory) {
                    $oldInventory->quantity = $oldInventory->quantity + 1;  // "mengembalikan" stok
                    $oldInventory->save();
                }
                // Cek inventory baru
                $newInventory = $maintenance->inventory;
                if (! $newInventory) {
                    throw new \Exception("New inventory item not found.");
                }
                if ($newInventory->quantity <= 0) {
                    throw new \Exception("Cannot update maintenance: new inventory quantity is zero.");
                }
                // Kurangi stok inventory baru
                $newInventory->quantity = $newInventory->quantity - 1;
                $newInventory->save();
            }
            // Jika inventory_id sama, bisa saja kamu tidak butuh perubahan stok
        });

        // Optional: handle delete / soft-delete jika pemulihan stok dibutuhkan
        static::deleting(function (Maintenance $maintenance) {
            // Misal: kembalikan stok inventory jika maintenance dihapus
            $inventory = $maintenance->inventory;
            if ($inventory) {
                $inventory->quantity = $inventory->quantity + 1;
                $inventory->save();
            }
        });
    }

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
}
