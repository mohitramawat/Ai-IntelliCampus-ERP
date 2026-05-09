<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\Department;
use App\Models\Course;
use App\Models\Batch;
use App\Models\Student;
use App\Models\StudentUnitFee;
use App\Models\StudentUnitInstallment;
use App\Models\InstallmentPayment;
use App\Models\InstallmentFine;
use App\Models\FeeStructure;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class FeeManagementController extends Controller
{
    /**
     * Display a detailed transaction log with search and filters.
     */
    public function transactions(Request $request)
    {
        $query = InstallmentPayment::with(['installment.studentUnitFee.student.user'])
            ->orderBy('payment_date', 'desc');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('installment.studentUnitFee.student.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        if ($request->filled('payment_method')) {
            $query->where('payment_method', $request->payment_method);
        }

        if ($request->filled('date_from')) {
            $query->whereDate('payment_date', '>=', $request->date_from);
        }

        if ($request->filled('date_to')) {
            $query->whereDate('payment_date', '<=', $request->date_to);
        }

        $transactions = $query->paginate(20);

        return view('accounts.fees.transactions', compact('transactions'));
    }

    /**
     * List students with their total dues and balance status.
     */
    public function dues(Request $request)
    {
        $query = StudentUnitFee::with(['student.user', 'student.batch.course', 'installments.fines'])
            ->whereRaw('total_paid < unit_fee');

        if ($request->filled('search')) {
            $search = $request->search;
            $query->whereHas('student.user', function($q) use ($search) {
                $q->where('name', 'like', "%{$search}%");
            });
        }

        $dues = $query->paginate(20);

        return view('accounts.fees.dues', compact('dues'));
    }

    /**
     * List of students with overdue installments.
     */
    public function defaulters(Request $request)
    {
        $today = Carbon::today();
        
        $defaulters = StudentUnitInstallment::with(['studentUnitFee.student.user', 'studentUnitFee.student.batch.course'])
            ->where('status', '!=', 'paid')
            ->whereDate('due_date', '<', $today)
            ->where('is_active', true)
            ->orderBy('due_date', 'asc')
            ->paginate(20);

        return view('accounts.fees.defaulters', compact('defaulters'));
    }

    /**
     * Collection reports (Daily, Weekly, Monthly).
     */
    public function reports(Request $request)
    {
        $type = $request->get('type', 'daily'); // daily, weekly, monthly
        
        $query = InstallmentPayment::query();

        if ($type == 'daily') {
            $data = $query->select(DB::raw('DATE(payment_date) as label'), DB::raw('SUM(amount_paid) as total'))
                ->whereDate('payment_date', '>', now()->subDays(30))
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        } elseif ($type == 'weekly') {
            $data = $query->select(DB::raw('YEARWEEK(payment_date) as label'), DB::raw('SUM(amount_paid) as total'))
                ->whereDate('payment_date', '>', now()->subWeeks(12))
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        } else { // monthly
            $data = $query->select(DB::raw('DATE_FORMAT(payment_date, "%Y-%m") as label'), DB::raw('SUM(amount_paid) as total'))
                ->whereDate('payment_date', '>', now()->subMonths(12))
                ->groupBy('label')
                ->orderBy('label', 'asc')
                ->get();
        }

        return view('accounts.fees.reports', compact('data', 'type'));
    }

    /**
     * Read-only view of fee structures.
     */
    public function structures()
    {
        $structures = FeeStructure::with(['course'])->orderBy('effective_from_year', 'desc')->get();
        return view('accounts.fees.structures', compact('structures'));
    }

    /**
     * Fine application interface.
     */
    public function finesIndex()
    {
        $departments = Department::all();
        $courses = Course::all();
        $batches = Batch::all();
        
        return view('accounts.fees.fines', compact('departments', 'courses', 'batches'));
    }

    /**
     * Apply a manual fine to a specific group of students.
     */
    public function applyManualFine(Request $request)
    {
        $request->validate([
            'course_id' => 'required|exists:courses,id',
            'batch_id' => 'required|exists:batches,id',
            'department_id' => 'required|exists:departments,id',
            'fine_amount' => 'required|numeric|min:1',
            'deadline_date' => 'required|date',
            'reason' => 'required|string|max:255',
        ]);

        $deadline = Carbon::parse($request->deadline_date);

        // Find students matching the criteria
        $students = Student::where('course_id', $request->course_id)
            ->where('batch_id', $request->batch_id)
            ->pluck('id');

        if ($students->isEmpty()) {
            return back()->with('error', 'No students found matching these criteria.');
        }

        // Find unpaid installments for these students whose due_date is on or before the selected deadline
        $installments = StudentUnitInstallment::whereHas('studentUnitFee', function($q) use ($students) {
            $q->whereIn('student_id', $students);
        })
        ->where('status', '!=', 'paid')
        ->where('is_active', true)
        ->whereDate('due_date', '<=', $deadline)
        ->get();

        if ($installments->isEmpty()) {
            return back()->with('error', 'No eligible overdue installments found for the selected deadline.');
        }

        // Ensure a FineRule exists for this amount/course
        $rule = \App\Models\FineRule::firstOrCreate(
            [
                'course_id' => $request->course_id,
                'fine_amount' => $request->fine_amount,
            ],
            [
                'unit_number' => 1, 
                'effective_from_date' => now(),
                'is_active' => true,
                'created_by' => auth()->id() ?? 1
            ]
        );

        $count = 0;
        foreach ($installments as $installment) {
            // Apply fine if not already applied for this rule
            $existing = InstallmentFine::where('student_unit_installment_id', $installment->id)
                ->where('fine_rule_id', $rule->id)
                ->exists();

            if (!$existing) {
                InstallmentFine::create([
                    'student_unit_installment_id' => $installment->id, 
                    'fine_rule_id' => $rule->id,
                    'fine_amount' => $request->fine_amount,
                    'applied_on' => now()->toDateString(),
                    'is_paid' => false
                ]);
                $count++;
            }
        }

        return back()->with('success', "Fine applied to {$count} student installments overdue by {$deadline->format('d M Y')}.");
    }
}
