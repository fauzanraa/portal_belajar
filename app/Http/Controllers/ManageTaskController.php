<?php

namespace App\Http\Controllers;

use App\Models\Meeting;
use App\Models\StudentTaskSession;
use App\Models\TaskQuestion;
use App\Models\TaskSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ManageTaskController extends Controller
{
    public function index($id){
        $decryptedId = Crypt::decrypt($id);
        $data_tugas = TaskSession::find($decryptedId);
        $data_soal = TaskQuestion::where('task_session_id', $decryptedId)->get();
        $sesi_tugas_siswa = StudentTaskSession::with(['student.classroom'])
        ->where('task_session_id', $decryptedId)
        ->get()
        ->groupBy('student.classroom.class_name');

        return view('guru.manage-meetings.detail-task', compact('data_tugas', 'data_soal', 'sesi_tugas_siswa'));
    }

    public function store($id, Request $request){
        $request->validate([
            'task' => 'required',
            'type' => 'required',
            'start' => 'required|date',
            'end' => 'required|date',
            'time_start' => 'required',
            'time_end' => 'required',
            'duration' => 'required|numeric'
        ]);

        try {
            DB::beginTransaction();
            
            $decryptedId = Crypt::decrypt($id);
            $data_pertemuan = Meeting::find($decryptedId);
            $start = Carbon::parse($request->start)->format('Y-m-d');
            $time_start = Carbon::parse($request->time_start)->format('H:i');
            $open_at = Carbon::createFromFormat('Y-m-d H:i', $start . ' ' . $time_start);
            $end = Carbon::parse($request->end)->format('Y-m-d');
            $time_end = Carbon::parse($request->time_end)->format('H:i');
            $close_at = Carbon::createFromFormat('Y-m-d H:i', $end . ' ' . $time_end);

            $task = new TaskSession();
            $task->meeting_id = $data_pertemuan->id;
            $task->type = $request->type;
            $task->name = $request->task;
            $task->open_at = $open_at;
            $task->close_at = $close_at;
            $task->created_by = Auth::user()->userable->nip;
            $task->duration = $request->duration;
            $task->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Task created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create task: ' . $e->getMessage());
        }
    }

    public function question($id){
        $decryptedId = Crypt::decrypt($id);
        $data_tugas = TaskSession::find($decryptedId);

        return view('guru.manage-meetings.question', compact('data_tugas'));
    }

    public function storeQuestion($id, Request $request){    
        $request->validate([
            'question_*' => 'required',
            'type_*' => 'required'
        ]);
        
        try {
            DB::beginTransaction();
            
            $decryptedId = Crypt::decrypt($id);
            $data_tugas = TaskSession::find($decryptedId);
            
            for ($i=0; $i < $request->total_question; $i++) { 
                $question = new TaskQuestion();
                $question->task_session_id = $data_tugas->id;
                $question->question = $request->question[$i];
                $question->type = $request->type[$i];
                $question->save();
            }
            
            DB::commit();

            return redirect()->route('detail-tasks', ['id' => $id])->with('success', 'Question created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create question: ' . $e->getMessage());
        }
    }
}
