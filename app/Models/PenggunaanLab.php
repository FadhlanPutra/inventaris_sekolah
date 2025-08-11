<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\ClearsResponseCache;

class PenggunaanLab extends Model
{
    use ClearsResponseCache;
    protected $fillable = ['full_name', 'no_lab', 'fungsi_lab', 'kondisi_akhir', 'catatan'];
}
