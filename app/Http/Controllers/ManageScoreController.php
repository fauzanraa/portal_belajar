<?php

namespace App\Http\Controllers;

use App\Models\Classroom;
use App\Models\Student;
use App\Models\StudentTaskSession;
use Carbon\Carbon;
use Illuminate\Http\Request;

class ManageScoreController extends Controller
{
    public function index(){
        $dataSiswa = StudentTaskSession::join('students', 'student_id', '=', 'students.id')
        ->join('classrooms', 'students.class_id', '=', 'classrooms.id')
        ->select('students.id as student_id', 'students.name', 'classrooms.class_name as class_name', 'student_task_sessions.*')
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

        return view('guru.manage-scores.index', compact('studentSession', 'pilihan_kelas', 'persentase_pengerjaan'));
    }

    public function detail($idStudent, $idTask){
        $decryptedStudent = decrypt($idStudent);
        $decryptedTask = decrypt($idTask);

        $dataSiswa = StudentTaskSession::join('students', 'student_id', '=', 'students.id')
        ->join('classrooms', 'students.class_id', '=', 'classrooms.id')
        ->join('task_sessions', 'task_session_id', '=', 'task_sessions.id')
        ->join('meetings', 'task_sessions.meeting_id', '=', 'meetings.id')
        ->where('task_session_id', $decryptedTask)
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

        $jumlahTugas = StudentTaskSession::where('student_id', $decryptedStudent)
        ->where('task_session_id', $decryptedTask)
        ->count();

        $jumlahSelesai = StudentTaskSession::where('student_id', $decryptedStudent)
        ->where('task_session_id', $decryptedTask)
        ->where('status', 'finished')
        ->count();

        return view('guru.manage-scores.detail-score', compact('dataSiswa', 'jumlahTugas', 'jumlahSelesai'));
    }

    public function getStudentModalData($idTask, $idMeeting, $idStudent){
        try {
            $dataSiswa = StudentTaskSession::join('students', 'student_task_sessions.student_id', '=', 'students.id')
                ->join('classrooms', 'students.class_id', '=', 'classrooms.id')
                ->join('task_sessions', 'student_task_sessions.task_session_id', '=', 'task_sessions.id')
                ->join('meetings', 'task_sessions.meeting_id', '=', 'meetings.id')
                ->where('task_sessions.id', $idTask)
                ->where('meetings.id', $idMeeting)
                ->where('student_task_sessions.student_id', $idStudent)
                ->select(
                    'student_task_sessions.*', 
                    'task_sessions.type', 
                    'task_sessions.name', 
                    'task_sessions.meeting_id',
                    'students.name as student_name', 
                    'meetings.title',
                    'meetings.description',
                )
                ->get();

            if ($dataSiswa->isEmpty()) {
                return response()->json([
                    'success' => false,
                    'message' => 'Data siswa tidak ditemukan'
                ]);
            }

            $tasks = $dataSiswa->map(function($session) {
                return [
                    'id' => $session->id,
                    'name' => $session->name,
                    'type' => $session->type,
                    'date' => $session->finished_at ? Carbon::parse($session->finished_at)->format('d M Y') : '-',
                    'duration' => $session->duration ? $session->duration . ' menit' : '-',
                    'status' => $session->status,
                    'score' => $session->score ?? 0,
                    'accuracy' => 10,
                    'efficiency' => 10,
                    // 'start_time' => $session->start_time,
                    // 'end_time' => $session->end_time,
                    // 'status' => $session->status ?? 'pending'
                ];
            });

            return response()->json([
                'success' => true,
                'data' => [
                    'title' => $dataSiswa->first()->title,
                    'description' => $dataSiswa->first()->description,
                    'student_name' => $dataSiswa->first()->student_name,
                    'task_session_id' => $idTask,
                    'meeting_id' => $idMeeting,
                    'student_id' => $idStudent,
                    'tasks' => $tasks
                ]
            ]);
        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Terjadi kesalahan: ' . $e->getMessage()
            ]);
        }
    }


}
