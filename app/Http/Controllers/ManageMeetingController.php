<?php

namespace App\Http\Controllers;

use App\Models\MaterialSession;
use App\Models\Meeting;
use App\Models\TaskSession;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;

class ManageMeetingController extends Controller
{
    public function index(){
        $user = Auth::user();
        $data_pertemuan = Meeting::where('created_by', $user->userable->nip)->get();
        
        return view('guru.manage-meetings.index', compact('data_pertemuan', 'user'));
    }

    public function store(Request $request){
        $request->validate([
            'type' => 'required',
            'meeting' => 'required',
            'start' => 'required|date',
            'end' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $start = Carbon::parse($request->start)->format('Y-m-d');
            $end = Carbon::parse($request->end)->format('Y-m-d');

            $meeting = new Meeting();
            $meeting->title = $request->meeting;
            $meeting->description = $request->desc_meeting;
            $meeting->open_at = $start;
            $meeting->close_at = $end;
            $meeting->type = $request->type;
            $meeting->created_by = Auth::user()->userable->nip;
            $meeting->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menambahkan data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambahkan data!');
        }
    }

    public function update(Request $request){
        $request->validate([
            'type_edit' => 'required',
            'meeting_edit' => 'required',
            'start' => 'required|date',
            'end' => 'required|date'
        ]);

        try {
            DB::beginTransaction();

            $start = Carbon::parse($request->start)->format('Y-m-d');
            $end = Carbon::parse($request->end)->format('Y-m-d');

            $meeting = Meeting::find($request->meeting_id);
            $meeting->title = $request->meeting_edit;
            $meeting->description = $request->desc_meeting_edit;
            $meeting->open_at = $start;
            $meeting->close_at = $end;
            $meeting->type = $request->type_edit;
            $meeting->created_by = Auth::user()->userable->nip;
            $meeting->save();
            
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
            
            $meeting = Meeting::find($id);
            $meeting->delete();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menghapus data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data!');
        }
    }

    public function indexMaterial($id){
        $user = Auth::user();
        $id_meeting = $id;
        $decryptedId = Crypt::decrypt($id);
        $data_pertemuan = Meeting::find($decryptedId);
        $data_materi = MaterialSession::where('meeting_id', $data_pertemuan->id)->get();
        $data_tugas = TaskSession::where('meeting_id', $data_pertemuan->id)->get();
        $pretest = TaskSession::where('meeting_id', $data_pertemuan->id)->where('type', 'pretest')->get();
        $posttest = TaskSession::where('meeting_id', $data_pertemuan->id)->where('type', 'posttest')->get();

        return view('guru.manage-meetings.modul', compact('data_pertemuan', 'user', 'id_meeting', 'data_materi', 'data_tugas' , 'pretest', 'posttest'));
    }
}
