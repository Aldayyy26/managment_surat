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

        // Hapus file gambar lama
        Storage::disk('public')->delete('stempels/stempel_kaprodi.png');

        $file = $request->file('gambar');
        $filename = 'stempel_kaprodi.png';
        $tmpPath = $file->getPathname();
        $storagePath = storage_path('app/public/stempels/' . $filename);

        $this->removeWhiteBackgroundAndSaveAsPng($tmpPath, $storagePath);

        // Opsional: hanya simpan satu data stempel di database
        Stempel::truncate();

        Stempel::create([
            'nama' => $request->nama,
            'gambar' => 'stempels/' . $filename,
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

        $stempel->nama = $request->nama;

        if ($request->hasFile('gambar')) {
            // Hapus file lama
            Storage::disk('public')->delete('stempels/stempel_kaprodi.png');

            $file = $request->file('gambar');
            $filename = 'stempel_kaprodi.png';
            $tmpPath = $file->getPathname();
            $storagePath = storage_path('app/public/stempels/' . $filename);

            $this->removeWhiteBackgroundAndSaveAsPng($tmpPath, $storagePath);

            $stempel->gambar = 'stempels/' . $filename;
        }

        $stempel->save();

        return redirect()->route('stempels.index')->with('success', 'Stempel berhasil diperbarui.');
    }

    public function destroy(Stempel $stempel)
    {
        Storage::disk('public')->delete('stempels/stempel_kaprodi.png');
        $stempel->delete();

        return redirect()->route('stempels.index')->with('success', 'Stempel berhasil dihapus.');
    }

    private function removeWhiteBackgroundAndSaveAsPng($inputPath, $outputPath)
    {
        $info = getimagesize($inputPath);
        $mime = $info['mime'];

        if ($mime === 'image/png') {
            $img = imagecreatefrompng($inputPath);
        } elseif ($mime === 'image/jpeg') {
            $img = imagecreatefromjpeg($inputPath);
        } else {
            copy($inputPath, $outputPath);
            return;
        }

        $width = imagesx($img);
        $height = imagesy($img);

        $newImg = imagecreatetruecolor($width, $height);
        imagesavealpha($newImg, true);
        imagealphablending($newImg, false);

        $transparent = imagecolorallocatealpha($newImg, 0, 0, 0, 127);
        imagefill($newImg, 0, 0, $transparent);

        for ($x = 0; $x < $width; $x++) {
            for ($y = 0; $y < $height; $y++) {
                $rgba = imagecolorat($img, $x, $y);
                $colors = imagecolorsforindex($img, $rgba);

                if ($colors['red'] > 240 && $colors['green'] > 240 && $colors['blue'] > 240) {
                    imagesetpixel($newImg, $x, $y, $transparent);
                } else {
                    $color = imagecolorallocatealpha($newImg, $colors['red'], $colors['green'], $colors['blue'], 0);
                    imagesetpixel($newImg, $x, $y, $color);
                }
            }
        }

        imagepng($newImg, $outputPath);

        imagedestroy($img);
        imagedestroy($newImg);
    }

    // Static path helper jika dibutuhkan
    public static function getStempelPath()
    {
        return 'stempels/stempel_kaprodi.png';
    }
}
