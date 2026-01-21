@extends('layouts.app')

@section('title', 'Dashboard Owner')

@section('content')
<div class="container my-4">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2><i class="bi bi-speedometer2"></i> Dashboard Owner</h2>
    </div>

    <!-- Owner Actions -->
    <div class="owner-actions">
        <div class="row g-3">
            <div class="col-md-8">
                <form action="{{ route('owner.dashboard') }}" method="GET" class="row g-3">
                    <div class="col-md-5">
                        <input type="text" name="location" class="form-control" placeholder="Cari lokasi..." value="{{ request('location') }}">
                    </div>
                    <div class="col-md-4">
                        <input type="number" name="max_price" class="form-control" placeholder="Harga maks..." value="{{ request('max_price') }}">
                    </div>
                    <div class="col-md-3">
                        <select name="type" class="form-select">
                            <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua Tipe</option>
                            <option value="Putra" {{ request('type') == 'Putra' ? 'selected' : '' }}>Putra</option>
                            <option value="Putri" {{ request('type') == 'Putri' ? 'selected' : '' }}>Putri</option>
                            <option value="Campuran" {{ request('type') == 'Campuran' ? 'selected' : '' }}>Campuran</option>
                        </select>
                    </div>
                </form>
            </div>
            <div class="col-md-4 text-end">
                <button class="btn btn-success btn-lg" data-bs-toggle="modal" data-bs-target="#addKostModal">
                    <i class="bi bi-plus-circle"></i> Tambah Kost
                </button>
            </div>
        </div>
    </div>

    <!-- Kost Cards -->
    <div class="row mt-4">
        @forelse($kosts as $kost)
        <div class="col-md-4 mb-4">
            <div class="card kost-card">
                <div class="position-relative">
                    @if(count($kost->images) > 0)
                        <img src="{{ asset('storage/' . $kost->images[0]) }}" class="card-img-top" alt="{{ $kost->name }}">
                    @else
                        <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                            <i class="bi bi-house-door-fill" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <span class="badge bg-primary badge-type">{{ $kost->type }}</span>
                </div>
                <div class="card-body">
                    <h5 class="card-title">{{ $kost->name }}</h5>
                    
                    <!-- Status Approval -->
                    @if($kost->approval_status == 'pending')
                        <div class="alert alert-warning py-2 px-2 small mb-2">
                            <i class="bi bi-clock"></i> Menunggu persetujuan admin
                        </div>
                    @elseif($kost->approval_status == 'rejected')
                        <div class="alert alert-danger py-2 px-2 small mb-2">
                            <i class="bi bi-x-circle"></i> Ditolak: {{ Str::limit($kost->rejection_reason, 50) }}
                        </div>
                    @else
                        <div class="alert alert-success py-2 px-2 small mb-2">
                            <i class="bi bi-check-circle"></i> Disetujui admin
                        </div>
                    @endif
                    
                    <p class="text-muted mb-2">
                        <i class="bi bi-geo-alt"></i> {{ $kost->location }}
                    </p>
                    <p class="mb-2">
                        <i class="bi bi-people"></i> Maks {{ $kost->max_occupants }} orang
                    </p>
                    <div class="mb-2">
                        <small class="text-muted">Fasilitas:</small><br>
                        @php
                            $facilities = explode(',', $kost->facilities);
                            $mainFacilities = array_slice($facilities, 0, 3);
                        @endphp
                        @foreach($mainFacilities as $facility)
                            <span class="facility-item">{{ trim($facility) }}</span>
                        @endforeach
                        @if(count($facilities) > 3)
                            <span class="facility-item">+{{ count($facilities) - 3 }} lainnya</span>
                        @endif
                    </div>
                    <div class="price-tag mb-3">
                        Rp {{ number_format($kost->price, 0, ',', '.') }}/bulan
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-warning flex-fill" data-bs-toggle="modal" data-bs-target="#editModal{{ $kost->id }}">
                            <i class="bi bi-pencil-square"></i> Edit
                        </button>
                        <form action="{{ route('kost.toggle', $kost) }}" method="POST" class="flex-fill">
                            @csrf
                            @method('PATCH')
                            <button type="submit" class="btn btn-{{ $kost->is_active ? 'success' : 'secondary' }} w-100" title="{{ $kost->is_active ? 'Nonaktifkan' : 'Aktifkan' }}">
                                <i class="bi bi-{{ $kost->is_active ? 'check-circle' : 'x-circle' }}"></i>
                                {{ $kost->is_active ? 'Aktif' : 'Nonaktif' }}
                            </button>
                        </form>
                    </div>
                    @if(!$kost->is_active)
                    <div class="alert alert-warning mt-2 mb-0 py-1 px-2 small">
                        <i class="bi bi-exclamation-triangle"></i> Kost tidak tampil di publik
                    </div>
                    @endif
                </div>
            </div>
        </div>

        <!-- Modal Edit Kost -->
        <div class="modal fade" id="editModal{{ $kost->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">Edit Kost - {{ $kost->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <form action="{{ route('kost.update', $kost) }}" method="POST" enctype="multipart/form-data">
                        @csrf
                        @method('PUT')
                        <div class="modal-body">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Nama Kost <span class="text-danger">*</span></label>
                                    <input type="text" name="name" class="form-control" value="{{ $kost->name }}" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Tipe Kamar <span class="text-danger">*</span></label>
                                    <select name="type" class="form-select" required>
                                        <option value="Putra" {{ $kost->type == 'Putra' ? 'selected' : '' }}>Putra</option>
                                        <option value="Putri" {{ $kost->type == 'Putri' ? 'selected' : '' }}>Putri</option>
                                        <option value="Campuran" {{ $kost->type == 'Campuran' ? 'selected' : '' }}>Campuran</option>
                                    </select>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                                    <input type="text" name="location" class="form-control" value="{{ $kost->location }}" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Link Google Maps</label>
                                    <input type="url" name="google_maps_link" class="form-control" value="{{ $kost->google_maps_link }}" placeholder="https://maps.google.com/...">
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Maksimal Penghuni <span class="text-danger">*</span></label>
                                    <input type="number" name="max_occupants" class="form-control" value="{{ $kost->max_occupants }}" min="1" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Harga/Bulan (Rp) <span class="text-danger">*</span></label>
                                    <input type="number" name="price" class="form-control" value="{{ $kost->price }}" min="0" required>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Fasilitas <span class="text-danger">*</span></label>
                                    <textarea name="facilities" class="form-control" rows="3" placeholder="Pisahkan dengan koma. Contoh: WiFi, AC, Kamar Mandi Dalam" required>{{ $kost->facilities }}</textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Deskripsi</label>
                                    <textarea name="description" class="form-control" rows="3">{{ $kost->description }}</textarea>
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Foto Kost Saat Ini</label>
                                    @if(count($kost->images) > 0)
                                    <div class="row g-2 mb-3" id="imagePreview{{ $kost->id }}">
                                        @foreach($kost->images as $index => $image)
                                        <div class="col-md-3" id="imageItem{{ $kost->id }}_{{ $index }}">
                                            <div class="position-relative">
                                                <img src="{{ asset('storage/' . $image) }}" class="img-thumbnail" style="width: 100%; height: 150px; object-fit: cover;">
                                                <button type="button" class="btn btn-danger btn-sm position-absolute top-0 end-0 m-1" 
                                                    onclick="deleteImage({{ $kost->id }}, '{{ $image }}', {{ $index }})"
                                                    title="Hapus foto ini">
                                                    <i class="bi bi-x-lg"></i>
                                                </button>
                                            </div>
                                        </div>
                                        @endforeach
                                    </div>
                                    <button type="button" class="btn btn-danger btn-sm mb-2" onclick="deleteAllImages({{ $kost->id }})">
                                        <i class="bi bi-trash"></i> Hapus Semua Foto
                                    </button>
                                    @else
                                    <div class="alert alert-info">
                                        <i class="bi bi-info-circle"></i> Belum ada foto yang diupload
                                    </div>
                                    @endif
                                </div>
                                <div class="col-md-12 mb-3">
                                    <label class="form-label">Upload Foto Baru</label>
                                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                    <small class="text-muted">Upload foto baru atau biarkan kosong jika tidak ingin mengubah</small>
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">WhatsApp</label>
                                    <input type="text" name="contact_whatsapp" class="form-control" value="{{ $kost->contact_whatsapp }}" placeholder="08123456789">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Instagram</label>
                                    <input type="text" name="contact_instagram" class="form-control" value="{{ $kost->contact_instagram }}" placeholder="username">
                                </div>
                                <div class="col-md-4 mb-3">
                                    <label class="form-label">Facebook</label>
                                    <input type="text" name="contact_facebook" class="form-control" value="{{ $kost->contact_facebook }}" placeholder="username">
                                </div>
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $kost->id }}" data-bs-dismiss="modal">
                                <i class="bi bi-trash"></i> Hapus Kost
                            </button>
                            <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                            <button type="submit" class="btn btn-primary">
                                <i class="bi bi-save"></i> Simpan Perubahan
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>

        <!-- Modal Konfirmasi Delete -->
        <div class="modal fade" id="deleteModal{{ $kost->id }}" tabindex="-1">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header bg-danger text-white">
                        <h5 class="modal-title">
                            <i class="bi bi-exclamation-triangle"></i> Konfirmasi Hapus Kost
                        </h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <div class="text-center mb-3">
                            <i class="bi bi-trash text-danger" style="font-size: 4rem;"></i>
                        </div>
                        <h5 class="text-center mb-3">Apakah Anda yakin ingin menghapus kost ini?</h5>
                        <div class="alert alert-warning">
                            <strong>Kost:</strong> {{ $kost->name }}<br>
                            <strong>Lokasi:</strong> {{ $kost->location }}
                        </div>
                        <p class="text-danger text-center">
                            <i class="bi bi-exclamation-circle"></i>
                            <strong>Perhatian:</strong> Data yang dihapus tidak dapat dikembalikan!
                        </p>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                            <i class="bi bi-x-circle"></i> Batal
                        </button>
                        <form action="{{ route('kost.destroy', $kost) }}" method="POST" class="d-inline">
                            @csrf
                            @method('DELETE')
                            <button type="submit" class="btn btn-danger">
                                <i class="bi bi-trash"></i> Ya, Hapus Kost
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle fs-1"></i>
                <h5 class="mt-3">Anda belum memiliki kost</h5>
                <p>Klik tombol "Tambah Kost" untuk menambahkan kost pertama Anda!</p>
            </div>
        </div>
        @endforelse
    </div>
