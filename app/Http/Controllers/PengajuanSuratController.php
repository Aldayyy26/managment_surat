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



class PengajuanSuratController extends Controller
{
    use HasFactory;

    public function index()
    {
        $pengajuanSurats = PengajuanSurat::where('user_id', Auth::id())->get();
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
        $url = "https://console.wablas.com/api/send-message";

        $data = [
            "phone" => $phoneNumber,
            "message" => $message,
        ];

        $headers = [
            "Authorization: Bearer VIgCefPPbm",  // pastikan "Bearer " ada di depan token
            "Content-Type: application/json",
        ];

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($data));
        $response = curl_exec($ch);
        $err = curl_error($ch);
        curl_close($ch);

        if ($err) {
            \Log::error("Wablas API Error: " . $err);
            return false;
        } else {
            \Log::info("Wablas API Response: " . $response);
        }

        return $response;
    }


    public function store(Request $request)
    {
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
