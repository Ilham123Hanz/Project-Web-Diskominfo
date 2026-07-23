<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Support\Str;

class MasterOpd extends Model
{
    use HasFactory;

    /**
     * Nama tabel database yang terikat dengan model ini.
     *
     * @var string
     */
    protected $table = 'master_opd';

    /**
     * Primary Key tabel (default: id).
     *
     * @var string
     */
    protected $primaryKey = 'id';

    /**
     * Atribut yang dapat diisi secara massal (Mass Assignment).
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'kode_opd',
        'nama_opd',
        'deskripsi',
    ];

    /**
     * Format tipe data atribut (Eloquent Casts).
     *
     * @var array<string, string>
     */
    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];

    /*
    |--------------------------------------------------------------------------
    | ACCESSORS & MUTATORS (SANITAISI & FORMAT OTOMATIS)
    |--------------------------------------------------------------------------
    */

    /**
     * Mutator & Accessor untuk Kode OPD:
     * - Otomatis diubah menjadi HURUF KAPITAL tanpa spasi luar saat disimpan.
     */
    protected function kodeOpd(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? strtoupper($value) : null,
            set: fn (?string $value) => $value ? strtoupper(trim($value)) : null,
        );
    }

    /**
     * Mutator & Accessor untuk Nama OPD:
     * - Trimming otomatis dan kapitalisasi judul (Title Case).
     */
    protected function namaOpd(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ? trim($value) : null,
            set: fn (?string $value) => $value ? trim($value) : null,
        );
    }

    /*
    |--------------------------------------------------------------------------
    | RELASI DATABASE (RELATIONSHIPS)
    |--------------------------------------------------------------------------
    */

    /**
     * Relasi One-to-Many: Satu OPD memiliki banyak Pengguna / Pegawai.
     */
    public function users(): HasMany
    {
        return $this->hasMany(User::class, 'opd_id', 'id');
    }

    /**
     * Relasi One-to-Many: Satu OPD dapat terkait dengan banyak Laporan Patroli / Cyber Patrol.
     */
    public function patrols(): HasMany
    {
        return $this->hasMany(Patrol::class, 'opd_id', 'id');
    }

    /*
    |--------------------------------------------------------------------------
    | LOCAL SCOPES (KUERIES TERSTRUKTUR & SEARCH)
    |--------------------------------------------------------------------------
    */

    /**
     * Scope untuk pencarian cepat berdasarkan kata kunci (Pencarian Dinamis).
     *
     * @param Builder $query
     * @param string|null $keyword
     * @return Builder
     */
    public function scopeSearch(Builder $query, ?string $keyword): Builder
    {
        if (blank($keyword)) {
            return $query;
        }

        $cleanKeyword = trim($keyword);

        return $query->where(function (Builder $q) use ($cleanKeyword) {
            $q->where('nama_opd', 'LIKE', "%{$cleanKeyword}%")
              ->orWhere('kode_opd', 'LIKE', "%{$cleanKeyword}%")
              ->orWhere('deskripsi', 'LIKE', "%{$cleanKeyword}%");
        });
    }

    /**
     * Scope untuk menyusun data berdasarkan nama OPD secara alfabetis.
     *
     * @param Builder $query
     * @param string $direction ('asc' atau 'desc')
     * @return Builder
     */
    public function scopeOrderByName(Builder $query, string $direction = 'asc'): Builder
    {
        return $query->orderBy('nama_opd', $direction);
    }

    /*
    |--------------------------------------------------------------------------
    | MODEL EVENTS (PROTEKSI INTEGRITAS DATA)
    |--------------------------------------------------------------------------
    */

    /**
     * Event boot model untuk memproteksi penghapusan data terikat.
     */
    protected static function boot()
    {
        parent::boot();

        // Mencegah hapus record jika masih ada relasi di User atau Patrol
        static::deleting(function (MasterOpd $opd) {
            if ($opd->users()->exists()) {
                throw new \Exception("Gagal Hapus: OPD '{$opd->nama_opd}' masih digunakan oleh data Pengguna.");
            }

            if ($opd->patrols()->exists()) {
                throw new \Exception("Gagal Hapus: OPD '{$opd->nama_opd}' masih terikat dengan data Laporan Patroli.");
            }
        });
    }
}