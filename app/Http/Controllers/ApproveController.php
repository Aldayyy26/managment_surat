<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    public function index()
    {
        $pengajuanSurats = PengajuanSurat::with('template', 'user')->where('status', 'proses')->get();
        return view('approve.index', compact('pengajuanSurats'));
    }

    public function approve(Request $request, PengajuanSurat $pengajuanSurat)
    {
        // Validasi bahwa signature wajib ada dan berupa string
        $request->validate([
            'signature' => 'required|string',
        ]);

        try {
            // Mendekode data base64 tanda tangan
            $signatureData = $request->signature;
            list($type, $data) = explode(';', $signatureData);
            list(, $data) = explode(',', $data);

            // Mendekode data gambar dari base64
            $imageData = base64_decode($data);

            // Membuat nama file unik dengan timestamp
            $filename = 'signature_' . time() . '.png';

            // Menentukan path penyimpanan gambar tanda tangan
            $path = storage_path('app/public/signatures/' . $filename);

            // Menyimpan gambar tanda tangan ke file
            file_put_contents($path, $imageData);

            // Memperbarui data pengajuan surat dengan status diterima dan nama file tanda tangan
            $pengajuanSurat->update([
                'status' => 'diterima',  // Misalnya status diubah menjadi diterima
                'signature' => 'signatures/' . $filename,  // Menyimpan path file gambar tanda tangan
            ]);

            // Mengembalikan respons sukses dengan pesan
            return response()->json(['message' => 'Surat telah disetujui.']);
            
        } catch (\Exception $e) {
            // Mengembalikan respons error jika ada masalah dalam proses
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }


    public function reject(PengajuanSurat $pengajuanSurat)
    {
        // Perbarui status surat menjadi ditolak
        $pengajuanSurat->update([
            'status' => 'ditolak',
        ]);
        return response()->json(['message' => 'Surat telah ditolak.']);
    }
    
    public function detail($id)
    {
        $surat = TemplateSurat::with('template', 'user')->findOrFail($id);
        return response()->json(['surat' => $surat]);
    }
}
