<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsResponseCache;

class PenggunaanLab extends Model
{
    use ClearsResponseCache;
    protected $fillable = ['full_name', 'num_lab', 'lab_function', 'end_state', 'notes'];
}
