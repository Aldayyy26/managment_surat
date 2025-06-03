<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class SignatureController extends Controller
{

    public function index()
    {
        $signaturePath = self::getSignaturePath();
        $signatureUrl = Storage::disk('public')->exists($signaturePath)
            ? asset('storage/' . $signaturePath)
            : null;

        return view('signature.index', compact('signatureUrl'));
    }

    public function destroy()
    {
        $signaturePath = self::getSignaturePath();

        if (Storage::disk('public')->exists($signaturePath)) {
            Storage::disk('public')->delete($signaturePath);
            return response()->json(['message' => 'Tanda tangan berhasil dihapus.']);
        }

        return response()->json(['message' => 'Tanda tangan tidak ditemukan.'], 404);
    }

    public function create()
    {
        return view('signature.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        $signatureData = $request->signature;
        list($type, $data) = explode(';', $signatureData);
        list(, $data) = explode(',', $data);
        $imageData = base64_decode($data);

        $filename = 'signature_kaprodi.png';
        Storage::disk('public')->put("signatures/{$filename}", $imageData);

        return response()->json(['message' => 'Tanda tangan berhasil disimpan.']);
    }

    public static function getSignaturePath()
    {
        return 'signatures/signature_kaprodi.png';
    }
}

