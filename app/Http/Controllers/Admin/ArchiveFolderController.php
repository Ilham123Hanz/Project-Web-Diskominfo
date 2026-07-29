<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\ArchiveFolder;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

class ArchiveFolderController extends Controller
{
    /**
     * Menampilkan daftar folder virtual arsip
     */
   public function index(Request $request)
{
    $query = ArchiveFolder::query();

    if ($request->filled('search')) {
        $query->where('name', 'like', '%' . $request->search . '%')
              ->orWhere('description', 'like', '%' . $request->search . '%');
    }

    $folders = $query->latest()->get();

    // Pastikan memanggil 'admin.folder-virtual' (sesuai nama file blade Anda)
    return view('admin.folder-virtual', compact('folders'));
}

    /**
     * Menyimpan folder baru ke Database & Storage Fisik
     */
    public function store(Request $request)
    {
        $request->validate([
            'name'        => 'required|string|max:100|unique:archive_folders,name',
            'description' => 'nullable|string|max:255',
        ], [
            'name.required' => 'Nama folder wajib diisi.',
            'name.unique'   => 'Nama folder ini sudah ada.',
        ]);

        try {
            // Format nama folder (ganti spasi dengan underscore)
            $formattedName = str_replace(' ', '_', trim($request->name));
            $slug = Str::slug($formattedName, '_');

            // 1. Buat folder fisik di storage/app/public/virtual_archives/{slug}
            $folderPath = 'virtual_archives/' . $slug;
            if (!Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->makeDirectory($folderPath);
            }

            // 2. Simpan record di Database
            ArchiveFolder::create([
                'name'        => $formattedName,
                'slug'        => $slug,
                'description' => $request->description,
            ]);

            return redirect()->back()->with('success', 'Folder "' . $formattedName . '" berhasil dibuat.');

        } catch (\Exception $e) {
            \Log::error('Gagal Buat Folder: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal membuat folder virtual.');
        }
    }

    /**
     * Menghapus folder beserta berkas fisik di dalamnya
     */
    public function destroy($id)
    {
        try {
            $folder = ArchiveFolder::findOrFail($id);

            // 1. Hapus folder fisik di storage
            $folderPath = 'virtual_archives/' . $folder->slug;
            if (Storage::disk('public')->exists($folderPath)) {
                Storage::disk('public')->deleteDirectory($folderPath);
            }

            // 2. Hapus data dari database
            $folderName = $folder->name;
            $folder->delete();

            return redirect()->back()->with('success', 'Folder "' . $folderName . '" beserta isinya berhasil dihapus.');

        } catch (\Exception $e) {
            \Log::error('Gagal Hapus Folder: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menghapus folder.');
        }
    }
}