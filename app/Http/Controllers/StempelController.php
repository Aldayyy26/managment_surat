<?php

namespace App\Http\Controllers;

use App\Models\Stempel;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class StempelController extends Controller
{
    public function index()
    {
        $stempels = Stempel::all();
        return view('stempel.index', compact('stempels'));
    }

    public function create()
    {
        return view('stempel.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'required|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        $path = $request->file('gambar')->store('stempels', 'public');

        Stempel::create([
            'nama' => $request->nama,
            'gambar' => $path,
        ]);

        return redirect()->route('stempels.index')->with('success', 'Stempel berhasil ditambahkan.');
    }

    public function edit(Stempel $stempel)
    {
        return view('stempel.edit', compact('stempel'));
    }

    public function update(Request $request, Stempel $stempel)
    {
        $request->validate([
            'nama' => 'required|string|max:255',
            'gambar' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        if ($request->hasFile('gambar')) {
            // Hapus gambar lama
            Storage::disk('public')->delete($stempel->gambar);

            // Upload gambar baru
            $path = $request->file('gambar')->store('stempels', 'public');
            $stempel->gambar = $path;
        }

        $stempel->nama = $request->nama;
        $stempel->save();

        return redirect()->route('stempels.index')->with('success', 'Stempel berhasil diperbarui.');
    }

    public function destroy(Stempel $stempel)
    {
        // Hapus gambar dari storage
        Storage::disk('public')->delete($stempel->gambar);

        // Hapus dari database
        $stempel->delete();

        return redirect()->route('stempels.index')->with('success', 'Stempel berhasil dihapus.');
    }
}
