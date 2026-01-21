@extends('admin.layouts.admin')

@section('title', 'Kelola Kost')
@section('page-title', 'Kelola & Approval Kost')

@section('content')
<div class="card-custom">
    <div class="row mb-4">
        <div class="col-md-9">
            <form action="{{ route('admin.kosts') }}" method="GET" class="row g-2">
                <div class="col-md-4">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama kost, lokasi, owner..." value="{{ request('search') }}">
                </div>
                <div class="col-md-3">
                    <select name="status" class="form-select">
                        <option value="">Semua Status Approval</option>
                        <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>⏳ Pending</option>
                        <option value="approved" {{ request('status') == 'approved' ? 'selected' : '' }}>✅ Approved</option>
                        <option value="rejected" {{ request('status') == 'rejected' ? 'selected' : '' }}>❌ Rejected</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <select name="active" class="form-select">
                        <option value="">Semua Status Aktif</option>
                        <option value="yes" {{ request('active') == 'yes' ? 'selected' : '' }}>Aktif</option>
                        <option value="no" {{ request('active') == 'no' ? 'selected' : '' }}>Nonaktif</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-3 text-end">
            <a href="{{ route('admin.kosts') }}" class="btn btn-outline-secondary">
                <i class="bi bi-arrow-clockwise"></i> Reset
            </a>
        </div>
    </div>

    <!-- Stats Quick View -->
    <div class="row mb-4">
        <div class="col-md-3">
            <div class="text-center p-3 bg-warning bg-opacity-10 rounded">
                <h4 class="text-warning">{{ $kosts->where('approval_status', 'pending')->count() }}</h4>
                <small>Menunggu Approval</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3 bg-success bg-opacity-10 rounded">
                <h4 class="text-success">{{ $kosts->where('approval_status', 'approved')->count() }}</h4>
                <small>Disetujui</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3 bg-danger bg-opacity-10 rounded">
                <h4 class="text-danger">{{ $kosts->where('approval_status', 'rejected')->count() }}</h4>
                <small>Ditolak</small>
            </div>
        </div>
        <div class="col-md-3">
            <div class="text-center p-3 bg-info bg-opacity-10 rounded">
                <h4 class="text-info">{{ $kosts->total() }}</h4>
                <small>Total Kost</small>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="3%">#</th>
                    <th width="20%">Nama Kost</th>
                    <th width="15%">Owner</th>
                    <th width="15%">Lokasi</th>
                    <th width="8%">Harga</th>
                    <th width="8%" class="text-center">Status Aktif</th>
                    <th width="10%" class="text-center">Approval</th>
                    <th width="10%" class="text-center">Ditambahkan</th>
                    <th width="11%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($kosts as $index => $kost)
                <tr>
                    <td>{{ $kosts->firstItem() + $index }}</td>
                    <td>
                        <strong>{{ $kost->name }}</strong><br>
                        <small class="text-muted">
                            <span class="badge bg-{{ $kost->type == 'Putra' ? 'primary' : ($kost->type == 'Putri' ? 'danger' : 'success') }}">
                                {{ $kost->type }}
                            </span>
                            @if(count($kost->images) > 0)
                                <i class="bi bi-images"></i> {{ count($kost->images) }}
                            @endif
                        </small>
                    </td>
                    <td>
                        <small>{{ $kost->owner->name }}</small>
                    </td>
                    <td>
                        <small>{{ Str::limit($kost->location, 30) }}</small>
                    </td>
                    <td>
                        <small><strong>Rp {{ number_format($kost->price / 1000, 0) }}k</strong></small>
                    </td>
                    <td class="text-center">
                        @if($kost->is_active)
                            <span class="badge bg-success">Aktif</span>
                        @else
                            <span class="badge bg-secondary">Nonaktif</span>
                        @endif
                    </td>
                    <td class="text-center">
                        @if($kost->approval_status == 'pending')
                            <span class="badge bg-warning text-dark">
                                <i class="bi bi-clock"></i> Pending
                            </span>
                        @elseif($kost->approval_status == 'approved')
                            <span class="badge bg-success">
                                <i class="bi bi-check-circle"></i> Approved
                            </span>
                        @else
                            <span class="badge bg-danger">
                                <i class="bi bi-x-circle"></i> Rejected
                            </span>
                        @endif
                    </td>
                    <td class="text-center">
                        <small>{{ $kost->created_at->format('d/m/Y') }}</small>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            @if($kost->approval_status == 'pending')
                            <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#approveModal{{ $kost->id }}" title="Approve">
                                <i class="bi bi-check-circle"></i>
                            </button>
                            @endif
                            <a href="{{ route('admin.kosts.show', $kost) }}" class="btn btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteModal{{ $kost->id }}" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Modal Quick Approve -->
                @if($kost->approval_status == 'pending')
                <div class="modal fade" id="approveModal{{ $kost->id }}" tabindex="-1">
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
                                    <i class="bi bi-check-circle-fill text-success" style="font-size: 3rem;"></i>
                                </div>
                                <h6 class="text-center mb-3">Yakin menyetujui kost ini?</h6>
                                <div class="alert alert-info">
                                    <strong>Kost:</strong> {{ $kost->name }}<br>
                                    <strong>Owner:</strong> {{ $kost->owner->name }}<br>
                                    <strong>Tipe:</strong> {{ $kost->type }}<br>
                                    <strong>Harga:</strong> Rp {{ number_format($kost->price, 0, ',', '.') }}
                                </div>
                                <p class="text-center small">
                                    <i class="bi bi-info-circle text-success"></i>
                                    Kost akan tampil di halaman publik
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary btn-sm" data-bs-dismiss="modal">Batal</button>
                                <form action="{{ route('admin.kosts.approve', $kost) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('PATCH')
                                    <button type="submit" class="btn btn-success btn-sm">
                                        <i class="bi bi-check-circle"></i> Setujui
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Modal Hapus -->
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
                                <h5 class="text-center">Yakin hapus kost ini?</h5>
                                <div class="alert alert-warning mt-3">
                                    <strong>Kost:</strong> {{ $kost->name }}<br>
                                    <strong>Owner:</strong> {{ $kost->owner->name }}<br>
                                    <strong>Lokasi:</strong> {{ $kost->location }}
                                </div>
                                <p class="text-danger text-center">
                                    <strong>Peringatan:</strong> Tindakan ini tidak dapat dibatalkan!
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
                @empty
                <tr>
                    <td colspan="9" class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-2">Tidak ada data kost ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($kosts->hasPages())
    <div class="mt-4">
        {{ $kosts->links() }}
    </div>
    @endif
</div>
@endsection