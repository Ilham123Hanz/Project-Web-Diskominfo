<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Support\Str;

class ArchiveFolder extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'year',
        'parent_id',
        'created_by',
        'main_menu',
    ];

    /**
     * Boot method to auto-generate slug
     */
    protected static function boot()
    {
        parent::boot();
        
        static::creating(function ($folder) {
            if (empty($folder->slug)) {
                $folder->slug = Str::slug($folder->name) . '-' . $folder->year;
                // Ensure unique slug
                $originalSlug = $folder->slug;
                $counter = 1;
                while (static::where('slug', $folder->slug)->exists()) {
                    $folder->slug = $originalSlug . '-' . $counter++;
                }
            }
        });
    }

    /**
     * Get the user who created this folder.
     */
    public function creator(): BelongsTo
    {
        return $this->belongsTo(User::class, 'created_by');
    }

    /**
     * Get the parent folder (for nested folders).
     */
    public function parent(): BelongsTo
    {
        return $this->belongsTo(ArchiveFolder::class, 'parent_id');
    }

    /**
     * Get sub-folders.
     */
    public function subFolders(): HasMany
    {
        return $this->hasMany(ArchiveFolder::class, 'parent_id');
    }

    /**
     * Get reports in this folder (virtual relationship based on year/category).
     */
    public function reports(): HasMany
    {
        return $this->hasMany(Laporan::class, 'main_menu', 'name');
    }

    /**
     * Get virtual files in this folder.
     */
    public function virtualFiles(): HasMany
    {
        return $this->hasMany(VirtualFile::class, 'archive_folder_id');
    }
}