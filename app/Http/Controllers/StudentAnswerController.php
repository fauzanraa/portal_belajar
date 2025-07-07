<?php

namespace App\Http\Controllers;

use App\Models\ComponentSetting;
use App\Models\StudentTaskSession;
use App\Models\TaskAnswer;
use App\Models\TaskQuestion;
use App\Models\TaskSession;
use App\Models\Teacher;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;

class StudentAnswerController extends Controller
{
    public function index($id){
        $user = Auth::user();
        $studentId = $user->userable->id;
        $encryptedStudent = encrypt($studentId);

        $taskId = decrypt($id);
        $sessionTask = StudentTaskSession::with('taskSession', 'taskQuestion', 'taskAnswer')
        ->where('task_session_id', $taskId)
        ->where('student_id', $studentId)
        ->first();
        // dd($sessionTask);

        $questionTask = $sessionTask->taskQuestion()->get();

        $teacher = Teacher::where('nip', $sessionTask->taskSession->created_by)->first();
        $encryptedTeacher = encrypt($teacher->nip);

        $encryptedMeeting = encrypt($sessionTask->taskSession->id);

        $pengaturanKomponen = ComponentSetting::where('task_session_id', $sessionTask->task_session_id)
                ->where('is_enabled', true)
                ->pluck('component_name');
        
        // foreach ($sessionTask as $task) {
        //     if ($task->taskSession) {
        //         $teacher = Teacher::where('nip', $task->taskSession->created_by)->first();
        //         $encryptedTeacher = encrypt($teacher->nip);
        //     }
        // }

        // foreach ($sessionTask as $task) {
        //     if ($task->taskSession) {
        //         $encryptedMeeting = encrypt($task->taskSession->id);
        //     }
        // }

        // foreach ($sessionTask as $task) {
        //     $pengaturanKomponen = ComponentSetting::where('task_session_id', $task->task_session_id)
        //         ->where('is_enabled', true)
        //         ->pluck('component_name');
        // }

        return view('siswa.draw', compact('sessionTask', 'studentId', 'questionTask', 'encryptedTeacher', 'encryptedMeeting', 'encryptedStudent', 'pengaturanKomponen'));
    }

    public function store($id, Request $request){
        try {
            $request->validate([
                'question_id' => 'required|array',
                'question_id.*.question_id' => 'required|integer',
                'question_id.*.flowchart_data' => 'required|string',
                'student_id' => 'required',
                'duration' => 'nullable|integer',
                'flowchart_images' => 'nullable|array',
                'flowchart_images.*.question_id' => 'nullable|integer',
                'flowchart_images.*.flowchart_image' => 'nullable|string'
            ]);

            $savedAnswers = [];
            $totalScore = 0;
            $questionCount = 0;

            // Process gambar dan buat mapping - LOGIKA SEDERHANA
            $imageMap = [];
            if ($request->has('flowchart_images') && is_array($request->flowchart_images)) {
                foreach ($request->flowchart_images as $imageData) {
                    if (isset($imageData['question_id']) && isset($imageData['flowchart_image']) && !empty($imageData['flowchart_image'])) {
                        $questionId = $imageData['question_id'];
                        
                        try {
                            $imageDataProcessed = $imageData['flowchart_image'];
                            if (strpos($imageDataProcessed, 'data:image') === 0) {
                                $imageDataProcessed = preg_replace('/^data:image\/\w+;base64,/', '', $imageDataProcessed);
                            }
                            
                            $decodedImage = base64_decode($imageDataProcessed);
                            
                            if ($decodedImage === false) {
                                throw new \Exception('Invalid base64 image data');
                            }
                            
                            $imageFileName = 'studentAnswer' . $request->student_id . '_' . $questionId . '_' . time() . '.png';
                            
                            if (!Storage::disk('public')->exists('assets/flowcharts/studentAnswers')) {
                                Storage::disk('public')->makeDirectory('assets/flowcharts/studentAnswers');
                            }

                            // Simpan gambar
                            $saved = Storage::disk('public')->put('assets/flowcharts/studentAnswers/' . $imageFileName, $decodedImage);
                            
                            if (!$saved) {
                                throw new \Exception('Failed to save image file');
                            }
                            
                            $imageMap[$questionId] = $imageFileName;
                            
                        } catch (\Exception $e) {
                            Log::warning('Failed to save image for question ' . $questionId . ': ' . $e->getMessage());
                            continue;
                        }
                    }
                }
            }

            foreach ($request->question_id as $answerData) {
                $decoded = json_decode($answerData['flowchart_data'], true);
                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data flowchart tidak valid untuk question_id: ' . $answerData['question_id']
                    ], 400);
                }

                $answer = new TaskAnswer();
                $answer->task_session_id = $id;
                $answer->task_question_id = $answerData['question_id'];
                $answer->student_id = $request->student_id;
                $answer->answer = $answerData['flowchart_data'];
                
                // Tambahkan gambar jika ada
                if (isset($imageMap[$answerData['question_id']])) {
                    $answer->flowchart_img = $imageMap[$answerData['question_id']];
                }
                
                $answer->save();

                $savedAnswers[] = $answer;

                $question = TaskQuestion::find($answerData['question_id']);
                
