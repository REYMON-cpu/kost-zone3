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
            'contact_whatsapp' => $request->contact_whatsapp,
            'contact_instagram' => $request->contact_instagram,
            'contact_facebook' => $request->contact_facebook,
        ]);

        return redirect()->route('owner.dashboard')->with('success', 'Kost berhasil ditambahkan');
    }

    public function update(Request $request, Kost $kost)
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

        $images = $kost->images;
        if ($request->hasFile('images')) {
            // Delete old images
            foreach ($kost->images as $oldImage) {
                Storage::disk('public')->delete($oldImage);
            }
            
            $images = [];
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
}