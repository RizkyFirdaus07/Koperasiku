@extends('layouts.app')

@section('title', 'Data Anggota')
@section('page-title', 'Data Anggota')

@section('content')
@php $bisaUbah = auth()->user()->hasRole('admin', 'pengurus'); @endphp

<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Kelola data anggota koperasi simpan pinjam.</p>

    @if($bisaUbah)
        <a href="{{ route('anggotas.create') }}" class="btn btn-primary">
            + Tambah Anggota
        </a>
    @endif
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('anggotas.index') }}" class="form-row mb-3">
            <div class="col-md-6 mb-2">
                <input type="text" name="search" class="form-control"
                    placeholder="Cari nomor anggota, NIK, nama, atau HP..."
                    value="{{ $keyword }}">
            </div>
            <div class="col-auto mb-2">
                <button type="submit" class="btn btn-primary">Cari</button>
                <a href="{{ route('anggotas.index') }}" class="btn btn-outline-secondary">
                    Reset
                </a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>No. Anggota</th>
                        <th>NIK</th>
                        <th>Nama</th>
                        <th>Jenis Kelamin</th>
                        <th>No. HP</th>
                        <th>Tanggal Bergabung</th>
                        <th>Status</th>
                        <th style="min-width: 190px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($anggota as $item)
                        <tr>
                            <td>{{ $anggota->firstItem() + $loop->index }}</td>
                            <td>{{ $item->no_anggota }}</td>
                            <td>{{ $item->nik }}</td>
                            <td>{{ $item->nama }}</td>
                            <td>{{ $item->jenis_kelamin === 'L' ? 'Laki-laki' : 'Perempuan' }}</td>
                            <td>{{ $item->no_hp }}</td>
                            <td>{{ $item->tanggal_bergabung->format('d-m-Y') }}</td>
                            <td>
                                @if($item->status === 'aktif')
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('anggotas.show', $item) }}"
                                    class="btn btn-sm btn-info">Detail</a>

                                @if($bisaUbah)
                                    <a href="{{ route('anggotas.edit', $item) }}"
                                        class="btn btn-sm btn-warning">Edit</a>

                                    <form action="{{ route('anggotas.destroy', $item) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus anggota ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-danger">
                                            Hapus
                                        </button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="9" class="text-center py-4">
                                Data anggota belum tersedia.
                            </td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
            <small class="text-muted">
                Menampilkan {{ $anggota->firstItem() ?? 0 }}–{{ $anggota->lastItem() ?? 0 }}
                dari {{ $anggota->total() }} anggota
            </small>
            {{ $anggota->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
