<?php

namespace App\Http\Controllers;

use App\Models\RoleUser;
use App\Models\School;
use App\Models\Admin;
use App\Models\Teacher;
use App\Models\Student;
use App\Models\UserSystem;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;

class ManageUserController extends Controller
{
    public function index(){
        $data_user = UserSystem::with('userable')->get();   

        return view('admin.manage-users.index', compact('data_user'));
    }

    public function sync(Request $request){
        $teacher = Teacher::all();
        foreach ($teacher as $data) {
            $teacher_birthday = Carbon::parse($data->birthday)->format('Ymd');
            $user_guru = UserSystem::updateOrCreate(
                [
                    'username' => $data->nip,        
                ],
                [
                    'userable_id' => $data->id,
                    'userable_type' => 'App\Models\Teacher',
                    'password' => Hash::make($teacher_birthday)
                ]
            )->id;

            RoleUser::updateOrCreate(
                [
                    'user_system_id' => $user_guru,
                    'role_id' => 2
                ]
            );
        }

        $student = Student::all();
        foreach ($student as $data) {
            $student_birthday = Carbon::parse($data->birthday)->format('Ymd');
            $user_data = UserSystem::updateOrCreate(
                [
                    'username' => $data->nisn,        
                ],
                [
                    'userable_id' => $data->id,
                    'userable_type' => 'App\Models\Student',
                    'password' => Hash::make($student_birthday)
                ]
            )->id;

            RoleUser::updateOrCreate(
                [
                    'user_system_id' => $user_data,
                    'role_id' => 3
                ]
            );
        }

        return back();
    }

    public function update(Request $request){
        try {
                $request->validate([
                'user_id' => 'required',
                'username' => 'required',
                'password' => 'min:5',
            ]);

            DB::beginTransaction();

            $user = UserSystem::find($request->user_id);
            $user->username = $request->username;
            if($request->new_password){
                $user->password = Hash::make($request->new_password);
            }
            $user->save();

            DB::commit();

            return redirect()->back()->with('success', 'Berhasil mengudpate data!');
        } catch (\Throwable $th) {
            return redirect()->back()->with('success', 'Gagal mengudpate data!');
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            
            $role_user = RoleUser::where('user_system_id', $id)->delete();

            $user = UserSystem::find($id);
            $user->delete();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menghapus data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menghapus data!');
        }
    }
}
