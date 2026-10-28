@extends('layouts.app')

@section('title', 'Tambah User')
@section('page-title', 'Tambah User')

@section('content')
<div class="card shadow-sm">
    <div class="card-body">
        <form action="{{ route('users.store') }}" method="POST">
            @csrf
            @include('users.form')
        </form>
    </div>
</div>
@endsection
