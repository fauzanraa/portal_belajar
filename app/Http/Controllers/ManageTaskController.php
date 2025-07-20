<?php

namespace App\Http\Controllers;

use App\Models\ComponentSetting;
use App\Models\Meeting;
use App\Models\StudentTaskSession;
use App\Models\TaskQuestion;
use App\Models\TaskSession;
use App\Models\QuestionExpectedAnswer;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Crypt;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Prism\Prism\Prism;
use Prism\Prism\Enums\Provider;

class ManageTaskController extends Controller
{
    public function index($id){
        $decryptedId = Crypt::decrypt($id);

        $dataTugas = TaskSession::find($decryptedId);

        $dataSoal = TaskQuestion::where('task_session_id', $decryptedId)->get();

        $sesiSiswaSistem = StudentTaskSession::with(['student.classroom'])
        ->where('task_session_id', $decryptedId)
        ->where('access', 'system')
        ->get()
        ->groupBy('student.classroom.class_name');

        $sesiSiswaNonSistem = StudentTaskSession::with(['student.classroom'])
        ->where('task_session_id', $decryptedId)
        ->where('access', 'non_system')
        ->get()
        ->groupBy('student.classroom.class_name');

        $encryptedTaskSession = Crypt::encrypt($dataTugas->id);

        $user = Auth::user();

        $pengaturanKomponen = $this->getAllowedComponents($decryptedId);

        return view('guru.manage-meetings.detail-task', compact('dataTugas', 'dataSoal', 'sesiSiswaSistem', 'sesiSiswaNonSistem' ,'user', 'encryptedTaskSession', 'pengaturanKomponen'));
    }

    private function getAllowedComponents($taskId) {
        $settings = ComponentSetting::where('task_session_id', $taskId)
            ->where('is_enabled', true)
            ->pluck('component_name')
            ->toArray();
        
        return $settings;
    }

    public function updateComponentSettings(Request $request, $id) {
        if ($request->has('components') && is_array($request->components)) {
            $task = TaskSession::findOrFail($id);

            $questions = TaskQuestion::where('task_session_id', $task->id)->get();
        
            $requiredComponents = [];
            foreach ($questions as $question) {
                if ($question->required_components) {
                    if (is_string($question->required_components)) {
                        $components = json_decode($question->required_components, true) ?: [];
                    } else {
                        $components = $question->required_components;
                    }
                    
                    $requiredComponents = array_merge($requiredComponents, $components);
                }
            }
            
            $requiredComponents = array_unique($requiredComponents);
            
            $selectedComponents = $request->components;
            
            foreach ($requiredComponents as $required) {
                if (!in_array($required, $selectedComponents)) {
                    return response()->json([
                        'success' => false,
                        'message' => "Komponen '{$required}' wajib dipilih karena digunakan dalam soal"
                    ]);
                }
            }

            ComponentSetting::where('task_session_id', $task->id)->delete();

            $componentData = [];
            foreach ($request->components as $componentName) {
                $componentData[] = [
                    'task_session_id' => $task->id,
                    'component_name' => $componentName,
                    'is_enabled' => true,
                    'created_at' => now(),
                    'updated_at' => now()
                ];
            }

            if (!empty($componentData)) {
                ComponentSetting::insert($componentData);
            }
            
            return response()->json(['success' => true]);
        }
    }

    public function store($id, Request $request){
        $request->validate([
            'task' => 'required',
            'type' => 'required',
            'start' => 'required|date',
            'end' => 'required|date',
            'time_start' => 'required',
            'time_end' => 'required',
            'duration' => 'required|numeric'
        ]);

        try {
            DB::beginTransaction();
            
            $decryptedId = Crypt::decrypt($id);
            $data_pertemuan = Meeting::find($decryptedId);
            $start = Carbon::parse($request->start)->format('Y-m-d');
            $time_start = Carbon::parse($request->time_start)->format('H:i');
            $open_at = Carbon::createFromFormat('Y-m-d H:i', $start . ' ' . $time_start);
            $end = Carbon::parse($request->end)->format('Y-m-d');
            $time_end = Carbon::parse($request->time_end)->format('H:i');
            $close_at = Carbon::createFromFormat('Y-m-d H:i', $end . ' ' . $time_end);

            $task = new TaskSession();
            $task->meeting_id = $data_pertemuan->id;
            $task->type = $request->type;
            $task->name = $request->task;
            $task->open_at = $open_at;
            $task->close_at = $close_at;
            $task->created_by = Auth::user()->userable->nip;
            $task->duration = $request->duration;
            $task->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil menambah data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambah data!');
        }
    }

