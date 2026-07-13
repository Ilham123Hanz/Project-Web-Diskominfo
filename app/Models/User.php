<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;

class User extends Authenticatable 
{
    use Notifiable;

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'name',
        'username',
        'password',
        'role', // Nilai: 'Admin' atau 'Petugas'
    ];

    /**
     * Atribut yang harus disembunyikan dalam representasi Serialisasi (JSON/Array).
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Otomatis melakukan hashing password ketika data disimpan.
     * (Mutator bawaan Laravel 10/11)
     */
    protected function setPasswordAttribute($value)
    {
        if (!empty($value)) {
            $this->attributes['password'] = Hash::needsRehash($value) ? Hash::make($value) : $value;
        }
    }

    /**
     * Relasi One-to-Many ke model Patrol (Laporan Transmisi Berkas Siber).
     * Seorang user dapat memiliki banyak rekaman laporan patroli.
     * * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function patrols(): HasMany
    {
        return $this->hasMany(Patrol::class);
    }

    /**
     * Relasi One-to-Many ke model Attendance (Log Presensi Harian).
     * Seorang user dapat memiliki banyak riwayat absensi harian.
     * * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}