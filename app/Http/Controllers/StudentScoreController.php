<?php

namespace App\Http\Controllers;

use App\Models\StudentTaskSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentScoreController extends Controller
{
    public function index(){
        $user = Auth::user();
        $studentId = $user->userable->id;

        $taskSession = StudentTaskSession::with('taskQuestion', 'taskAnswer')
        ->where('student_id', $studentId)
        ->get()
        ->groupBy('task_session_id');

        return view('siswa.scores', compact('taskSession'));
    }
}
