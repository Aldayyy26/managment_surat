<?php

namespace App\Http\Controllers;

use App\Models\TemplateSurat;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function index(Request $request)
    {
        $query = TemplateSurat::query();

        if ($request->filled('search')) {
            $query->where('judul', 'like', '%' . $request->search . '%');
        }

        $surats = $query->paginate(10); 
        return view('surats.index', compact('surats'));
    }

    public function create()
    {
        return view('surats.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'lampiran' => 'required|string|max:255',
            'perihal' => 'nullable|string|max:255',
            'kepada_yth' => 'nullable|string|max:255',
            'pembuka' => 'nullable|string|max:255',
            'teks_atas' => 'nullable|string|max:255',
            'konten' => 'required|array',
            'konten.*.label' => 'required|string|max:255',
            'konten.*.type' => 'required|string|in:text,date,number,email,textarea,checkbox,radio,select',
            'konten.*.value' => 'nullable|string|max:255',
            'teks_bawah' => 'nullable|string|max:255',
            'penutup' => 'nullable|string|max:255',
        ]);

        TemplateSurat::create([
            'judul' => $request->judul,
            'lampiran' => $request->lampiran,
            'perihal' => $request->perihal,
            'kepada_yth' => $request->kepada_yth,
            'pembuka' => $request->pembuka,
            'teks_atas' => $request->teks_atas,
            'konten' => json_encode($request->konten), 
            'teks_bawah' => $request->teks_bawah,
            'penutup' => $request->penutup,
        ]);

        return redirect()->route('surats.index')->with('success', 'Template surat berhasil dibuat.');
    }

    public function update(Request $request, TemplateSurat $surat)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'lampiran' => 'required|string|max:255',
            'perihal' => 'nullable|string|max:255',
            'kepada_yth' => 'nullable|string|max:255',
            'pembuka' => 'nullable|string|max:255',
            'teks_atas' => 'nullable|string|max:255',
            'konten' => 'required|array',
            'konten.*.label' => 'required|string|max:255',
            'konten.*.type' => 'required|string|in:text,date,number,email,textarea,checkbox,radio,select',
            'konten.*.value' => 'nullable|string|max:255',
            'teks_bawah' => 'nullable|string|max:255',
            'penutup' => 'nullable|string|max:255',
        ]);

        $surat->update([
            'judul' => $request->judul,
            'lampiran' => $request->lampiran,
            'perihal' => $request->perihal,
            'kepada_yth' => $request->kepada_yth,
            'pembuka' => $request->pembuka,
            'teks_atas' => $request->teks_atas,
            'konten' => json_encode($request->konten),
            'teks_bawah' => $request->teks_bawah,
            'penutup' => $request->penutup,
        ]);

        return redirect()->route('surats.index')->with('success', 'Template surat berhasil diperbarui.');
    }


    public function destroy(TemplateSurat $surat)
    {
        $surat->delete();
        return redirect()->route('surats.index')->with('success', 'Template surat berhasil dihapus.');
    }
    public function getTemplateFields($id)
    {
        $template = TemplateSurat::find($id);

        if (!$template) {
            return response()->json(['error' => 'Template tidak ditemukan'], 404);
        }

        return response()->json(json_decode($template->konten, true));
    }
    

}
