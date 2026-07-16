<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Builder;
use Carbon\Carbon;

class Attendance extends Model
{
    use HasFactory;

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignable).
     * Terintegrasi penuh dengan skema absensi reguler non-GPS & non-shift.
     */
    protected $fillable = [
        'user_id',
        'attendance_date',
        'clock_in',
        'clock_out',
        'status_in',
        'status_out',
        'notes_in',
        'notes_out',
        'ip_address_in',
        'ip_address_out',
        'device_agent'
    ];

    /**
     * Casting tipe data kolom database ke objek Carbon/Date.
     */
    protected $casts = [
        'attendance_date' => 'date',
    ];

    /**
     * Menambahkan append virtual attributes agar otomatis ikut ter-render 
     * jika data model diubah menjadi array atau JSON (API Ready).
     */
    protected $appends = [
        'date',
        'time_in',
        'time_out',
        'duration'
    ];

    // Konstanta Jam Kerja Standar Instansi (Format 24 Jam)
    const JAM_MASUK_STANDAR  = '07:30:00';
    const JAM_PULANG_STANDAR = '16:00:00';

    /**
     * Booted function untuk menangani Model Events secara otomatis.
     * Mengamankan integritas data langsung sebelum masuk ke database (Data Guard).
     */
    protected static function booted(): void
    {
        // 1. Intersepsi Aksi Pembuatan Data Baru (Clock In)
        static::creating(function (Attendance $attendance) {
            // Normalisasi format waktu input agar seragam (H:i:s)
            if ($attendance->clock_in) {
                $attendance->clock_in = Carbon::parse($attendance->clock_in)->toTimeString();
                // Otomatisasi kalkulasi status masuk jika kosong
                if (empty($attendance->status_in)) {
                    $attendance->status_in = self::checkIsLate($attendance->clock_in);
                }
            }
            
            // Default status_out dipastikan null di awal sesi
            $attendance->status_out = null;
        });

        // 2. Intersepsi Aksi Pembaruan Data (Clock Out)
        static::updating(function (Attendance $attendance) {
            // Jika ada perubahan atau pengisian kolom clock_out, hitung status kepulangannya
            if ($attendance->isDirty('clock_out') && $attendance->clock_out) {
                $attendance->clock_out = Carbon::parse($attendance->clock_out)->toTimeString();
                if (empty($attendance->status_out)) {
                    $attendance->status_out = self::checkIsEarlyLeave($attendance->clock_out);
                }
            }
        });
    }

    // =========================================================================
    // VIRTUAL ACCESSORS & MUTATORS (MODERN LARAVEL STYLE)
    // =========================================================================

    /**
     * Virtual Accessor 'date' untuk kompatibilitas template lama ($attendance->date)
     */
    protected function date(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->attendance_date?->format('Y-m-d'),
        );
    }

    /**
     * Accessor & Mutator 'time_in': Mengembalikan format bersih H:i di view
     */
    protected function timeIn(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->clock_in ? Carbon::parse($this->clock_in)->format('H:i') : null,
        );
    }

    /**
     * Accessor & Mutator 'time_out': Mengembalikan format bersih H:i di view
     */
    protected function timeOut(): Attribute
    {
        return Attribute::make(
            get: fn () => $this->clock_out ? Carbon::parse($this->clock_out)->format('H:i') : null,
        );
    }

    /**
     * Accessor Kustom: Menghitung durasi kerja bersih secara dinamis.
     */
    public function getDurationAttribute(): string
    {
        if (!$this->clock_in) {
            return 'Data Tidak Valid';
        }
        if (!$this->clock_out) {
            return 'Sedang Bertugas';
        }

        $timeIn  = Carbon::createFromTimeString($this->clock_in);
        $timeOut = Carbon::createFromTimeString($this->clock_out);

        // Fallback jika melewati tengah malam (Cross-day shift safety)
        if ($timeOut->lessThan($timeIn)) {
            $timeOut->addDay();
        }

        $durasiMenit = $timeIn->diffInMinutes($timeOut);
        $jam   = floor($durasiMenit / 60);
        $menit = $durasiMenit % 60;

        return "{$jam} Jam {$menit} Menit";
    }

    // =========================================================================
    // LOGIKA BISNIS UTAMA & VALIDATOR WAKTU NYATA
    // =========================================================================

    /**
     * Helper Static: Menentukan status keterlambatan absensi masuk (Clock In).
     */
    public static function checkIsLate($clockInTime): string
    {
        $limit = Carbon::createFromTimeString(self::JAM_MASUK_STANDAR);
        $time  = Carbon::createFromTimeString($clockInTime);
        
        return $time->gt($limit) ? 'Terlambat' : 'Tepat Waktu';
    }

    /**
     * Helper Static: Menentukan status ketepatan waktu pulang (Clock Out).
     */
    public static function checkIsEarlyLeave($clockOutTime): string
    {
        $limit = Carbon::createFromTimeString(self::JAM_PULANG_STANDAR);
        $time  = Carbon::createFromTimeString($clockOutTime);
        
        return $time->lt($limit) ? 'Pulang Awal' : 'Selesai';
    }

    // =========================================================================
    // SCOPE QUERIES (ADVANCED FILTERING)
    // =========================================================================

    /**
     * Scope: Memfilter rekap absensi dalam rentang tanggal tertentu.
     */
    public function scopeDateBetween(Builder $query, $startDate, $endDate): Builder
    {
        return $query->whereBetween('attendance_date', [$startDate, $endDate]);
    }

    /**
     * Scope: Memfilter data presensi berdasarkan status masuk tertentu.
     */
    public function scopeFilterStatusIn(Builder $query, string $status): Builder
    {
        return $query->where('status_in', $status);
    }

    /**
     * Scope: Memfilter data presensi yang sudah selesai penuh hari ini.
     */
    public function scopeCompleted(Builder $query): Builder
    {
        return $query->whereNotNull('clock_in')->whereNotNull('clock_out');
    }

    // =========================================================================
    // ELOQUENT RELATIONSHIPS
    // =========================================================================

    /**
     * Relasi Balik Many-to-One ke model User.
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'user_id');
    }

    /**
     * Relasi One-to-Many ke model Patrol.
     */
    public function patrols(): HasMany
    {
        return $this->hasMany(Patrol::class, 'attendance_id');
    }
}