    public function update($id, Request $request){   
        $request->validate([
            'task_id' => 'required',
            'task' => 'required',
            'start' => 'required|date',
            'end' => 'required|date',
            'time_start' => 'required',
            'time_end' => 'required',
            'duration' => 'required|numeric'
        ]);

        try {
            DB::beginTransaction();

            $open_at = ($request->start . ' ' . $request->time_start);
            $close_at = ($request->end . ' ' . $request->time_end);

            $task = TaskSession::find($request->task_id);
            $task->name = $request->task;
            $task->open_at = $open_at;
            $task->close_at = $close_at;
            $task->duration = $request->duration;
            $task->save();
            
            DB::commit();

            return redirect()->back()->with('success', 'Berhasil mengupdate data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal mengupdate data!');
        }
    }

    public function question($id){
        $decryptedId = Crypt::decrypt($id);
        $data_tugas = TaskSession::find($decryptedId);

        return view('guru.manage-meetings.question', compact('data_tugas'));
    }

    public function storeQuestion($id, Request $request){
        $request->validate([
            'question_*' => 'required',
            'type_*' => 'required',
            'flowchart_components' => 'array'
        ]);
        
        try {
            DB::beginTransaction();
            
            $decryptedId = Crypt::decrypt($id);
            $data_tugas = TaskSession::find($decryptedId);
            $question = TaskQuestion::where('task_session_id', $data_tugas->id)->get();
            if($question->isNotEmpty()){
                $question->each(function ($questions) {
                    $questions->delete();
                });
            }
            
            for ($i=0; $i < $request->total_question; $i++) { 
                $question = new TaskQuestion();
                $question->task_session_id = $data_tugas->id;
                $question->question = $request->question[$i];
                $question->type = $request->type[$i];
                $componentKey = $i + 1;
            
                if (isset($request->flowchart_components[$componentKey])) {
                    $components = $request->flowchart_components[$componentKey];
                    
                    if (is_string($components)) {
                        $components = json_decode($components, true);
                    }
                    
                    if (is_array($components) && !empty($components)) {
                        $question->required_components = $components;
                    }
                }

                $question->save();
                
                $requiredComponents = is_array($question->required_components) ? implode(", ", $question->required_components) : '';
                $systemPromptIntro = "Anda adalah asisten teknis yang hanya menjawab dalam format JSON lengkap yang kompatibel dengan GoJS. Setiap jawaban harus berisi struktur lengkap JSON GoJS dengan minimal satu node di dalam array nodeDataArray. Tidak perlu menyambungkan node (tanpa linkDataArray). Jangan kirim jawaban dalam bentuk array saja — selalu kirim dalam format: { \"class\": ..., \"nodeDataArray\": [...], \"linkDataArray\": [...]  }.";
                $promptIntro = "Buatkan satu node flowchart dari studi kasus berikut. Komponen yang dimaksud cukup satu node sesuai konteks, tanpa perlu menyambungkan node lain. Formatkan dalam JSON GoJS dengan isi node adalah key, text dan category. Soal: {$question->question}";

                $systemPromptCase = "Anda adalah asisten yang sangat terstruktur dalam menggambar flowchart. Buat expected result dalam bentuk JSON GoJS. Gunakan nodeDataArray dan linkDataArray. Setiap flowchart harus dimulai dengan komponen Terminator (Start) dan diakhiri dengan Terminator (End). Jika ada kondisi, gunakan komponen Decision dengan label Yes/No. Gunakan komponen sesuai fungsi masing-masing.";
                $promptCase = "Buatkan aku expected result dari studi kasus untuk menggambar flowchart dengan ketentuan text terminator start/end, lalu decision berisi yes/no. Buatkan aku dalam bentuk JSON dan format GoJS pada soal berikut: {$question->question}. Dengan komponen wajib adalah {$requiredComponents}";

                if($question->type == 'intro'){
                    $response = Prism::text()
                    ->using(Provider::OpenAI, 'gpt-4.1')
                    ->withSystemPrompt($systemPromptIntro)
                    ->withPrompt($promptIntro)
                    ->asText();
                } elseif($question->type == 'study_case'){
                    $response = Prism::text()
                    ->using(Provider::OpenAI, 'gpt-4.1')
                    ->withSystemPrompt($systemPromptCase)
                    ->withPrompt($promptCase)
                    ->asText();
                }

                if (!empty($response->text)) {
                    $expected_answer = new QuestionExpectedAnswer();
                    $expected_answer->task_question_id = $question->id;
                    $expected_answer->task_session_id = $question->task_session_id;
                    $expected_answer->answer = $response->text;
                    $expected_answer->save();
                }
            }
            
            DB::commit();

            return redirect()->route('detail-tasks', ['id' => $id])->with('success', 'Berhasil menambah data!');
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('error', 'Gagal menambah data!' . $e->getMessage());
        }
    }
}
