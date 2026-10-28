@extends('layouts.app')

@section('title', 'Edit Simpanan')
@section('page-title', 'Edit Simpanan')

@section('content')
<p class="text-muted">
    Kode transaksi: {{ $simpanan->kode_transaksi }}
</p>

<div class="alert alert-warning">
    Hanya transaksi berstatus menunggu yang dapat diedit.
</div>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('simpanans.update', $simpanan) }}" method="POST">
            @csrf
            @method('PUT')
            @include('simpanan._form')
        </form>
    </div>
</div>
@endsection
