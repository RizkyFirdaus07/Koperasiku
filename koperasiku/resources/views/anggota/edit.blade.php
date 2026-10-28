@extends('layouts.app')

@section('title', 'Edit Anggota')
@section('page-title', 'Edit Anggota')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <p class="text-muted mb-0">
        Perbarui data {{ $anggota->nama }}.
    </p>
    <a href="{{ route('anggotas.index') }}" class="btn btn-outline-secondary">
        Kembali
    </a>
</div>

<div class="card shadow-sm">
    <div class="card-header bg-white py-3">
        <strong>Formulir Perubahan Data</strong>
    </div>
    <div class="card-body">
        <form action="{{ route('anggotas.update', $anggota) }}" method="POST">
            @csrf
            @method('PUT')
            @include('anggota.form')
        </form>
    </div>
</div>
@endsection
