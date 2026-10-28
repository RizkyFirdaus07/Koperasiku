@extends('layouts.app')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Koperasi')

@section('content')

<div class="row">

    @if(auth()->user()->hasRole('admin', 'pengurus', 'bendahara'))
        <div class="col-lg-4 col-12">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ $jumlahAnggota }}</h3>
                    <p>Data Anggota Koperasi (aktif)</p>
                </div>
                <div class="icon">
                    <i class="fas fa-users"></i>
                </div>
                <a href="{{ route('anggotas.index') }}" class="small-box-footer">
                    Lihat Data <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>

        <div class="col-lg-4 col-12">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>Rp {{ number_format($totalSimpanan, 0, ',', '.') }}</h3>
                    <p>Simpanan Terverifikasi
                        @if($jumlahMenunggu > 0)
                            &middot; {{ $jumlahMenunggu }} menunggu verifikasi
                        @endif
                    </p>
                </div>
                <div class="icon">
                    <i class="fas fa-wallet"></i>
                </div>
                <a href="{{ route('simpanans.index') }}" class="small-box-footer">
                    Lihat Simpanan <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    @endif

    @if(auth()->user()->role === 'admin')
        <div class="col-lg-4 col-12">
            <div class="small-box bg-warning">
                <div class="inner">
                    <h3>{{ $jumlahUser }}</h3>
                    <p>Pengguna &middot; Manajemen User</p>
                </div>
                <div class="icon">
                    <i class="fas fa-user-cog"></i>
                </div>
                <a href="{{ route('users.index') }}" class="small-box-footer">
                    Kelola User <i class="fas fa-arrow-circle-right"></i>
                </a>
            </div>
        </div>
    @endif

</div>

<div class="card">
    <div class="card-header">
        <h3 class="card-title">Selamat Datang</h3>
    </div>
    <div class="card-body">
        Selamat datang, <strong>{{ auth()->user()->name }}</strong>,
        di aplikasi KoperasiKu.
    </div>
</div>

@endsection
