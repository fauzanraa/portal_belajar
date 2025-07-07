<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\TaskSession;
use App\Models\StudentTaskSession;
use App\Models\Meeting;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;

class ManageScoreController extends Controller
{
    public function index(){
        $user = Auth::user();
        $teacher = $user->userable;

        $sessionMeeting = Meeting::where('created_by', $teacher->nip)
        ->with('teacher')
        ->get();

        return view('guru.manage-scores.index', compact('sessionMeeting'));
    }

    public function detail($idModul){
        $decryptedModul = decrypt($idModul);

        $taskSession = TaskSession::where('meeting_id', $decryptedModul)->first();

        $dataSiswa = StudentTaskSession::join('students', 'student_id', '=', 'students.id')
        ->join('classrooms', 'students.class_id', '=', 'classrooms.id')
        ->join('task_sessions', 'task_sessions.id', '=', 'student_task_sessions.task_session_id')
        ->where('task_session_id', $taskSession->id)
        ->select(
            'student_task_sessions.*', 
            'students.name',
            'classrooms.class_name',
            'task_sessions.type',
            )
        ->get();

        return view('guru.manage-scores.detail', compact('dataSiswa', 'taskSession'));
    }

    public function assessment($idModul, $idSession){
        $decryptedSession = decrypt($idSession);

        $decryptedModul = decrypt($idModul);

        $sessionSiswa = StudentTaskSession::join('task_sessions', 'task_sessions.id', '=', 'student_task_sessions.task_session_id')
        ->join('meetings', 'meetings.id', '=', 'task_sessions.meeting_id')
        ->where('student_task_sessions.id', $decryptedSession)
        ->select(
            'student_task_sessions.*',
            'task_sessions.type',
            'task_sessions.duration',
            'meetings.title'
        )
        ->first();

        $dataSiswa = Student::find($sessionSiswa->student_id); 

        return view('guru.manage-scores.assessment', compact('sessionSiswa', 'dataSiswa', 'idModul'));
    }

    public function store($idModul, Request $request){
        $request->validate([
            'student_session' => 'required',
            'score' => 'required|numeric',
            'time' => 'required|numeric',
            'correct_elements' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $sessionSiswa = StudentTaskSession::find($request->student_session);
            $sessionSiswa->score = $request->score;
            $sessionSiswa->duration = ($request->time * 60);
            $sessionSiswa->correct_elements = $request->correct_elements;
            $sessionSiswa->save();
            
            DB::commit();

            return redirect()->route('detail-moduls', $idModul)->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan data!');
        }
    }
}
