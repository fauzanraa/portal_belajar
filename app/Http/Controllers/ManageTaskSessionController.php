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
    public function index($id, $type){
        $user = Auth::user();

        $decryptedId = Crypt::decrypt($id);

        $dataSiswa = Student::with('classroom')->get();

        $siswaTerdaftar = StudentTaskSession::where('task_session_id', $decryptedId)
        ->pluck('student_id')
        ->toArray();

        $siswaTerdaftarPerAccess = StudentTaskSession::where('task_session_id', $decryptedId)
        ->where('access', $type)
        ->pluck('student_id')
        ->toArray();

        $filterSiswa = $dataSiswa->filter(function ($student) use ($siswaTerdaftar, $siswaTerdaftarPerAccess) {
            return !in_array($student->id, $siswaTerdaftar) || in_array($student->id, $siswaTerdaftarPerAccess);
        });

        return view('guru.manage-meetings.task-session', compact('user', 'filterSiswa', 'id', 'type', 'siswaTerdaftarPerAccess'));
    }

    public function store($id, $type, Request $request){
        try {
            DB::beginTransaction();
            
            $decryptedId = Crypt::decrypt($id);
            $dataTugas = TaskSession::find($decryptedId);

            $siswaTerdaftar = StudentTaskSession::where('task_session_id', $dataTugas->id)
            ->where('access', $type)
            ->pluck('student_id')
            ->toArray();

            $sesiSiswa = $request->student_id ?? [];

            $hapusSesi = array_diff($siswaTerdaftar, $sesiSiswa);

            if (!empty($hapusSesi)) {
                StudentTaskSession::where('task_session_id', $dataTugas->id)
                    ->where('access', $type)
                    ->whereIn('student_id', $hapusSesi)
                    ->delete();
            }

            foreach ($sesiSiswa as $studentId) {
                $exist = StudentTaskSession::where('task_session_id', $dataTugas->id)
                ->where('access', $type)
                ->where('student_id', $studentId)
                ->exists();

                if(!$exist){
                    StudentTaskSession::create([
                        'task_session_id' => $dataTugas->id,
                        'student_id' => $studentId,
                        'score' => 0,
                        'status' => 'in_progress',
                        'duration' => 0,
                        'finished_at' => null,
                        'access' => $type
                    ]);
                }
            }
            DB::commit();

            return redirect()->route('detail-tasks', $id)->with('success', 'Berhasil menambah data');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambah data!' . $e->getMessage());
        }
    }
}
