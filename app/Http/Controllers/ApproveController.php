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
    $request->validate([
        'signature' => 'required|string',
    ]);
    // Menyimpan tanda tangan dalam kolom signature
    $pengajuanSurat->update([
        'status' => 'diterima',
        'signature' => $request->signature,
    ]);
        return response()->json(['message' => 'Surat telah disetujui.']);

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
