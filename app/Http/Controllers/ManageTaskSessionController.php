<?php

namespace App\Http\Controllers;

use App\Models\Student;
use App\Models\StudentTaskSession;
use App\Models\TaskSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ManageTaskSessionController extends Controller
{
    public function index($id){
        $user = Auth::user();
        $data_siswa = Student::with('classroom')->get();

        return view('guru.manage-meetings.task-session', compact('user', 'data_siswa', 'id'));
    }

    public function store($id, Request $request){
        // dd($request->all());
        $request->validate([
            'student_id' => 'required'
        ]);

        try {
            DB::beginTransaction();
            
            $decryptedId = Crypt::decrypt($id);
            $data_tugas = TaskSession::find($decryptedId);

            foreach ($request->student_id as $studentId) {
                $session = new StudentTaskSession();
                $session->task_session_id = $data_tugas->id;
                $session->student_id = $studentId;
                $session->score = 0;
                $session->status = 'in_progress';
                $session->duration = 0;
                $session->finished_at = null;
                $session->save();
            }
            
            DB::commit();

            return redirect()->route('detail-tasks', $id)->with('success', 'Session created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create session: ' . $e->getMessage());
        }
    }
}
