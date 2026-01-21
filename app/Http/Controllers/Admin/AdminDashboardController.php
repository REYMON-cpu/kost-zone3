<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use App\Models\Kost;
use App\Models\PageView;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AdminDashboardController extends Controller
{
    public function index()
{
    // Statistik umum
    $stats = [
        'total_owners' => Owner::count(),
        'verified_owners' => Owner::whereNotNull('email_verified_at')->count(),
        'total_kosts' => Kost::count(),
        'active_kosts' => Kost::where('is_active', true)->count(),
        'inactive_kosts' => Kost::where('is_active', false)->count(),
        'pending_kosts' => Kost::where('approval_status', 'pending')->count(),
        'approved_kosts' => Kost::where('approval_status', 'approved')->count(),
        'rejected_kosts' => Kost::where('approval_status', 'rejected')->count(),
        'total_page_views' => PageView::count(),
        'unique_visitors' => PageView::distinct('ip_address')->count('ip_address'),
        'today_visitors' => PageView::whereDate('viewed_at', today())->distinct('ip_address')->count('ip_address'),
    ];

    // Statistik pengunjung per hari (7 hari terakhir)
    $visitorStats = PageView::select(
            DB::raw('DATE(viewed_at) as date'),
            DB::raw('COUNT(DISTINCT ip_address) as visitors')
        )
        ->where('viewed_at', '>=', now()->subDays(7))
        ->groupBy('date')
        ->orderBy('date')
        ->get();

    // Statistik kost per tipe
    $kostByType = Kost::select('type', DB::raw('count(*) as total'))
        ->groupBy('type')
        ->get();

    // Owner terbaru
    $recentOwners = Owner::latest()->take(5)->get();

    // Kost terbaru
    $recentKosts = Kost::with('owner')->latest()->take(5)->get();

    return view('admin.dashboard', compact('stats', 'visitorStats', 'kostByType', 'recentOwners', 'recentKosts'));
}
}