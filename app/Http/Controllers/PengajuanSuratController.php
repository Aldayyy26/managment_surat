<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class PengajuanSuratController extends Controller
{
    public function index()
    {
        $pengajuanSurats = PengajuanSurat::where('user_id', Auth::id())->get();
        
        return view('pengajuan_surat.index', compact('pengajuanSurats'));
    }

    public function create()
    {
        $templates = TemplateSurat::all();
        return view('pengajuan_surat.create', compact('templates'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'template_id' => 'required|exists:template_surats,id',
            'konten' => 'required|array',
        ]);

        PengajuanSurat::create([
            'user_id' => Auth::id(),
            'template_id' => $request->template_id,
            'konten' => json_encode($request->konten),
            'status' => 'pending',
        ]);

        return redirect()->route('pengajuan-surat.index')->with('success', 'Pengajuan surat berhasil diajukan.');
    }

    public function show(PengajuanSurat $pengajuanSurat)
    {
        return view('pengajuan_surat.show', compact('pengajuanSurat'));
    }
    
    public function approve(PengajuanSurat $pengajuanSurat, Request $request)
    {
    $request->validate([
        'signature' => 'required',
    ]);

    $pengajuanSurat->update([
        'status' => 'approved',
        'signature' => $request->signature, // Simpan tanda tangan
    ]);

    return redirect()->route('pengajuan-surat.index')->with('success', 'Surat telah disetujui.');
    }

    public function reject(PengajuanSurat $pengajuanSurat)
    {
        $pengajuanSurat->update([
            'status' => 'rejected',
        ]);

        return redirect()->route('pengajuan-surat.index')->with('error', 'Surat telah ditolak.');
    }

}
