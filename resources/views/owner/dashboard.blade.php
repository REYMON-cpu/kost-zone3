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
                                    <label class="form-label">Foto Kost (Upload baru untuk mengganti)</label>
                                    <input type="file" name="images[]" class="form-control" multiple accept="image/*">
                                    <small class="text-muted">Biarkan kosong jika tidak ingin mengubah foto</small>
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
                            <button type="button" class="btn btn-danger" onclick="confirmDelete({{ $kost->id }})">
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

        <!-- Form Delete (Hidden) -->
        <form id="deleteForm{{ $kost->id }}" action="{{ route('kost.destroy', $kost) }}" method="POST" style="display: none;">
            @csrf
            @method('DELETE')
        </form>
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
function confirmDelete(kostId) {
    if (confirm('Apakah Anda yakin ingin menghapus kost ini? Data yang dihapus tidak dapat dikembalikan.')) {
        document.getElementById('deleteForm' + kostId).submit();
    }
}
</script>
@endpush
@endsection