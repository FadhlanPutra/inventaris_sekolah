<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\ClearsResponseCache;

class Category extends Model
{
    use ClearsResponseCache;
    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/categories',
    ];
    protected $fillable = ['name'];

    public function inventaris(): HasMany
    {
        return $this->hasMany(Inventory::class);
    }
}
