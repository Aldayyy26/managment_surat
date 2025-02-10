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
        'signature' => 'required|string',  // Menambahkan validasi tipe data string
    ]);

    // Menyimpan tanda tangan dalam kolom signature
    $pengajuanSurat->update([
        'status' => 'Disetujui',
        'signature' => $request->signature,  // Menyimpan tanda tangan yang diterima
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
