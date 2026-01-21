<?php

namespace App\Http\Controllers;

use App\Models\Kost;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;

class KostController extends Controller
{
    

    public function store(Request $request)
{
    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'location' => 'required|string',
        'google_maps_link' => 'nullable|url',
        'type' => 'required|in:Putra,Putri,Campuran',
        'max_occupants' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'facilities' => 'required|string',
        'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        'contact_whatsapp' => 'nullable|string',
        'contact_instagram' => 'nullable|string',
        'contact_facebook' => 'nullable|string',
    ]);

    $images = [];
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('kost-images', 'public');
            $images[] = $path;
        }
    }

    Kost::create([
        'owner_id' => auth()->guard('owner')->id(),
        'name' => $request->name,
        'description' => $request->description,
        'location' => $request->location,
        'google_maps_link' => $request->google_maps_link,
        'type' => $request->type,
        'max_occupants' => $request->max_occupants,
        'price' => $request->price,
        'facilities' => $request->facilities,
        'images' => $images,
        'approval_status' => 'pending', // Otomatis pending
        'contact_whatsapp' => $request->contact_whatsapp,
        'contact_instagram' => $request->contact_instagram,
        'contact_facebook' => $request->contact_facebook,
    ]);

    return redirect()->route('owner.dashboard')->with('success', 'Kost berhasil ditambahkan dan menunggu persetujuan admin');
}

    public function update(Request $request, Kost $kost)
{
    // Cek apakah owner yang login adalah pemilik kost ini
    if ($kost->owner_id !== auth()->guard('owner')->id()) {
        abort(403, 'Unauthorized action.');
    }

    $request->validate([
        'name' => 'required|string|max:255',
        'description' => 'nullable|string',
        'location' => 'required|string',
        'google_maps_link' => 'nullable|url',
        'type' => 'required|in:Putra,Putri,Campuran',
        'max_occupants' => 'required|integer|min:1',
        'price' => 'required|numeric|min:0',
        'facilities' => 'required|string',
        'images.*' => 'image|mimes:jpeg,png,jpg|max:2048',
        'contact_whatsapp' => 'nullable|string',
        'contact_instagram' => 'nullable|string',
        'contact_facebook' => 'nullable|string',
    ]);

    // Ambil images yang ada
    $images = $kost->images ?? [];
    
    // Jika ada upload foto baru, tambahkan ke array
    if ($request->hasFile('images')) {
        foreach ($request->file('images') as $image) {
            $path = $image->store('kost-images', 'public');
            $images[] = $path;
        }
    }

    $kost->update([
        'name' => $request->name,
        'description' => $request->description,
        'location' => $request->location,
        'google_maps_link' => $request->google_maps_link,
        'type' => $request->type,
        'max_occupants' => $request->max_occupants,
        'price' => $request->price,
        'facilities' => $request->facilities,
        'images' => $images,
        'contact_whatsapp' => $request->contact_whatsapp,
        'contact_instagram' => $request->contact_instagram,
        'contact_facebook' => $request->contact_facebook,
    ]);

    return redirect()->route('owner.dashboard')->with('success', 'Kost berhasil diupdate');
}

    public function destroy(Kost $kost)
    {

        // Delete images
        foreach ($kost->images as $image) {
            Storage::disk('public')->delete($image);
        }

        $kost->delete();

        return redirect()->route('owner.dashboard')->with('success', 'Kost berhasil dihapus');
    }
    public function toggleActive(Kost $kost)
{
    // Cek apakah owner yang login adalah pemilik kost ini
    if ($kost->owner_id !== auth()->guard('owner')->id()) {
        abort(403, 'Unauthorized action.');
    }

    $kost->update([
        'is_active' => !$kost->is_active
    ]);

    $status = $kost->is_active ? 'diaktifkan' : 'dinonaktifkan';
    return redirect()->route('owner.dashboard')->with('success', "Kost berhasil {$status}");
}
public function deleteImage(Request $request, Kost $kost)
{
    // Cek apakah owner yang login adalah pemilik kost ini
    if ($kost->owner_id !== auth()->guard('owner')->id()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    $request->validate([
        'image_path' => 'required|string'
    ]);

    $images = $kost->images ?? [];
    $imagePath = $request->image_path;

    // Cek apakah gambar ada di array
    if (in_array($imagePath, $images)) {
        // Hapus file fisik
        \Illuminate\Support\Facades\Storage::disk('public')->delete($imagePath);
        
        // Hapus dari array
        $images = array_values(array_diff($images, [$imagePath]));
        
        // Update database
        $kost->update(['images' => $images]);
        
        return response()->json([
            'success' => true, 
            'message' => 'Foto berhasil dihapus',
            'remaining_images' => count($images)
        ]);
    }

    return response()->json(['success' => false, 'message' => 'Foto tidak ditemukan'], 404);
}

public function deleteAllImages(Kost $kost)
{
    // Cek apakah owner yang login adalah pemilik kost ini
    if ($kost->owner_id !== auth()->guard('owner')->id()) {
        return response()->json(['success' => false, 'message' => 'Unauthorized'], 403);
    }

    $images = $kost->images ?? [];
    
    // Hapus semua file fisik
    foreach ($images as $image) {
        \Illuminate\Support\Facades\Storage::disk('public')->delete($image);
    }
    
    // Update database
    $kost->update(['images' => []]);
    
    return response()->json([
        'success' => true, 
        'message' => 'Semua foto berhasil dihapus'
    ]);
}
}