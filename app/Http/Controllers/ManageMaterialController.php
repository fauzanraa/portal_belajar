<?php

namespace App\Http\Controllers;

use App\Models\MaterialSession;
use App\Models\Meeting;
use App\Models\StudentMaterialSession;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ManageMaterialController extends Controller
{
    public function index($id){
        $user = Auth::user();
        $decryptedId = Crypt::decrypt($id);
        $data_materi = MaterialSession::find($decryptedId);
        $sesi_materi_siswa = StudentMaterialSession::with(['student.classroom'])
        ->where('material_session_id', $decryptedId)
        ->get()
        ->groupBy('student.classroom.class_name');

        return view('guru.manage-meetings.detail-material', compact('user', 'data_materi', 'sesi_materi_siswa'));
    }

    public function store($id, Request $request){
        $request->validate([
            'material' => 'required',
            'file' => 'file|mimes:pdf|max:10000',
        ]);

        try {
            DB::beginTransaction();
            
            $decryptedId = Crypt::decrypt($id);
            $data_pertemuan = Meeting::find($decryptedId);
            date_default_timezone_set('Asia/Jakarta');
            $cur_time = date('hisdmY');

            $material = new MaterialSession();
            $material->meeting_id = $data_pertemuan->id;
            $material->name = $request->material;
            if($request->file_material == true){
                $file_name = 'materi_' . $cur_time . '.' . $request->file_material->extension();
                $path = $request->file_material->storeAs('public/assets/materials',$file_name);
                $material->file = $file_name;
            } else {
                $material->file = '';
            }
            $material->created_by = Auth::user()->userable->nip;
            $material->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Material created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create material: ' . $e->getMessage());
        }
    }

    public function storeFile($id, Request $request){
        $request->validate([
            'file' => 'file|mimes:pdf|max:10000',
        ]);

        try {
            DB::beginTransaction();
            
            $decryptedId = Crypt::decrypt($id);
            $data_materi = Meeting::find($decryptedId);
            date_default_timezone_set('Asia/Jakarta');
            $cur_time = date('hisdmY');

            $material = MaterialSession::find($decryptedId);

            if (!Storage::disk('public')->exists('assets/materials')) {
                Storage::disk('public')->makeDirectory('assets/materials');
            }

            if($request->file_material == true){
                $file_name = 'materi_' . $cur_time . '.' . $request->file_material->extension();
                $path = $request->file_material->storeAs('public/assets/materials',$file_name);
                $material->file = $file_name;
            } elseif($request->file_material_update == true) {
                Storage::delete('public/assets/materials/' .$material->file);

                $file_name = 'materi_' . $cur_time . '.' . $request->file_material_update->extension();
                $path = $request->file_material_update->storeAs('public/assets/materials',$file_name);
                $material->file = $file_name;
            } else {
                $material->file = '';
            }
            $material->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Material created successfully!' .$path);
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create material: ' . $e->getMessage());
        }
    }
}
