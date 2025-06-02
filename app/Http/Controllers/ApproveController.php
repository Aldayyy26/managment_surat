<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class ApproveController extends Controller
{
    private function sendWablasNotification($phoneNumber, $message)
    {
        $url = "https://gateway.poltektegal.ac.id/api/whatsapp/sendSingleMessage";

        $username = "admin";
        $password = "e-geteway123456!";

        $data = [
            "key-gateway" => "UqyRJ7pujEyHK4PDWKZXNpMv3qKW9C",
            "phone" => $phoneNumber,
            "message" => $message,
            "token_app" => "VIgCefPPbm",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPAUTH, CURLAUTH_BASIC);
        curl_setopt($ch, CURLOPT_USERPWD, "$username:$password");
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        curl_setopt($ch, CURLOPT_HTTPHEADER, [
            'Content-Type: application/json',
        ]);

        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            Log::error("WhatsApp Gateway API Error: " . $err);
            return false;
        } else {
            Log::info("WhatsApp Gateway API Response: " . $response);
        }

        return $response;
    }

    public function index()
    {
        $pengajuanSurats = PengajuanSurat::with('template', 'user')->where('status', 'proses')->get();
        return view('approve.index', compact('pengajuanSurats'));
    }

    public function approve(Request $request, PengajuanSurat $pengajuanSurat)
    {
        $request->validate([
            'signature' => 'required|string',
        ]);

        try {
            $signatureData = $request->signature;
            list($type, $data) = explode(';', $signatureData);
            list(, $data) = explode(',', $data);

            $imageData = base64_decode($data);

            $filename = 'signature_' . time() . '.png';
            $path = storage_path('app/public/signatures/' . $filename);
            file_put_contents($path, $imageData);

            $pengajuanSurat->update([
                'status' => 'diterima',
                'signature' => 'signatures/' . $filename,
            ]);

            // Kirim notifikasi WA ke user pengaju
            $user = $pengajuanSurat->user;
            if ($user && $user->whatsapp_number) {
                $message = "Halo {$user->name}, pengajuan surat Anda dengan judul surat {$pengajuanSurat->judul} telah *disetujui* silahkan cek ke aplikasi untuk mengunduh.";
                $this->sendWablasNotification($user->whatsapp_number, $message);
            }

            return response()->json(['message' => 'Surat telah disetujui.']);

        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function reject(PengajuanSurat $pengajuanSurat)
    {
        $pengajuanSurat->update([
            'status' => 'ditolak',
        ]);

        // Kirim notifikasi WA ke user pengaju
        $user = $pengajuanSurat->user;
        if ($user && $user->whatsapp_number) {
            $message = "Halo {$user->name},pengajuan surat Anda dengan judul surat{$pengajuanSurat->surat} telah *ditolak* oleh kaprodi. silahkan ajukan ulang dengan data yang benar.";
            $this->sendWablasNotification($user->whatsapp_number, $message);
        }

        return response()->json(['message' => 'Surat telah ditolak.']);
    }

    public function detail($id)
    {
        $surat = TemplateSurat::with('template', 'user')->findOrFail($id);
        return response()->json(['surat' => $surat]);
    }
}
