<?php

// app/Http/Controllers/TemplateSuratController.php

namespace App\Http\Controllers;

use App\Models\TemplateSurat;
use Illuminate\Http\Request;

class TemplateSuratController extends Controller
{
    public function index()
    {
        // Fetch all template surat records
        $surats = TemplateSurat::all();
        return view('surats.index', compact('surats'));
    }

    public function create()
    {
        // Return the view for creating a new template surat
        return view('surats.create');
    }

    public function store(Request $request)
    {
        // Validate the incoming request data
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|array',
            'konten.*' => 'required|string',
        ]);

        // Store the data
        TemplateSurat::create([
            'judul' => $request->judul,
            'konten' => $request->konten,
        ]);

        return redirect()->route('surats.index')->with('success', 'Surat berhasil dibuat');
    }

    public function edit($id)
    {
        // Find the template surat by ID
        $surat = TemplateSurat::findOrFail($id);

        // Return the view for editing the template surat
        return view('surats.edit', compact('surat'));
    }

    public function update(Request $request, $id)
    {
        // Validate the incoming request data
        $request->validate([
            'judul' => 'required|string|max:255',
            'konten' => 'required|array',
            'konten.*' => 'required|string',
        ]);

        // Find the template surat and update the data
        $surat = TemplateSurat::findOrFail($id);
        $surat->update([
            'judul' => $request->judul,
            'konten' => $request->konten,
        ]);

        return redirect()->route('surats.index')->with('success', 'Surat berhasil diperbarui');
    }

    public function destroy($id)
    {
        // Find the template surat and delete it
        $surat = TemplateSurat::findOrFail($id);
        $surat->delete();

        return redirect()->route('surats.index')->with('success', 'Surat berhasil dihapus');
    }
}
