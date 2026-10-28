<?php

namespace App\Http\Controllers;

use App\Models\Anggota;
use App\Models\Simpanan;
use App\Models\User;

class DashboardController extends Controller
{
    public function index()
    {
        return view('dashboard', [
            'jumlahAnggota' => Anggota::where('status', 'aktif')->count(),
            'jumlahMenunggu' => Simpanan::where('status', 'menunggu')->count(),
            'totalSimpanan' => (float) Simpanan::where('status', 'terverifikasi')->sum('nominal'),
            'jumlahUser' => User::count(),
        ]);
    }
}
