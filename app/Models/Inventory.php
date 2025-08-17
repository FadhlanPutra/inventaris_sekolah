<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\ClearsResponseCache;

class Inventory extends Model
{
    use ClearsResponseCache;
    protected static array $cacheClearUrls = [
        '/dashboard/inventories',
    ];
    protected $table = 'inventories';
    protected $fillable = ['item_name', 'category', 'condition', 'quantity', 'status', 'desc'];


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function maintenances():HasMany
    {
        return $this->hasMany(Maintenance::class);
    }
}
