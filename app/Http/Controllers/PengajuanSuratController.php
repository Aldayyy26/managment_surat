<?php

namespace App\Http\Controllers;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use App\Models\PengajuanSurat;
use App\Models\TemplateSurat;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Log;
use Spatie\Permission\Models\Role;
use PhpOffice\PhpWord\TemplateProcessor;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Response;
use Mpdf\Mpdf;

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
        // Fetch pengajuanSurat with the associated user and template
        $pengajuanSurats = PengajuanSurat::where('status', 'proses')
            ->with(['user', 'template']) // Eager load the relationships
            ->get();

        return view('approval.index', compact('pengajuanSurats'));
    }


   public function create()
    {
        $templates = TemplateSurat::all();

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

        $pengajuan = PengajuanSurat::create([
            'user_id' => Auth::id(),
            'template_id' => $request->template_id,
            'konten' => json_encode($request->konten),
            'status' => 'proses',
        ]);

        $kaprodis = User::role('kepalaprodi')->get();
        $user = Auth::user();

        foreach ($kaprodis as $kaprodi) {
            $phone = $kaprodi->whatsapp_number;

            if (!$phone) {
                Log::warning("User {$kaprodi->id} role kepalaprodi tidak punya nomor whatsapp");
                continue;
            }

            $message = "Halo {$kaprodi->name}, ada pengajuan surat baru dari {$user->name}.\n" .
                    "Jenis Surat: {$template->judul}\n" .
                    "Silakan cek aplikasi untuk melakukan approval.";

            $this->sendWablasNotification($phone, $message);
        }

        return redirect()->route('pengajuan_surat.index')->with('success', 'Pengajuan surat berhasil diajukan dan notifikasi terkirim.');
    }

    private function getKaprodiData()
    {
        $kaprodi = User::role('kepalaprodi')->first();

        return [
            'nama_kaprodi' => $kaprodi->name ?? '-',
            'nipy_kaprodi' => $kaprodi->nipy ?? '-',
            'ttd_kaprodi' => asset('storage/signatures/signature_kaprodi.png'),
            'stempel' => asset('storage/stempels/stempel_kaprodi.png'),
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

        $templateProcessor = new TemplateProcessor($filePath);

        $konten = json_decode($pengajuan->konten, true) ?? [];
        $placeholders = json_decode($template->required_placeholders, true) ?? [];

        // Data sistem (kaprodi, tanggal, dll)
        $systemData = $this->getKaprodiData();

        // Gabungkan konten user dan data sistem
        $allData = array_merge($konten, $systemData);

        // Replace semua placeholder text dulu
        foreach ($allData as $key => $value) {
            // skip gambar di sini
            if (in_array($key, ['ttd_kaprodi', 'stempel'])) continue;

            if (!is_string($value) && !is_numeric($value)) {
                $value = json_encode($value);
            }
            $templateProcessor->setValue($key, $value);
        }

        // Set gambar dengan setImageValue
        $templateProcessor->setImageValue('ttd_kaprodi', [
            'path' => storage_path('app/public/signatures/signature_kaprodi.png'),
            'width' => 150,
            'height' => 80,
        ]);
        $templateProcessor->setImageValue('stempel', [
            'path' => storage_path('app/public/stempels/stempel_kaprodi.png'),
            'width' => 150,
            'height' => 80,
        ]);

        // Save dan download
        $filename = Str::slug($template->nama_surat) . '-' . time() . '.docx';
        $outputDir = storage_path('app/public/generated');

        if (!file_exists($outputDir)) {
            mkdir($outputDir, 0755, true);
        }

        $outputPath = $outputDir . '/' . $filename;
        $templateProcessor->saveAs($outputPath);

        return response()->download($outputPath)->deleteFileAfterSend(true);
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
