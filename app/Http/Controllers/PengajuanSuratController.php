<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PengajuanSurat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Storage;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Mpdf\Mpdf;
use Intervention\Image\Facades\Image;
use Intervention\Image\ImageManager;


class PengajuanSuratController extends Controller
{
    use HasFactory;

    public function index(Request $request)
    {
        $query = PengajuanSurat::with('template')
            ->where('user_id', Auth::id());

        if ($request->filled('judul')) {
            $query->whereHas('template', function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->judul . '%');
            });
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $pengajuanSurats = $query->latest()->get();

        return view('pengajuan_surat.index', compact('pengajuanSurats'));
    }

    public function getPlaceholders($id)
    {
        $template = TemplateSurat::findOrFail($id);

        $raw = $template->required_placeholders;

        if (!$raw) {
            return response()->json([]);
        }

        $placeholders = json_decode($raw, true);

        if (!is_array($placeholders) || $placeholders === null) {
            return response()->json([], 400);
        }

        return response()->json($placeholders);
    }



    public function approvalIndex()
    {
        $pengajuanSurats = PengajuanSurat::where('status', 'proses')
            ->with(['user', 'template']) // Eager load the relationships
            ->get();

        return view('approval.index', compact('pengajuanSurats'));
    }


    public function create()
    {
        $user = Auth::user();
        $role = $user->roles->pluck('name')->first();
        logger("Role user yang login: " . $role);

        $templates = TemplateSurat::where('user_type', $role)->get();

        logger("Template surat untuk role {$role}: " . $templates->count());

        return view('pengajuan_surat.create', compact('templates'));
    }


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



    public function store(Request $request)
    {
        Log::info('Masuk ke method store.');

        $request->validate([
            'template_id' => 'required|exists:template_surats,id',
            'konten' => 'required|array',
        ]);

        Log::info('Validasi berhasil.', $request->all());

        $template = TemplateSurat::findOrFail($request->template_id);
        $placeholders = json_decode($template->required_placeholders, true);

        Log::info('Template ditemukan:', ['judul' => $template->judul]);

        if (!is_array($placeholders)) {
            Log::error('Placeholder tidak valid.', ['raw' => $template->required_placeholders]);
            return back()->with('error', 'Format placeholders pada template tidak valid.');
        }

        foreach ($placeholders as $key => $config) {
            if (!isset($request->konten[$key]) || empty($request->konten[$key])) {
                Log::warning("Konten '{$key}' kosong.");
                return back()->withInput()->withErrors([
                    "konten.{$key}" => "Field '{$config['label']}' wajib diisi.",
                ]);
            }
        }

        Log::info('Semua konten placeholder valid.');

        $pengajuan = PengajuanSurat::create([
            'user_id' => Auth::id(),
            'template_id' => $request->template_id,
            'konten' => json_encode($request->konten),
            'status' => 'proses',
        ]);

        Log::info('Pengajuan surat berhasil dibuat.', ['id' => $pengajuan->id]);

        $kaprodis = User::role('kepalaprodi')->get();
        $user = Auth::user();

        foreach ($kaprodis as $kaprodi) {
            $phone = $kaprodi->whatsapp_number;

            if (!$phone) {
                Log::warning("User {$kaprodi->id} tidak punya nomor WhatsApp.");
                continue;
            }

            $message = "Halo {$kaprodi->name}, ada pengajuan surat baru dari {$user->name}.\n" .
                "Nama Surat: {$template->nama_surat}\n" .
                "Silakan cek aplikasi untuk melakukan approval.";

            Log::info("Mengirim notifikasi ke {$phone}.");
            $this->sendWablasNotification($phone, $message);
        }

        return redirect()->route('pengajuan_surat.index')->with('success', 'Pengajuan surat berhasil diajukan dan notifikasi terkirim.');
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

    private function getKaprodiData()
    {
        $kaprodi = User::role('kepalaprodi')->first();

        return [
            'nama_kaprodi' => $kaprodi->name ?? '-',
            'nipy_kaprodi' => $kaprodi->nip ?? '-',
            'tanggalsekarang' => now()->format('d F Y'),
        ];
    }



    public function download($id)
    {
        $pengajuan = PengajuanSurat::with('template')->findOrFail($id);
        $template = $pengajuan->template;
        $filePath = storage_path('app/public/' . $template->file_path);

        if (!file_exists($filePath)) {
            return back()->with('error', 'File template tidak ditemukan.');
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($filePath);

        $konten = json_decode($pengajuan->konten, true) ?? [];
        $systemData = $this->getKaprodiData();
        $systemData['nomor_surat'] = $this->getNomorSurat($pengajuan);

        $allData = array_merge($konten, $systemData);

        foreach ($allData as $key => $value) {
            if (in_array($key, ['ttd_kaprodi', 'stempel', 'ttdstempelbasah'])) continue;

            if (!is_string($value) && !is_numeric($value)) {
                $value = json_encode($value);
            }

            $templateProcessor->setValue($key, $value ?? '');
        }

        $filename = \Illuminate\Support\Str::slug($template->nama_surat) . '-' . time();
        $outputDir = storage_path('app/public/generated');

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $signaturePath = storage_path('app/public/' . $pengajuan->signature);
        $stempelPath = storage_path('app/public/stempels/stempel_kaprodi.png');

        if (file_exists($signaturePath) && file_exists($stempelPath)) {
            $stempel   = imagecreatefrompng($stempelPath);
            $signature = imagecreatefrompng($signaturePath);

            imagesavealpha($stempel, true);
            imagealphablending($stempel, true);

            imagesavealpha($signature, true);
            imagealphablending($signature, true);

            $width = 200;
            $height = 100;
            $combined = imagecreatetruecolor($width, $height);
            imagesavealpha($combined, true);
            $transparent = imagecolorallocatealpha($combined, 0, 0, 0, 127);
            imagefill($combined, 0, 0, $transparent);

            imagecopy($combined, $signature, 0, 0, 0, 0, imagesx($signature), imagesy($signature));

            imagecopy($combined, $stempel, 20, 20, 0, 0, imagesx($stempel), imagesy($stempel));

            $combinedPath = $outputDir . '/' . $filename . '-combined.png';
            imagepng($combined, $combinedPath);

            imagedestroy($combined);
            imagedestroy($stempel);
            imagedestroy($signature);

            $templateProcessor->setImageValue('ttdstempelbasah', [
                'path' => $combinedPath,
                'width' => 150,
                'height' => 80,
            ]);
        } else {
            $templateProcessor->setValue('ttdstempelbasah', '');
        }


        $docxPath = $outputDir . '/' . $filename . '.docx';
        $pdfPath  = $outputDir . '/' . $filename . '.pdf';

        $templateProcessor->saveAs($docxPath);

        // Path LibreOffice di Windows
        $libreOfficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
        $command = $libreOfficePath . ' --headless --convert-to pdf --outdir "' . $outputDir . '" "' . $docxPath . '"';
        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($pdfPath)) {
            return back()->with('error', 'Gagal mengonversi file ke PDF.');
        }

        unlink($docxPath); // Hapus file .docx jika tidak dibutuhkan

        return response()->download($pdfPath)->deleteFileAfterSend(true);
    }



    public function edit($id)
    {
        $pengajuan = PengajuanSurat::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $templates = TemplateSurat::all();

        $konten = json_decode($pengajuan->konten, true) ?? [];
        $template = $pengajuan->template;

        return view('pengajuan_surat.edit', compact('pengajuan', 'templates', 'konten', 'template'));
    }


    public function update(Request $request, $id)
    {
        $pengajuan = PengajuanSurat::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();

        $request->validate([
            'template_id' => 'required|exists:template_surats,id',
            'konten' => 'required|array',
        ]);

        $template = TemplateSurat::findOrFail($request->template_id);
        $placeholders = json_decode($template->required_placeholders, true);

        foreach ($placeholders as $key => $config) {
            if (!isset($request->konten[$key]) || empty($request->konten[$key])) {
                return back()->withInput()->withErrors([
                    "konten.{$key}" => "Field '{$config['label']}' wajib diisi.",
                ]);
            }
        }

        $pengajuan->update([
            'template_id' => $request->template_id,
            'konten' => json_encode($request->konten),
            'status' => 'proses', // bisa disesuaikan logikanya
        ]);

        return redirect()->route('pengajuan_surat.index')->with('success', 'Pengajuan surat berhasil diperbarui.');
    }
}
