<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Owner;
use Illuminate\Support\Facades\Storage;

class AdminOwnerManagementController extends Controller
{
    public function destroy(Owner $owner)
    {
        // Hapus semua kost beserta foto-fotonya
        foreach ($owner->kosts as $kost) {
            if (!empty($kost->images)) {
                foreach ($kost->images as $image) {
                    Storage::disk('public')->delete($image);
                }
            }
            $kost->delete();
        }

        $ownerName = $owner->name;
        $owner->delete();

        return redirect()->route('admin.owners')->with('success', "Owner '{$ownerName}' dan semua kost-nya berhasil dihapus");
    }
}