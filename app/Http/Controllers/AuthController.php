<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController
{
    public function login(Request $request)
    {
        if ($request->isMethod('get')) {
            return view('auth.login');
        } else {
            $credentials = $request->validate([
                'email' => 'required|email',
                'password' => 'required|min:3|max:12',
            ]);

            if (Auth::attempt($credentials)) {
                $request->session()->regenerate();

                $role = Auth::user()->getRoleNames()->first();
                switch ($role) {
                    case 'owner':
                        return redirect()->intended('/owner/dashboard')->with('success', 'Login berhasil');
                    case 'admin':
                        return redirect()->intended('/admin/dashboard')->with('success', 'Login berhasil');
                    case 'warehouse':
                        return redirect()->intended('/warehouse/dashboard')->with('success', 'Login berhasil');
                    case 'sales':
                        return redirect()->intended('/sales/dashboard')->with('success', 'Login berhasil');
                    case 'customer':
                        return redirect()->intended('/customer/dashboard')->with('success', 'Login berhasil');
                    default:
                        # code...
                        break;
                }
            }

            return back()
                ->withErrors([
                    'email' => 'Email atau password salah',
                ])
                ->onlyInput('email');
        }
    }

    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect('/')->with('success', 'Anda berhasil logout');
    }

    public function me(Request $request)
    {
        // return Auth::user();
        dd(Auth::user());
    }
}
