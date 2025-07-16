<?php

namespace App\Http\Controllers;

use App\Models\ComponentSetting;
use App\Models\TaskQuestion;
use App\Models\QuestionExpectedAnswer;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\Storage;

class ManageAnswerController extends Controller
{
    public function index($id){
        $decryptedId = Crypt::decrypt($id);
        $data_soal = TaskQuestion::find($decryptedId);
        $encryptedTask = Crypt::encrypt($data_soal->task_session_id);
        $encryptedQuestion = Crypt::encrypt($data_soal->id);
        $pengaturanKomponen = ComponentSetting::where('task_session_id', $data_soal->task_session_id)
            ->where('is_enabled', true)
            ->pluck('component_name');

        $requiredComponents = $data_soal->required_components ?? [];

        return view('guru.manage-meetings.draw', compact('data_soal', 'encryptedTask', 'encryptedQuestion', 'pengaturanKomponen', 'requiredComponents'));
    }

    public function store(Request $request){
        try {
            $request->validate([
                'soal_id' => 'required|integer',
                'flowchart_data' => 'required|string',
                'flowchart_image' => 'required|string',
                'encrypted_task' => 'required|string'
            ]);

            $decoded = json_decode($request->flowchart_data, true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data flowchart tidak valid'
                ], 400);
            }

