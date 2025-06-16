<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use App\Models\TemplateSurat;
use App\Models\PengajuanSurat;
use Carbon\Carbon;

class DashboardController extends Controller
{
    public function index()
    {
        $user = Auth::user();

        $totalUsers = User::count();
        $totalSurat = TemplateSurat::count();
        $totalApply = PengajuanSurat::count();
        $userApply = PengajuanSurat::where('user_id', $user->id)->count();

        // Hitung waktu ucapan
        $hour = Carbon::now()->format('H');
        if ($hour >= 5 && $hour < 11) {
            $salam  = 'Selamat pagi';
        } elseif ($hour >= 11 && $hour < 15) {
            $salam  = 'Selamat siang';
        } elseif ($hour >= 15 && $hour < 18) {
            $salam  = 'Selamat sore';
        } else {
            $salam  = 'Selamat malam';
        }

        return view('dashboard', compact(
            'totalUsers',
            'totalSurat',
            'totalApply',
            'userApply',
            'salam',
            'user'
        ));
    }
}
