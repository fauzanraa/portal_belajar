<?php

namespace App\Http\Controllers;

use App\Models\School;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ManageSchoolController extends Controller
{
    public function index()
    {
        $data_sekolah = School::all();
        return view('admin.manage-schools.index', compact('data_sekolah'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'school_name' => 'required',
            'school_address' => 'required',
            'school_email' => 'required|email'
        ]);

        try {
            DB::beginTransaction();
            
            $school = new School();
            $school->name_school = $request->school_name;
            $school->address = $request->school_address;
            $school->email = $request->school_email;
            $school->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'School created successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to create school: ' . $e->getMessage());
        }
    }

    public function update(Request $request)
    {
        $request->validate([
            'school_name_edit' => 'required',
            'school_address_edit' => 'required',
            'school_email_edit' => 'required|email'
        ]);

        try {
            DB::beginTransaction();
            
            $school = School::find($request->school_id);
            $school->name_school = $request->school_name_edit;
            $school->address = $request->school_address_edit;
            $school->email = $request->school_email_edit;
            $school->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'School updated successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to update school: ' . $e->getMessage());
        }
    }

    public function delete($id)
    {
        try {
            DB::beginTransaction();
            
            $school = School::find($id);
            $school->delete();
            
            DB::commit();

            return redirect()->back()->with('success', 'School deleted successfully!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Failed to delete school: ' . $e->getMessage());
        }
    }

}
