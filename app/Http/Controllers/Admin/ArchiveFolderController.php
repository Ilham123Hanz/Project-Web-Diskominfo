<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchiveFolder;
use App\Models\Laporan;
use App\Models\VirtualFile;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArchiveFolderController extends Controller
{
    /**
     * Display a listing of the virtual archive folders.
     */
    public function index()
    {
        $folders = ArchiveFolder::with(['creator', 'virtualFiles'])
            ->orderBy('year', 'desc')
            ->orderBy('name', 'asc')
            ->paginate(20);

        return view('admin.folder-virtual', compact('folders'));
    }

    /**
     * Display the specified folder with its files.
     */
    public function show($id)
    {
        $folder = ArchiveFolder::with([
            'creator', 
            'virtualFiles.uploader', 
            'virtualFiles.laporan.user'
        ])->findOrFail($id);

        // Get laporan that can be imported (matching year and main_menu category)
        $importableLaporan = Laporan::whereYear('created_at', $folder->year)
            ->where('main_menu', $folder->main_menu)
            ->whereDoesntHave('virtualFiles', function($q) use ($folder) {
                $q->where('archive_folder_id', $folder->id);
            })
            ->with('user')
            ->get();

        return view('admin.folder-virtual-detail', compact('folder', 'importableLaporan'));
    }

    /**
     * Store a newly created virtual folder.
     */
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255|unique:archive_folders,name',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'main_menu' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ], [
            'name.unique' => 'Nama folder sudah digunakan. Silakan gunakan nama yang berbeda.',
        ]);

        try {
            DB::beginTransaction();

            // Generate slug from name
            $slug = Str::slug($request->name) . '-' . $request->year;
            // Ensure unique slug
            $originalSlug = $slug;
            $counter = 1;
            while (ArchiveFolder::where('slug', $slug)->exists()) {
                $slug = $originalSlug . '-' . $counter++;
            }

            $folder = ArchiveFolder::create([
                'name' => $request->name,
                'slug' => $slug,
                'year' => $request->year,
                'main_menu' => $request->main_menu,
                'description' => $request->description,
                'created_by' => Auth::id(),
            ]);

            DB::commit();

            Log::info("Folder virtual arsip dibuat: {$folder->name} ({$folder->year}) [{$folder->main_menu}] oleh Admin ID " . Auth::id());

            return redirect()->back()->with('success', 'Folder virtual arsip berhasil dibuat.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal membuat folder virtual arsip: ' . $e->getMessage() . ' | Trace: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal membuat folder virtual arsip: ' . $e->getMessage());
        }
    }

    /**
     * Update the specified folder.
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'year' => 'required|integer|min:2020|max:' . (date('Y') + 1),
            'main_menu' => 'required|string|max:255',
            'description' => 'nullable|string|max:1000',
        ]);

        $folder = ArchiveFolder::findOrFail($id);

        try {
            DB::beginTransaction();

            $folder->update([
                'name' => $request->name,
                'year' => $request->year,
                'main_menu' => $request->main_menu,
                'description' => $request->description,
            ]);

            DB::commit();

            Log::info("Folder virtual arsip diperbarui: {$folder->name} ({$folder->year}) [{$folder->main_menu}] oleh Admin ID " . Auth::id());

            return redirect()->back()->with('success', 'Folder virtual arsip berhasil diperbarui.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal memperbarui folder virtual arsip: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal memperbarui folder virtual arsip.');
        }
    }

    /**
     * Remove the specified virtual folder.
     */
    public function destroy($id)
    {
        try {
            DB::beginTransaction();

            $folder = ArchiveFolder::findOrFail($id);
            $folderName = $folder->name;

            // Delete physical files in storage
            foreach ($folder->virtualFiles as $file) {
                if (Storage::disk('public')->exists($file->path)) {
                    Storage::disk('public')->delete($file->path);
                }
            }

            $folder->delete();

            DB::commit();

            Log::info("Folder virtual arsip dihapus: {$folderName} oleh Admin ID " . Auth::id());

            return redirect()->route('admin.folders.index')->with('success', 'Folder virtual arsip berhasil dihapus.');

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Gagal menghapus folder virtual arsip: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus folder virtual arsip.');
        }
    }

    /**
     * Upload file to folder.
     */
    public function uploadFile(Request $request, $id)
    {
        $folder = ArchiveFolder::findOrFail($id);

        $request->validate([
            'file' => 'required|file|max:20480', // Max 20MB
        ]);

        try {
            $file = $request->file('file');
            $originalName = $file->getClientOriginalName();
            $mimeType = $file->getMimeType();
            $size = $file->getSize();
            $extension = $file->getClientOriginalExtension();

            // Store file
            $year = $folder->year;
            $cleanFolderName = Str::slug($folder->name);
            $subFolder = "arsip_virtual/{$year}/{$cleanFolderName}";
            
            $fileName = 'FILE_' . time() . '_' . Auth::id() . '_' . Str::random(8) . '.' . $extension;
            $path = $file->storeAs($subFolder, $fileName, 'public');

            // Create virtual file record
            $virtualFile = VirtualFile::create([
                'archive_folder_id' => $folder->id,
                'name' => $fileName,
                'original_name' => $originalName,
                'path' => $path,
                'mime_type' => $mimeType,
                'size' => $size,
                'source' => 'manual',
                'uploaded_by' => Auth::id(),
            ]);

            Log::info("File diunggah ke folder arsip: {$virtualFile->original_name} ke {$folder->name} oleh Admin ID " . Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'File berhasil diunggah.',
                'file' => [
                    'id' => $virtualFile->id,
                    'name' => $virtualFile->original_name,
                    'size' => $virtualFile->human_size,
                    'icon' => $virtualFile->file_icon,
                    'is_image' => $virtualFile->is_image,
                    'is_pdf' => $virtualFile->is_pdf,
                    'url' => asset('storage/' . $virtualFile->path),
                    'created_at' => $virtualFile->created_at->format('d M Y H:i'),
                ],
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal mengunggah file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal mengunggah file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
     * Delete file from folder.
     */
    public function deleteFile($folderId, $fileId)
    {
        try {
            $folder = ArchiveFolder::findOrFail($folderId);
            $virtualFile = VirtualFile::where('archive_folder_id', $folderId)->findOrFail($fileId);

            // Delete physical file
            if (Storage::disk('public')->exists($virtualFile->path)) {
                Storage::disk('public')->delete($virtualFile->path);
            }

            $fileName = $virtualFile->original_name;
            $virtualFile->delete();

            Log::info("File dihapus dari folder arsip: {$fileName} dari {$folder->name} oleh Admin ID " . Auth::id());

            return response()->json([
                'success' => true,
                'message' => 'File berhasil dihapus.',
            ]);

        } catch (\Exception $e) {
            Log::error('Gagal menghapus file: ' . $e->getMessage());
            return response()->json([
                'success' => false,
                'message' => 'Gagal menghapus file: ' . $e->getMessage(),
            ], 500);
        }
    }

    /**
         * Import laporan from petugas into this folder.
         */
        public function importLaporan(Request $request, $id)
        {
            $folder = ArchiveFolder::findOrFail($id);

            $request->validate([
                'laporan_ids' => 'required|array|min:1',
                'laporan_ids.*' => 'exists:laporan,id',
            ]);

            $imported = 0;
            $skipped = 0;

            try {
                DB::beginTransaction();

                foreach ($request->laporan_ids as $laporanId) {
                    $laporan = Laporan::with('user')->find($laporanId);
                
                    if (!$laporan) {
                        $skipped++;
                        continue;
                    }

                    // Check if already imported
                    $exists = VirtualFile::where('archive_folder_id', $folder->id)
                        ->where('laporan_id', $laporan->id)
                        ->exists();

                    if ($exists) {
                        $skipped++;
                        continue;
                    }

                    // If laporan has file evidence, find and copy it
                    if ($laporan->file_evidence) {
                        $year = $laporan->created_at->format('Y');
                        // Search for actual file in storage (category folder name might differ)
                        $allFiles = Storage::disk('public')->allFiles("bukti_files/{$year}");
                        $foundPath = collect($allFiles)->first(function($f) use ($laporan) {
                            return str_ends_with($f, $laporan->file_evidence);
                        });

                        if ($foundPath && Storage::disk('public')->exists($foundPath)) {
                            // Copy to archive folder
                            $cleanFolderName = Str::slug($folder->name);
                            $destFolder = "arsip_virtual/{$year}/{$cleanFolderName}";
                            $extension = pathinfo($laporan->file_evidence, PATHINFO_EXTENSION);
                            $destName = 'EVIDENCE_' . $laporan->log_code . '_' . Str::random(8) . '.' . $extension;
                            $destPath = "{$destFolder}/{$destName}";

                            Storage::disk('public')->copy($foundPath, $destPath);

                            VirtualFile::create([
                                'archive_folder_id' => $folder->id,
                                'name' => $destName,
                                'original_name' => $laporan->file_evidence,
                                'path' => $destPath,
                                'mime_type' => Storage::disk('public')->mimeType($foundPath),
                                'size' => Storage::disk('public')->size($foundPath),
                                'source' => 'patroli',
                                'laporan_id' => $laporan->id,
                                'uploaded_by' => Auth::id(),
                            ]);
                        } else {
                            // Create placeholder record if file not found
                            VirtualFile::create([
                                'archive_folder_id' => $folder->id,
                                'name' => 'LAPORAN_' . $laporan->log_code,
                                'original_name' => 'Laporan: ' . $laporan->log_code . ' - ' . $laporan->opd_sasaran,
                                'path' => '',
                                'mime_type' => 'application/x-empty',
                                'size' => 0,
                                'source' => 'patroli',
                                'laporan_id' => $laporan->id,
                                'uploaded_by' => Auth::id(),
                            ]);
                        }
                    } else {
                        // Create placeholder record even without file
                        VirtualFile::create([
                            'archive_folder_id' => $folder->id,
                            'name' => 'LAPORAN_' . $laporan->log_code,
                            'original_name' => 'Laporan: ' . $laporan->log_code . ' - ' . $laporan->opd_sasaran,
                            'path' => '',
                            'mime_type' => 'application/x-empty',
                            'size' => 0,
                            'source' => 'patroli',
                            'laporan_id' => $laporan->id,
                            'uploaded_by' => Auth::id(),
                        ]);
                    }

                    $imported++;
                }

                DB::commit();

                Log::info("Import laporan ke folder arsip: {$imported} berhasil, {$skipped} dilewati ke {$folder->name} oleh Admin ID " . Auth::id());

                return redirect()->back()->with('success', "Berhasil mengimpor {$imported} laporan ke folder {$folder->name}." . ($skipped ? " ({$skipped} dilewati)" : ""));

            } catch (\Exception $e) {
                DB::rollBack();
                Log::error('Gagal mengimpor laporan: ' . $e->getMessage());
                return redirect()->back()->with('error', 'Gagal mengimpor laporan: ' . $e->getMessage());
            }
        }
    }