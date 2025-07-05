<?php

namespace App\Http\Controllers;

use App\Models\School;
use App\Models\Teacher;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageTeacherController extends Controller
{
    public function index()
    {
        $data_guru = Teacher::all();
        return view('admin.manage-teachers.index', compact('data_guru'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'teacher_nip' => 'required|numeric|unique:teachers,nip',
            'teacher_name' => 'required',
            'teacher_gender' => 'required',
            'teacher_address' => 'required',
            'teacher_birthday' => 'required'
        ]);

        try {
            DB::beginTransaction();
            
            $teacher_birthday = Carbon::parse($request->teacher_birthday)->format('Y-m-d');

            $teacher = new Teacher();
            $teacher->nip = $request->teacher_nip;
            $teacher->name = $request->teacher_name;
            $teacher->gender = $request->teacher_gender;
            $teacher->address = $request->teacher_address;
            $teacher->birthday = $teacher_birthday;
            $teacher->save();
            
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
            'teacher_nip_edit' => 'required|numeric',
            'teacher_name_edit' => 'required',
            'teacher_gender_edit' => 'required',
            'teacher_address_edit' => 'required',
            'teacher_birthday_edit' => 'required'
        ]);

        try {
            DB::beginTransaction();

            $teacher_birthday = Carbon::parse($request->teacher_birthday_edit)->format('Y-m-d');
            
            $teacher = Teacher::find($request->teacher_id);
            $teacher->nip = $request->teacher_nip_edit;
            $teacher->name = $request->teacher_name_edit;
            $teacher->gender = $request->teacher_gender_edit;
            $teacher->address = $request->teacher_address_edit;
            $teacher->birthday = $teacher_birthday;
            $teacher->save();
            
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
            
            $teacher = Teacher::find($id);
            $teacher->delete();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menghapus data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data!');
        }
    }
}
