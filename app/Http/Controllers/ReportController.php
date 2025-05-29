<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanSurat;

class ReportController extends Controller
{
    public function history(Request $request)
    {
        $query = PengajuanSurat::query()->with(['user', 'template']);

        if ($request->pengaju) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->pengaju . '%');
            });
        }

        if ($request->judul) {
            $query->whereHas('template', function ($q) use ($request) {
                $q->where('judul', 'like', '%' . $request->judul . '%');
            });
        }

        if ($request->status) {
            $query->where('status', $request->status);
        }

        if ($request->tanggal) {
            $query->whereDate('created_at', $request->tanggal);
        }

        $pengajuanSurats = $query->latest()->paginate(10);

        return view('admin.report', compact('pengajuanSurats'));
    }
}
