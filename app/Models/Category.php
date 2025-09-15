<?php

namespace App\Models;

use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

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
