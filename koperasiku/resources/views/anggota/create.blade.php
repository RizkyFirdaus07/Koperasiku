@extends('layouts.app')

@section('title', 'Tambah Anggota')
@section('page-title', 'Tambah Anggota')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">Masukkan data anggota koperasi baru.</p>
    <a href="{{ route('anggotas.index') }}" class="btn btn-outline-secondary">
        Kembali
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <strong>Formulir Anggota</strong>
    </div>
    <div class="card-body">
        <form action="{{ route('anggotas.store') }}" method="POST">
            @csrf
            @include('anggota.form')
        </form>
    </div>
</div>
@endsection
