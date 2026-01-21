@extends('layouts.app')

@section('title', 'Cari Kost Terbaik')

@section('content')
<div class="container my-4">
    <!-- Banner -->
    <div class="banner-container">
        <img src="{{ asset('images/banner.png') }}" alt="Banner" onerror="this.style.display='none'; this.nextElementSibling.style.display='flex';">
        <div class="banner-placeholder" style="display: none;">
            Selamat Datang di Kost Zone
        </div>
    </div>

    <!-- Filter Bar -->
    <div class="filter-bar">
        <form action="{{ route('home') }}" method="GET" class="row g-3">
            <div class="col-md-4">
                <label class="form-label">
                    <i class="bi bi-geo-alt-fill"></i> Lokasi
                </label>
                <input type="text" name="location" class="form-control" placeholder="Cari lokasi..." value="{{ request('location') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bi bi-cash-coin"></i> Harga Maksimal
                </label>
                <input type="number" name="max_price" class="form-control" placeholder="Contoh: 1000000" value="{{ request('max_price') }}">
            </div>
            <div class="col-md-3">
                <label class="form-label">
                    <i class="bi bi-people-fill"></i> Tipe Kamar
                </label>
                <select name="type" class="form-select">
                    <option value="all" {{ request('type') == 'all' ? 'selected' : '' }}>Semua</option>
                    <option value="Putra" {{ request('type') == 'Putra' ? 'selected' : '' }}>Putra</option>
                    <option value="Putri" {{ request('type') == 'Putri' ? 'selected' : '' }}>Putri</option>
                    <option value="Campuran" {{ request('type') == 'Campuran' ? 'selected' : '' }}>Campuran</option>
                </select>
            </div>
            <div class="col-md-2 d-flex align-items-end">
                <button type="submit" class="btn btn-primary w-100">
                    <i class="bi bi-search"></i> Cari
                </button>
            </div>
        </form>
    </div>

    <!-- Kost Cards -->
    <div class="row">
        @forelse($kosts as $kost)
        <div class="col-md-4 mb-4">
            <div class="card kost-card">
                <div class="position-relative">
                    @if(count($kost->images) > 0)
                        <div id="previewCarousel{{ $kost->id }}" class="carousel slide" data-bs-ride="false">
                            <div class="carousel-inner">
                                @foreach($kost->images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" class="card-img-top" alt="{{ $kost->name }}">
                                </div>
                                @endforeach
                            </div>
                            @if(count($kost->images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#previewCarousel{{ $kost->id }}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#previewCarousel{{ $kost->id }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                            <div class="carousel-indicators" style="bottom: 5px;">
                                @foreach($kost->images as $index => $image)
                                <button type="button" data-bs-target="#previewCarousel{{ $kost->id }}" data-bs-slide-to="{{ $index }}" class="{{ $index == 0 ? 'active' : '' }}" style="width: 8px; height: 8px; border-radius: 50%;"></button>
                                @endforeach
                            </div>
                            @endif
                        </div>
                    @else
                        <div style="height: 200px; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); display: flex; align-items: center; justify-content: center; color: white; font-size: 1.2rem;">
                            <i class="bi bi-house-door-fill" style="font-size: 3rem;"></i>
                        </div>
                    @endif
                    <span class="badge bg-primary badge-type">{{ $kost->type }}</span>
                    @if(count($kost->images) > 1)
                    <span class="badge bg-dark" style="position: absolute; top: 10px; left: 10px;">
                        <i class="bi bi-images"></i> {{ count($kost->images) }} Foto
                    </span>
                    @endif
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
                    <button class="btn btn-primary w-100" data-bs-toggle="modal" data-bs-target="#detailModal{{ $kost->id }}">
                        <i class="bi bi-eye"></i> Detail Kost
                    </button>
                </div>
            </div>
        </div>

        <!-- Modal Detail Kost -->
        <div class="modal fade" id="detailModal{{ $kost->id }}" tabindex="-1">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header">
                        <h5 class="modal-title">{{ $kost->name }}</h5>
                        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"></button>
                    </div>
                    <div class="modal-body">
                        <!-- Carousel Images -->
                        @if(count($kost->images) > 0)
                        <div id="carousel{{ $kost->id }}" class="carousel slide mb-3" data-bs-ride="carousel">
                            <div class="carousel-inner">
                                @foreach($kost->images as $index => $image)
                                <div class="carousel-item {{ $index == 0 ? 'active' : '' }}">
                                    <img src="{{ asset('storage/' . $image) }}" class="d-block w-100" alt="Foto {{ $index + 1 }}">
                                </div>
                                @endforeach
                            </div>
                            @if(count($kost->images) > 1)
                            <button class="carousel-control-prev" type="button" data-bs-target="#carousel{{ $kost->id }}" data-bs-slide="prev">
                                <span class="carousel-control-prev-icon"></span>
                            </button>
                            <button class="carousel-control-next" type="button" data-bs-target="#carousel{{ $kost->id }}" data-bs-slide="next">
                                <span class="carousel-control-next-icon"></span>
                            </button>
                            @endif
                        </div>
                        @endif

                        <h6><i class="bi bi-info-circle"></i> Informasi Kost</h6>
                        <table class="table table-borderless">
                            <tr>
                                <td width="30%"><strong>Nama Kost</strong></td>
                                <td>{{ $kost->name }}</td>
                            </tr>
                            <tr>
                                <td><strong>Tipe Kamar</strong></td>
                                <td><span class="badge bg-primary">{{ $kost->type }}</span></td>
                            </tr>
                            <tr>
                                <td><strong>Lokasi</strong></td>
                                <td>
                                    {{ $kost->location }}
                                    @if($kost->google_maps_link)
                                        <a href="{{ $kost->google_maps_link }}" target="_blank" class="btn btn-sm btn-outline-primary ms-2">
                                            <i class="bi bi-map"></i> Lihat di Maps
                                        </a>
                                    @endif
                                </td>
                            </tr>
                            <tr>
                                <td><strong>Maksimal Penghuni</strong></td>
                                <td>{{ $kost->max_occupants }} orang</td>
                            </tr>
                            <tr>
                                <td><strong>Harga</strong></td>
                                <td class="price-tag">Rp {{ number_format($kost->price, 0, ',', '.') }}/bulan</td>
                            </tr>
                        </table>

                        @if($kost->description)
                        <h6 class="mt-3"><i class="bi bi-file-text"></i> Deskripsi</h6>
                        <p>{{ $kost->description }}</p>
                        @endif

                        <h6 class="mt-3"><i class="bi bi-star-fill"></i> Fasilitas</h6>
                        <div>
                            @foreach(explode(',', $kost->facilities) as $facility)
                                <span class="facility-item">{{ trim($facility) }}</span>
                            @endforeach
                        </div>

                        <h6 class="mt-4"><i class="bi bi-telephone-fill"></i> Hubungi Pemilik</h6>
                        <div>
                            @if($kost->contact_whatsapp)
                            <a href="https://wa.me/{{ preg_replace('/[^0-9]/', '', $kost->contact_whatsapp) }}" target="_blank" class="btn btn-success contact-btn">
                                <i class="bi bi-whatsapp"></i> WhatsApp
                            </a>
                            @endif
                            @if($kost->contact_instagram)
                            <a href="https://instagram.com/{{ $kost->contact_instagram }}" target="_blank" class="btn btn-danger contact-btn">
                                <i class="bi bi-instagram"></i> Instagram
                            </a>
                            @endif
                            @if($kost->contact_facebook)
                            <a href="https://facebook.com/{{ $kost->contact_facebook }}" target="_blank" class="btn btn-primary contact-btn">
                                <i class="bi bi-facebook"></i> Facebook
                            </a>
                            @endif
                        </div>
                    </div>
                    <div class="modal-footer">
                        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Tutup</button>
                    </div>
                </div>
            </div>
        </div>
        @empty
        <div class="col-12">
            <div class="alert alert-info text-center">
                <i class="bi bi-info-circle fs-1"></i>
                <h5 class="mt-3">Belum ada kost tersedia</h5>
                <p>Silakan coba dengan filter yang berbeda atau periksa kembali nanti.</p>
            </div>
        </div>
        @endforelse
    </div>
</div>
@endsection