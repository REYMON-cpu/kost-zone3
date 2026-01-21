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
            
            // Cek apakah email sudah diverifikasi
            if (!$owner->hasVerifiedEmail()) {
                Auth::guard('owner')->logout();
                return back()->withErrors(['email' => 'Email Anda belum diverifikasi. Silakan cek inbox email Anda.']);
            }
            
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
    
    public function verify(Request $request, $id, $hash)
    {
        $owner = Owner::findOrFail($id);

        if (!hash_equals((string) $hash, sha1($owner->getEmailForVerification()))) {
            abort(403, 'Invalid verification link.');
        }

        if ($owner->hasVerifiedEmail()) {
            return redirect()->route('home')->with('info', 'Email sudah diverifikasi sebelumnya.');
        }

        $owner->markEmailAsVerified();

        Auth::guard('owner')->login($owner);

        return redirect()->route('owner.dashboard')->with('success', 'Email berhasil diverifikasi! Selamat datang di Kost Zone.');
    }
    
    public function resendVerification(Request $request)
    {
        $request->validate(['email' => 'required|email']);
        
        $owner = Owner::where('email', $request->email)->first();
        
        if (!$owner) {
            return back()->withErrors(['email' => 'Email tidak ditemukan.']);
        }
        
        if ($owner->hasVerifiedEmail()) {
            return back()->with('info', 'Email sudah diverifikasi.');
        }
        
        $owner->sendEmailVerificationNotification();
        
        return back()->with('success', 'Email verifikasi telah dikirim ulang!');
    }
}