                if ($question && $question->correct_answer) {
                    $correctAnswer = $question->correct_answer;
                    $studentAnswer = $answer->answer;

                    $correctAnswerData = json_decode($correctAnswer, true);
                    $studentAnswerData = json_decode($studentAnswer, true);

                    $isStudentAnswerEmpty = empty($studentAnswerData['nodeDataArray']) && empty($studentAnswerData['linkDataArray']);

                    if ($isStudentAnswerEmpty) {
                        $questionScore = 0;
                    } else {
                        
                        $correctNodes = 0;
                        $correctLinks = 0;
                        $totalNodes = count($correctAnswerData['nodeDataArray'] ?? []); 
                        $totalLinks = count($correctAnswerData['linkDataArray'] ?? []); 

                        if (isset($correctAnswerData['nodeDataArray']) && isset($studentAnswerData['nodeDataArray'])) {
                            foreach ($correctAnswerData['nodeDataArray'] as $index => $node) {
                                if (isset($studentAnswerData['nodeDataArray'][$index])) {
                                    $correctNode = [
                                        'key' => $node['key'] ?? '',
                                        'text' => $node['text'] ?? ''
                                    ];
                                    $studentNode = [
                                        'key' => $studentAnswerData['nodeDataArray'][$index]['key'] ?? '',
                                        'text' => $studentAnswerData['nodeDataArray'][$index]['text'] ?? ''
                                    ];

                                    if ($correctNode['key'] === $studentNode['key'] && 
                                        $correctNode['text'] === $studentNode['text']) {
                                        $correctNodes++;
                                    }
                                }
                            }
                        }

                        if (isset($correctAnswerData['linkDataArray']) && isset($studentAnswerData['linkDataArray'])) {
                            foreach ($correctAnswerData['linkDataArray'] as $index => $link) {
                                if (isset($studentAnswerData['linkDataArray'][$index])) {
                                    $correctLink = [
                                        'from' => $link['from'] ?? '',
                                        'to' => $link['to'] ?? ''
                                    ];
                                    $studentLink = [
                                        'from' => $studentAnswerData['linkDataArray'][$index]['from'] ?? '',
                                        'to' => $studentAnswerData['linkDataArray'][$index]['to'] ?? ''
                                    ];

                                    if ($correctLink['from'] === $studentLink['from'] && 
                                        $correctLink['to'] === $studentLink['to']) {
                                        $correctLinks++;
                                    }
                                }
                            }
                        }

                        $correctElements = $correctNodes + $correctLinks;
                        $totalElements = $totalNodes + $totalLinks;
                        
                        if ($totalElements > 0) {
                            $questionScore = ($correctElements / $totalElements) * 100;
                            $totalScore += $questionScore;
                            $questionCount++;
                        }
                    }
                }
            }

            $averageScore = $questionCount > 0 ? $totalScore / $questionCount : 0;

            $taskSession = StudentTaskSession::where('task_session_id', $id)
                ->where('student_id', $request->student_id)
                ->first();
                
            if (!$taskSession) {
                return response()->json([
                    'success' => false,
                    'message' => 'Task session not found'
                ], 404);
            }

            $taskSession->score = round($averageScore, 2);
            $taskSession->status = 'finished';
            $taskSession->duration = $request->duration ?? 0;
            $taskSession->correct_elements = $correctElements;
            $taskSession->total_elements = $totalElements;
            $taskSession->finished_at = now();
            $taskSession->save();

            return response()->json([
                'success' => true,
                'message' => 'Flowchart berhasil disimpan',
                'data' => [
                    'answers_saved' => count($savedAnswers),
                    'images_saved' => count($imageMap),
                    'average_score' => round($averageScore, 2)
                ]
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

    public function summary($id){
        $user = Auth::user();
        $studentId = $user->userable->id;

        $decryptedId = Crypt::decrypt($id);

        $taskSession = TaskSession::with('meeting')->find($decryptedId);

        $studentSession = StudentTaskSession::with('taskSession')
        ->where('task_session_id', $decryptedId)
        ->where('student_id', $studentId)
        ->first();

        $taskQuestions = TaskQuestion::where('task_session_id', $taskSession->id)->get();
        
        $taskAnswers = TaskAnswer::where('task_session_id', $taskSession->id)
        ->where('student_id', $studentId)
        ->get();

        $ratioError = (($studentSession->total_elements - $studentSession->correct_elements) / $studentSession->total_elements) * 100;

        $taskPreTest = TaskSession::with('studentTaskSession')
        ->where('meeting_id', $taskSession->meeting_id)
        ->where('type', 'pretest')
        ->first();
        $taskPostTest = TaskSession::with('studentTaskSession')
        ->where('meeting_id', $taskSession->meeting_id)
        ->where('type', 'posttest')
        ->first();

        if ($taskPreTest && $taskPostTest) {
            $scorePreTest = $taskPreTest->studentTaskSession()->score ?? 0;
            $scorePostTest = $taskPostTest->studentTaskSession()->score ?? 0;
            if ($scorePostTest >= $scorePreTest){
                $evaluation = "Paham";
            } else {
                $evaluation = "Belum Paham";
            }
        }

        $answersMap = [];
        foreach ($taskAnswers as $answer) {
            $answersMap[$answer->task_question_id] = $answer;
        }

        return view('siswa.summary', compact('taskSession', 'taskQuestions', 'taskAnswers', 'studentSession', 'ratioError', 'evaluation', 'answersMap'));
    }
}
