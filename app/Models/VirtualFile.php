<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class VirtualFile extends Model
{
    use HasFactory;

    protected $table = 'virtual_files';

    protected $fillable = [
        'archive_folder_id',
        'name',
        'original_name',
        'path',
        'mime_type',
        'size',
        'source',
        'laporan_id',
        'uploaded_by',
    ];

    protected $casts = [
        'size' => 'integer',
    ];

    /**
     * Get the archive folder that owns the virtual file.
     */
    public function archiveFolder(): BelongsTo
    {
        return $this->belongsTo(ArchiveFolder::class, 'archive_folder_id');
    }

    /**
     * Get the laporan associated with the virtual file.
     */
    public function laporan(): BelongsTo
    {
        return $this->belongsTo(Laporan::class, 'laporan_id');
    }

    /**
     * Get the user who uploaded the file.
     */
    public function uploader(): BelongsTo
    {
        return $this->belongsTo(User::class, 'uploaded_by');
    }

    /**
     * Get human readable file size.
     */
    public function getHumanSizeAttribute(): string
    {
        return self::formatBytes($this->size);
    }

    /**
     * Format bytes to human readable string (static method for Blade views).
     */
    public static function formatBytes(int $bytes): string
    {
        if ($bytes >= 1073741824) {
            return number_format($bytes / 1073741824, 2) . ' GB';
        } elseif ($bytes >= 1048576) {
            return number_format($bytes / 1048576, 2) . ' MB';
        } elseif ($bytes >= 1024) {
            return number_format($bytes / 1024, 2) . ' KB';
        } else {
            return $bytes . ' B';
        }
    }

    /**
     * Get the file icon based on mime type.
     */
    public function getFileIconAttribute(): string
    {
        if (!$this->mime_type) {
            return 'fas fa-file';
        }

        if (str_starts_with($this->mime_type, 'image/')) {
            return 'fas fa-file-image';
        } elseif (str_starts_with($this->mime_type, 'video/')) {
            return 'fas fa-file-video';
        } elseif (str_starts_with($this->mime_type, 'audio/')) {
            return 'fas fa-file-audio';
        } elseif (in_array($this->mime_type, [
            'application/pdf',
        ])) {
            return 'fas fa-file-pdf';
        } elseif (in_array($this->mime_type, [
            'application/msword',
            'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
        ])) {
            return 'fas fa-file-word';
        } elseif (in_array($this->mime_type, [
            'application/vnd.ms-excel',
            'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
        ])) {
            return 'fas fa-file-excel';
        } elseif (in_array($this->mime_type, [
            'application/vnd.ms-powerpoint',
            'application/vnd.openxmlformats-officedocument.presentationml.presentation',
        ])) {
            return 'fas fa-file-powerpoint';
        } elseif (in_array($this->mime_type, [
            'application/zip',
            'application/x-rar-compressed',
            'application/x-7z-compressed',
        ])) {
            return 'fas fa-file-archive';
        } else {
            return 'fas fa-file';
        }
    }

    /**
     * Check if file is an image.
     */
    public function getIsImageAttribute(): bool
    {
        return $this->mime_type && str_starts_with($this->mime_type, 'image/');
    }

    /**
     * Check if file is a PDF.
     */
    public function getIsPdfAttribute(): bool
    {
        return $this->mime_type === 'application/pdf';
    }
}