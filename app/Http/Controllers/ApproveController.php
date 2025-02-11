<?php

namespace App\Http\Controllers;
use App\Models\PengajuanSurat;
use Illuminate\Http\Request;

class ApproveController extends Controller
{
    public function index()
    {
        $pengajuanSurats = PengajuanSurat::with('template', 'user')->where('status', 'pending')->get();
        return view('approve.index', compact('pengajuanSurats'));
    }

    public function approve(Request $request, PengajuanSurat $pengajuanSurat)
    {
    $request->validate([
        'signature' => 'required|string',  // Menambahkan validasi tipe data string
    ]);
    // Menyimpan tanda tangan dalam kolom signature
    $pengajuanSurat->update([
        'status' => 'approved',
        'signature' => $request->signature,  // Menyimpan tanda tangan yang diterima
    ]);
        return response()->json(['message' => 'Surat telah disetujui.']);

    }

    public function reject(PengajuanSurat $pengajuanSurat)
    {
        // Perbarui status surat menjadi ditolak
        $pengajuanSurat->update([
            'status' => 'rejected',
        ]);
        return response()->json(['message' => 'Surat telah ditolak.']);
    }
}
