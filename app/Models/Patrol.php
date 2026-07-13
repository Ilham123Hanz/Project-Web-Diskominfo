<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class Patrol extends Model {
    protected $fillable = [
        'user_id', 'shift', 'main_menu', 'category', 'agency_name', 
        'target_url', 'threat_level', 'description', 'coordination_note', 
        'pdf_evidence', 'status', 'admin_correction'
    ];

    public function user() {
        return $this->belongsTo(User::class);
    }
}