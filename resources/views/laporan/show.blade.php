<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Detail Laporan - {{ $report->title }}</title>

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
                    <img src="{{ $report->user->avatar ?? asset('images/default-avatar.png') }}" alt="User"
                        class="avatar">
                    <div>
                        <h4 class="user-name">{{ $report->user->name }}</h4>
                        <p class="report-date"><i class="fas fa-clock"></i>
                            {{ $report->created_at->format('d M Y, H:i') }}</p>
                    </div>
                </div>
                <div class="meta-badges">
                    <span
                        class="badge kategori-{{ strtolower($report->category) }}">{{ ucfirst($report->category) }}</span>
                    <span class="badge priority-{{ $report->priority }}">{{ ucfirst($report->priority) }}</span>
                    <span
                        class="badge status-{{ $report->status }}">{{ ucfirst(str_replace('_', ' ', $report->status)) }}</span>
                </div>
            </div>

            <h3 class="laporan-judul">{{ $report->title }}</h3>
            <p class="laporan-deskripsi">{{ $report->description }}</p>

            <div class="laporan-lokasi">
                <i class="fas fa-map-marker-alt"></i>
                <span>{{ $report->location }}</span>
            </div>

            @if($report->image)
                <div class="laporan-gambar">
                    <img src="{{ asset('storage/' . $report->image) }}" alt="Foto Laporan">
                </div>
            @endif

            @if($report->response)
                <div class="laporan-response">
                    <h4><i class="fas fa-reply"></i> Tanggapan Pengurus</h4>
                    <div class="response-content">
                        <p>{{ $report->response }}</p>
                        <div class="response-meta">
                            <span><i class="fas fa-user-shield"></i> {{ $report->respondedBy->name ?? 'Admin' }}</span>
                            <span><i class="fas fa-clock"></i>
                                {{ $report->responded_at ? $report->responded_at->format('d M Y, H:i') : '' }}</span>
                        </div>
                    </div>
                </div>
            @endif

            <div class="laporan-aksi">
                @if(Auth::id() === $report->user_id && $report->status === 'pending')
                    {{-- <a href="{{ route('laporan.edit', $report->id) }}" class="btn-warning">
                        <i class="fas fa-edit"></i>
                        Edit
                    </a> --}}
                    <form action="{{ route('laporan.destroy', $report->id) }}" method="POST" class="delete-form"
                        style="display: inline;" onsubmit="return confirmDelete(event)">
                        @csrf
                        @method('DELETE')
                        <button type="submit" class="btn-danger">
                            <i class="fas fa-trash"></i>
                            Hapus
                        </button>
                    </form>
                @endif

                @if(Auth::user()->role === 'admin')
                    <div class="admin-actions">
                        <form action="{{ route('laporan.updateStatus', $report->id) }}" method="POST"
                            style="display: inline;">
                            @csrf
                            @method('PATCH')
                            <select name="status" onchange="this.form.submit()" class="status-select">
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Menunggu
                                </option>
                                <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>Sedang
                                    Ditangani</option>
                                <option value="completed" {{ $report->status === 'completed' ? 'selected' : '' }}>Selesai
                                </option>
                                <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Ditolak
                                </option>
                            </select>
                        </form>

                        @if(!$report->response)
                            <button type="button" class="btn-primary" onclick="showResponseForm()">
                                <i class="fas fa-reply"></i>
                                Berikan Tanggapan
                            </button>
                        @endif
                    </div>
                @endif
            </div>

            @if(Auth::user()->role === 'admin' && !$report->response)
                <div id="responseForm" class="response-form" style="display: none;">
                    <h4>Berikan Tanggapan</h4>
                    <form action="{{ route('laporan.respond', $report->id) }}" method="POST">
                        @csrf
                        <div class="form-group">
                            <label for="response">Tanggapan</label>
                            <textarea name="response" id="response" rows="4" required
                                placeholder="Berikan tanggapan untuk laporan ini..."></textarea>
                        </div>
                        <div class="form-group">
                            <label for="status">Update Status</label>
                            <select name="status" id="status">
                                <option value="pending" {{ $report->status === 'pending' ? 'selected' : '' }}>Menunggu
                                </option>
                                <option value="in_progress" {{ $report->status === 'in_progress' ? 'selected' : '' }}>Sedang
                                    Ditangani</option>
                                <option value="completed" {{ $report->status === 'completed' ? 'selected' : '' }}>Selesai
                                </option>
                                <option value="rejected" {{ $report->status === 'rejected' ? 'selected' : '' }}>Ditolak
                                </option>
                            </select>
                        </div>
                        <div class="form-actions">
                            <button type="submit" class="btn-primary">
                                <i class="fas fa-send"></i>
                                Kirim Tanggapan
                            </button>
                            <button type="button" class="btn-secondary" onclick="hideResponseForm()">
                                <i class="fas fa-times"></i>
                                Batal
                            </button>
                        </div>
                    </form>
                </div>
            @endif
        </div>
    </div>

    <script>
        document.addEventListener('DOMContentLoaded', function () {
            // Handle delete form specifically
            const deleteForm = document.querySelector('.delete-form');
            if (deleteForm) {
                deleteForm.addEventListener('submit', function (e) {
                    e.preventDefault();

                    if (confirm('Yakin ingin menghapus laporan ini?')) {
                        // Show loading state
                        const submitButton = this.querySelector('button[type="submit"]');
                        const originalText = submitButton.innerHTML;

                        submitButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Menghapus...';
                        submitButton.disabled = true;

                        // Submit form
                        this.submit();
                    }
                });
            }
        });

        function confirmDelete(event) {
            event.preventDefault(); // Cegah submit langsung

            if (confirm('Yakin ingin menghapus laporan ini?')) {
                // Jika user mengklik OK, submit form
                event.target.closest('form').submit();
                return true;
            }

            return false; // Jika user mengklik Cancel
        }

        function showResponseForm() {
            document.getElementById('responseForm').style.display = 'block';
        }

        function hideResponseForm() {
            document.getElementById('responseForm').style.display = 'none';
        }
    </script>
</body>

</html>