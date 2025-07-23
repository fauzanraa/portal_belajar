<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentTaskSession;
use App\Models\TaskAnswer;
use App\Models\TaskQuestion;
use App\Models\TaskSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManageProgressController extends Controller
{
    public function index(){
        $dataSiswa = StudentTaskSession::join('students', 'student_id', '=', 'students.id')
        ->join('classrooms', 'students.class_id', '=', 'classrooms.id')
        ->select('students.id as student_id', 'students.name', 'classrooms.class_name as class_name', 'student_task_sessions.*')
        ->where('student_task_sessions.access', 'system')
        ->get();

        $studentSession = $dataSiswa->groupBy('student_id')->map(function ($group) {
            $student_info = $group->first();
            return (object) [
                'student_id' => $student_info->student_id,
                'name' => $student_info->name,
                'class_name' => $student_info->class_name,
                'task_sessions' => $group 
            ];
        });

        $jumlah_tugas = StudentTaskSession::all();
        $jumlah_selesai = StudentTaskSession::where('status', 'finished')->get();
        $tugas_tiap_siswa = $jumlah_tugas->groupBy('student_id')->map(function ($group) {
            return $group->count(); 
        });
        $selesai_tiap_siswa = $jumlah_selesai->groupBy('student_id')->map(function ($group) {
            return $group->count(); 
        });

        $persentase_pengerjaan = [];
        foreach ($tugas_tiap_siswa as $student_id => $total_tugas) {
            $total_selesai = $selesai_tiap_siswa->get($student_id, 0); 
            $persentase = $total_tugas > 0 ? ($total_selesai / $total_tugas) * 100 : 0;
            $persentase_pengerjaan[$student_id] = round($persentase, 2);
        }

        $pilihan_kelas = Classroom::all();

        return view('guru.manage-progress.index', compact('studentSession', 'pilihan_kelas', 'persentase_pengerjaan'));
    }

    public function detail($idStudent){
        $decryptedStudent = decrypt($idStudent);

        $dataTugas = StudentTaskSession::join('students', 'student_id', '=', 'students.id')
        ->join('classrooms', 'students.class_id', '=', 'classrooms.id')
        ->join('task_sessions', 'task_session_id', '=', 'task_sessions.id')
        ->join('meetings', 'task_sessions.meeting_id', '=', 'meetings.id')
        ->where('student_id', $decryptedStudent)
        ->select(
            'student_task_sessions.*', 
            'task_sessions.type', 
            'task_sessions.name', 
            'task_sessions.meeting_id',
            'students.name', 
            'meetings.title',
            'meetings.description'
            )
        ->get();
        // dd($dataTugas);

        return view('guru.manage-progress.detail', compact('dataTugas'));
    }

    public function summary($idSession){
        $decryptedSession = decrypt($idSession);
        
        $studentSession = StudentTaskSession::with('taskSession')
        ->where('id', $decryptedSession)
        ->first();

        $taskSession = TaskSession::with('meeting')->find($studentSession->task_session_id);

        $meetingId = $taskSession->meeting_id;

        $taskQuestions = TaskQuestion::where('task_session_id', $taskSession->id)->get();
        
        $taskAnswers = TaskAnswer::where('task_session_id', $taskSession->id)
        ->where('student_id', $studentSession->student_id)
        ->get();

        if($studentSession->total_elements != 0 && $studentSession->correct_elements != 0) {
            $ratioError = round((($studentSession->total_elements - $studentSession->correct_elements) / $studentSession->total_elements) * 100, 2);
        } else {
            $ratioError = 100;
        }

        $tasks = StudentTaskSession::where('student_id', $studentSession->student_id)
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
                if ($postScore > 60) {
                    $evaluation = "Siswa menunjukkan pemahaman yang lebih baik terhadap materi setelah mengikuti pembelajaran.";
                } else {
                    $evaluation = "Nilai posttest siswa masih di bawah 60. Disarankan untuk memberikan penguatan tambahan pada materi terkait.";
                }
            } elseif ($postScore < $preScore) {
                $evaluation = "Siswa mengalami penurunan skor. Disarankan guru mengecek kembali bagian yang belum dipahami siswa.";
            } elseif ($postScore = $preScore) {
                $evaluation = "Siswa mungkin membutuhkan pendekatan belajar yang lain untuk memperdalam materi.";
            }
        } else {
            $evaluation = "Data pretest atau posttest belum lengkap untuk dievaluasi.";
        }

        $answersMap = [];
        foreach ($taskAnswers as $answer) {
            $answersMap[$answer->task_question_id] = $answer;
        }

        return view('guru.manage-progress.summary', compact('studentSession', 'taskSession', 'taskQuestions', 'taskAnswers', 'answersMap', 'ratioError', 'evaluation'));
    }
}
