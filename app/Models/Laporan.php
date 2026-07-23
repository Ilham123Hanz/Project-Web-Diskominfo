<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Carbon\Carbon;

class Laporan extends Model
{
    use HasFactory;

    /**
     * Menentukan nama tabel database secara eksplisit.
     */
    protected $table = 'laporan';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     * Terintegrasi penuh dengan skema migration laporan & presensi Bahasa Indonesia.
     */
    protected $fillable = [
        'user_id',
        'presensi_id',        // Relasi ke tabel presensi (tanpa huruf 's')
        'log_code',           // Kode unik log kasus (Contoh: LOG-20260722-001)
        'rumpun_kategori',
        'main_menu',
        'kategori_insiden',
        'opd_sasaran',
        'target_url',
        'threat_level',
        'description',
        'coordination_note',
        'file_evidence', 
        'status',             // Approved, Pending, Revision
        'admin_correction',   // Catatan umpan balik/revisi dari admin
        'verified_by',        // ID Admin Peninjau
        'verified_at'         // Waktu Eksekusi Verifikasi
    ];

    /**
     * Casting tipe data kolom database menjadi objek Carbon/DateTime.
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Virtual Attributes yang otomatis disertakan saat model diubah ke Array/JSON.
     */
    protected $appends = [
        'date_log',
        'threat_badge_color',
        'status_badge_color',
    ];

    // =========================================================================
    // BOOTING MODEL & LOGIC AUTOMATION (Model Events - Fail-Safe & Robust)
    // =========================================================================

    /**
     * Mengatur logika otomatisasi saat model disimpan ke database.
     */
    protected static function booted(): void
    {
        /**
         * Otomatisasi generator 'log_code' unik berbasis serial tanggal dan urutan harian
         * serta fallback value untuk kriteria default sebelum di-insert ke Database.
         */
        static::creating(function (Laporan $laporan) {
            // 1. GENERATOR SERIAL LOG CODE OTOMATIS
            if (empty($laporan->log_code)) {
                $todayCount = static::whereDate('created_at', Carbon::today())->count() + 1;
                $laporan->log_code = 'LOG-' . date('Ymd') . '-' . sprintf('%03d', $todayCount);
            }

            // 2. LOGIKA SINKRONISASI VALUE DEFAULT (Mencegah SQL Integrity Violation)
            if (empty($laporan->rumpun_kategori)) {
                $laporan->rumpun_kategori = 'Patroli Harian';
            }
            if (empty($laporan->main_menu)) {
                $laporan->main_menu = 'Patroli Siber';
            }
            if (empty($laporan->threat_level)) {
                $laporan->threat_level = 'Medium';
            }
            if (empty($laporan->status)) {
                $laporan->status = 'Pending';
            }
        });
    }

    // =========================================================================
    // VIRTUAL ACCESSORS & COMPATIBILITY LAYER (Mencegah Crash UI & Multi-Format)
    // =========================================================================

    /**
     * Virtual Accessor 'date_log' agar sinkron dengan pemanggilan tanggal laporan di Blade View.
     */
    protected function dateLog(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        );
    }

    /**
     * Accessor 'threat_badge_color': Menerjemahkan level ancaman menjadi warna badge Tailwind/Bootstrap.
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
     * Accessor 'status_badge_color': Menerjemahkan status pemeriksaan ke skema warna UI secara fleksibel.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Approved', 'Verified', 'Disetujui Admin' => 'bg-green-100 text-green-800 border-green-200',
            'Revision', 'Perlu Perbaikan', 'Rejection' => 'bg-red-100 text-red-800 border-red-200',
            'Pending', 'Menunggu Validasi'             => 'bg-amber-100 text-amber-800 border-amber-200',
            default                                   => 'bg-slate-100 text-slate-600 border-slate-200',
        };
    }

    /**
     * Helper Method: Mengecek apakah laporan saat ini membutuhkan perbaikan/revisi dari petugas.
     */
    public function needsRevision(): bool
    {
        return in_array($this->status, ['Revision', 'Perlu Perbaikan', 'Rejection']);
    }

    /**
     * Helper Method: Mengecek apakah laporan sudah terverifikasi secara sah oleh Admin.
     */
    public function isVerified(): bool
    {
        return in_array($this->status, ['Approved', 'Verified', 'Disetujui Admin']);
    }

    // =========================================================================
    // ADVANCED QUERY SCOPES (Mendukung Fitur Pencarian & Filter di Controller)
    // =========================================================================

    /**
     * Scope: Filter laporan berdasarkan status peninjauan (Toleran terhadap variasi enum/label).
     */
    public function scopeByStatus(Builder $query, string $status): Builder
    {
        if (in_array($status, ['Approved', 'Verified', 'Disetujui Admin'])) {
            return $query->whereIn('status', ['Approved', 'Verified', 'Disetujui Admin']);
        }
        if (in_array($status, ['Revision', 'Perlu Perbaikan', 'Rejection'])) {
            return $query->whereIn('status', ['Revision', 'Perlu Perbaikan', 'Rejection']);
        }
        return $query->where('status', $status);
    }

    /**
     * Scope: Pencarian global kata kunci OPD, Kategori, atau Kode Log.
     */
    public function scopeSearchKeyword(Builder $query, ?string $keyword): Builder
    {
        if (empty($keyword)) {
            return $query;
        }

        return $query->where(function ($q) use ($keyword) {
            $q->where('opd_sasaran', 'like', "%{$keyword}%")
              ->orWhere('kategori_insiden', 'like', "%{$keyword}%")
              ->orWhere('log_code', 'like', "%{$keyword}%");
        });
    }

    // =========================================================================
    // RELASI DATABASE (ELOQUENT RELATIONSHIPS)
    // =========================================================================

    /**
     * Relasi BelongsTo ke model User (Petugas Pelapor / Pemilik Log).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi BelongsTo ke model Presensi (Sesi Jam Kerja / Presensi Harian).
     */
    public function presensi(): BelongsTo
    {
        return $this->belongsTo(Presensi::class, 'presensi_id');
    }

    /**
     * Relasi BelongsTo ke model User (Admin Penilai / Verifikator).
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by');
    }
}