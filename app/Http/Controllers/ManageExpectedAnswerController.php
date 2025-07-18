<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\QuestionExpectedAnswer;
use Illuminate\Support\Facades\Crypt;


class ManageExpectedAnswerController extends Controller
{
    public function index($idTask){
        $decryptedTask = decrypt($idTask);

        $expectedAnswer = QuestionExpectedAnswer::where('task_question_id', $decryptedTask)->first();

        $encryptedQuestion = encrypt($expectedAnswer->task_question_id);
        $encryptedTask = encrypt($expectedAnswer->task_session_id);
        
        $answer = $expectedAnswer->answer;

        return view('guru.manage-meetings.expect-answer', compact('expectedAnswer' ,'answer' , 'encryptedTask', 'encryptedQuestion'));
    }

    public function update(Request $request, $idQuestion){
        try {
            $decryptedQuestion = Crypt::decrypt($idQuestion);
            
            $expectedAnswer = QuestionExpectedAnswer::where('task_question_id', $decryptedQuestion)->first();
            
            if (!$expectedAnswer) {
                return response()->json(['success' => false, 'message' => 'Data tidak ditemukan!']);
            }
            
            $expectedAnswer->answer = $request->input('answer');
            $expectedAnswer->save();
            
            return response()->json(['success' => true, 'message' => 'Berhasil mengupdate data!']);
            
        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Gagal mengupdate data!']);
        }
    }
}
