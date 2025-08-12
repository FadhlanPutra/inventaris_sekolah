<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsResponseCache;

class LabUsage extends Model
{
    use ClearsResponseCache;

    protected $table = 'lab_usages';
    protected $fillable = ['full_name', 'num_lab', 'lab_function', 'end_state', 'notes'];
}
