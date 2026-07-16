<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes; 
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Carbon\Carbon;
use Illuminate\Database\Eloquent\Casts\Attribute;

class User extends Authenticatable 
{
    use HasFactory, Notifiable, SoftDeletes;

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     */
    protected $fillable = [
        'name',
        'username',      
        'email',         
        'password',
        'role',          
        
        // --- IDENTITAS KEPEGAWAIAN (Diskominfo/Pemerintahan) ---
        'nip',
        'phone_number',
        'jabatan',
        'bio',            

        // --- MANAJEMEN STATUS & INTEGRITAS AKUN ---
        'status',          // Active, Inactive, Suspended
        'login_attempts',  // Menghitung percobaan gagal login
        'locked_until',    // Waktu pembekuan akun akibat salah password
        'password_changed_at',

        // --- AUDIT LOG / JEJAK DIGITAL SIBER ---
        'last_login_at',
        'last_login_ip',
        
        // --- INTEGRASI GOOGLE OAUTH EXPANDED ---
        'google_id',
        'google_token',
        'google_refresh_token',
        'avatar_url',
    ];

    /**
     * Atribut yang harus disembunyikan dalam representasi Serialisasi (JSON/Array).
     */
    protected $hidden = [
        'password',
        'remember_token',
        'google_token',  
        'google_refresh_token',
    ];

    /**
     * Mengubah tipe data kolom database ke tipe objek/karakter khusus di PHP (Casting).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed', 
        'locked_until' => 'datetime',
        'password_changed_at' => 'datetime',
        'last_login_at' => 'datetime',
        'login_attempts' => 'integer',
    ];

    /**
     * Booted function untuk menangani Model Events secara otomatis (Cyber Integrity).
     */
    protected static function booted(): void
    {
        // Otomatis mencatat riwayat perubahan password jika terdeteksi ada pembaruan password
        static::updating(function (User $user) {
            if ($user->isDirty('password')) {
                $user->password_changed_at = Carbon::now();
            }
        });
    }

    // =========================================================================
    // ACCESSORS & MUTATORS (FORMATTING OTOMATIS)
    // =========================================================================

    /**
     * Memastikan username selalu disimpan dalam format huruf kecil tanpa spasi.
     */
    protected function username(): Attribute
    {
        return Attribute::make(
            set: fn (string $value) => strtolower(str_replace(' ', '', $value)),
        );
    }

    /**
     * Menghasilkan Fallback Avatar berdasarkan nama jika user tidak memiliki avatar_url.
     */
    public function getProfileAvatarAttribute(): string
    {
        if ($this->avatar_url) {
            return $this->avatar_url;
        }
        // Menggunakan API pihak ketiga yang aman untuk generate avatar inisial nama
        return 'https://ui-avatars.com/api/?name=' . urlencode($this->name) . '&background=0f3057&color=fff&bold=true';
    }

    // =========================================================================
    // UTILITIES / SECURITY & BUSINESS LOGIC FUNCTIONS
    // =========================================================================

    /**
     * Mengecek apakah akun sedang dikunci/ditangguhkan akibat Brute Force.
     */
    public function isLockedOut(): bool
    {
        return $this->locked_until && $this->locked_until->isFuture();
    }

    /**
     * Memeriksa apakah status akun aktif sepenuhnya.
     */
    public function isActive(): bool
    {
        return $this->status === 'Active' && !$this->isLockedOut();
    }

    /**
     * Pengecekan Hak Akses Tingkat Tinggi (Role Helper)
     */
    public function isSuperAdmin(): bool
    {
        return $this->role === 'Super Admin';
    }

    public function isAdmin(): bool
    {
        return $this->role === 'Admin';
    }

    public function isPetugas(): bool
    {
        return $this->role === 'Petugas';
    }

    /**
     * Mengecek apakah petugas sudah melakukan absensi masuk (clock in) hari ini.
     * Disesuaikan dengan struktur tabel absensi Anda yang menggunakan kolom 'date'.
     */
    public function hasCheckedInToday(): bool
    {
        return $this->attendances()
            ->whereDate('date', Carbon::today())
            ->exists();
    }

    /**
     * Mengambil data absensi hari ini milik user.
     */
    public function currentAttendanceToday()
    {
        return $this->attendances()
            ->whereDate('date', Carbon::today())
            ->first();
    }

    // =========================================================================
    // SCOPES (QUERIES HELPER)
    // =========================================================================

    public function scopeActive($query)
    {
        return $query->where('status', 'Active');
    }

    public function scopeFilterRole($query, string $role)
    {
        return $query->where('role', $role);
    }

    // =========================================================================
    // RELASI DATABASE (ONE-TO-MANY)
    // =========================================================================

    /**
     * Relasi One-to-Many ke model Patrol (Laporan Transmisi Berkas Siber).
     */
    public function patrols(): HasMany
    {
        return $this->hasMany(Patrol::class);
    }

    /**
     * Relasi One-to-Many ke model Attendance (Log Presensi Harian).
     */
    public function attendances(): HasMany
    {
        return $this->hasMany(Attendance::class);
    }
}