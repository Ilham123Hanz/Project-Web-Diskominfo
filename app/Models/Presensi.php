<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Presensi extends Model
{
    use HasFactory, SoftDeletes;

    /**
     * Nama tabel database.
     *
     * @var string
     */
    protected $table = 'presensi';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     */
    protected $fillable = [
        'user_id',
        'tanggal_presensi',
        'jam_masuk',
        'jam_pulang',
        'durasi_kerja_menit',
        'status_masuk',
        'status_pulang',
        'status_kehadiran',
        'verifikasi_admin',
        'metode_masuk',
        'metode_pulang',
        'catatan_masuk',
        'catatan_pulang',
        'catatan_admin',
        'ip_address_masuk',
        'ip_address_pulang',
        'user_agent_masuk',
        'user_agent_pulang',
    ];

    /**
     * Casting tipe data kolom database ke tipe data native PHP / Carbon.
     */
    protected $casts = [
        'tanggal_presensi'   => 'date:Y-m-d',
        'durasi_kerja_menit' => 'integer',
        'created_at'         => 'datetime',
        'updated_at'         => 'datetime',
        'deleted_at'         => 'datetime',
    ];

    /**
     * Virtual attributes yang otomatis ikut ter-render jika data dikonversi ke Array / JSON.
     */
    protected $appends = [
        'waktu_masuk_format',
        'waktu_pulang_format',
        'durasi_kerja_formatted',
        'is_terlambat',
    ];

    // Jam Kerja Standar Instansi (Format 24 Jam)
    public const JAM_MASUK_STANDAR  = '08:00:00';
    public const JAM_PULANG_STANDAR = '17:00:00';

    /**
     * Booted function untuk menangani Model Events secara otomatis (Data Protection Layer).
     */
    protected static function booted(): void
    {
        // 1. Event Pembuatan Data Baru (Clock In)
        static::creating(function (Presensi $presensi) {
            // Default tanggal presensi jika tidak dikirim
            if (empty($presensi->tanggal_presensi)) {
                $presensi->tanggal_presensi = Carbon::today('Asia/Jakarta')->toDateString();
            }

            // Sanitasi dan Evaluasi Clock In
            if ($presensi->jam_masuk) {
                $cleanTime = self::sanitizeTimeString($presensi->jam_masuk);
                $presensi->jam_masuk = $cleanTime;

                if (empty($presensi->status_masuk)) {
                    $presensi->status_masuk = self::checkIsLate($cleanTime);
                }
            }

            // Default Attributes
            $presensi->status_kehadiran = $presensi->status_kehadiran ?? 'Hadir';
            $presensi->verifikasi_admin = $presensi->verifikasi_admin ?? 'Approved';
            $presensi->metode_masuk     = $presensi->metode_masuk ?? 'Web Portal';
            $presensi->durasi_kerja_menit = 0;
            $presensi->status_pulang    = null;
        });

        // 2. Event Pembaruan Data (Clock Out / Edit Presensi)
        static::updating(function (Presensi $presensi) {
            if ($presensi->isDirty('jam_pulang') && $presensi->jam_pulang) {
                $cleanTimeOut = self::sanitizeTimeString($presensi->jam_pulang);
                $presensi->jam_pulang = $cleanTimeOut;

                if (empty($presensi->status_pulang)) {
                    $presensi->status_pulang = self::checkIsEarlyLeave($cleanTimeOut);
                }

                // Kalkulasi Durasi Kerja
                if ($presensi->jam_masuk) {
                    $cleanTimeIn = self::sanitizeTimeString($presensi->jam_masuk);
                    $presensi->durasi_kerja_menit = self::calculateDurationMinutes($cleanTimeIn, $cleanTimeOut);
                }
            }
        });
    }

    // =========================================================================
    // SANITIZER & HELPER INTERNAL PENCEGAH BUG CARBON
    // =========================================================================

    /**
     * Sanitasi String Jam agar tidak mengandung tanggal ganda atau teks tambahan.
     */
    public static function sanitizeTimeString(?string $timeStr): string
    {
        if (empty($timeStr)) {
            return Carbon::now('Asia/Jakarta')->format('H:i:s');
        }

        // Jika string membawa format datetime (contoh: '2026-07-23 09:06:00')
        if (strlen(trim($timeStr)) > 8) {
            try {
                return Carbon::parse($timeStr)->format('H:i:s');
            } catch (\Exception $e) {
                // Ekstrak bagian jam secara manual jika parsing gagal
                preg_match('/(\d{2}:\d{2}:\d{2})|(\d{2}:\d{2})/', $timeStr, $matches);
                if (!empty($matches[0])) {
                    return strlen($matches[0]) === 5 ? $matches[0] . ':00' : $matches[0];
                }
            }
        }

        return $timeStr;
    }

    // =========================================================================
    // ACCESSORS & MUTATORS (MODERN LARAVEL ATTRIBUTE CASTING)
    // =========================================================================

    /**
     * Accessor 'waktu_masuk_format': Format jam masuk (misal: 08:00 WIB)
     */
    protected function waktuMasukFormat(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->jam_masuk ? Carbon::parse(self::sanitizeTimeString($this->jam_masuk))->format('H:i') . ' WIB' : '-'
        );
    }

    /**
     * Accessor 'waktu_pulang_format': Format jam pulang (misal: 17:00 WIB)
     */
    protected function waktuPulangFormat(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->jam_pulang ? Carbon::parse(self::sanitizeTimeString($this->jam_pulang))->format('H:i') . ' WIB' : '-'
        );
    }

    /**
     * Accessor 'durasi_kerja_formatted': Format teks durasi (Contoh: "8 Jam 30 Menit")
     */
    protected function durasiKerjaFormatted(): Attribute
    {
        return Attribute::make(
            get: function () {
                if (!$this->jam_masuk) {
                    return 'Belum Absen';
                }
                if (!$this->jam_pulang) {
                    return 'Sedang Bertugas';
                }

                $menit = $this->durasi_kerja_menit ?? self::calculateDurationMinutes($this->jam_masuk, $this->jam_pulang);
                $jam = floor($menit / 60);
                $sisaMenit = $menit % 60;

                return "{$jam} Jam {$sisaMenit} Menit";
            }
        );
    }

    /**
     * Accessor 'is_terlambat': Indikator boolean status keterlambatan
     */
    protected function isTerlambat(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->status_masuk === 'Terlambat'
        );
    }

    // =========================================================================
    // LOGIKA BISNIS & HELPER STATIK
    // =========================================================================

    /**
     * Helper: Evaluasi Status Keterlambatan
     */
    public static function checkIsLate(string $jamMasuk): string
    {
        $cleanJam = self::sanitizeTimeString($jamMasuk);
        $limit = Carbon::createFromTimeString(self::JAM_MASUK_STANDAR);
        $time  = Carbon::createFromTimeString($cleanJam);

        return $time->gt($limit) ? 'Terlambat' : 'Tepat Waktu';
    }

    /**
     * Helper: Evaluasi Status Kepulangan
     */
    public static function checkIsEarlyLeave(string $jamPulang): string
    {
        $cleanJam = self::sanitizeTimeString($jamPulang);
        $limit = Carbon::createFromTimeString(self::JAM_PULANG_STANDAR);
        $time  = Carbon::createFromTimeString($cleanJam);

        return $time->lt($limit) ? 'Pulang Cepat' : 'Selesai';
    }

    /**
     * Helper: Mengkalkulasi durasi menit antara dua Waktu
     */
    public static function calculateDurationMinutes(string $jamMasuk, string $jamPulang): int
    {
        $cleanIn  = self::sanitizeTimeString($jamMasuk);
        $cleanOut = self::sanitizeTimeString($jamPulang);

        $timeIn  = Carbon::createFromTimeString($cleanIn);
        $timeOut = Carbon::createFromTimeString($cleanOut);

        // Penanganan jika shift melintasi tengah malam (night shift)
        if ($timeOut->lessThan($timeIn)) {
            $timeOut->addDay();
        }

        return (int) $timeIn->diffInMinutes($timeOut);
    }

    // =========================================================================
    // ADVANCED SCOPE QUERIES
    // =========================================================================

    /**
     * Scope: Filter berdasarkan rentang tanggal
     */
    public function scopeRentangTanggal(Builder $query, string $startDate, string $endDate): Builder
    {
        return $query->whereBetween('tanggal_presensi', [$startDate, $endDate]);
    }

    /**
     * Scope: Filter data presensi hari ini
     */
    public function scopeHariIni(Builder $query): Builder
    {
        return $query->whereDate('tanggal_presensi', Carbon::today('Asia/Jakarta'));
    }

    /**
     * Scope: Filter berdasarkan status keterlambatan
     */
    public function scopeFilterStatusMasuk(Builder $query, string $status): Builder
    {
        return $query->where('status_masuk', $status);
    }

    /**
     * Scope: Filter berdasarkan status kehadiran (Hadir, Izin, Sakit)
     */
    public function scopeStatusKehadiran(Builder $query, string $status): Builder
    {
        return $query->where('status_kehadiran', $status);
    }

    /**
     * Scope: Filter presensi lengkap (Clock-In & Clock-Out selesai)
     */
    public function scopeSelesai(Builder $query): Builder
    {
        return $query->whereNotNull('jam_masuk')->whereNotNull('jam_pulang');
    }

    /**
     * Scope: Filter presensi yang sedang bertugas (Belum Clock-Out)
     */
    public function scopeBelumPulang(Builder $query): Builder
    {
        return $query->whereNotNull('jam_masuk')->whereNull('jam_pulang');
    }

    // =========================================================================
    // ELOQUENT RELATIONSHIPS
    // =========================================================================

    /**
     * Relasi ke User
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi ke Patrol
     */
    public function patrols(): HasMany
    {
        return $this->hasMany(Patrol::class, 'presensi_id');
    }
}