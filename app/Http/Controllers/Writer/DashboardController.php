<?php

namespace App\Http\Controllers\Writer;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\Batch;
use App\Models\Teacher;
use App\Models\StudentDocument;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $totalStudents = Student::count();
        $totalTeachers = Teacher::count();
        $totalDocs     = StudentDocument::count();
        $totalBatches  = Batch::count();

        return view('writer.dashboard', compact('totalStudents', 'totalTeachers', 'totalDocs', 'totalBatches'));
    }
}
