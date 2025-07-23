<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\TaskSession;
use App\Models\TaskQuestion;
use App\Models\StudentTaskSession;
use App\Models\Meeting;
use App\Exports\ScoreExport;
use Spatie\SimpleExcel\SimpleExcelWriter;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

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

    public function detail(Request $request, $idModul){
        $decryptedModul = decrypt($idModul);

        $accessFilter = $request->get('access', 'system');
    
        if (!in_array($accessFilter, ['system', 'non_system'])) {
            $accessFilter = 'system';
        }

        $taskSession = TaskSession::where('meeting_id', $decryptedModul)->first();

        $dataSiswa = StudentTaskSession::join('students', 'student_id', '=', 'students.id')
        ->join('classrooms', 'students.class_id', '=', 'classrooms.id')
        ->join('task_sessions', 'task_sessions.id', '=', 'student_task_sessions.task_session_id')
        ->join('meetings', 'meetings.id', '=', 'task_sessions.meeting_id')
        ->where('meetings.id', $decryptedModul)
        ->where('student_task_sessions.access', $accessFilter)
        ->select(
            'student_task_sessions.*', 
            'students.name',
            'classrooms.class_name',
            'task_sessions.type',
            )
        ->get();

        return view('guru.manage-scores.detail', compact('dataSiswa', 'taskSession', 'accessFilter'));
    }

    public function exportExcel($idModul){
        $decryptedModul = decrypt($idModul);

        $taskSession = TaskSession::where('meeting_id', $decryptedModul)->first();

        $dataSiswa = StudentTaskSession::join('students', 'student_id', '=', 'students.id')
            ->join('classrooms', 'students.class_id', '=', 'classrooms.id')
            ->join('task_sessions', 'task_sessions.id', '=', 'student_task_sessions.task_session_id')
            ->join('meetings', 'meetings.id', '=', 'task_sessions.meeting_id')
            ->where('meetings.id', $decryptedModul)
            ->select(
                'students.name as Nama Siswa',
                'classrooms.class_name as Kelas',
                'task_sessions.type as Tipe Tugas',
                'meetings.title as Modul', 
                'student_task_sessions.score as Nilai',
                DB::raw("CASE WHEN student_task_sessions.access = 'system' THEN 'Kelas Eksperimen' ELSE 'Kelas Kontrol' END as Tipe_Kelas")
            )
            ->get()
            ->toArray();

        $modulTitle = $taskSession->meeting->title ?? 'modul';    

        // $filename = storage_path('app/public/excel' . Str::slug($modulTitle) . '.xlsx');
        $filename = (Str::slug($modulTitle) . '.xlsx');

        SimpleExcelWriter::create($filename)
            ->addRows($dataSiswa);

        return response()->download($filename)->deleteFileAfterSend();
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
            'correct_elements' => 'required|numeric',
        ]);

        try {
            DB::beginTransaction();

            $sessionSiswa = StudentTaskSession::find($request->student_session);
            $question = TaskQuestion::where('task_session_id', $sessionSiswa->task_session_id)->get();
            $totalElements = 0;

            foreach ($question as $data) {
                $flowchart = json_decode($data->correct_answer);

                $totalLinks = count($flowchart->linkDataArray);
                $totalNodes = count($flowchart->nodeDataArray);
                $totalElements += $totalLinks + $totalNodes;
            }

            if ($request->correct_elements > $totalElements) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Elemen benar tidak boleh lebih dari ' . $totalElements
                ], 422);
            }

            $sessionSiswa->score = $totalElements > 0 ? round(($request->correct_elements / $totalElements) * 100, 2) : 0;
            $sessionSiswa->duration = ($request->time * 60);
            $sessionSiswa->total_elements = $totalElements;
            $sessionSiswa->correct_elements = $request->correct_elements;
            $sessionSiswa->save();
            
            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Data berhasil disimpan!',
                'redirect' => route('detail-moduls', $idModul),
            ]);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan data!');
        }
    }
}
