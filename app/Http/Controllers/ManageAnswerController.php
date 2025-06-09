<?php

namespace App\Http\Controllers;

use App\Models\TaskQuestion;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;

class ManageAnswerController extends Controller
{
    public function index($id){
        $decryptedId = Crypt::decrypt($id);
        $data_soal = TaskQuestion::find($decryptedId);
        $encryptedTask = Crypt::encrypt($data_soal->task_session_id);
        $encryptedQuestion = Crypt::encrypt($data_soal->id);

        return view('guru.manage-meetings.draw', compact('data_soal', 'encryptedTask', 'encryptedQuestion'));
    }

    public function store(Request $request)
    {
        try {
            $request->validate([
                'soal_id' => 'required|integer',
                'flowchart_data' => 'required|string'
            ]);

            // Validasi JSON
            $decoded = json_decode($request->flowchart_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data flowchart tidak valid'
                ], 400);
            }

            // Simpan ke database
            $question = TaskQuestion::find($request->soal_id);
            
            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Soal tidak ditemukan'
                ], 404);
            }

            $question->correct_answer = $request->flowchart_data;
            $question->save();

            return response()->json([
                'success' => true,
                'message' => 'Flowchart berhasil disimpan',
                'data' => [
                    'soal_id' => $request->soal_id,
                    'saved_at' => now()->format('Y-m-d H:i:s')
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            // Log::error('Error saving flowchart', [
            //     'error' => $e->getMessage(),
            //     'soal_id' => $request->soal_id ?? null
            // ]);

            // return response()->json([
            //     'success' => false,
            //     'message' => 'Terjadi kesalahan saat menyimpan'
            // ], 500);
        }
    }

    public function editAnswer($id)
    {
        $decryptedId = Crypt::decrypt($id);
        $data_soal = TaskQuestion::findOrFail($decryptedId);
        $encryptedTask = Crypt::encrypt($data_soal->task_session_id);
        $encryptedQuestion = Crypt::encrypt($data_soal->id);
        
        $hasCorrectAnswer = !empty($data_soal->correct_answer);
        $correctAnswerData = null;
        
        if ($hasCorrectAnswer) {
            $correctAnswerData = $data_soal->correct_answer;
            
            if (is_string($correctAnswerData)) {
                $decoded = json_decode($correctAnswerData, true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    $hasCorrectAnswer = false;
                    $correctAnswerData = null;
                }
            }
        }
        
        return view('guru.manage-meetings.draw', compact('data_soal', 'hasCorrectAnswer', 'correctAnswerData', 'encryptedTask', 'encryptedQuestion'));
    }

}
