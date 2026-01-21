@extends('admin.layouts.admin')

@section('title', 'Detail Kost')
@section('page-title', 'Detail Kost')

@section('content')
<div class="mb-3">
    <a href="{{ route('admin.kosts') }}" class="btn btn-outline-secondary">
        <i class="bi bi-arrow-left"></i> Kembali
    </a>
</div>

<!-- Approval Actions -->
@if($kost->approval_status == 'pending')
<div class="card-custom bg-warning bg-opacity-10 border-warning">
    <h5 class="text-warning">
        <i class="bi bi-exclamation-triangle"></i> Kost Menunggu Persetujuan
    </h5>
    <p class="mb-3">Kost ini perlu di-approve atau reject oleh admin.</p>
    <div class="d-flex gap-2">
        <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal">
            <i class="bi bi-check-circle"></i> Setujui Kost
        </button>
        <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#rejectModal">
            <i class="bi bi-x-circle"></i> Tolak Kost
        </button>
    </div>
</div>
@elseif($kost->approval_status == 'approved')
<div class="alert alert-success">
    <i class="bi bi-check-circle"></i>
    <strong>Kost Disetujui</strong>
    @if($kost->approvedBy)
        oleh {{ $kost->approvedBy->name }} pada {{ $kost->approved_at->format('d M Y H:i') }}
    @endif
</div>
@else
<div class="alert alert-danger">
    <i class="bi bi-x-circle"></i>
    <strong>Kost Ditolak</strong><br>
    <small>Alasan: {{ $kost->rejection_reason }}</small>
</div>
@endif

