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
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

class StudentAnswerController extends Controller
{
    public function index($id){
        $user = Auth::user();
        $studentId = $user->userable->id;
        $encryptedStudent = encrypt($studentId);

        $taskId = decrypt($id);
        $sessionTask = StudentTaskSession::with([
            'taskSession',
            'taskQuestion' => function($query) use ($taskId) {
                $query->where('task_session_id', $taskId);
            },
            'taskAnswer'
        ])
        ->where('task_session_id', $taskId)
        ->where('student_id', $studentId)
        ->first();

        $questionTask = $sessionTask->taskQuestion()->get();

        $teacher = Teacher::where('nip', $sessionTask->taskSession->created_by)->first();
        $encryptedTeacher = encrypt($teacher->nip);

        $encryptedMeeting = encrypt($sessionTask->taskSession->id);

        $pengaturanKomponen = ComponentSetting::where('task_session_id', $sessionTask->task_session_id)
                ->where('is_enabled', true)
                ->pluck('component_name');

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
            $imageMap = [];
            $totalCorrectElements = 0;
            $totalExpectedElements = 0;

            foreach ($request->flowchart_images ?? [] as $imageData) {
                if (!empty($imageData['flowchart_image']) && isset($imageData['question_id'])) {
                    $filename = 'studentAnswer' . $request->student_id . '_' . $imageData['question_id'] . '_' . time() . '.png';
                    $imageDataBase64 = preg_replace('/^data:image\/\w+;base64,/', '', $imageData['flowchart_image']);
                    $decodedImage = base64_decode($imageDataBase64);

                    if ($decodedImage !== false) {
                        Storage::disk('public')->put("assets/flowcharts/studentAnswers/{$filename}", $decodedImage);
                        $imageMap[$imageData['question_id']] = $filename;
                    }
                }
            }

            foreach ($request->question_id as $answerData) {
                $studentAnswerData = json_decode($answerData['flowchart_data'], true);
                $studentAnswerData = $this->normalizeFlowchartKeys($studentAnswerData);

                if (json_last_error() !== JSON_ERROR_NONE) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Data flowchart tidak valid untuk question_id: ' . $answerData['question_id']
                    ], 400);
                }

                $taskSession = StudentTaskSession::where('id', $id)
                ->where('student_id', $request->student_id)
                ->first();

                $answer = new TaskAnswer();
                $answer->task_session_id = $taskSession->task_session_id;
                $answer->task_question_id = $answerData['question_id'];
                $answer->student_id = $request->student_id;
                $answer->answer = json_encode($studentAnswerData);
                $answer->flowchart_img = $imageMap[$answerData['question_id']] ?? null;
                $answer->save();
                $savedAnswers[] = $answer;

                $question = TaskQuestion::find($answerData['question_id']);

                if ($question && $question->correct_answer) {
                    $correctAnswerData = json_decode($question->correct_answer, true);

                    if (empty($studentAnswerData['nodeDataArray']) && empty($studentAnswerData['linkDataArray'])) {
                        $score = 0;
                    } else {
                        // try {
                        //     [$score, $correctElements, $totalElements] = $this->evaluateFlowchartWithAI($correctAnswerData, $studentAnswerData);
                        // } catch (\Exception $e) {
                        //     // fallback jika AI gagal
                        //     Log::error("Evaluasi AI bermasalah: ".$e->getMessage());
                        //     $score = 0;
                        //     $correctElements = 0;
                        //     $totalElements = count($correctAnswerData['nodeDataArray'] ?? []) + count($correctAnswerData['linkDataArray'] ?? []);
                        // }

                        [$correctElements, $totalElements] = $this->evaluateFlowchartAnswer($correctAnswerData, $studentAnswerData);
                        $totalCorrectElements += $correctElements;
                        $totalExpectedElements += $totalElements;
                        $score = $totalElements > 0 ? ($correctElements / $totalElements) * 100 : 0;
                    }

                    // $totalCorrectElements += $correctElements;
                    // $totalExpectedElements += $totalElements;
                    $totalScore += $score;
                    $questionCount++;

                    // $totalScore += $score;
                    // $questionCount++;
                }
            }

            $averageScore = $questionCount > 0 ? round($totalScore / $questionCount, 2) : 0;

            $taskSession = StudentTaskSession::where('id', $id)
                ->where('student_id', $request->student_id)
                ->first();

            if (!$taskSession) {
                return response()->json(['success' => false, 'message' => 'Task session not found'], 404);
            }

            $taskSession->score = $averageScore;
            $taskSession->status = 'finished';
            $taskSession->duration = $request->duration ?? 0;
            $taskSession->total_elements = $totalExpectedElements;
            $taskSession->correct_elements = $totalCorrectElements;
            $taskSession->finished_at = now();
            $taskSession->save();

            return response()->json([
                'success' => true,
                'message' => 'Jawaban berhasil disimpan',
                'data' => [
                    'answers_saved' => count($savedAnswers),
                    'images_saved' => count($imageMap),
                    'average_score' => $averageScore
                ]
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            return response()->json(['success' => false, 'message' => 'Data tidak valid', 'errors' => $e->errors()], 422);

        } catch (\Exception $e) {
            return response()->json(['success' => false, 'message' => 'Terjadi kesalahan saat menyimpan: ' . $e->getMessage()], 500);
        }
    }

    private function normalizeFlowchartKeys(array $json): array {
        $nodes = $json['nodeDataArray'] ?? [];
        $links = $json['linkDataArray'] ?? [];

        $oldToNewKey = [];
        $newKey = 1;

        foreach ($nodes as &$node) {
            $oldKey = $node['key'];
            $oldToNewKey[$oldKey] = $newKey;
            $node['key'] = $newKey;
            $newKey++;
        }

        foreach ($links as &$link) {
            if (isset($oldToNewKey[$link['from']])) {
                $link['from'] = $oldToNewKey[$link['from']];
            }
            if (isset($oldToNewKey[$link['to']])) {
                $link['to'] = $oldToNewKey[$link['to']];
            }
        }

        return [
            'nodeDataArray' => $nodes,
            'linkDataArray' => $links
        ];
    }

    // private function evaluateFlowchartWithAI(array $expected, array $student): array{
    //     $system = "Kamu adalah AI evaluator untuk flowchart. Berdasarkan dua data JSON berikut, nilai jawaban siswa terhadap jawaban benar. Hitung jumlah elemen yang benar (node atau link), total elemen yang seharusnya dilihat dari node dan links kunci jawaban, dan skor dalam bentuk persentase (0-100). Format output JSON hanya seperti ini: {\"score\": 85, \"correct_elements\": 10, \"total_elements\": 12}";

    //     $prompt = "Berikut adalah jawaban benar:\n" . json_encode($expected, JSON_PRETTY_PRINT) .
    //             "\n\nBerikut adalah jawaban siswa:\n" . json_encode($student, JSON_PRETTY_PRINT) .
    //             "\n\nTolong nilai sesuai instruksi di atas.";

    //     $response = Prism::text()
    //         ->using(Provider::OpenAI, 'gpt-4.1')
    //         ->withSystemPrompt($system)
    //         ->withPrompt($prompt)
    //         ->asText();

    //     $responseText = $response->text;

    //     Log::info('AI Raw Response', ['response' => $responseText]);

    //     $aiResult = json_decode($responseText, true);

    //     if (!is_array($aiResult) ||
    //         !isset($aiResult['score'], $aiResult['correct_elements'], $aiResult['total_elements']) ||
    //         !is_numeric($aiResult['score']) ||
    //         !is_numeric($aiResult['correct_elements']) ||
    //         !is_numeric($aiResult['total_elements'])) {

    //         throw new \Exception("Response AI tidak valid atau tidak bisa diproses: " . json_encode($aiResult));
    //     }

    //     return [
    //         (float) $aiResult['score'],
    //         (int) $aiResult['correct_elements'],
    //         (int) $aiResult['total_elements']
    //     ];
    // }

    private function evaluateFlowchartAnswer(array $expected, array $student): array {
        $normalizeNodes = fn($arr) => array_map(fn($n) => [
            'text' => strtolower(trim($n['text'] ?? '')),
            'category' => strtolower(trim($n['category'] ?? '')),
        ], $arr);

        $mapLabelByKey = function ($nodes) {
            $map = [];
            foreach ($nodes as $n) {
                $label = strtolower(trim($n['text'] ?? '')) . '|' . strtolower(trim($n['category'] ?? ''));
                $map[$n['key']] = $label;
            }
            return $map;
        };

        $normalizeLinks = function ($links, $labelMap) {
            return array_map(function ($l) use ($labelMap) {
                return [
                    'from' => $labelMap[$l['from']] ?? 'unknown',
                    'to' => $labelMap[$l['to']] ?? 'unknown',
                    'text' => strtolower(trim($l['text'] ?? '')),
                ];
            }, $links);
        };

        $compareItems = fn(array $a, array $b): bool => 
            strtolower($a['category']) === strtolower($b['category']) &&
            (
                similar_text(strtolower($a['text']), strtolower($b['text']), $percent) && $percent >= 70
            );

        $compareLinks = fn(array $a, array $b): bool =>
            $a['from'] === $b['from'] && $a['to'] === $b['to'] && $a['text'] === $b['text'];

        $arrayIntersectSmart = function (array $a, array $b, callable $compareFn): int {
            $matched = 0;
            $usedIndexes = [];
            foreach ($a as $itemA) {
                foreach ($b as $i => $itemB) {
                    if (!in_array($i, $usedIndexes) && $compareFn($itemA, $itemB)) {
                        $matched++;
                        $usedIndexes[] = $i;
                        break;
                    }
                }
            }
            return $matched;
        };

        $expectedNodes = $normalizeNodes($expected['nodeDataArray'] ?? []);
        $studentNodes = $normalizeNodes($student['nodeDataArray'] ?? []);
        $correctNodes = $arrayIntersectSmart($expectedNodes, $studentNodes, $compareItems);
        $totalNodes = count($expectedNodes);

        $expectedLabelMap = $mapLabelByKey($expected['nodeDataArray'] ?? []);
        $studentLabelMap = $mapLabelByKey($student['nodeDataArray'] ?? []);
        $expectedLinks = $normalizeLinks($expected['linkDataArray'] ?? [], $expectedLabelMap);
        $studentLinks = $normalizeLinks($student['linkDataArray'] ?? [], $studentLabelMap);
        $correctLinks = $arrayIntersectSmart($expectedLinks, $studentLinks, $compareLinks);
        $totalLinks = count($expectedLinks);

        return [$correctNodes + $correctLinks, $totalNodes + $totalLinks];
    }

    public function summary($id){
        $user = Auth::user();
        $studentId = $user->userable->id;

        $decryptedId = Crypt::decrypt($id);

        $studentSession = StudentTaskSession::with('taskSession')
        ->where('task_session_id', $decryptedId)
        ->where('student_id', $studentId)
        ->first();

        $taskSession = TaskSession::with('meeting')->find($decryptedId);

        $meetingId = $taskSession->meeting_id;
        
        $taskQuestions = TaskQuestion::where('task_session_id', $taskSession->id)->get();
        
        $taskAnswers = TaskAnswer::where('task_session_id', $taskSession->id)
        ->where('student_id', $studentId)
        ->get();

        // $timeSaved = $taskSession->duration - $studentSession->duration;
        // $timeBonus = 0;
        // if ($timeSaved > 0 && $taskSession->duration > 0) {
        //     $timeBonus = ($timeSaved / $taskSession->duration) * 100;
        // }
        // $finalScore = $studentSession->score + $timeBonus;
        // $finalScore = min($finalScore, 100);
        // $rasioError = round(100 - $finalScore, 2);

        if($studentSession->total_elements != 0 && $studentSession->correct_elements != 0) {
            $ratioError = round((($studentSession->total_elements - $studentSession->correct_elements) / $studentSession->total_elements) * 100, 2);
        } else {
            $ratioError = 100;
        }

        $tasks = StudentTaskSession::where('student_id', $studentId)
        ->whereHas('taskSession', function ($query) use ($meetingId) {
            $query->where('meeting_id', $meetingId)
                ->whereIn('type', ['pretest', 'posttest']);
        })
        ->with('taskSession') 
        ->get()
        ->keyBy(fn($item) => $item->taskSession->type);
            
        $pretest = $tasks['pretest'] ?? null;
        $posttest = $tasks['posttest'] ?? null;

        $evaluation = null;

        if ($pretest && $posttest) {
            $preScore = $pretest->score ?? 0;
            $postScore = $posttest->score ?? 0;

            if ($postScore > $preScore) {
                $diff = number_format($postScore - $preScore, 2);
                $evaluation = "Nilai kamu meningkat {$diff} poin! Terlihat kamu memahami materi dengan baik setelah belajar.";
            } elseif ($postScore < $preScore) {
                $diff = number_format($preScore - $postScore, 2);
                $evaluation = "Nilai kamu turun {$diff} poin. Coba review kembali materi dan diskusikan dengan guru atau teman ya!";
            } else {
                $evaluation = "Yuk, tingkatkan pemahamanmu untuk hasil yang lebih baik!";
            }
        } else {
            $evaluation = "Data pretest atau posttest belum lengkap untuk dievaluasi.";
        }

        $answersMap = [];
        foreach ($taskAnswers as $answer) {
            $answersMap[$answer->task_question_id] = $answer;
        }

        return view('siswa.summary', compact('taskSession', 'taskQuestions', 'taskAnswers', 'studentSession', 'ratioError', 'evaluation', 'answersMap'));
    }
}
