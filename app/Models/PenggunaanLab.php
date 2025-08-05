<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class PenggunaanLab extends Model
{
    protected $fillable = ['full_name', 'no_lab', 'fungsi_lab', 'kondisi_akhir', 'catatan'];
}
