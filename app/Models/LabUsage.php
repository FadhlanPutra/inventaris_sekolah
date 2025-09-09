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
    protected $fillable = ['user_id', 'num_lab', 'lab_function', 'end_state', 'notes', 'num_students', 'class_name', 'status'];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    protected static function booted()
    {
        static::saving(function ($labUsage) {
            // Daftar field penting
            $requiredFields = ['num_lab', 'class_name', 'num_students', 'lab_function', 'end_state'];

            // Cek apakah semua field terisi
            $allFilled = collect($requiredFields)->every(fn($field) => !empty($labUsage->$field));

            // Update status otomatis
            $labUsage->status = $allFilled ? 'complete' : 'incomplete';
        });
    }
}
