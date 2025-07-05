<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\School;
use App\Models\Student;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ManageStudentController extends Controller
{
    public function index()
    {
        $data_siswa = Student::with('classroom')->get();
        $data_kelas = Classroom::all();
        return view('admin.manage-students.index', compact('data_siswa', 'data_kelas'));
    }

    // public function getClasses(Request $request)
    // {
    //     if ($request->input('school_id')) {
    //         $decryptedId = Crypt::decrypt($request->input('school_id'));
    //         $schoolId = $decryptedId;
    //         $classes = Classroom::where('school_id', $schoolId)->get(); 
            
    //         return response()->json([
    //             'classes' => $classes
    //         ]);
    //     } elseif ($request->input('school_edit')){
    //         $classes = Classroom::where('school_id', $request->input('school_edit'))->get();

    //         return response()->json([
    //             'classes' => $classes
    //         ]);
    //     } else {
    //         return response()->json([
    //             'classes' => []
    //         ]);
    //     }
    // }

    public function store(Request $request)
    {
        $request->validate([
            'student_nisn' => 'required|numeric|unique:students,nisn',
            'student_name' => 'required',
            'classroom' => 'required',
            'student_gender' => 'required',
            'student_address' => 'required',
            'student_birthday' => 'required'
        ]);

        try {
            DB::beginTransaction();
            
            $student_birthday = Carbon::parse($request->student_birthday)->format('Y-m-d');

            $student = new Student();
            $student->nisn = $request->student_nisn;
            $student->name = $request->student_name;
            $student->class_id = $request->classroom;
            $student->gender = $request->student_gender;
            $student->address = $request->student_address;
            $student->birthday = $student_birthday;
            $student->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menambahkan data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan data!');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'student_nisn_edit' => 'required|numeric',
            'student_name_edit' => 'required',
            'classroom_edit' => 'required',
            'student_gender_edit' => 'required',
            'student_address_edit' => 'required',
            'student_birthday_edit' => 'required'
        ]);

        try {
            DB::beginTransaction();
            
            $student_birthday = Carbon::parse($request->student_birthday_edit)->format('Y-m-d');

            $student = Student::find($request->student_id);
            $student->nisn = $request->student_nisn_edit;
            $student->name = $request->student_name_edit;
            $student->class_id = $request->classroom_edit;
            $student->gender = $request->student_gender_edit;
            $student->address = $request->student_address_edit;
            $student->birthday = $student_birthday;
            $student->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil mengupdate data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengupdate data!');
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            
            $student = Student::find($id);
            $student->delete();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menghapus data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data!');
        }
    }
}
