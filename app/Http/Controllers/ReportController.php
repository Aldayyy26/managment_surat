<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\PengajuanSurat;

class ReportController extends Controller
{
    public function history(Request $request)
    {
        $query = PengajuanSurat::with('template', 'user');

        if ($request->filled('pengaju')) {
            $query->whereHas('user', function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->pengaju . '%');
            });
        }

        $pengajuanSurats = $query->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.report', compact('pengajuanSurats'));
    }
}
