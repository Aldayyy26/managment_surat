<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PengajuanSurat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;


class PengajuanSuratController extends Controller
{
    use HasFactory;

    public function index()
    {
        $pengajuanSurats = PengajuanSurat::where('user_id', Auth::id())->get();
        return view('pengajuan_surat.index', compact('pengajuanSurats'));
    }

    public function approvalIndex()
    {
        // Fetch pengajuanSurat with the associated user and template
        $pengajuanSurats = PengajuanSurat::where('status', 'pending')
            ->with(['user', 'template']) // Eager load the relationships
            ->get();

        return view('approval.index', compact('pengajuanSurats'));
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
    // app/Models/PengajuanSurat.php
    public function user()
    {
        return $this->belongsTo(User::class);
    }

}
