<?php

namespace App\Http\Controllers;

use App\Models\TemplateSurat;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function index()
    {
        $templates = TemplateSurat::all();
        return view('template_surat.index', compact('templates'));
    }

    public function create()
    {
        return view('template_surat.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required',
        ]);

        TemplateSurat::create($request->all());
        return redirect()->route('template_surat.index')->with('success', 'Template surat berhasil dibuat.');
    }

    public function edit(TemplateSurat $templateSurat)
    {
        return view('template_surat.edit', compact('templateSurat'));
    }

    public function update(Request $request, TemplateSurat $templateSurat)
    {
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required',
        ]);

        $templateSurat->update($request->all());
        return redirect()->route('template_surat.index')->with('success', 'Template surat berhasil diperbarui.');
    }

    public function destroy(TemplateSurat $templateSurat)
    {
        $templateSurat->delete();
        return redirect()->route('template_surat.index')->with('success', 'Template surat berhasil dihapus.');
    }
}
