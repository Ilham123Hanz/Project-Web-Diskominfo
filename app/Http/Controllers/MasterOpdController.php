<?php

namespace App\Http\Controllers;

use App\Models\MasterOpd;
use App\Models\OpdEmail;
use App\Models\OpdSosmed;
use App\Models\OpdAplikasi;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\StreamedResponse;

class MasterOpdController extends Controller
{
    /**
     * Halaman Utama Master OPD (Menampilkan 3 Tab Data)
     */
    public function index()
    {
        // Data Master OPD untuk dropdown modal
        $masterOpd = MasterOpd::orderBy('nama_opd', 'asc')->get();

        // Data untuk masing-masing tab
        $opdEmails   = OpdEmail::with('masterOpd')->latest()->get();
        $opdSosmeds  = OpdSosmed::with('masterOpd')->latest()->get();
        $opdAplikasis = OpdAplikasi::with('masterOpd')->latest()->get();

        return view('admin.master-opd', compact('masterOpd', 'opdEmails', 'opdSosmeds', 'opdAplikasis'));
    }

    // ==========================================
    // --- EMAIL OPD ---
    // ==========================================

    public function storeEmail(Request $request)
    {
        $request->validate([
            'master_opd_id' => 'required',
            'email'         => 'required|email',
        ]);

        OpdEmail::create([
            'opd_id'       => $request->master_opd_id,
            'alamat_email' => $request->email,
            'keterangan'   => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Data Email OPD berhasil disimpan.');
    }

    public function updateEmail(Request $request, $id)
    {
        $request->validate([
            'master_opd_id' => 'required',
            'email'         => 'required|email',
        ]);

        $email = OpdEmail::findOrFail($id);
        $email->update([
            'opd_id'       => $request->master_opd_id, // Ditambahkan agar OPD bisa diubah saat edit
            'alamat_email' => $request->email,
            'keterangan'   => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Data Email OPD berhasil diperbarui.');
    }

    public function destroyEmail($id)
    {
        OpdEmail::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Email OPD berhasil dihapus.');
    }

    public function exportEmailCsv()
    {
        $emails = OpdEmail::with('masterOpd')->get();
        $response = new StreamedResponse(function () use ($emails) {
            $handle = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM agar rapi di Microsoft Excel
            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['NO', 'NAMA OPD / INSTANSI', 'ALAMAT EMAIL RESMI', 'KETERANGAN']);

            foreach ($emails as $index => $item) {
                fputcsv($handle, [
                    $index + 1,
                    $item->masterOpd->nama_opd ?? '-',
                    $item->alamat_email,
                    $item->keterangan ?? '-'
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="data_email_opd.csv"');

        return $response;
    }

    // ==========================================
    // --- MEDIA SOSIAL OPD ---
    // ==========================================

    public function storeSosmed(Request $request)
    {
        $request->validate([
            'master_opd_id' => 'required',
            'instagram'     => 'required',
        ]);

        OpdSosmed::create([
            'opd_id'       => $request->master_opd_id,
            'nama_akun_ig' => $request->instagram,
            'keterangan'   => $request->keterangan,
            'status'       => 'aktif',
        ]);

        return redirect()->back()->with('success', 'Data Media Sosial berhasil disimpan.');
    }

    public function updateSosmed(Request $request, $id)
    {
        $request->validate([
            'master_opd_id' => 'required',
            'instagram'     => 'required',
        ]);

        $sosmed = OpdSosmed::findOrFail($id);
        $sosmed->update([
            'opd_id'       => $request->master_opd_id, // Ditambahkan agar OPD bisa diubah saat edit
            'nama_akun_ig' => $request->instagram,
            'keterangan'   => $request->keterangan,
        ]);

        return redirect()->back()->with('success', 'Data Media Sosial berhasil diperbarui.');
    }

    public function destroySosmed($id)
    {
        OpdSosmed::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Media Sosial berhasil dihapus.');
    }

    public function exportSosmedCsv()
    {
        $fileName = 'data_sosmed_opd_' . date('Y-m-d') . '.csv';

        $headers = [
            "Content-type"        => "text/csv; charset=UTF-8",
            "Content-Disposition" => "attachment; filename=$fileName",
            "Pragma"              => "no-cache",
            "Cache-Control"       => "must-revalidate, post-check=0, pre-check=0",
            "Expires"             => "0"
        ];

        return response()->stream(function() {
            $file = fopen('php://output', 'w');

            // Tambahkan UTF-8 BOM agar Excel rapi dan kolom terpisah otomatis
            fputs($file, "\xEF\xBB\xBF");

            // Header CSV (menggunakan titik koma ';' sebagai separator)
            fputcsv($file, ['NO', 'NAMA OPD / INSTANSI', 'INSTAGRAM', 'KETERANGAN'], ';');

            $data = OpdSosmed::with('masterOpd')->get(); 

            foreach ($data as $index => $row) {
                fputcsv($file, [
                    $index + 1,
                    $row->masterOpd->nama_opd ?? '-',
                    $row->nama_akun_ig ?? '-',
                    $row->keterangan ?? '-'
                ], ';');
            }
            
            fclose($file);
        }, 200, $headers);
    }

    // ==========================================
    // --- APLIKASI PEMPROV ---
    // ==========================================

    public function storeAplikasi(Request $request)
    {
        $request->validate([
            'master_opd_id' => 'required',
            'nama_aplikasi' => 'required',
            'kode_aset'     => 'nullable',
            'url'           => 'required',
            'status'        => 'required',
        ]);

        OpdAplikasi::create([
            'opd_id'             => $request->master_opd_id,
            'nama_sistem'        => $request->nama_aplikasi,
            'kode_aset'          => $request->kode_aset,
            'domain_url'         => $request->url,
            'status_operasional' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Data Aplikasi berhasil disimpan.');
    }

    public function updateAplikasi(Request $request, $id)
    {
        $request->validate([
            'master_opd_id' => 'required',
            'nama_aplikasi' => 'required',
            'kode_aset'     => 'nullable',
            'url'           => 'required',
            'status'        => 'required',
        ]);

        $aplikasi = OpdAplikasi::findOrFail($id);
        $aplikasi->update([
            'opd_id'             => $request->master_opd_id,
            'nama_sistem'        => $request->nama_aplikasi,
            'kode_aset'          => $request->kode_aset,
            'domain_url'         => $request->url,
            'status_operasional' => $request->status,
        ]);

        return redirect()->back()->with('success', 'Data Aplikasi berhasil diperbarui.');
    }

    public function destroyAplikasi($id)
    {
        OpdAplikasi::findOrFail($id)->delete();
        return redirect()->back()->with('success', 'Data Aplikasi berhasil dihapus.');
    }

    public function exportAplikasiCsv()
    {
        $aplikasis = OpdAplikasi::with('masterOpd')->get();
        $response = new StreamedResponse(function () use ($aplikasis) {
            $handle = fopen('php://output', 'w');

            fprintf($handle, chr(0xEF).chr(0xBB).chr(0xBF));

            fputcsv($handle, ['NO', 'NAMA APLIKASI', 'KODE ASET', 'DOMAIN/URL', 'OPD PEMILIK', 'STATUS']);

            foreach ($aplikasis as $index => $item) {
                fputcsv($handle, [
                    $index + 1,
                    $item->nama_sistem,
                    $item->kode_aset ?? '-',
                    $item->domain_url,
                    $item->masterOpd->nama_opd ?? '-',
                    $item->status_operasional
                ]);
            }
            fclose($handle);
        });

        $response->headers->set('Content-Type', 'text/csv; charset=UTF-8');
        $response->headers->set('Content-Disposition', 'attachment; filename="data_aplikasi_pemprov.csv"');

        return $response;
    }
}