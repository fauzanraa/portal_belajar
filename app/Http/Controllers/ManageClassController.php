<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ManageClassController extends Controller
{
    public function index(){
        $data_kelas = Classroom::all();

        return view('admin.manage-schools.class', compact('data_kelas'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'classroom' => 'required',
        ]);

        try {
            DB::beginTransaction();

            $classroom = new Classroom();
            $classroom->class_name = $request->classroom;
            $classroom->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Data berhasil disimpan!');
        } catch (\Exception $e) {
            DB::rollBack();
            // return redirect()->back()->with('error', 'Gagal menyimpan data' . $e->getMessage());
            return redirect()->back()->with('error', 'Gagal menambahkan data!');
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'class_id' => 'required',
            'classroom_edit' => 'required',
        ]);

        try {
            DB::beginTransaction();
            
            $classroom = Classroom::find($request->class_id);
            $classroom->class_name = $request->classroom_edit;
            $classroom->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil mengupdate data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengupdate data!');
        }
    }

    public function delete($id)
    {
        $decryptedId = Crypt::decrypt($id);

        try {
            DB::beginTransaction();
            
            $classroom = Classroom::find($decryptedId);
            $classroom->delete();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menghapus data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data!');
        }
    }
}
