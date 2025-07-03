<?php

namespace App\Http\Controllers;

use App\Models\MaterialSession;
use App\Models\Meeting;
use App\Models\StudentTaskSession;
use App\Models\TaskSession;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentMeetingController extends Controller
{
    public function index(){
        $user = Auth::user();
        $studentId = $user->userable->id;

        $sessionMeeting = Meeting::with('teacher')
        ->get()
        ->groupBy('teacher.name');

        $allTaskSessions = collect();
        foreach ($sessionMeeting as $teacherName => $sessions) {
            foreach ($sessions as $data) {
                $sessionTask = TaskSession::where('meeting_id', $data->id)->get();
                $allTaskSessions = $allTaskSessions->merge($sessionTask);
            }
        }

        $studentTasks = StudentTaskSession::whereIn('task_session_id', $allTaskSessions->pluck('id'))
        ->where('student_id', $studentId)
        ->get();

        $jumlahTugas = $studentTasks->count();
        $jumlahTugasSelesai = $studentTasks->where('status', 'finished')->count();
        $progress = $jumlahTugas > 0 ? ($jumlahTugasSelesai / $jumlahTugas) * 100 : 0;
        
        return view('siswa.list-teachers', compact('sessionMeeting', 'progress'));
    }

    public function listMeeting($idTeacher){
        $user = Auth::user();
        $studentId = $user->userable->id;

        $decryptedIdTeacher = decrypt($idTeacher);
        $teacher = Teacher::find($decryptedIdTeacher);

        $sessionMeeting = Meeting::where('created_by', $teacher->nip)
        ->with('teacher')
        ->get();

        $materialSession = collect();
        $taskSession = collect();

        $taskSessionId = collect();

        foreach ($sessionMeeting as $data) {
            $material = MaterialSession::where('meeting_id', $data->id)
            ->get();
            $materialSession->put($data->id, $material);

            $task = TaskSession::where('meeting_id', $data->id)
            ->get();
            $taskSession->put($data->id, $task);

            $taskSessionId = $taskSessionId->merge($task->pluck('id'));
        }

        $progress = [];

        foreach ($taskSession as $meetingId => $tasks) {
            foreach ($tasks as $task) {
                $studentTasks = StudentTaskSession::where('task_session_id', $task->id)
                    ->where('student_id', $studentId)
                    ->get();

                $jumlahTugas = $studentTasks->count();
                $jumlahTugasSelesai = $studentTasks->where('status', 'finished')->count();

                $progressTask = $jumlahTugas > 0 ? ($jumlahTugasSelesai / $jumlahTugas) * 100 : 0;

                $progress[$task->id] = $progressTask;
            }
        }

        $progressMeeting = [];

        foreach ($taskSession as $meetingId => $tasks) {
            $totalProgress = 0;
            $jumlahTask = count($tasks);

            foreach ($tasks as $task) {
                $totalProgress += $progress[$task->id] ?? 0;
            }

            $progressMeeting[$meetingId] = $jumlahTask > 0 ? ($totalProgress / $jumlahTask) : 0;
        }

        return view('siswa.list-meetings', compact('sessionMeeting', 'materialSession', 'taskSession', 'progressMeeting'));
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

        $preTest = $sessionTask->where('type', 'pretest')->first();
        $isPreTestDone = false;

        if ($preTest) {
            $studentPreTest = StudentTaskSession::where('task_session_id', $preTest->id)
                ->where('student_id', $studentId)
                ->first();

            $isPreTestDone = $studentPreTest && $studentPreTest->status === 'finished';
        }

        return view('siswa.detail-meetings', compact('meeting', 'sessionMaterial', 'sessionTask', 'isPreTestDone'));
    }
}
