<?php

namespace App\Services;

use App\Models\Student;
use App\Models\AttendanceRecord;
use App\Models\LectureSession;
use App\Models\Subject;
use Illuminate\Support\Facades\DB;

class AttendanceSummaryService
{
    public function getStudentSummary(Student $student): array
    {
        // PART 1: Overall Stats
        $totalLectures = AttendanceRecord::where('student_id', $student->id)->count();
        $totalPresent  = AttendanceRecord::where('student_id', $student->id)->where('status', 'present')->count();
        $totalAbsent   = AttendanceRecord::where('student_id', $student->id)->where('status', 'absent')->count();

        $percentage = $totalLectures > 0 ? round(($totalPresent / $totalLectures) * 100, 2) : 0;

        $overallStats = [
            'total'          => $totalLectures,
            'total_lectures' => $totalLectures,
            'present'        => $totalPresent,
            'absent'         => $totalAbsent,
            'percentage'     => (float) $percentage,
        ];

        // PART 2: Subject-wise Breakdown
        // Join directly via lecture_sessions.subject_id — no assignment table
        $subjectStatsRaw = DB::table('attendance_records')
            ->join('lecture_sessions', 'attendance_records.lecture_session_id', '=', 'lecture_sessions.id')
            ->join('subjects', 'lecture_sessions.subject_id', '=', 'subjects.id')
            ->where('attendance_records.student_id', $student->id)
            ->groupBy('subjects.id', 'subjects.name')
            ->select(
                'subjects.name as subject_name',
                DB::raw('COUNT(*) as total_lectures'),
                DB::raw("SUM(CASE WHEN attendance_records.status = 'present' THEN 1 ELSE 0 END) as present_count"),
                DB::raw("SUM(CASE WHEN attendance_records.status = 'absent' THEN 1 ELSE 0 END) as absent_count")
            )
            ->get();

            
        $subjectsBreakdown = [];
        
        foreach ($subjectStatsRaw as $stat) {
            $total = (int) $stat->total_lectures;
            $present = (int) $stat->present_count;
            $absent = (int) $stat->absent_count;
            $subjectPercentage = $total > 0 ? round(($present / $total) * 100, 2) : 0;
            
            $subjectsBreakdown[] = [
                'subject'    => $stat->subject_name,
                'total'      => $total,
                'present'    => $present,
                'absent'     => $absent,
                'percentage' => (float) $subjectPercentage,
            ];
        }

        // PART 4: Last 10 Lectures Trend
        $trendStats = AttendanceRecord::where('student_id', $student->id)
            ->join('lecture_sessions', 'attendance_records.lecture_session_id', '=', 'lecture_sessions.id')
            ->orderBy('lecture_sessions.lecture_date', 'desc')
            ->orderBy('lecture_sessions.period_number', 'desc')
            ->take(10)
            ->select('lecture_sessions.lecture_date as date', 'attendance_records.status')
            ->get()
            ->reverse() // Chronological order
            ->map(function ($record) {
                return [
                    'date'   => $record->date,
                    'status' => $record->status,
                ];
            })
            ->values()
            ->toArray();

        // PART 5: Final Return Structure
        return [
            'overall'  => $overallStats,
            'subjects' => $subjectsBreakdown,
            'trend'    => $trendStats,
        ];
    }
}
