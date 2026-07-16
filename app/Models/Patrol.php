<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Patrol extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     * Menjamin keamanan data dari Mass Assignment Vulnerability.
     */
    protected $fillable = [
        'id_log',            // Sinkronisasi dengan Controller & Database Serial
        'user_id',
        'attendance_id',
        'rumpun_kategori',
        'main_menu',
        'kategori_insiden',
        'opd_sasaran',
        'target_url',
        'threat_level',
        'description',
        'coordination_note',
        'file_evidence', 
        'status',            // Pending, Verified, Perlu Perbaikan
        'admin_correction',  // Catatan umpan balik/koreksi dari admin
        'verified_by',       // ID User Admin Peninjau
        'verified_at'        // Waktu Eksekusi Verifikasi
    ];

    /**
     * Mengubah tipe data kolom database menjadi objek waktu Carbon di PHP (Casting).
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    // =========================================================================
    // BOOTING MODEL & LOGIC AUTOMATION (Model Events - Fail-Safe & Robust)
    // =========================================================================

    /**
     * Mengatur logika otomatisasi saat model disimpan ke database.
     */
    protected static function boot()
    {
        parent::boot();

        /**
         * Otomatisasi pembuatan 'id_log' unik berbasis serial berurutan (e.g., LOG-001)
         * serta fallback value untuk parameter data klaster sebelum masuk ke engine DB.
         */
        static::creating(function ($patrol) {
            // 1. GENERATOR SERIAL ID LOG OTOMATIS (Aman & Tidak Bergantung pada Regex Tahun)
            if (empty($patrol->id_log)) {
                $nextId = (self::max('id') ?? 0) + 1;
                $patrol->id_log = 'LOG-' . str_pad($nextId, 3, '0', STR_PAD_LEFT);
            }

            // 2. LOGIKA SINKRONISASI VALUE DEFAULT (Mencegah SQL Integrity Violation)
            if (empty($patrol->rumpun_kategori)) {
                $patrol->rumpun_kategori = 'Patroli Harian';
            }
            if (empty($patrol->main_menu)) {
                $patrol->main_menu = 'Patroli Siber';
            }
            if (empty($patrol->threat_level)) {
                $patrol->threat_level = 'Medium';
            }
            if (empty($patrol->status)) {
                $patrol->status = 'Pending';
            }
        });
    }

    // =========================================================================
    // VIRTUAL ACCESSORS / COMPATIBILITY LAYER (Mencegah Crash UI & Multi-Format)
    // =========================================================================

    /**
     * Virtual Accessor 'date_log' agar sinkron dengan pemanggilan data tanggal di view.
     */
    protected function dateLog(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        );
    }

    /**
     * Menerjemahkan level ancaman (Threat Level) menjadi warna badge Tailwind/Bootstrap CSS.
     */
    public function getThreatBadgeColorAttribute(): string
    {
        return match ($this->threat_level) {
            'Critical' => 'bg-red-600 text-white',
            'High'     => 'bg-orange-500 text-white',
            'Medium'   => 'bg-blue-500 text-white',
            'Low'      => 'bg-slate-400 text-white',
            default    => 'bg-slate-100 text-slate-700',
        };
    }

    /**
     * Menerjemahkan status pemeriksaan ke skema warna komponen UI secara toleran.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Verified', 'Approved', 'Disetujui Admin' => 'bg-green-100 text-green-800 border-green-200',
            'Perlu Perbaikan', 'Rejection'            => 'bg-red-100 text-red-800 border-red-200',
            'Pending', 'Menunggu Validasi'            => 'bg-amber-100 text-amber-800 border-amber-200',
            default                                   => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }

    /**
     * Mengecek apakah status berkas laporan saat ini membutuhkan tindakan revisi dari petugas.
     */
    public function needsRevision(): bool
    {
        return in_array($this->status, ['Perlu Perbaikan', 'Rejection']);
    }

    /**
     * Mengecek apakah laporan sudah terverifikasi secara valid oleh Admin Pusat.
     */
    public function isVerified(): bool
    {
        return in_array($this->status, ['Verified', 'Approved', 'Disetujui Admin']);
    }

    // =========================================================================
    // ADVANCED QUERY SCOPES (Mendukung Fitur Pencarian & Filter di Controller)
    // =========================================================================

    /**
     * Scope untuk menyaring laporan berdasarkan status peninjauan.
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        if (in_array($status, ['Verified', 'Approved', 'Disetujui Admin'])) {
            return $query->whereIn('status', ['Verified', 'Approved', 'Disetujui Admin']);
        }
        if (in_array($status, ['Perlu Perbaikan', 'Rejection'])) {
            return $query->whereIn('status', ['Perlu Perbaikan', 'Rejection']);
        }
        return $query->where('status', $status);
    }

    /**
     * Scope untuk melakukan pencarian global kata kunci OPD, Kategori, atau Kode Log.
     */
    public function scopeSearchKeyword(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('opd_sasaran', 'like', "%{$keyword}%")
              ->orWhere('kategori_insiden', 'like', "%{$keyword}%")
              ->orWhere('id_log', 'like', "%{$keyword}%");
        });
    }

    // =========================================================================
    // RELASI DATABASE (ELOQUENT RELATIONSHIPS)
    // =========================================================================

    /**
     * Relasi Many-to-One balik ke model User (Identitas Petawai Lapangan / Pemilik Log).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi Many-to-One balik ke model Attendance (Sesi Jam Kerja Piket).
     */
    public function attendance(): BelongsTo
    {
        return $this->belongsTo(Attendance::class, 'attendance_id');
    }

    /**
     * Relasi Many-to-One balik ke model User (Identitas Admin Penilai Matriks).
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}