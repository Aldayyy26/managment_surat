<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    /**
     * Menampilkan daftar surat yang perlu persetujuan.
     */
    public function index()
    {
        // Ambil semua pengajuan surat yang masih dalam status pending
        $pengajuanSurats = PengajuanSurat::with('template', 'user')->where('status', 'pending')->get();

        // Kirimkan data ke view
        return view('approve.index', compact('pengajuanSurats'));
    }

    /**
     * Menyetujui pengajuan surat dengan tanda tangan.
     */
    public function approve(Request $request, PengajuanSurat $pengajuanSurat)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        // Konversi base64 ke file gambar
        $signatureData = $request->signature;
        $signatureData = str_replace('data:image/png;base64,', '', $signatureData);
        $signatureData = str_replace(' ', '+', $signatureData);
        $signatureImage = base64_decode($signatureData);
        
        // Simpan gambar ke dalam storage
        $fileName = 'signatures/' . uniqid() . '.png';
        Storage::disk('public')->put($fileName, $signatureImage);

        // Simpan path gambar ke database
        $pengajuanSurat->update([
            'status' => 'Disetujui',
            'signature' => $fileName, // Simpan path gambar
        ]);

        return response()->json(['message' => 'Surat telah disetujui.']);
    }

    /**
     * Menolak pengajuan surat.
     */
    public function reject(PengajuanSurat $pengajuanSurat)
    {
        // Perbarui status surat menjadi ditolak
        $pengajuanSurat->update([
            'status' => 'Ditolak',
        ]);

        // Mengembalikan respons sukses
        return response()->json(['message' => 'Surat telah ditolak.']);
    }
}
