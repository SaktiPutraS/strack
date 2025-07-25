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
            session(['simple_logged_in' => true]);
            session(['role' => 'admin']);
            session(['admin_id' => 1]);
            return redirect('/dashboard-admin');
        } elseif ($password === '120906') {
            session(['simple_logged_in' => true]);
            session(['role' => 'user']);
            session(['user_id' => 1]);
            return redirect('/dashboard-user');
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