<!-- Kost Info -->
<div class="row mt-3">
    <div class="col-md-8">
        <div class="card-custom">
            <h5 class="mb-4">
                <i class="bi bi-house-fill"></i> Informasi Kost
            </h5>

            <!-- Carousel Foto -->
            @if(count($kost->images) > 0)
            <div id="kostCarousel" class="carousel slide mb-4" data-bs-ride="carousel">
                <div class="carousel-inner">
                    @foreach($kost->images as $index => $image)
                    <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                        <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" style="height: 400px; object-fit: cover; border-radius: 10px;" alt="Foto {{ $index + 1 }}">
                    </div>
                    @endforeach
                </div>
                @if(count($kost->images) > 1)
                <button class="carousel-control-prev" type="button" data-bs-target="#kostCarousel" data-bs-slide="prev">
                    <span class="carousel-control-prev-icon"></span>
                </button>
                <button class="carousel-control-next" type="button" data-bs-target="#kostCarousel" data-bs-slide="next">
                    <span class="carousel-control-next-icon"></span>
                </button>
                @endif
            </div>
            @else
            <div class="mb-4 p-5 text-center bg-light rounded">
                <i class="bi bi-image" style="font-size: 4rem; opacity: 0.3;"></i>
                <p class="text-muted mt-2">Tidak ada foto</p>
            </div>
            @endif

            <table class="table table-borderless">
                <tr>
                    <th width="30%">Nama Kost</th>
                    <td>: <strong>{{ $kost->name }}</strong></td>
                </tr>
                <tr>
                    <th>Tipe Kamar</th>
                    <td>: <span class="badge bg-{{ $kost->type == 'Putra' ? 'primary' : ($kost->type == 'Putri' ? 'danger' : 'success') }}">{{ $kost->type }}</span></td>
                </tr>
                <tr>
                    <th>Lokasi</th>
                    <td>
                        : {{ $kost->location }}
                        @if($kost->google_maps_link)
                            <a href="{{ $kost->google_maps_link }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                <i class="bi bi-map"></i> Maps
                            </a>
                        @endif
                    </td>
                </tr>
                <tr>
                    <th>Maksimal Penghuni</th>
                    <td>: {{ $kost->max_occupants }} orang</td>
                </tr>
                <tr>
                    <th>Harga/Bulan</th>
                    <td>: <strong class="text-primary">Rp {{ number_format($kost->price, 0, ',', '.') }}</strong></td>
                </tr>
                <tr>
                    <th>Status Aktif</th>
                    <td>: 
                        @if($kost->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                </tr>
            </table>

            @if($kost->description)
            <h6 class="mt-4">Deskripsi</h6>
            <p>{{ $kost->description }}</p>
            @endif

            <h6 class="mt-4">Fasilitas</h6>
            <div>
                @foreach(explode(',', $kost->facilities) as $facility)
                    <span class="badge bg-light text-dark border me-1 mb-1">{{ trim($facility) }}</span>
                @endforeach
            </div>

            <h6 class="mt-4">Kontak</h6>
            <div>
                @if($kost->contact_whatsapp)
                    <span class="badge bg-success me-2">
                        <i class="bi bi-whatsapp"></i> {{ $kost->contact_whatsapp }}
                    </span>
                @endif
                @if($kost->contact_instagram)
                    <span class="badge bg-danger me-2">
                        <i class="bi bi-instagram"></i> {{ $kost->contact_instagram }}
                    </span>
                @endif
                @if($kost->contact_facebook)
                    <span class="badge bg-primary me-2">
                        <i class="bi bi-facebook"></i> {{ $kost->contact_facebook }}
                    </span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <!-- Owner Info -->
        <div class="card-custom">
            <h6 class="mb-3">
                <i class="bi bi-person-circle"></i> Informasi Owner
            </h6>
            <table class="table table-sm table-borderless">
                <tr>
                    <th>Nama</th>
                    <td>: {{ $kost->owner->name }}</td>
                </tr>
                <tr>
                    <th>Email</th>
                    <td>: {{ $kost->owner->email }}</td>
                </tr>
                <tr>
                    <th>Telepon</th>
                    <td>: {{ $kost->owner->phone ?? '-' }}</td>
                </tr>
                <tr>
                    <th>Status</th>
                    <td>: 
                        @if($kost->owner->email_verified_at)
                            <span class="badge bg-success">Verified</span>
                        @else
                            <span class="badge bg-warning">Pending</span>
                        @endif
                    </td>
                </tr>
            </table>
            <a href="{{ route('admin.owners.show', $kost->owner) }}" class="btn btn-sm btn-outline-primary w-100 mt-2">
                <i class="bi bi-eye"></i> Lihat Detail Owner
            </a>
        </div>

        <!-- Timeline -->
        <div class="card-custom mt-3">
            <h6 class="mb-3">
                <i class="bi bi-clock-history"></i> Timeline
            </h6>
            <div class="timeline">
                <div class="mb-3">
                    <small class="text-muted">Ditambahkan</small><br>
                    <strong>{{ $kost->created_at->format('d M Y H:i') }}</strong>
                </div>
                @if($kost->approved_at)
                <div class="mb-3">
                    <small class="text-muted">Disetujui</small><br>
                    <strong>{{ $kost->approved_at->format('d M Y H:i') }}</strong>
                </div>
                @endif
                <div>
                    <small class="text-muted">Terakhir Update</small><br>
                    <strong>{{ $kost->updated_at->format('d M Y H:i') }}</strong>
                </div>
            </div>
        </div>

        <!-- Danger Zone -->
        <div class="card-custom mt-3 border-danger">
            <h6 class="text-danger mb-3">
                <i class="bi bi-exclamation-triangle"></i> Danger Zone
            </h6>
            <button type="button" class="btn btn-danger w-100" data-bs-toggle="modal" data-bs-target="#deleteKostModal">
                <i class="bi bi-trash"></i> Hapus Kost Paksa
            </button>
        </div>
    </div>
</div>

<!-- Modal Approve -->
<div class="modal fade" id="approveModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-success text-white">
                <h5 class="modal-title">
                    <i class="bi bi-check-circle"></i> Setujui Kost
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-check-circle-fill text-success" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-center mb-3">Yakin menyetujui kost ini?</h5>
                <div class="alert alert-info">
                    <strong>Kost:</strong> {{ $kost->name }}<br>
                    <strong>Owner:</strong> {{ $kost->owner->name }}<br>
                    <strong>Lokasi:</strong> {{ $kost->location }}<br>
                    <strong>Harga:</strong> Rp {{ number_format($kost->price, 0, ',', '.') }}/bulan
                </div>
                <p class="text-center">
                    <i class="bi bi-info-circle text-success"></i>
                    Kost yang disetujui akan tampil di halaman publik dan bisa dilihat oleh pencari kost.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">
                    <i class="bi bi-x-circle"></i> Batal
                </button>
                <form action="{{ route('admin.kosts.approve', $kost) }}" method="POST" class="d-inline">
                    @csrf
                    @method('PATCH')
                    <button type="submit" class="btn btn-success">
                        <i class="bi bi-check-circle"></i> Ya, Setujui Kost
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

<!-- Modal Reject -->
<div class="modal fade" id="rejectModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-x-circle"></i> Tolak Kost
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <form action="{{ route('admin.kosts.reject', $kost) }}" method="POST">
                @csrf
                @method('PATCH')
                <div class="modal-body">
                    <div class="mb-3">
                        <label class="form-label">Alasan Penolakan <span class="text-danger">*</span></label>
                        <textarea name="rejection_reason" class="form-control" rows="4" placeholder="Jelaskan alasan penolakan..." required></textarea>
                        <small class="text-muted">Alasan ini akan dilihat oleh owner kost</small>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                    <button type="submit" class="btn btn-danger">
                        <i class="bi bi-x-circle"></i> Tolak Kost
                    </button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Modal Delete Kost -->
<div class="modal fade" id="deleteKostModal" tabindex="-1">
    <div class="modal-dialog">
        <div class="modal-content">
            <div class="modal-header bg-danger text-white">
                <h5 class="modal-title">
                    <i class="bi bi-exclamation-triangle"></i> Hapus Kost Paksa
                </h5>
                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <div class="text-center mb-3">
                    <i class="bi bi-trash text-danger" style="font-size: 4rem;"></i>
                </div>
                <h5 class="text-center">Yakin hapus kost ini?</h5>
                <div class="alert alert-warning mt-3">
                    <strong>Kost:</strong> {{ $kost->name }}<br>
                    <strong>Owner:</strong> {{ $kost->owner->name }}
                </div>
                <p class="text-danger text-center">
                    <strong>⚠️ PERINGATAN:</strong> Tindakan ini tidak dapat dibatalkan! Semua data dan foto akan dihapus permanen.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                <form action="{{ route('admin.kosts.destroy', $kost) }}" method="POST" class="d-inline">
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
@endsection