</div>

<!-- Modal Tambah Kost -->
<div class="modal fade" id="addKostModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Tambah Kost Baru</h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('kost.store') }}" method="POST" enctype="multipart/form-data">
                @csrf
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Nama Kost <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Tipe Kamar <span class="text-danger">*</span></label>
                            <select name="type" class="form-select" required>
                                <option value="">Pilih Tipe</option>
                                <option value="Putra">Putra</option>
                                <option value="Putri">Putri</option>
                                <option value="Campuran">Campuran</option>
                            </select>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Lokasi <span class="text-danger">*</span></label>
                            <input type="text" name="location" class="form-control" placeholder="Contoh: Jl. Sudirman No. 123, Jakarta Pusat" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Link Google Maps</label>
                            <input type="url" name="google_maps_link" class="form-control" placeholder="https://maps.google.com/...">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Maksimal Penghuni <span class="text-danger">*</span></label>
                            <input type="number" name="max_occupants" class="form-control" min="1" value="1" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Harga/Bulan (Rp) <span class="text-danger">*</span></label>
                            <input type="number" name="price" class="form-control" min="0" placeholder="Contoh: 1500000" required>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Fasilitas <span class="text-danger">*</span></label>
                            <textarea name="facilities" class="form-control" rows="3" placeholder="Pisahkan dengan koma. Contoh: WiFi, AC, Kamar Mandi Dalam, Kasur, Lemari" required></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Deskripsi</label>
                            <textarea name="description" class="form-control" rows="3" placeholder="Deskripsi singkat tentang kost Anda..."></textarea>
                        </div>
                        <div class="col-md-12 mb-3">
                            <label class="form-label">Foto Kost</label>
                            <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                            <small class="text-muted">Anda bisa upload beberapa foto sekaligus</small>
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">WhatsApp</label>
                            <input type="text" name="contact_whatsapp" class="form-control" placeholder="08123456789">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Instagram</label>
                            <input type="text" name="contact_instagram" class="form-control" placeholder="username">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Facebook</label>
                            <input type="text" name="contact_facebook" class="form-control" placeholder="username">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-primary">
                        <i class="bi bi-plus-circle"></i> Tambah Kost
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

