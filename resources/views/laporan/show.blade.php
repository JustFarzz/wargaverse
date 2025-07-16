<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>

    <link rel="stylesheet" href="{{ asset('css/navbarcomponents.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">

    <link rel="stylesheet" href="{{ asset('css/showreport.css') }}">

</head>
<body>
    @include('components.navbar')
    
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
                    <img src="{{ $report->user->avatar ?? asset('images/default-avatar.png') }}" alt="User" class="avatar">
                    <div>
                        <h4 class="user-name">{{ $report->user->name }}</h4>
                        <p class="report-date"><i class="fas fa-clock"></i> {{ $report->created_at->format('d M Y, H:i') }}
                        </p>
                    </div>
                </div>
                <span class="badge kategori-{{ strtolower($report->category) }}">{{ $report->category }}</span>
            </div>
    
            <h3 class="laporan-judul">{{ $report->title }}</h3>
            <p class="laporan-deskripsi">{{ $report->description }}</p>
    
            @if($report->image)
                <div class="laporan-gambar">
                    <img src="{{ asset('storage/' . $report->image) }}" alt="Foto Laporan">
                </div>
            @endif
    
            <div class="laporan-aksi">
                @if(Auth::id() === $report->user_id)
                    <a href="{{ route('laporan.edit', $report->id) }}" class="btn btn-primary"><i class="fas fa-edit"></i>
                        Edit</a>
                    <form action="{{ route('laporan.destroy', $report->id) }}" method="POST"
                        onsubmit="return confirm('Hapus laporan ini?')">
                        @csrf @method('DELETE')
                        <button class="btn btn-danger"><i class="fas fa-trash"></i> Hapus</button>
                    </form>
                @endif
            </div>
        </div>
    </div>
</body>
</html