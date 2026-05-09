<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use App\Models\StudentUnitFee;
use App\Models\InstallmentPayment;
use App\Models\Student;

class DashboardController extends Controller
{
    public function index()
    {
        $expectedFees = StudentUnitFee::sum('unit_fee');
        $collectedFees = StudentUnitFee::sum('total_paid');
        $pendingFees = $expectedFees - $collectedFees;
        
        $totalStudents = Student::count();
        
        $recentPayments = InstallmentPayment::with(['installment.studentUnitFee.student.user'])
            ->orderBy('payment_date', 'desc')
            ->limit(10)
            ->get();

        return view('accounts.dashboard', compact(
            'expectedFees', 
            'collectedFees', 
            'pendingFees', 
            'totalStudents',
            'recentPayments'
        ));
    }
}
