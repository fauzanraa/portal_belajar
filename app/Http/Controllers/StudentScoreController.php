<?php

namespace App\Http\Controllers;

use App\Models\MaterialSession;
use App\Models\StudentTaskSession;
use App\Models\TaskSession;
use App\Models\Meeting;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class StudentScoreController extends Controller
{
    public function index(){
        $user = Auth::user();
        $studentId = $user->userable->id;

        // Ambil semua session siswa dengan relasi yang diperlukan
        $studentSessions = StudentTaskSession::with(['taskSession.meeting'])
            ->where('student_id', $studentId)
            ->where('status', 'finished')
            ->get();

        // Group berdasarkan meeting_id
        $groupedSessions = $studentSessions->groupBy(function ($item) {
            return $item->taskSession->meeting_id;
        });

        $completedModules = collect();

        foreach ($groupedSessions as $meetingId => $sessions) {
            $pretestDone = $sessions->contains(function ($session) {
                return $session->taskSession->type === 'pretest' && $session->status == 'finished';
            });

            $postestDone = $sessions->contains(function ($session) {
                return $session->taskSession->type === 'posttest' && $session->status == 'finished';
            });

            if ($pretestDone && $postestDone) {
                // Ambil data meeting
                $meeting = Meeting::find($meetingId);

                $material = MaterialSession::where('meeting_id', $meetingId)->get();

                $posttestTask = $sessions->first(function ($s) {
                    return optional($s->taskSession)->type === 'posttest';
                });

                $posttestTaskId = $posttestTask?->task_session_id ?? null;
                
                $pretestScore = $sessions->where('taskSession.type', 'pretest')->first()->score ?? 0;
                $postestScore = $sessions->where('taskSession.type', 'posttest')->first()->score ?? 0;
                // $averageScore = ($pretestScore + $postestScore) / 2;

                $totalTasks = $sessions->count();
                $completedTasks = $sessions->where('status', 'finished')->count();

                $moduleData = (object) [
                    'id' => $meetingId,
                    'task_session_id' => $posttestTaskId,
                    'title' => $meeting->title ?? 'Pertemuan ' . $meetingId,
                    'description' => $meeting->description ?? 'Materi pembelajaran',
                    // 'average_score' => round($averageScore, 1),
                    'pretest_score' => round($pretestScore, 1),
                    'posttest_score' => round($postestScore, 1),
                    'total_tasks' => $totalTasks,
                    'completed_tasks' => $completedTasks,
                    'material' => $material,
                    'meeting' => $meeting,
                    'sessions' => $sessions
                ];

                $completedModules->push($moduleData);
            }
            // dd($completedModules);  
        }

        return view('siswa.scores', compact('completedModules'));
    }
}
