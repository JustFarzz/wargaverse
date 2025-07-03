@extends('layouts.app')

@section('content')
<link rel="stylesheet" href="{{ asset('css/showlaporan.css') }}">

<div class="laporan-detail-container">
    <div class="laporan-header">
        <a href="{{ route('laporan.index') }}" class="back-btn">
            <i class="fas fa-arrow-left"></i> Kembali
        </a>
        <h2 class="laporan-title">Detail Laporan</h2>
    </div>

    <div class="laporan-card">
        <div class="laporan-meta">
            <div class="meta-user">
                <img src="{{ $laporan->user->avatar ?? asset('images/default-avatar.png') }}" alt="User" class="avatar">
                <div>
                    <h4 class="user-name">{{ $laporan->user->name }}</h4>
                    <p class="report-date"><i class="fas fa-clock"></i> {{ $laporan->created_at->format('d M Y, H:i') }}</p>
                </div>
            </div>
            <span class="badge kategori-{{ strtolower($laporan->kategori) }}">{{ $laporan->kategori }}</span>
        </div>

        <h3 class="laporan-judul">{{ $laporan->judul }}</h3>
        <p class="laporan-deskripsi">{{ $laporan->deskripsi }}</p>

        @if($laporan->foto)
        <div class="laporan-gambar">
            <img src="{{ asset('storage/laporan/' . $laporan->foto) }}" alt="Foto Laporan">
        </div>
        @endif

        <div class="laporan-aksi">
            @if(Auth::id() === $laporan->user_id)
                <a href="{{ route('laporan.edit', $laporan->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i> Edit</a>
                <form action="{{ route('laporan.destroy', $laporan->id) }}" method="POST" onsubmit="return confirm('Hapus laporan ini?')">
                    @csrf @method('DELETE')
                    <button class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                </form>
            @endif
        </div>
    </div>
</div>
@endsection
