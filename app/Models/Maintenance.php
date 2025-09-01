<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsResponseCache;

class Maintenance extends Model
{
    use ClearsResponseCache;
    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/maintenances',
    ];
    protected $fillable = ['inventory_id', 'item_name', 'condition', 'breaking', 'condition_before', 'condition_after', 'add_notes'];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
    
}
