<?php

namespace App\Http\Controllers;

use App\Models\ComponentSetting;
use App\Models\TaskQuestion;
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

        return view('guru.manage-meetings.draw', compact('data_soal', 'encryptedTask', 'encryptedQuestion', 'pengaturanKomponen'));
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

            $validasiFlow = $this->validateDiagramJson($decoded);

            if ($validasiFlow !== true) {
                return response()->json([
                    'success' => false,
                    'message' => $validasiFlow,
                ], 422);
            }

            $question = TaskQuestion::find($request->soal_id);
            
            if (!$question) {
                return response()->json([
                    'success' => false,
                    'message' => 'Soal tidak ditemukan'
                ], 404);
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

            $question->correct_answer = $request->flowchart_data; 
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
                'message' => 'Terjadi kesalahan saat menyimpan flowchart'
            ], 500);
        }
    }

    private function validateDiagramJson(array $json): bool|string{
        $nodes = $json['nodeDataArray'] ?? [];
        $links = $json['linkDataArray'] ?? [];

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
        return true;
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
