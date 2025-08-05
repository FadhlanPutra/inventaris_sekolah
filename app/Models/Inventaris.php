<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Inventaris extends Model
{
    protected $fillable = ['nama_barang', 'kategori', 'kondisi', 'jumlah', 'status', 'deskripsi'];

    public function category(): BelongsTo
    {
        return $this->belongsTo(Category::class);
    }

}
