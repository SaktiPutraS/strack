<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class SimpleLoginController extends Controller
{
    public function showLoginForm()
    {
        return view('login');
    }

    public function login(Request $request)
    {
        $password = $request->input('password');

        if ($password === '123698') {
            // Simpan status login di session
            session(['simple_logged_in' => true]);
            return redirect('/');
        } else {
            return back()->with('error', 'PIN yang Anda masukkan salah!');
        }
    }

    public function logout(Request $request)
    {
        $request->session()->forget('simple_logged_in');
        return redirect('/login');
    }
}
