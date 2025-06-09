<?php

namespace App\Http\Controllers;

use App\Models\MaterialSession;
use App\Models\Student;
use App\Models\StudentMaterialSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ManageMaterialSessionController extends Controller
{
    public function index($id){
        $user = Auth::user();
        $data_siswa = Student::with('classroom')->get();

        return view('guru.manage-meetings.material-session', compact('user', 'data_siswa', 'id'));
    }

    public function store($id, Request $request){
        $request->validate([
            'student_id' => 'required'
        ]);

        try {
            DB::beginTransaction();
            
            $decryptedId = Crypt::decrypt($id);
            $data_materi = MaterialSession::find($decryptedId);

            foreach ($request->student_id as $studentId) {
                $session = new StudentMaterialSession();
                $session->material_session_id = $data_materi->id;
                $session->student_id = $studentId;
                $session->status = 'visible';
                $session->save();
            }
            
            DB::commit();

            return redirect()->route('detail-materials', $id)->with('success', 'Session created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create session: ' . $e->getMessage());
        }
    }
}
