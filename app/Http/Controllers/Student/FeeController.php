<?php

namespace App\Http\Controllers\Student;

use App\Http\Controllers\Controller;
use App\Models\Student;
use App\Models\StudentUnitFee;
use App\Models\StudentUnitInstallment;
use App\Models\InstallmentPayment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class FeeController extends Controller
{
    public function index()
    {
        $student = Student::where('user_id', Auth::id())->firstOrFail();
        $fees = StudentUnitFee::where('student_id', $student->id)
            ->with(['installments' => function($q) {
                $q->orderBy('due_date', 'asc');
            }])
            ->get();

        return view('student.fees.index', compact('student', 'fees'));
    }

    /**
     * Mock Payment Simulation
     */
    public function simulatePayment(Request $request)
    {
        $request->validate([
            'unit_fee_id' => 'required|exists:student_unit_fees,id',
            'amount' => 'required|numeric|min:1',
            'method' => 'required|string'
        ]);

        try {
            DB::beginTransaction();

            $unitFee = StudentUnitFee::with(['installments' => function($q) {
                // Only get unpaid/partial installments, ordered by due date
                $q->where('status', '!=', 'paid')->orderBy('due_date', 'asc');
            }])->findOrFail($request->unit_fee_id);

            $remainingAmountToApply = $request->amount;
            
            // Check if amount is valid
            $totalDue = $unitFee->unit_fee - $unitFee->total_paid;
            if ($remainingAmountToApply > $totalDue) {
                // If they pay more than due, cap it to total due
                $remainingAmountToApply = $totalDue; 
            }
            
            if ($remainingAmountToApply <= 0) {
                 return response()->json(['success' => false, 'message' => 'This unit fee is already fully paid.']);
            }

            $actualApplied = 0;

            foreach ($unitFee->installments as $installment) {
                if ($remainingAmountToApply <= 0) break;

                $dueForThisInstallment = $installment->installment_amount - $installment->paid_amount;
                
                if ($dueForThisInstallment <= 0) continue;

                $amountToApplyHere = min($dueForThisInstallment, $remainingAmountToApply);

                // Map display method to DB enum value
                $modeMap = [
                    'UPI'                => 'upi',
                    'Credit/Debit Card'  => 'online',
                    'Net Banking'        => 'bank_transfer',
                    'Education Loan/EMI' => 'other',
                ];
                $paymentMode = $modeMap[$request->method] ?? 'other';

                // Create payment record
                InstallmentPayment::create([
                    'student_unit_installment_id' => $installment->id,
                    'amount_paid'                 => $amountToApplyHere,
                    'payment_date'                => now()->toDateString(),
                    'payment_mode'                => $paymentMode,
                    'transaction_reference'       => 'MOCK-' . strtoupper(uniqid()),
                    'remarks'                     => 'Mock payment via student portal',
                ]);

                // Update installment
                $newPaid = $installment->paid_amount + $amountToApplyHere;
                $installment->update([
                    'paid_amount' => $newPaid,
                    'status' => ($newPaid >= $installment->installment_amount) ? 'paid' : 'partial'
                ]);

                $remainingAmountToApply -= $amountToApplyHere;
                $actualApplied += $amountToApplyHere;
            }

            // Update parent unit fee total
            $newTotalPaid = $unitFee->total_paid + $actualApplied;
            $unitFee->update([
                'total_paid' => $newTotalPaid,
                'status' => ($newTotalPaid >= $unitFee->unit_fee) ? 'paid' : 'partial'
            ]);

            DB::commit();

            return response()->json([
                'success' => true,
                'message' => 'Payment Successful! ₹' . number_format($actualApplied, 2) . ' applied.',
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            return response()->json(['success' => false, 'message' => $e->getMessage()], 500);
        }
    }
}
