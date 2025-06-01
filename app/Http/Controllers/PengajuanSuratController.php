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
use PhpOffice\PhpWord\TemplateProcessor;
use Spatie\Permission\Models\Role;



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
            \Log::error("WhatsApp Gateway API Error: " . $err);
            return false;
        } else {
            \Log::info("WhatsApp Gateway API Response: " . $response);
        }

        return $response;
    }

    public function store(Request $request)
    {
        // ambil template surat
        $template = new TemplateProcessor(storage_path('app/public/surat_templates/2nTscnG644bteK06YQdL2G7AiV8BZmi0ID8pSAzi.docx'));
        $template->setValue('${nomor_surat}', 'nomor_suratxxx');
        $template->setValue('${judul_surat}', 'judul_suratxxx');
        $template->setValue('${judul}', 'judulxxx');
        $template->setValue('${kepada_yth}', 'kepada_ythxxx');
        $template->setValue('${nama_mahasiswa}', 'nama_mahasiswaxxx');
        $template->setValue('${nim_mahasiswa}', 'nim_mahasiswaxxx');
        $template->setValue('${prodi_mahasiswa}', 'prodi_mahasiswaxxx');
        $template->setValue('${tangalsekarang}', 'tangalsekarangxxx');
        $template->setValue('${ttd}', 'ttdxxx');
        $template->setValue('${stemple}', 'stempelxxx');
        $template->setValue('${kaprodi}', 'kaprodixxx');
        $template->setValue('${nipy_kaprodi}', 'nipykaprodixxx');

        // Simpan hasil edit
        $docPath = storage_path('app/public/output.docx');
        $template->saveAs($docPath);

        // nama pdf
        $pdfPath = storage_path('app/public/output.pdf');

        try {
            // $command = "libreoffice --headless --convert-to pdf --outdir " . escapeshellarg(dirname($pdfPath)) . ' ' . escapeshellarg($docPath);
            $command = "soffice --headless --convert-to pdf --outdir " . escapeshellarg(dirname($pdfPath)) . ' ' . escapeshellarg($docPath);
            exec($command, $output, $resultCode);
        } catch (\Throwable $th) {
            return 'Gagal mengonversi dokumen ke PDF: ' . $th->getMessage();
        }

        return response()->download($pdfPath);
        $request->validate([
            'template_id' => 'required|exists:template_surats,id',
            'konten' => 'required|array',
        ]);

        $pengajuan = PengajuanSurat::create([
            'user_id' => Auth::id(),
            'template_id' => $request->template_id,
            'konten' => json_encode($request->konten),
            'status' => 'proses',
        ]);

        $kaprodis = User::role('kepalaprodi')->get();

        $user = Auth::user();
        $template = TemplateSurat::find($request->template_id);

        foreach ($kaprodis as $kaprodi) {
            $phone = $kaprodi->whatsapp_number;

            if (!$phone) {
                \Log::warning("User {$kaprodi->id} role kepalaprodi tidak punya nomor whatsapp");
                continue;
            }

            $message = "Halo {$kaprodi->name}, ada pengajuan surat baru dari {$user->name}.\n" .
                "Jenis Surat: {$template->judul}\n" .
                "Silakan cek aplikasi untuk melakukan approval.";

            $this->sendWablasNotification($phone, $message);
        }

        return redirect()->route('pengajuan-surat.index')->with('success', 'Pengajuan surat berhasil diajukan dan notifikasi terkirim.');
    }

    public function user()
    {
        return $this->belongsTo(User::class);
    }

    public function download(PengajuanSurat $pengajuanSurat)
    {
        if ($pengajuanSurat->status != 'diterima') {
            return redirect()->route('pengajuan-surat.index')
                ->with('error', 'Hanya surat yang telah disetujui yang dapat diunduh.');
        }

        $pdf = Pdf::loadView('pengajuan_surat.surat_pdf', compact('pengajuanSurat'))
            ->setPaper('a4', 'portrait');

        return $pdf->download('Surat_Pengajuan_' . $pengajuanSurat->id . '.pdf');
    }

    public function edit(PengajuanSurat $pengajuanSurat)
    {
        if ($pengajuanSurat->user_id !== Auth::id() || $pengajuanSurat->status !== 'proses') {
            return redirect()->route('pengajuan-surat.index')->with('error', 'Hanya surat dalam status pending yang dapat diedit.');
        }

        $templates = TemplateSurat::all();
        return view('pengajuan_surat.edit', compact('pengajuanSurat', 'templates'));
    }

    public function update(Request $request, PengajuanSurat $pengajuanSurat)
    {
        if ($pengajuanSurat->user_id !== Auth::id() || $pengajuanSurat->status !== 'proses') {
            return redirect()->route('pengajuan-surat.index')->with('error', 'Hanya surat dalam status pending yang dapat diperbarui.');
        }

        $request->validate([
            'template_id' => 'required|exists:template_surats,id',
            'konten' => 'required|array',
        ]);

        $pengajuanSurat->update([
            'template_id' => $request->template_id,
            'konten' => json_encode($request->konten),
        ]);

        return redirect()->route('pengajuan-surat.index')->with('success', 'Pengajuan surat berhasil diperbarui.');
    }
}
