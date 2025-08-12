<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Traits\ClearsResponseCache;

class Borrow extends Model
{
    protected $fillable = ['user_id', 'borrow_time', 'return_time', 'penggunaanlab_id', 'quantity', 'status'];

    public function penggunaanlab(): BelongsTo
    {
        return $this->belongsTo(PenggunaanLab::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }
}
