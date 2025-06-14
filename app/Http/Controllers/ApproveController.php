<?php

namespace App\Http\Controllers;

use App\Models\PengajuanSurat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use App\Http\Controllers\SignatureController;
use Illuminate\Support\Facades\Storage;

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

    private function getNomorSurat(PengajuanSurat $pengajuan)
    {
        $template = $pengajuan->template;
        $kodeJenis = str_pad($template->no_jenis_surat, 2, '0', STR_PAD_LEFT);

        // Hitung jumlah pengajuan disetujui berdasarkan kode jenis surat (bukan template_id)
        $jumlahSurat = PengajuanSurat::whereHas('template', function ($query) use ($template) {
            $query->where('no_jenis_surat', $template->no_jenis_surat);
        })
            ->where('status', 'disetujui')
            ->count() + 1;

        $nomorUrut = str_pad($jumlahSurat, 2, '0', STR_PAD_LEFT);

        $bagianTetap = 'TI.PHB';

        $bulanRomawi = [
            1 => 'I',
            2 => 'II',
            3 => 'III',
            4 => 'IV',
            5 => 'V',
            6 => 'VI',
            7 => 'VII',
            8 => 'VIII',
            9 => 'IX',
            10 => 'X',
            11 => 'XI',
            12 => 'XII'
        ];

        $bulan = $bulanRomawi[date('n')];
        $tahun = date('Y');

        return "{$nomorUrut}.{$kodeJenis}/{$bagianTetap}/{$bulan}/{$tahun}";
    }

    public function approve(Request $request, PengajuanSurat $pengajuanSurat)
    {
        try {
            $ttdType = $request->input('ttd_type'); // 'basah' atau 'digital'

            // Cek dan generate nomor_surat jika belum ada
            if (!$pengajuanSurat->nomor_surat) {
                $pengajuanSurat->nomor_surat = $this->getNomorSurat($pengajuanSurat);
            }

            if ($ttdType === 'digital') {
                $signaturePath = SignatureController::getSignaturePath();

                if (!Storage::disk('public')->exists($signaturePath)) {
                    return response()->json(['message' => 'Tanda tangan kaprodi belum disimpan. Silakan buat terlebih dahulu.'], 400);
                }

                $pengajuanSurat->ttd_type = 'digital';
                $pengajuanSurat->signature = $signaturePath;
            } else {
                $pengajuanSurat->ttd_type = 'basah';
                $pengajuanSurat->signature = null;
            }

            $pengajuanSurat->status = 'diterima';
            $pengajuanSurat->save();

            // Kirim WA notifikasi
            $user = $pengajuanSurat->user;
            if ($user && $user->whatsapp_number) {
                $message = "Halo {$user->name}, pengajuan surat Anda dengan judul surat {$pengajuanSurat->template->nama_surat} telah *disetujui* dengan tipe tanda tangan: *{$ttdType}.* Silakan cek aplikasi untuk mengunduh.";
                $this->sendWablasNotification($user->whatsapp_number, $message);
            }

            return response()->json(['message' => 'Surat telah disetujui.']);
        } catch (\Exception $e) {
            return response()->json(['message' => 'Terjadi kesalahan: ' . $e->getMessage()], 500);
        }
    }

    public function reject(Request $request, PengajuanSurat $pengajuanSurat)
    {
        $catatan = $request->input('catatan');

        $pengajuanSurat->update([
            'status' => 'ditolak',
            'catatan_penolakan' => $catatan,
        ]);

        $user = $pengajuanSurat->user;
        if ($user && $user->whatsapp_number) {
            $message = "Halo {$user->name}, pengajuan surat Anda dengan judul surat {$pengajuanSurat->template->nama_surat} telah *ditolak*. Alasan: {$catatan}";
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
