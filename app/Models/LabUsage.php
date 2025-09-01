<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsResponseCache;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class LabUsage extends Model
{
    use ClearsResponseCache;
    protected static array $cacheClearUrls = [
        '/dashboard',
        '/dashboard/lab-usages',
    ];
    protected $table = 'lab_usages';
    protected $fillable = ['user_id', 'num_lab', 'lab_function', 'end_state', 'notes'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(\App\Models\User::class, 'user_id', 'id');
    }
}
