<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class HomepageController extends Controller
{
    public function welcome(){
        return view('welcome');
    }

    public function indexAdmin(){
        $jumlah_siswa = Student::count();
        $jumlah_guru = Teacher::count();
        return view('admin.index', compact('jumlah_siswa', 'jumlah_guru'));
    }

    public function indexGuru(){
        $user = Auth::user();

        return view('guru.index', compact('user'));
    }

    public function indexSiswa(){
        $user = Auth::user();

        return view('siswa.index', compact('user'));
    }
}
