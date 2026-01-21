<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Kost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class AdminKostController extends Controller
{
    public function index(Request $request)
    {
        $query = Kost::with('owner');

        // Filter berdasarkan status approval
        if ($request->filled('status')) {
            $query->where('approval_status', $request->status);
        }

        // Filter berdasarkan is_active
        if ($request->filled('active')) {
            $query->where('is_active', $request->active === 'yes' ? true : false);
        }

        // Search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('location', 'like', "%{$search}%")
                  ->orWhereHas('owner', function($q2) use ($search) {
                      $q2->where('name', 'like', "%{$search}%");
                  });
            });
        }

        $kosts = $query->latest()->paginate(20);

        return view('admin.kosts', compact('kosts'));
    }

    public function show(Kost $kost)
    {
        $kost->load('owner', 'approvedBy');
        return view('admin.kost-detail', compact('kost'));
    }

    public function approve(Kost $kost)
    {
        $kost->update([
            'approval_status' => 'approved',
            'approved_at' => now(),
            'approved_by' => auth()->guard('admin')->id(),
            'rejection_reason' => null,
        ]);

        return back()->with('success', 'Kost berhasil disetujui');
    }

    public function reject(Request $request, Kost $kost)
    {
        $request->validate([
            'rejection_reason' => 'required|string|max:500'
        ]);

        $kost->update([
            'approval_status' => 'rejected',
            'rejection_reason' => $request->rejection_reason,
            'approved_at' => null,
            'approved_by' => null,
        ]);

        return back()->with('success', 'Kost ditolak dengan alasan: ' . $request->rejection_reason);
    }

    public function destroy(Kost $kost)
    {
        // Hapus semua foto
        if (!empty($kost->images)) {
            foreach ($kost->images as $image) {
                Storage::disk('public')->delete($image);
            }
        }

        $kostName = $kost->name;
        $kost->delete();

        return redirect()->route('admin.kosts')->with('success', "Kost '{$kostName}' berhasil dihapus");
    }
}