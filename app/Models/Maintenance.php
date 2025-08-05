<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Model;

class Maintenance extends Model
{
    protected $fillable = ['nama_barang', 'kondisi', 'kerusakan', 'kondisi_sebelum', 'kondisi_sesudah', 'catatan'];

    public function inventaris(): BelongsTo
    {
        return $this->belongsTo(Inventaris::class);
    }
    
}
