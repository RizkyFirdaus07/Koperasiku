@extends('layouts.app')

@section('title', 'Tambah Simpanan')
@section('page-title', 'Tambah Simpanan')

@section('content')
<p class="text-muted">Transaksi baru akan berstatus menunggu.</p>

<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('simpanans.store') }}" method="POST">
            @csrf
            @include('simpanan._form')
        </form>
    </div>
</div>
@endsection
