<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class BuyerAuthController
{
    public function showLogin()
    {
        return view('buyer_login');
    }

    public function login(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email',
            'password' => 'required|string'
        ]);

        $user = User::where('email', $data['email'])->where('role', 'buyer')->first();
        if (! $user || ! Hash::check($data['password'], $user->password)) {
            return redirect('/buyer/login')->with('error', 'Email atau password salah');
        }

        session(['buyer_user_id' => $user->id]);
        return redirect('/buyer');
    }

    public function logout()
    {
        session()->forget('buyer_user_id');
        return redirect('/buyer/login');
    }

    public function showRegister()
    {
        return view('buyer_register');
    }

    public function register(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string',
            'email' => 'required|email|unique:users,email',
            'password' => 'required|string|min:6|confirmed'
        ]);

        $user = User::create([
            'name' => $data['name'],
            'email' => $data['email'],
            'password' => bcrypt($data['password']),
            'role' => 'buyer'
        ]);

        session(['buyer_user_id' => $user->id]);
        return redirect('/buyer');
    }
}
