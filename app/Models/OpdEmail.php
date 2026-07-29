<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class OpdEmail extends Model
{
    use HasFactory;

    protected $table = 'opd_emails';

    protected $fillable = [
        'opd_id',
        'alamat_email',
        'keterangan',
        'keterangan_pic',
    ];

    public function masterOpd()
    {
        return $this->belongsTo(MasterOpd::class, 'opd_id');
    }
}