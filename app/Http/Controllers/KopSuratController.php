<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\KopSurat;

class KopSuratController extends Controller
{
    public function index()
    {
        $kop = KopSurat::first();
        return view('kop.index', compact('kop'));
    }

    public function edit()
    {
        $kop = KopSurat::first();
        return view('kop.edit', compact('kop'));
    }

    public function update(Request $request)
    {
        $request->validate([
            'gambar' => 'nullable|image|mimes:png,jpg,jpeg|max:2048',
        ]);

        $kop = KopSurat::firstOrCreate([]);

        if ($request->hasFile('gambar')) {
            $pathGambar = $request->file('gambar')->store('kop', 'public');
            $kop->gambar = $pathGambar;
        }

        $kop->save();

        return redirect()->route('kop.index')->with('success', 'Kop surat berhasil diperbarui.');
    }
}
