<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\ClearsResponseCache;

class Inventaris extends Model
{
    use ClearsResponseCache;
    protected $fillable = ['nama_barang', 'kategori', 'kondisi', 'jumlah', 'status', 'deskripsi'];


    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }
    
    public function maintenances():HasMany
    {
        return $this->hasMany(Maintenance::class);
    }

}
