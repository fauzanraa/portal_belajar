<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(){
        return view('login');
    }

    public function postLogin(Request $request){
        $credentials = $request->only('username', 'password');

        if (Auth::attempt($credentials)) {
            $user = Auth::user();
            $roles = $user->roles->pluck('name')->toArray();

            if (in_array('admin', $roles)) {
                return redirect()->route('admin-index'); 
            } elseif (in_array('guru', $roles)) {
                return redirect()->route('teacher-index'); 
            } elseif (in_array('siswa', $roles)) {
                return redirect()->route('student-index'); 
            } else {
                return redirect()->route('login')->withErrors(['role_error' => 'Role tidak valid.']);
            }
        }
        return redirect()->route('login');
    }

    public function logout(){
        Auth::logout();

        session()->invalidate();
        session()->regenerateToken();

        return redirect()->route('login');
    }
}
