<?php

namespace App\Http\Controllers;

use App\Models\MaterialSession;
use App\Models\Meeting;
use App\Models\TaskSession;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMeetingController extends Controller
{
    public function index(){
        $user = Auth::user();
        $studentId = $user->userable->id;

        $sessionMeeting = Meeting::join('material_sessions', 'meetings.id', '=', 'material_sessions.meeting_id')
        ->join('student_material_sessions', 'material_sessions.id', '=', 'student_material_sessions.material_session_id')
        ->join('task_sessions', 'meetings.id', '=', 'task_sessions.meeting_id')
        ->join('student_task_sessions', 'task_sessions.id', '=', 'student_task_sessions.task_session_id')
        ->where('student_material_sessions.student_id', $studentId) 
        ->orWhere('student_task_sessions.student_id', $studentId)
        ->select('meetings.*')
        ->with('teacher')
        ->get()
        ->groupBy('teacher.name');

        return view('siswa.list-teachers', compact('sessionMeeting'));
    }

    public function listMeeting($idTeacher){
        $user = Auth::user();
        $studentId = $user->userable->id;

        $decryptedIdTeacher = decrypt($idTeacher);
        $teacher = Teacher::find($decryptedIdTeacher);

        $sessionMeeting = Meeting::join('material_sessions', 'meetings.id', '=', 'material_sessions.meeting_id')
        ->join('student_material_sessions', 'material_sessions.id', '=', 'student_material_sessions.material_session_id')
        ->join('task_sessions', 'meetings.id', '=', 'task_sessions.meeting_id')
        ->join('student_task_sessions', 'task_sessions.id', '=', 'student_task_sessions.task_session_id')
        ->where('student_material_sessions.student_id', $studentId || 'student_task_sessions.student_id', $studentId)
        ->where('meetings.created_by', $teacher->nip)
        ->with('teacher')
        ->get();

        return view('siswa.list-meetings', compact('sessionMeeting'));
    }

    public function detailMeeting($idTeacher, $idMeeting){
        $user = Auth::user();
        $studentId = $user->userable->id;

        $decryptedIdTeacher = decrypt($idTeacher);
        $teacher = Teacher::where('nip', $decryptedIdTeacher)->first();

        $decryptedIdMeeting = decrypt($idMeeting);
        $meeting = Meeting::with('material', 'task')->find($decryptedIdMeeting);

        $sessionMaterial = MaterialSession::with('meeting', 'studentMaterialSession')
        ->where('meeting_id', $meeting->id)
        ->where('created_by', $teacher->nip)
        ->whereHas('studentMaterialSession', function($query) use ($studentId) {
            $query->where('student_id', $studentId);
        })
        ->get();

        $sessionTask = TaskSession::with('meeting', 'studentTaskSession')
        ->where('meeting_id', $meeting->id)
        ->where('created_by', $teacher->nip)
        ->whereHas('studentTaskSession', function($query) use ($studentId) { 
            $query->where('student_id', $studentId); 
        })
        ->get();

        return view('siswa.detail-meetings', compact('sessionMaterial', 'sessionTask'));
    }
}
