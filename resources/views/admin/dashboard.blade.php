@extends('admin.layouts.admin')

@section('title', 'Dashboard')
@section('page-title', 'Dashboard Statistik')

@section('content')
<!-- Stats Cards -->
<div class="row">
    <div class="col-md-3">
        <div class="stat-card">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Total Owner</div>
                    <div class="stat-value">{{ $stats['total_owners'] }}</div>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-people-fill"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card success">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Total Kost</div>
                    <div class="stat-value">{{ $stats['total_kosts'] }}</div>
                    <small class="text-success">
                        <i class="bi bi-check-circle"></i> {{ $stats['active_kosts'] }} Aktif
                    </small>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-house-fill"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card warning">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Pending Approval</div>
                    <div class="stat-value">{{ $stats['pending_kosts'] }}</div>
                    <small class="text-muted">
                        <i class="bi bi-check-circle"></i> {{ $stats['approved_kosts'] }} Approved
                    </small>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-clock-fill"></i>
                </div>
            </div>
        </div>
    </div>
    
    <div class="col-md-3">
        <div class="stat-card danger">
            <div class="d-flex justify-content-between align-items-center">
                <div>
                    <div class="stat-label">Pengunjung Hari Ini</div>
                    <div class="stat-value">{{ $stats['today_visitors'] }}</div>
                    <small class="text-muted">
                        <i class="bi bi-calendar-check"></i> {{ now()->format('d M Y') }}
                    </small>
                </div>
                <div class="stat-icon">
                    <i class="bi bi-graph-up"></i>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts -->
<div class="row mt-4">
    <div class="col-md-8">
        <div class="card-custom">
            <h5 class="mb-4">
                <i class="bi bi-bar-chart-line"></i> Statistik Pengunjung (7 Hari Terakhir)
            </h5>
            <canvas id="visitorChart" height="80"></canvas>
        </div>
    </div>
    
    <div class="col-md-4">
        <div class="card-custom">
            <h5 class="mb-4">
                <i class="bi bi-pie-chart"></i> Kost Per Tipe
            </h5>
            <canvas id="kostTypeChart"></canvas>
            <div class="mt-3">
                @foreach($kostByType as $type)
                <div class="d-flex justify-content-between mb-2">
                    <span>
                        @if($type->type == 'Putra')
                            <i class="bi bi-gender-male text-primary"></i>
                        @elseif($type->type == 'Putri')
                            <i class="bi bi-gender-female text-danger"></i>
                        @else
                            <i class="bi bi-gender-ambiguous text-success"></i>
                        @endif
                        {{ $type->type }}
                    </span>
                    <strong>{{ $type->total }} Kost</strong>
                </div>
                @endforeach
            </div>
        </div>
    </div>
</div>

<!-- Recent Data -->
<div class="row mt-4">
    <div class="col-md-6">
        <div class="card-custom">
            <h5 class="mb-3">
                <i class="bi bi-clock-history"></i> Owner Terbaru
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama</th>
                            <th>Email</th>
                            <th>Terdaftar</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentOwners as $owner)
                        <tr>
                            <td>{{ $owner->name }}</td>
                            <td>{{ $owner->email }}</td>
                            <td>{{ $owner->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada owner terdaftar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            <a href="{{ route('admin.owners') }}" class="btn btn-outline-primary btn-sm mt-2">
                Lihat Semua Owner <i class="bi bi-arrow-right"></i>
            </a>
        </div>
    </div>
    
    <div class="col-md-6">
        <div class="card-custom">
            <h5 class="mb-3">
                <i class="bi bi-clock-history"></i> Kost Terbaru
            </h5>
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Nama Kost</th>
                            <th>Owner</th>
                            <th>Status</th>
                            <th>Ditambahkan</th>
                        </tr>
                    </thead>
                    <tbody>
                        @forelse($recentKosts as $kost)
                        <tr>
                            <td>{{ Str::limit($kost->name, 20) }}</td>
                            <td>{{ $kost->owner->name }}</td>
                            <td>
                                @if($kost->is_active)
                                    <span class="badge bg-success">Aktif</span>
                                @else
                                    <span class="badge bg-secondary">Nonaktif</span>
                                @endif
                            </td>
                            <td>{{ $kost->created_at->diffForHumans() }}</td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="4" class="text-center text-muted">Belum ada kost terdaftar</td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script>
// Visitor Chart
const visitorCtx = document.getElementById('visitorChart').getContext('2d');
const visitorData = @json($visitorStats);

new Chart(visitorCtx, {
    type: 'line',
    data: {
        labels: visitorData.map(d => {
            const date = new Date(d.date);
            return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short' });
        }),
        datasets: [{
            label: 'Pengunjung Unik',
            data: visitorData.map(d => d.visitors),
            borderColor: '#667eea',
            backgroundColor: 'rgba(102, 126, 234, 0.1)',
            tension: 0.4,
            fill: true
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                display: false
            }
        },
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    stepSize: 1
                }
            }
        }
    }
});

// Kost Type Chart
const kostTypeCtx = document.getElementById('kostTypeChart').getContext('2d');
const kostTypeData = @json($kostByType);

new Chart(kostTypeCtx, {
    type: 'doughnut',
    data: {
        labels: kostTypeData.map(k => k.type),
        datasets: [{
            data: kostTypeData.map(k => k.total),
            backgroundColor: ['#3498db', '#e74c3c', '#27ae60'],
            borderWidth: 2,
            borderColor: '#fff'
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: true,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    }
});
</script>
@endpush