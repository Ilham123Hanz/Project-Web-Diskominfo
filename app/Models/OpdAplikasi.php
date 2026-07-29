<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpdAplikasi extends Model
{
    use HasFactory;

    protected $table = 'opd_aplikasis';

    protected $fillable = [
        'opd_id',
        'nama_sistem',
        'kode_aset',
        'domain_url',
        'status_operasional',
    ];

    public function masterOpd()
    {
        return $this->belongsTo(MasterOpd::class, 'opd_id');
    }
}