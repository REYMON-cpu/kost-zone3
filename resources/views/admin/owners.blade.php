@extends('admin.layouts.admin')

@section('title', 'Kelola Owner')
@section('page-title', 'Kelola Owner Kost')

@section('content')
<div class="card-custom">
    <div class="row mb-4">
        <div class="col-md-8">
            <form action="{{ route('admin.owners') }}" method="GET" class="row g-2">
                <div class="col-md-6">
                    <input type="text" name="search" class="form-control" placeholder="Cari nama, email, atau telepon..." value="{{ request('search') }}">
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="bi bi-search"></i> Cari
                    </button>
                </div>
            </form>
        </div>
        <div class="col-md-4 text-end">
            <div class="btn-group">
                <a href="{{ route('admin.owners') }}" class="btn btn-outline-secondary">
                    <i class="bi bi-arrow-clockwise"></i> Reset
                </a>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th width="5%">#</th>
                    <th width="20%">Nama</th>
                    <th width="20%">Email</th>
                    <th width="15%">Telepon</th>
                    <th width="10%" class="text-center">Jumlah Kost</th>
                    <th width="10%" class="text-center">Terdaftar</th>
                    <th width="10%" class="text-center">Aksi</th>
                </tr>
            </thead>
            <tbody>
                @forelse($owners as $index => $owner)
                <tr>
                    <td>{{ $owners->firstItem() + $index }}</td>
                    <td>
                        <strong>{{ $owner->name }}</strong>
                    </td>
                    <td>{{ $owner->email }}</td>
                    <td>{{ $owner->phone ?? '-' }}</td>
                    <td class="text-center">
                        <span class="badge bg-primary">{{ $owner->kosts_count }} Kost</span>
                    </td>
                    <td class="text-center">
                        <small>{{ $owner->created_at->format('d/m/Y') }}</small><br>
                        <small class="text-muted">{{ $owner->created_at->diffForHumans() }}</small>
                    </td>
                    <td class="text-center">
                        <div class="btn-group btn-group-sm">
                            <a href="{{ route('admin.owners.show', $owner) }}" class="btn btn-info" title="Detail">
                                <i class="bi bi-eye"></i>
                            </a>
                            <button type="button" class="btn btn-danger" data-bs-toggle="modal" data-bs-target="#deleteOwnerModal{{ $owner->id }}" title="Hapus">
                                <i class="bi bi-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>

                <!-- Modal Hapus Owner -->
                <div class="modal fade" id="deleteOwnerModal{{ $owner->id }}" tabindex="-1">
                    <div class="modal-dialog">
                        <div class="modal-content">
                            <div class="modal-header bg-danger text-white">
                                <h5 class="modal-title">
                                    <i class="bi bi-exclamation-triangle"></i> Hapus Owner Paksa
                                </h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                            </div>
                            <div class="modal-body">
                                <div class="text-center mb-3">
                                    <i class="bi bi-person-x text-danger" style="font-size: 4rem;"></i>
                                </div>
                                <h5 class="text-center">Yakin hapus owner ini?</h5>
                                <div class="alert alert-danger mt-3">
                                    <strong>Owner:</strong> {{ $owner->name }}<br>
                                    <strong>Email:</strong> {{ $owner->email }}<br>
                                    <strong>Jumlah Kost:</strong> {{ $owner->kosts_count }} kost
                                </div>
                                <p class="text-danger text-center">
                                    <strong>⚠️ PERINGATAN:</strong> Semua kost milik owner ini juga akan dihapus! Tindakan ini tidak dapat dibatalkan!
                                </p>
                            </div>
                            <div class="modal-footer">
                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Batal</button>
                                <form action="{{ route('admin.owners.destroy', $owner) }}" method="POST" class="d-inline">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="btn btn-danger">
                                        <i class="bi bi-trash"></i> Ya, Hapus Owner
                                    </button>
                                </form>
                            </div>
                        </div>
                    </div>
                </div>
                @empty
                <tr>
                    <td colspan="8" class="text-center py-5">
                        <i class="bi bi-inbox" style="font-size: 3rem; opacity: 0.3;"></i>
                        <p class="text-muted mt-2">Tidak ada data owner ditemukan</p>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($owners->hasPages())
    <div class="mt-4">
        {{ $owners->links() }}
    </div>
    @endif
</div>

<!-- Summary Stats -->
<div class="row mt-4">
    <div class="col-md-12">
        <div class="card-custom">
            <h6 class="mb-3">Ringkasan</h6>
            <div class="row text-center">
                <div class="col-md-3">
                    <h4 class="text-primary">{{ $owners->total() }}</h4>
                    <small class="text-muted">Total Owner</small>
                </div>
                <div class="col-md-3">
                    <h4 class="text-info">{{ $owners->sum('kosts_count') }}</h4>
                    <small class="text-muted">Total Kost</small>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection