<?php

namespace App\Http\Controllers;

use App\Models\StudentTaskSession;
use App\Models\TaskAnswer;
use App\Models\TaskQuestion;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentAnswerController extends Controller
{
    public function index($id){
        $user = Auth::user();
        $studentId = $user->userable->id;

        // $decryptedId = decrypt($id);
        $sessionTask = StudentTaskSession::with('taskSession', 'taskQuestion', 'taskAnswer')
        ->where('task_session_id', $id)
        ->get();
        
        foreach ($sessionTask as $task) {
            if ($task->taskSession) {
                $teacher = Teacher::where('nip', $task->taskSession->created_by)->first();
                $encryptedTeacher = encrypt($teacher->nip);
            }
        }

        foreach ($sessionTask as $task) {
            if ($task->taskSession) {
                $encryptedMeeting = encrypt($task->taskSession->id);
            }
        }

        return view('siswa.draw', compact('sessionTask', 'studentId', 'encryptedTeacher', 'encryptedMeeting'));
    }

    public function store($id, Request $request){
        try {
            // Validasi data yang masuk
            $request->validate([
                'question_id' => 'required|array',
                'student_id' => 'required',
                'flowchart_data' => 'required|string'
            ]);

            // Validasi JSON flowchart
            $decoded = json_decode($request->flowchart_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data flowchart tidak valid'
                ], 400);
            }

            // Ambil question_id pertama saja dan pastikan data valid
            $firstQuestion = $request->question_id[0] ?? null;
            if (!$firstQuestion || !isset($firstQuestion['question_id'])) {
                return response()->json([
                    'success' => false,
                    'message' => 'Invalid question_id data'
                ], 400);
            }

            // Menyimpan jawaban siswa
            $answer = new TaskAnswer();
            $answer->task_session_id = $id;
            $answer->task_question_id = $firstQuestion['question_id'];
            $answer->student_id = $request->student_id;
            $answer->answer = $request->flowchart_data;
            $answer->save();

            // Ambil jawaban yang benar dari tabel TaskQuestion
            $question = TaskQuestion::where('task_session_id', $id)->first();
            $correctAnswer = $question->correct_answer;
            $studentAnswer = $answer->answer;

            // Dekode jawaban JSON
            $correctAnswerData = json_decode($correctAnswer, true);
            $studentAnswerData = json_decode($studentAnswer, true);

            // Hitung jumlah node dan link yang benar
            $correctNodes = 0;
            $correctLinks = 0;
            $totalNodes = count($correctAnswerData['nodeDataArray'] ?? []); 
            $totalLinks = count($correctAnswerData['linkDataArray'] ?? []); 

            // Bandingkan node
            foreach ($correctAnswerData['nodeDataArray'] as $index => $node) {
                // Pastikan ada data di jawaban siswa
                if (isset($studentAnswerData['nodeDataArray'][$index])) {
                    // Hanya bandingkan key dan text
                    $correctNode = [
                        'key' => $node['key'],
                        'text' => $node['text']
                    ];
                    $studentNode = [
                        'key' => $studentAnswerData['nodeDataArray'][$index]['key'],
                        'text' => $studentAnswerData['nodeDataArray'][$index]['text']
                    ];

                    // Bandingkan key dan text
                    if (json_encode($correctNode) == json_encode($studentNode)) {
                        $correctNodes++;
                    }
                }
            }

            // Bandingkan link
            foreach ($correctAnswerData['linkDataArray'] as $index => $link) {
                // Pastikan ada data di jawaban siswa
                if (isset($studentAnswerData['linkDataArray'][$index])) {
                    // Hanya bandingkan to dan from
                    $correctLink = [
                        'from' => $link['from'],
                        'to' => $link['to']
                    ];
                    $studentLink = [
                        'from' => $studentAnswerData['linkDataArray'][$index]['from'],
                        'to' => $studentAnswerData['linkDataArray'][$index]['to']
                    ];

                    // Bandingkan from dan to
                    if (json_encode($correctLink) == json_encode($studentLink)) {
                        $correctLinks++;
                    }
                }
            }

            // Hitung skor
            $correctElements = $correctNodes + $correctLinks;
            $totalElements = $totalNodes + $totalLinks;
            $percentageScore = ($correctElements / $totalElements) * 100;

            // Update skor dan status pada taskSession
            $taskSession = StudentTaskSession::where('task_session_id', $id)
                ->where('student_id', $request->student_id)
                ->first();
            if (!$taskSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task session not found'
                ], 404);
            }

            $taskSession->score = $percentageScore;
            $taskSession->status = 'finished';
            $taskSession->duration = 5;
            $taskSession->finished_at = now();
            $taskSession->save();

            // Respons sukses
            return response()->json([
                'success' => true,
                'message' => 'Flowchart berhasil disimpan'
            ]);
        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json([
                'success' => false,
                'message' => 'Data tidak valid',
                'errors' => $e->errors()
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()
            ], 500);
        }
        }
}
