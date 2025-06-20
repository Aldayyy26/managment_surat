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
use setasign\Fpdi\Fpdi;


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

        Log::info('Validasi awal berhasil.', $request->all());

        $template = TemplateSurat::findOrFail($request->template_id);
        $placeholders = json_decode($template->required_placeholders, true);

        Log::info('Template ditemukan:', ['judul' => $template->judul]);

        if (!is_array($placeholders)) {
            Log::error('Placeholder tidak valid.', ['raw' => $template->required_placeholders]);
            return back()->with('error', 'Format placeholders pada template tidak valid.');
        }

        // Validasi dinamis berdasarkan config
        $dynamicRules = [];

        foreach ($placeholders as $key => $config) {
            $rules = [];

            // Tangani nullable
            if (!empty($config['nullable'])) {
                $rules[] = 'nullable';
            } else {
                $rules[] = 'required';
            }

            // Tambah validasi tipe data
            switch ($config['type']) {
                case 'number':
                    $rules[] = 'numeric';
                    break;
                case 'date':
                    $rules[] = 'date';
                    break;
                case 'text':
                case 'textarea':
                default:
                    $rules[] = 'string';
                    break;
            }

            $dynamicRules["konten.$key"] = $rules;
        }

        // Jalankan validasi untuk konten
        $request->validate($dynamicRules);

        Log::info('Validasi semua konten placeholder berhasil.');

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


    private function getKaprodiData()
    {
        $kaprodi = User::role('kepalaprodi')->first();

        return [
            'nama_kaprodi' => $kaprodi->name ?? '-',
            'nipy_kaprodi' => $kaprodi->nip ?? '-',
            'tanggalsekarang' => \Carbon\Carbon::now()->translatedFormat('d F Y'),
        ];
    }

    private function getNomorSurat(PengajuanSurat $pengajuan)
    {
        $template = $pengajuan->template;
        $kodeJenis = str_pad($template->no_jenis_surat, 2, '0', STR_PAD_LEFT);

        $jumlahSurat = PengajuanSurat::whereHas('template', function ($query) use ($template) {
            $query->where('no_jenis_surat', $template->no_jenis_surat);
        })
            ->where('status', 'diterima')
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
        $systemData['nomor_surat'] = $pengajuan->nomor_surat ?? $this->getNomorSurat($pengajuan);

        $allData = array_merge($konten, $systemData);

        foreach ($allData as $key => $value) {
            if (in_array($key, ['ttd_kaprodi', 'stempel', 'ttdstempelbasah'])) continue;

            if (is_null($value) || (is_array($value) && empty($value))) {
                $value = '';
            } elseif (!is_string($value) && !is_numeric($value)) {
                $value = json_encode($value);
            }

            $templateProcessor->setValue($key, $value);
        }

        // Kosongkan placeholder gambar
        $templateProcessor->setValue('ttdstempelbasah', '');

        $filename = \Illuminate\Support\Str::slug($template->nama_surat) . '-' . time();
        $outputDir = storage_path('app/public/generated');

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        // Gabungkan tanda tangan dan stempel ke satu gambar
        $signaturePath = storage_path('app/public/' . $pengajuan->signature);
        $stempelPath = storage_path('app/public/stempels/stempel_kaprodi.png');
        $combinedPath = null;

        if (!empty($pengajuan->signature) && file_exists($signaturePath) && file_exists($stempelPath)) {
            $stempel = imagecreatefrompng($stempelPath);
            $signature = imagecreatefrompng($signaturePath);

            imagesavealpha($stempel, true);
            imagealphablending($stempel, true);
            imagesavealpha($signature, true);
            imagealphablending($signature, true);

            // Ukuran canvas berdasarkan tanda tangan dominan
            $canvasWidth = 280;
            $canvasHeight = 130;

            $combined = imagecreatetruecolor($canvasWidth, $canvasHeight);
            imagesavealpha($combined, true);
            $transparent = imagecolorallocatealpha($combined, 0, 0, 0, 127);
            imagefill($combined, 0, 0, $transparent);

            // Resize tanda tangan (besar dan dominan)
            $signatureResized = imagecreatetruecolor($canvasWidth, $canvasHeight);
            imagesavealpha($signatureResized, true);
            imagefill($signatureResized, 0, 0, $transparent);
            imagecopyresampled(
                $signatureResized,
                $signature,
                0,
                0,
                0,
                0,
                $canvasWidth,
                $canvasHeight,
                imagesx($signature),
                imagesy($signature)
            );

            // Resize stempel (lebih kecil dari tanda tangan)
            $stempelWidth = 130;
            $stempelHeight = 70;
            $stempelResized = imagecreatetruecolor($stempelWidth, $stempelHeight);
            imagesavealpha($stempelResized, true);
            imagefill($stempelResized, 0, 0, $transparent);
            imagecopyresampled(
                $stempelResized,
                $stempel,
                0,
                0,
                0,
                0,
                $stempelWidth,
                $stempelHeight,
                imagesx($stempel),
                imagesy($stempel)
            );

            // Gabungkan: tanda tangan dulu
            imagecopy($combined, $signatureResized, 0, 0, 0, 0, $canvasWidth, $canvasHeight);

            // Lalu stempel di tengah bawah
            $stempelX = intval(($canvasWidth - $stempelWidth) / 2) - 20;
            $stempelY = intval(($canvasHeight - $stempelHeight) / 2 - 10);
            imagecopy($combined, $stempelResized, $stempelX, $stempelY, 0, 0, $stempelWidth, $stempelHeight);

            $combinedPath = $outputDir . '/' . $filename . '-combined.png';
            imagepng($combined, $combinedPath);

            // Cleanup
            imagedestroy($combined);
            imagedestroy($stempel);
            imagedestroy($signature);
            imagedestroy($stempelResized);
            imagedestroy($signatureResized);
        }

        $docxPath = $outputDir . '/' . $filename . '.docx';
        $pdfPath  = $outputDir . '/' . $filename . '.pdf';

        $templateProcessor->saveAs($docxPath);

        // Konversi ke PDF
        $os = strtoupper(substr(PHP_OS, 0, 3));
        $libreOfficePath = ($os === 'WIN')
            ? '"C:\Program Files\LibreOffice\program\soffice.exe"'
            : 'soffice';

        $command = $libreOfficePath . ' --headless --convert-to pdf --outdir "' . $outputDir . '" "' . $docxPath . '"';
        exec($command, $output, $resultCode);

        if ($resultCode !== 0 || !file_exists($pdfPath)) {
            return back()->with('error', 'Gagal mengonversi file ke PDF.');
        }

        unlink($docxPath);

        // Tempel gambar final di atas PDF
        if ($combinedPath && file_exists($combinedPath)) {
            $pdf = new Fpdi();
            $pageCount = $pdf->setSourceFile($pdfPath);
            $templateId = $pdf->importPage(1);
            $pdf->AddPage();
            $pdf->useTemplate($templateId);

            // Tempelkan tepat di placeholder (${ttdstempelbasah})
            $pdf->Image($combinedPath, 125, 200, 70, 35); // X, Y, width, height (mm)

            $pdf->Output('F', $pdfPath);
        }

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

    public function preview($id)
    {
        $pengajuan = PengajuanSurat::with('template')->findOrFail($id);
        $template = $pengajuan->template;
        $filePath = storage_path('app/public/' . $template->file_path);

        if (!file_exists($filePath)) {
            return response()->json(['error' => 'File template tidak ditemukan.'], 404);
        }

        $templateProcessor = new \PhpOffice\PhpWord\TemplateProcessor($filePath);

        $konten = json_decode($pengajuan->konten, true) ?? [];
        $systemData = $this->getKaprodiData();
        $systemData['nomor_surat'] = $pengajuan->nomor_surat ?? $this->getNomorSurat($pengajuan);
        $allData = array_merge($konten, $systemData);

        foreach ($allData as $key => $value) {
            if (in_array($key, ['ttd_kaprodi', 'stempel', 'ttdstempelbasah'])) continue;

            if ($value === null || (is_string($value) && trim($value) === '')) {
                // Kosong: hapus placeholder dengan mengisinya string kosong
                $templateProcessor->setValue($key, '');
                continue;
            }

            if (!is_string($value) && !is_numeric($value)) {
                $value = is_array($value) ? implode(', ', $value) : '';
            }

            $templateProcessor->setValue($key, $value);
        }



        $outputDir = storage_path('app/public/generated');
        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $filename = Str::slug($template->nama_surat) . '-preview-' . $pengajuan->id;
        $docxPath = $outputDir . '/' . $filename . '.docx';
        $pdfPath = $outputDir . '/' . $filename . '.pdf';

        $templateProcessor->saveAs($docxPath);

        // Deteksi OS dan atur path soffice
        $os = strtoupper(substr(PHP_OS, 0, 3));
        if ($os === 'WIN') {
            $libreOfficePath = '"C:\Program Files\LibreOffice\program\soffice.exe"';
        } else {
            $libreOfficePath = 'soffice'; // Linux: pastikan libreoffice diinstall dan ada di PATH
        }

        $command = $libreOfficePath . ' --headless --convert-to pdf --outdir "' . $outputDir . '" "' . $docxPath . '"';
        exec($command, $output, $resultCode);

        if (!file_exists($pdfPath)) {
            return response()->json(['error' => 'Gagal mengonversi file ke PDF.'], 500);
        }

        // Optional: hapus file docx hasil sementara
        unlink($docxPath);

        // Kembalikan URL ke PDF
        return response()->json([
            'url' => asset('storage/generated/' . $filename . '.pdf')
        ]);
    }
}
