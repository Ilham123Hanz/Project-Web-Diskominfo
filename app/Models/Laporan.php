<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class Laporan extends Model
{
    use HasFactory;

    /**
     * Nama tabel database yang terhubung.
     *
     * @var string
     */
    protected $table = 'laporan';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'user_id',
        'presensi_id',
        'log_code',
        'rumpun_kategori',
        'main_menu',
        'kategori_insiden',
        'opd_sasaran',
        'target_url',
        'threat_level',
        'description',
        'coordination_note',
        'file_evidence',
        'status',
        'admin_correction',
        'verified_by',
        'verified_at',
    ];

    /**
     * Casting tipe data kolom database ke format native/Carbon.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'verified_at' => 'datetime',
        'created_at'  => 'datetime',
        'updated_at'  => 'datetime',
    ];

    /**
     * Accessor virtual yang otomatis disertakan saat Model dikonversi ke Array/JSON.
     *
     * @var array<int, string>
     */
    protected $appends = [
        'date_log',
        'threat_badge_color',
        'status_badge_color',
        'file_evidence_url',
        'is_revisable',
    ];

    // =========================================================================
    // BOOTING MODEL & LOGIC AUTOMATION (Model Events - Fail-Safe & Robust)
    // =========================================================================

    /**
     * Mengatur logika otomatisasi sebelum data disimpan atau diperbarui ke Database.
     */
    protected static function booted(): void
    {
        static::creating(function (Laporan $laporan) {
            // 1. GENERATOR SERIAL LOG CODE OTOMATIS & UNIK
            if (empty($laporan->log_code)) {
                $laporan->log_code = static::generateUniqueLogCode();
            }

            // 2. SANITASI DAN PENANGANAN NORMALIASI STRING
            if ($laporan->target_url) {
                $laporan->target_url = trim($laporan->target_url);
            }

            // 3. LOGIKA SINKRONISASI VALUE DEFAULT (Mencegah SQL Integrity Violation)
            $laporan->rumpun_kategori = $laporan->rumpun_kategori ?: 'Patroli Harian';
            $laporan->main_menu       = $laporan->main_menu ?: 'Patroli Siber';
            $laporan->threat_level    = $laporan->threat_level ?: 'Medium';
            $laporan->status          = $laporan->status ?: 'Pending';
        });

        static::updating(function (Laporan $laporan) {
            // SINKRONISASI OTOMATIS VERIFIKASI SAAT STATUS BERUBAH KE APPROVED/VERIFIED
            if ($laporan->isDirty('status')) {
                if (in_array($laporan->status, ['Approved', 'Verified', 'Disetujui Admin'])) {
                    if (empty($laporan->verified_at)) {
                        $laporan->verified_at = now();
                    }
                    if (empty($laporan->verified_by) && auth()->check()) {
                        $laporan->verified_by = auth()->id();
                    }
                }
            }

            // SANITASI INPUT PADA UPDATE
            if ($laporan->isDirty('target_url') && $laporan->target_url) {
                $laporan->target_url = trim($laporan->target_url);
            }
        });
    }

    /**
     * Helper privat untuk membuat Log Code unik berbasis tanggal & acak aman dari bentrokan (Race Condition).
     */
    private static function generateUniqueLogCode(): string
    {
        $datePrefix = date('Ymd');
        $todayCount = static::whereDate('created_at', Carbon::today())->count() + 1;

        // Kombinasi Urutan Harian + Random String 3 Karakter Unik
        $logCode = 'LOG-' . $datePrefix . '-' . sprintf('%03d', $todayCount) . '-' . strtoupper(Str::random(3));

        // Memastikan tidak terjadi duplikasi jika ada entri bersamaan
        while (static::where('log_code', $logCode)->exists()) {
            $logCode = 'LOG-' . $datePrefix . '-' . sprintf('%03d', rand(100, 999)) . '-' . strtoupper(Str::random(3));
        }

        return $logCode;
    }

    // =========================================================================
    // VIRTUAL ACCESSORS & COMPATIBILITY LAYER
    // =========================================================================

    /**
     * Accessor 'date_log': Memudahkan format tanggal di Blade View.
     */
    protected function dateLog(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->created_at ? $this->created_at->format('Y-m-d') : null,
        );
    }

    /**
     * Accessor 'file_evidence_url': Mendapatkan URL publik bukti dukung secara instan.
     */
    protected function fileEvidenceUrl(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->file_evidence) {
                    return null;
                }
                if (Str::startsWith($this->file_evidence, ['http://', 'https://'])) {
                    return $this->file_evidence;
                }
                return asset('storage/' . $this->file_evidence);
            }
        );
    }

    /**
     * Accessor 'is_revisable': Indikator instan apakah laporan dalam status bisa diedit kembali.
     */
    protected function isRevisable(): Attribute
    {
        return Attribute::make(
            get: fn () => in_array($this->status, ['Revision', 'Perlu Perbaikan', 'Rejection', 'Ditolak']),
        );
    }

    /**
     * Accessor 'threat_badge_color': Warna indikator Badge Threat Level (Tailwind CSS / Bootstrap Ready).
     */
    public function getThreatBadgeColorAttribute(): string
    {
        return match (ucfirst(strtolower($this->threat_level))) {
            'Critical' => 'bg-rose-600 text-white font-semibold',
            'High'     => 'bg-orange-500 text-white font-semibold',
            'Medium'   => 'bg-sky-500 text-white font-semibold',
            'Low'      => 'bg-slate-500 text-white font-semibold',
            default    => 'bg-slate-200 text-slate-700',
        };
    }

    /**
     * Accessor 'status_badge_color': Skema warna status pemeriksaan fleksibel & multi-label.
     */
    public function getStatusBadgeColorAttribute(): string
    {
        return match ($this->status) {
            'Approved', 'Verified', 'Disetujui Admin' 
                => 'bg-emerald-100 text-emerald-800 border border-emerald-300',
            
            'Revision', 'Perlu Perbaikan' 
                => 'bg-amber-100 text-amber-800 border border-amber-300',

            'Rejection', 'Ditolak' 
                => 'bg-rose-100 text-rose-800 border border-rose-300',
            
            'Pending', 'Menunggu Validasi' 
                => 'bg-sky-100 text-sky-800 border border-sky-300',
            
            default 
                => 'bg-slate-100 text-slate-700 border border-slate-300',
        };
    }

    // =========================================================================
    // HELPER METHODS (KONDISIONAL WORKFLOW & STATE CHECKS)
    // =========================================================================

    /**
     * Cek apakah laporan perlu direvisi/diperbaiki oleh petugas.
     */
    public function needsRevision(): bool
    {
        return in_array($this->status, ['Revision', 'Perlu Perbaikan']);
    }

    /**
     * Cek apakah laporan ditolak oleh verifikator.
     */
    public function isRejected(): bool
    {
        return in_array($this->status, ['Rejection', 'Ditolak']);
    }

    /**
     * Cek apakah laporan telah berhasil disetujui / diverifikasi.
     */
    public function isVerified(): bool
    {
        return in_array($this->status, ['Approved', 'Verified', 'Disetujui Admin']);
    }

    /**
     * Cek apakah status laporan masih dalam antrean peninjauan admin.
     */
    public function isPending(): bool
    {
        return in_array($this->status, ['Pending', 'Menunggu Validasi']);
    }

    // =========================================================================
    // ADVANCED QUERY SCOPES
    // =========================================================================

    /**
     * Scope: Filter berdasarkan kelompok grup status (Mendukung Multi-Enum & String Normalization).
     */
    public function scopeByStatus(Builder $query, ?string $status): Builder
    {
        if (empty($status) || strtoupper($status) === 'ALL') {
            return $query;
        }

        if (in_array($status, ['Approved', 'Verified', 'Disetujui Admin'])) {
            return $query->whereIn('status', ['Approved', 'Verified', 'Disetujui Admin']);
        }

        if (in_array($status, ['Revision', 'Perlu Perbaikan'])) {
            return $query->whereIn('status', ['Revision', 'Perlu Perbaikan']);
        }

        if (in_array($status, ['Rejection', 'Ditolak'])) {
            return $query->whereIn('status', ['Rejection', 'Ditolak']);
        }

        if (in_array($status, ['Pending', 'Menunggu Validasi'])) {
            return $query->whereIn('status', ['Pending', 'Menunggu Validasi']);
        }

        return $query->where('status', $status);
    }

    /**
     * Scope: Pencarian fleksibel kata kunci OPD, Kategori, Kode Log, Deskripsi, atau Main Menu.
     */
    public function scopeSearchKeyword(Builder $query, ?string $keyword): Builder
    {
        $trimmed = trim((string) $keyword);

        if (blank($trimmed)) {
            return $query;
        }

        return $query->where(function (Builder $q) use ($trimmed) {
            $q->where('opd_sasaran', 'like', "%{$trimmed}%")
              ->orWhere('kategori_insiden', 'like', "%{$trimmed}%")
              ->orWhere('log_code', 'like', "%{$trimmed}%")
              ->orWhere('target_url', 'like', "%{$trimmed}%")
              ->orWhere('main_menu', 'like', "%{$trimmed}%")
              ->orWhere('rumpun_kategori', 'like', "%{$trimmed}%");
        });
    }

    /**
     * Scope: Filter laporan berdasar tingkat ancaman.
     */
    public function scopeByThreatLevel(Builder $query, ?string $level): Builder
    {
        if (empty($level) || strtoupper($level) === 'ALL') {
            return $query;
        }

        return $query->where('threat_level', $level);
    }

    /**
     * Scope: Filter berdasarkan rentang tanggal pembuatan laporan.
     */
    public function scopeDateBetween(Builder $query, ?string $startDate, ?string $endDate): Builder
    {
        if ($startDate && $endDate) {
            return $query->whereBetween('created_at', [
                Carbon::parse($startDate)->startOfDay(),
                Carbon::parse($endDate)->endOfDay()
            ]);
        }

        if ($startDate) {
            return $query->where('created_at', '>=', Carbon::parse($startDate)->startOfDay());
        }

        if ($endDate) {
            return $query->where('created_at', '<=', Carbon::parse($endDate)->endOfDay());
        }

        return $query;
    }

    /**
     * Scope: Pengurutan data terbaru.
     */
    public function scopeLatestFirst(Builder $query): Builder
    {
        return $query->orderBy('created_at', 'desc');
    }

    // =========================================================================
    // RELASI ELOQUENT
    // =========================================================================

    /**
     * Relasi ke User Pelapor (Petugas Patroli).
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id')->withDefault([
            'name' => 'Petugas Tidak Terdefinisi',
        ]);
    }

    /**
     * Relasi ke Presensi Harian Petugas (Sesi Tugas).
     */
    public function presensi(): BelongsTo
    {
        return $this->belongsTo(Presensi::class, 'presensi_id');
    }

    /**
     * Relasi ke User Admin (Penilai / Verifikator Laporan).
     */
    public function verifier(): BelongsTo
    {
        return $this->belongsTo(User::class, 'verified_by')->withDefault([
            'name' => 'Belum Diverifikasi',
        ]);
    }

    /**
     * Relasi ke VirtualFile (untuk arsip folder).
     */
    public function virtualFiles(): HasMany
    {
        return $this->hasMany(VirtualFile::class, 'laporan_id');
    }
}