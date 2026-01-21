<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;

class HomeController extends Controller
{
    public function index(Request $request)
{
    $query = Kost::where('is_active', true); // Hanya tampilkan yang aktif

    if ($request->filled('location')) {
        $query->where('location', 'like', '%' . $request->location . '%');
    }

    if ($request->filled('max_price')) {
        $query->where('price', '<=', $request->max_price);
    }

    if ($request->filled('type') && $request->type !== 'all') {
        $query->where('type', $request->type);
    }

    $kosts = $query->orderBy('created_at', 'desc')->get();

    return view('home', compact('kosts'));
}
}