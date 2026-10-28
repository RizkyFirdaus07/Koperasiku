@extends('layouts.app')

@section('title', 'Manajemen User')
@section('page-title', 'Manajemen User')

@section('content')
<div class="d-flex flex-wrap justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Kelola akun pengguna dan hak akses.</p>
    <a href="{{ route('users.create') }}" class="btn btn-primary">+ Tambah User</a>
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form method="GET" action="{{ route('users.index') }}" class="form-row mb-3">
            <div class="col-md-6 mb-2">
                <input type="text" name="search" class="form-control"
                    placeholder="Cari nama atau email..." value="{{ $keyword }}">
            </div>
            <div class="col-auto mb-2">
                <button type="submit" class="btn btn-primary">Cari</button>
                <a href="{{ route('users.index') }}" class="btn btn-outline-secondary">Reset</a>
            </div>
        </form>

        <div class="table-responsive">
            <table class="table table-hover table-bordered align-middle">
                <thead class="thead-light">
                    <tr>
                        <th>No.</th>
                        <th>Nama</th>
                        <th>Email</th>
                        <th>Role</th>
                        <th>Status</th>
                        <th style="min-width: 150px">Aksi</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($users as $item)
                        <tr>
                            <td>{{ $users->firstItem() + $loop->index }}</td>
                            <td>{{ $item->name }}</td>
                            <td>{{ $item->email }}</td>
                            <td>{{ ucfirst($item->role) }}</td>
                            <td>
                                @if($item->is_active)
                                    <span class="badge badge-success">Aktif</span>
                                @else
                                    <span class="badge badge-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td class="text-nowrap">
                                <a href="{{ route('users.edit', $item) }}"
                                    class="btn btn-sm btn-warning">Edit</a>

                                @if($item->id !== auth()->id())
                                    <form action="{{ route('users.destroy', $item) }}"
                                        method="POST" class="d-inline"
                                        onsubmit="return confirm('Yakin ingin menghapus user ini?')">
                                        @csrf
                                        @method('DELETE')
                                        <button class="btn btn-sm btn-danger">Hapus</button>
                                    </form>
                                @endif
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center py-4">Belum ada user.</td>
                        </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <div class="d-flex flex-wrap justify-content-between align-items-center mt-3">
            <small class="text-muted">Total {{ $users->total() }} user</small>
            {{ $users->links('pagination::bootstrap-4') }}
        </div>
    </div>
</div>
@endsection