            $question = TaskQuestion::find($request->soal_id);
            
            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Soal tidak ditemukan'
                ], 404);
            }

            $decoded = $this->normalizeFlowchartKeys($decoded);

            $validasiFlow = $this->validateDiagramJson($decoded, $question->id);

            if ($validasiFlow !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $validasiFlow,
                ], 422);
            }

            $imageFileName = null;
            if ($request->flowchart_image) {
                try {
                    $imageData = $request->flowchart_image;
                    if (strpos($imageData, 'data:image') === 0) {
                        $imageData = preg_replace('/^data:image\/\w+;base64,/', '', $imageData);
                    }
                    
                    $decodedImage = base64_decode($imageData);
                    
                    if ($decodedImage === false) {
                        throw new \Exception('Invalid base64 image data');
                    }
                    
                    $imageFileName = 'imgAnswer' . $request->soal_id . '_' . time() . '.png';
                    
                    if (!Storage::disk('public')->exists('assets/flowcharts/keyAnswers')) {
                        Storage::disk('public')->makeDirectory('assets/flowcharts/keyAnswers');
                    }
                    
                    if ($question->flowchart_img && Storage::disk('public')->exists('assets/flowcharts/keyAnswers/' . $question->flowchart_img)) {
                        Storage::disk('public')->delete('assets/flowcharts/keyAnswers/' . $question->flowchart_img);
                    }
                    
                    $saved = Storage::disk('public')->put('assets/flowcharts/keyAnswers/' . $imageFileName, $decodedImage);
                    
                    if (!$saved) {
                        throw new \Exception('Failed to save image file');
                    }
                    
                } catch (\Exception $e) {
                    return response()->json([
                        'success' => false,
                        'message' => 'Gagal menyimpan gambar!'
                    ], 500);
                }
            }

            $question->correct_answer = $decoded; 
            if ($imageFileName) {
                $question->flowchart_img = $imageFileName; 
            }
            $question->save();

            return response()->json([
                'success' => true,
                'message' => 'Flowchart berhasil disimpan',
                'data' => [
                    'soal_id' => $request->soal_id,
                    'flowchart_data_saved' => true, 
                    'flowchart_image_saved' => $imageFileName ? true : false, 
                    'saved_at' => now()->format('Y-m-d H:i:s'),
                    'image_url' => $imageFileName ? Storage::url('assets/flowcharts/keyAnswers/' . $imageFileName) : null
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
                'message' => 'Terjadi kesalahan saat menyimpan flowchart' .$e->getMessage()
            ], 500);
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

    // private function nodeIdentity(array $node): string {
    //     return strtolower(trim($node['text'] ?? '')) . '|' . strtolower(trim($node['category'] ?? ''));
    // }

    // private function buildKeyIdentityMap(array $nodes): array {
    //     $map = [];
    //     foreach ($nodes as $node) {
    //         $map[$node['key']] = $this->nodeIdentity($node);
    //     }
    //     return $map;
    // }

    // private function normalizeLinksWithIdentity(array $links, array $keyToIdentityMap): array {
    //     $normalized = [];
    //     foreach ($links as $link) {
    //         $from = $keyToIdentityMap[$link['from']] ?? null;
    //         $to = $keyToIdentityMap[$link['to']] ?? null;

    //         if ($from && $to) {
    //             $normalized[] = [
    //                 'from' => $from,
    //                 'to' => $to,
    //                 'text' => isset($link['text']) ? strtolower(trim($link['text'])) : null
    //             ];
    //         }
    //     }
    //     return $normalized;
    // }


    private function validateDiagramJson(array $json, int $questionId): bool|string {
        $nodes = $json['nodeDataArray'] ?? [];
        $links = $json['linkDataArray'] ?? [];

        $question = TaskQuestion::find($questionId);
        $requiredComponents = [];
        
        if ($question && $question->required_components) {
            $requiredComponents = $question->required_components;
            
            if (is_string($requiredComponents)) {
                $requiredComponents = json_decode($requiredComponents, true) ?: [];
            }
        }

        if ($question && $question->type != 'intro') {
            $terminators = array_filter($nodes, fn($n) => ($n['category'] ?? null) === 'Terminator');

            $startNodes = array_filter($terminators, fn($n) => strtolower(trim($n['text'] ?? '')) === 'start');
            $endNodes = array_filter($terminators, fn($n) => strtolower(trim($n['text'] ?? '')) === 'end');

            if (count($startNodes) !== 1 || count($endNodes) !== 1) {
                return 'Diagram harus memiliki tepat satu Start dan satu End (kategori Terminator).';
            }

            $startKey = array_values($startNodes)[0]['key'];
            $endKey = array_values($endNodes)[0]['key'];

            $incoming = [];
            $outgoing = [];

            foreach ($links as $link) {
                $from = $link['from'];
                $to = $link['to'];

                $outgoing[$from][] = $to;
                $incoming[$to][] = $from;
            }

            if (!empty($incoming[$startKey])) {
                return 'Node Terminator dengan teks "Start" tidak boleh memiliki koneksi masuk.';
            }

            if (!empty($outgoing[$endKey])) {
                return 'Node Terminator dengan teks "End" tidak boleh memiliki koneksi keluar.';
            }
        }

        if (!empty($requiredComponents)) {
            $usedComponents = array_unique(array_column($nodes, 'category'));
            
            $missingComponents = [];
            foreach ($requiredComponents as $required) {
                if (!in_array($required, $usedComponents)) {
                    $missingComponents[] = $required;
                }
            }
            
            if (!empty($missingComponents)) {
                return "Komponen berikut wajib digunakan dalam diagram flowchart: " . implode(', ', $missingComponents) . 
                       ". Komponen yang saat ini digunakan: " . implode(', ', $usedComponents);
            }
        }

        $expectedAnswer = QuestionExpectedAnswer::where('task_question_id', $question->id)
        ->where('task_session_id', $question->task_session_id)
        ->first();

        if (!$expectedAnswer) {
            return 'Expected result belum ditentukan untuk soal ini.';
        }

        $expectedJson = json_decode($expectedAnswer->answer, true);

        if (!$expectedJson || !isset($expectedJson['nodeDataArray'], $expectedJson['linkDataArray'])) {
            return 'Format expected result tidak valid.';
        }

        $normalizeNodes = fn($arr) => array_map(fn($n) => [
            'text' => strtolower(trim($n['text'] ?? '')),
            'category' => strtolower(trim($n['category'] ?? '')),
        ], $arr);

        $actualNodes = $normalizeNodes($nodes);
        $expectedNodes = $normalizeNodes($expectedJson['nodeDataArray']);

        function buildNodeMap(array $nodes): array {
            $map = [];
            foreach ($nodes as $n) {
                $key = $n['key'];
                $text = strtolower(trim($n['text'] ?? ''));
                $category = strtolower(trim($n['category'] ?? ''));
                $map[$key] = "{$text} ({$category})";
            }
            return $map;
        }

        $actualNodeMap = buildNodeMap($json['nodeDataArray']);
        $expectedNodeMap = buildNodeMap($expectedJson['nodeDataArray']);

        function normalizeLinks(array $links, array $nodeMap): array {
            return array_map(function ($link) use ($nodeMap) {
                return [
                    'from' => $nodeMap[$link['from']] ?? 'UNKNOWN',
                    'to' => $nodeMap[$link['to']] ?? 'UNKNOWN',
                    'text' => $link['text'] ?? null,
                ];
            }, $links);
        }

        $actualLinks = normalizeLinks($json['linkDataArray'], $actualNodeMap);
        $expectedLinks = normalizeLinks($expectedJson['linkDataArray'], $expectedNodeMap);

        if (count($actualNodes) !== count($expectedNodes)) {
            return "Jumlah node tidak sesuai. Diharapkan: " . count($expectedNodes) . ", Diberikan: " . count($actualNodes);
        }

        foreach ($expectedNodes as $en) {
            if (!in_array($en, $actualNodes)) {
                return "Node dengan text '{$en['text']}' dan category '{$en['category']}' tidak ditemukan.";
            }
        }

        if (count($actualLinks) !== count($expectedLinks)) {
            return "Jumlah link tidak sesuai. Diharapkan: " . count($expectedLinks) . ", Diberikan: " . count($actualLinks);
        }

        foreach ($expectedLinks as $el) {
            if (!in_array($el, $actualLinks)) {
                return "Link dari '{$el['from']}' ke '{$el['to']}'" . 
                    ($el['text'] ? " dengan teks '{$el['text']}'" : '') . " tidak ditemukan.";
            }
        }
        return true;
    }
}
