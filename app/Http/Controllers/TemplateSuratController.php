<?php

namespace App\Http\Controllers;

use App\Models\TemplateSurat;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function index()
    {
        $surats = TemplateSurat::all();
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
            'konten' => 'required|array',
            'konten.*.label' => 'required|string|max:255',
            'konten.*.type' => 'required|string|in:text,date,number,email,textarea,checkbox,radio,select',
            'konten.*.value' => 'nullable|string|max:255',
        ]);

        TemplateSurat::create([
            'judul' => $request->judul,
            'konten' => json_encode($request->konten), 
        ]);

        return redirect()->route('surats.index')->with('success', 'Template surat berhasil dibuat.');
    }

    public function edit(TemplateSurat $surat)
    {
        return view('surats.edit', [
            'surat' => $surat,
            'konten' => json_decode($surat->konten, true) ?? [],
        ]);
    }

    public function update(Request $request, TemplateSurat $surat)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|array',
            'konten.*.label' => 'required|string|max:255',
            'konten.*.type' => 'required|string|in:text,date,number,email,textarea,checkbox,radio,select',
            'konten.*.value' => 'nullable|string|max:255',
        ]);

        $surat->update([
            'judul' => $request->judul,
            'konten' => json_encode($request->konten), 
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