@push('scripts')
<script>
// Fungsi hapus satu foto
function deleteImage(kostId, imagePath, imageIndex) {
    if (!confirm('Apakah Anda yakin ingin menghapus foto ini?')) {
        return;
    }
    
    fetch(`/owner/kost/${kostId}/image`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        },
        body: JSON.stringify({ image_path: imagePath })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hapus element foto dari DOM
            const imageItem = document.getElementById(`imageItem${kostId}_${imageIndex}`);
            if (imageItem) {
                imageItem.remove();
            }
            
            // Jika tidak ada foto lagi, tampilkan alert
            if (data.remaining_images === 0) {
                const previewContainer = document.getElementById(`imagePreview${kostId}`);
                if (previewContainer) {
                    previewContainer.innerHTML = '<div class="col-12"><div class="alert alert-info"><i class="bi bi-info-circle"></i> Belum ada foto yang diupload</div></div>';
                }
            }
            
            alert(data.message);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus foto');
    });
}

// Fungsi hapus semua foto
function deleteAllImages(kostId) {
    if (!confirm('Apakah Anda yakin ingin menghapus SEMUA foto? Tindakan ini tidak dapat dibatalkan!')) {
        return;
    }
    
    fetch(`/owner/kost/${kostId}/images/all`, {
        method: 'DELETE',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
        }
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Hapus semua foto dari DOM
            const previewContainer = document.getElementById(`imagePreview${kostId}`);
            if (previewContainer) {
                previewContainer.innerHTML = '<div class="col-12"><div class="alert alert-info"><i class="bi bi-info-circle"></i> Belum ada foto yang diupload</div></div>';
            }
            
            alert(data.message);
            
            // Reload halaman setelah 1 detik
            setTimeout(() => {
                location.reload();
            }, 1000);
        } else {
            alert('Error: ' + data.message);
        }
    })
    .catch(error => {
        console.error('Error:', error);
        alert('Terjadi kesalahan saat menghapus foto');
    });
}
</script>
@endpush
@endsection