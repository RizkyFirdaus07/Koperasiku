@extends('layouts.app')

@section('title', 'Data Simpanan')
@section('page-title', 'Data Simpanan')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Transaksi simpanan anggota koperasi.</p>
    <a href="{{ route('simpanans.create') }}" class="btn btn-primary">
        + Tambah Simpanan
    </a>
</div>

<div class="row mb-2">
    @foreach([
        ['label' => 'Simpanan Pokok', 'value' => $rekap->total_pokok, 'bg' => 'bg-info'],
        ['label' => 'Simpanan Wajib', 'value' => $rekap->total_wajib, 'bg' => 'bg-primary'],
        ['label' => 'Simpanan Sukarela', 'value' => $rekap->total_sukarela, 'bg' => 'bg-warning'],
        ['label' => 'Total Terverifikasi', 'value' => $rekap->total_simpanan, 'bg' => 'bg-success'],
    ] as $card)
        <div class="col-sm-6 col-xl-3">
            <div class="info-box shadow-sm">
                <span class="info-box-icon {{ $card['bg'] }}"><i class="fas fa-coins"></i></span>
                <div class="info-box-content">
                    <span class="info-box-text">{{ $card['label'] }}</span>
                    <span class="info-box-number">
                        Rp {{ number_format((float) $card['value'], 0, ',', '.') }}
                    </span>
                </div>
            </div>
        </div>
    @endforeach
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('simpanans.index') }}" class="form-row mb-3">
            <div class="col-lg-3 col-md-6 mb-2">
                <input type="text" name="search" class="form-control"
                    placeholder="Cari kode atau nama anggota"
                    value="{{ request('search') }}">
            </div>

            <div class="col-lg-2 col-md-6 mb-2">
                <select name="jenis" class="form-control">
                    <option value="">Semua jenis</option>
                    <option value="pokok" @selected(request('jenis') === 'pokok')>Pokok</option>
                    <option value="wajib" @selected(request('jenis') === 'wajib')>Wajib</option>
                    <option value="sukarela" @selected(request('jenis') === 'sukarela')>Sukarela</option>
                </select>
            </div>

            <div class="col-lg-2 col-md-6 mb-2">
                <select name="status" class="form-control">
                    <option value="">Semua status</option>
                    @foreach(['menunggu', 'terverifikasi', 'ditolak', 'dibatalkan'] as $status)
                        <option value="{{ $status }}"
                            @selected(request('status') === $status)>
                            {{ ucfirst($status) }}
                        </option>
                    @endforeach
                </select>
            </div>

            <div class="col-lg-2 col-md-6 mb-2">
                <input type="date" name="dari" class="form-control"
                    aria-label="Tanggal awal" value="{{ request('dari') }}">
            </div>

            <div class="col-lg-2 col-md-6 mb-2">
                <input type="date" name="sampai" class="form-control"
                    aria-label="Tanggal akhir" value="{{ request('sampai') }}">
            </div>

            <div class="col-lg-1 col-md-6 mb-2">
                <button class="btn btn-primary btn-block" type="submit">Filter</button>
            </div>

            <div class="col-12">
                <a href="{{ route('simpanans.index') }}"
                    class="btn btn-sm btn-outline-secondary">Reset filter</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Kode</th>
                        <th>Anggota</th>
                        <th>Jenis</th>
                        <th>Nominal</th>
                        <th>Tanggal</th>
                        <th>Status</th>
                        <th style="min-width: 230px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($simpanan as $item)
                        <tr>
                            <td>{{ $simpanan->firstItem() + $loop->index }}</td>
                            <td>{{ $item->kode_transaksi }}</td>
                            <td>
                                {{ $item->anggota->nama }}<br>
                                <small class="text-muted">{{ $item->anggota->no_anggota }}</small>
                            </td>
                            <td>{{ ucfirst($item->jenis) }}</td>
                            <td class="text-nowrap">
                                Rp {{ number_format((float) $item->nominal, 0, ',', '.') }}
                            </td>
                            <td>{{ $item->tanggal_transaksi->format('d-m-Y') }}</td>
                            <td>
                                @php
                                    $badge = [
                                        'menunggu' => 'warning',
                                        'terverifikasi' => 'success',
                                        'ditolak' => 'danger',
                                        'dibatalkan' => 'secondary',
                                    ][$item->status];
                                @endphp
                                <span class="badge badge-{{ $badge }}">
                                    {{ ucfirst($item->status) }}
                                </span>
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('simpanans.show', $item) }}"
                                    class="btn btn-sm btn-info">Detail</a>

                                @if($item->status === 'menunggu')
                                    <a href="{{ route('simpanans.edit', $item) }}"
                                        class="btn btn-sm btn-warning">Edit</a>

                                    <form action="{{ route('simpanans.destroy', $item) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Batalkan transaksi ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-outline-danger">
                                            Batalkan
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="8" class="text-center py-4">
                                Belum ada transaksi simpanan.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center">
            <small class="text-muted">
                Total {{ $simpanan->total() }} transaksi
            </small>
            {{ $simpanan->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
