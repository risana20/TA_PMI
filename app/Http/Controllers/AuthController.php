<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Http;

class AuthController extends Controller
{
    public function login()
    {
        return view('pages.auth.login');
    }

    public function loginPost(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required',
             'g-recaptcha-response' => 'required'
        ]);

        $user = User::where('email', $request->email)->first();

        if ($user && !$user->is_active) {
            return back()->withErrors(['email' => 'Akun Anda telah dinonaktifkan.']);
        }

        if (!Auth::attempt($request->only('email', 'password'), $request->remember)) {
            return back()->withErrors(['email' => 'Email atau password salah.'])->withInput();
        }

        $request->session()->regenerate();

        $user = Auth::user();
        if ($user->hasRole('superadmin')) {
            return redirect()->route('superadmin.dashboard');
        } elseif ($user->hasRole('admin')) {
            return redirect()->route('admin.dashboard');
        }

        return redirect()->route('beranda');
    }

    public function register()
    {
        return view('pages.auth.register');
    }

    public function registerPost(Request $request)
    {
        $request->validate([
            'name'     => 'required|string|max:255',
            'email'    => 'required|email|unique:users',
            'phone'    => 'required|string|max:20',
            'address'  => 'required|string',
            'password' => 'required|min:8|confirmed',
        ]);

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'phone'    => $request->phone,
            'address'  => $request->address,
            'password' => Hash::make($request->password),
        ]);

        $user->assignRole('user');

        Auth::login($user);
        $request->session()->regenerate();

        return redirect()->route('beranda')->with('success', 'Registrasi berhasil! Selamat datang.');
    }

    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('beranda');
    }
    public function store(Request $request)
    {
        
        $response = Http::asForm()->post(
            'https://www.google.com/recaptcha/api/siteverify',
            [
                'secret' => env('RECAPTCHA_SECRET_KEY'),
                'response' => $request->input('g-recaptcha-response'),
                'remoteip' => $request->ip(),
            ]
        );

        if (!$response->json()['success']) {
        return back()->withErrors(['captcha' => 'Captcha tidak valid']);
        }

        if (!Auth::attempt($request->only('email','password'))) {
            return back()->withErrors(['email' => 'Login gagal']);
        }

        return redirect()->route('dashboard');

            // lanjut simpan data
        }
}
