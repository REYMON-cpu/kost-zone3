<?php

namespace App\Http\Controllers;

use App\Models\Owner;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required',
        ]);

        if (Auth::guard('owner')->attempt($request->only('email', 'password'))) {
            $owner = Auth::guard('owner')->user();
            
            return redirect()->route('owner.dashboard');
        }

        return back()->withErrors(['email' => 'Email atau password salah']);
    }

    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:owners',
            'password' => 'required|min:6|confirmed',
            'phone' => 'nullable|string',
        ]);

        $owner = Owner::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password),
            'phone' => $request->phone,
            'email_verified_at' => now(),
        ]);

        // Trigger event untuk kirim email verifikasi
        event(new Registered($owner));

        return redirect()->route('home')->with('success', 'Registrasi berhasil! Silakan cek email Anda untuk verifikasi.');
    }

    public function logout()
    {
        Auth::guard('owner')->logout();
        return redirect()->route('home');
    }
    
}