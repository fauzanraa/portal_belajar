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

                $answer = new TaskAnswer();
                $answer->task_session_id = $id;
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
                        [$correctElements, $totalElements] = $this->evaluateFlowchartAnswer($correctAnswerData, $studentAnswerData);
                        $totalCorrectElements += $correctElements;
                        $totalExpectedElements += $totalElements;
                        $score = $totalElements > 0 ? ($correctElements / $totalElements) * 100 : 0;
                    }

                    $totalScore += $score;
                    $questionCount++;
                }
            }

            $averageScore = $questionCount > 0 ? round($totalScore / $questionCount, 2) : 0;

            $taskSession = StudentTaskSession::where('task_session_id', $id)
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
            $a['text'] === $b['text'] && $a['category'] === $b['category'];

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

        $taskSession = TaskSession::with('meeting')->find($decryptedId);

        $studentSession = StudentTaskSession::with('taskSession')
        ->where('task_session_id', $decryptedId)
        ->where('student_id', $studentId)
        ->first();

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
            $ratioError = (($studentSession->total_elements - $studentSession->correct_elements) / $studentSession->total_elements) * 100;
        } else {
            $ratioError = 100;
        }
            

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
