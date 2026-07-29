<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpdSosmed extends Model
{
    use HasFactory;

    protected $table = 'opd_sosmeds';

    protected $fillable = [
        'opd_id',
        'nama_akun_ig',
        'keterangan',
    ];

    public function masterOpd()
    {
        return $this->belongsTo(MasterOpd::class, 'opd_id');
    }
}