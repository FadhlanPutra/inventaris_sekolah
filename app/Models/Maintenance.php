<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Maintenance extends Model
{
    use ClearsResponseCache;
    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/maintenances',
    ];
    protected $fillable = ['inventory_id', 'item_name', 'issue', 'condition_before', 'condition_after', 'add_notes'];

    public function inventory(): BelongsTo
    {
        return $this->belongsTo(Inventory::class);
    }
    
}
