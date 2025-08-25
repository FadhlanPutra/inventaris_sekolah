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
}
