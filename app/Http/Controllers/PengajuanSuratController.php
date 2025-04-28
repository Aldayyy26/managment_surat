<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PengajuanSurat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;



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
        $pengajuanSurats = PengajuanSurat::where('status', 'proses')
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
            'status' => 'proses',
        ]);

        return redirect()->route('pengajuan-surat.index')->with('success', 'Pengajuan surat berhasil diajukan.');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function download(PengajuanSurat $pengajuanSurat)
    {
        if ($pengajuanSurat->status != 'diterima') {
            return redirect()->route('pengajuan-surat.index')
                ->with('error', 'Hanya surat yang telah disetujui yang dapat diunduh.');
        }

        $pdf = Pdf::loadView('pengajuan_surat.surat_pdf', compact('pengajuanSurat'))
                ->setPaper('a4', 'portrait');

        return $pdf->download('Surat_Pengajuan_' . $pengajuanSurat->id . '.pdf');
    }

    public function edit(PengajuanSurat $pengajuanSurat)
    {
        if ($pengajuanSurat->user_id !== Auth::id() || $pengajuanSurat->status !== 'proses') {
            return redirect()->route('pengajuan-surat.index')->with('error', 'Hanya surat dalam status pending yang dapat diedit.');
        }

        $templates = TemplateSurat::all();
        return view('pengajuan_surat.edit', compact('pengajuanSurat', 'templates'));
    }

    public function update(Request $request, PengajuanSurat $pengajuanSurat)
    {
        if ($pengajuanSurat->user_id !== Auth::id() || $pengajuanSurat->status !== 'proses') {
            return redirect()->route('pengajuan-surat.index')->with('error', 'Hanya surat dalam status pending yang dapat diperbarui.');
        }

        $request->validate([
            'template_id' => 'required|exists:template_surats,id',
            'konten' => 'required|array',
        ]);

        $pengajuanSurat->update([
            'template_id' => $request->template_id,
            'konten' => json_encode($request->konten),
        ]);

        return redirect()->route('pengajuan-surat.index')->with('success', 'Pengajuan surat berhasil diperbarui.');
    }
}
