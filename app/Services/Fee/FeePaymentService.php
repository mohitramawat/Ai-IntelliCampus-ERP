<?php

namespace App\Services\Fee;

use App\Models\InstallmentPayment;
use App\Models\StudentUnitInstallment;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Exception;

class FeePaymentService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Record a new payment against an installment safely.
     *
     * @param StudentUnitInstallment $installment
     * @param array $paymentData
     * @return InstallmentPayment
     * @throws Exception
     */
    public function recordPayment(StudentUnitInstallment $installment, array $paymentData): InstallmentPayment
    {
        $payment = DB::transaction(function () use ($installment, $paymentData) {
            // Re-fetch with a row-level write lock to prevent concurrent payment race conditions
            $installment = StudentUnitInstallment::lockForUpdate()->findOrFail($installment->id);

            $amountPaid = (float) $paymentData['amount_paid'];

            // Validation Rules
            if ($amountPaid <= 0) {
                throw new Exception("Payment amount must be greater than zero.");
            }

            if (($installment->paid_amount + $amountPaid) > $installment->installment_amount) {
                throw new Exception("Payment exceeds the installment due amount.");
            }

            // Create InstallmentPayment record
            $payment = InstallmentPayment::create([
                'student_unit_installment_id' => $installment->id,
                'amount_paid' => $amountPaid,
                'payment_date' => $paymentData['payment_date'],
                'payment_mode' => $paymentData['payment_mode'],
                'transaction_reference' => $paymentData['transaction_reference'] ?? null,
                'remarks' => $paymentData['remarks'] ?? null,
                'created_by' => $paymentData['created_by'] ?? null,
            ]);

            // Update Installment
            $installment->paid_amount += $amountPaid;

            if ($installment->paid_amount == $installment->installment_amount) {
                $installment->status = 'paid';
            } elseif ($installment->paid_amount > 0 && $installment->paid_amount < $installment->installment_amount) {
                $installment->status = 'partial';
            } else {
                $installment->status = 'pending';
            }

            $installment->save();

            // Update Student Unit Fee
            $studentUnitFee = $installment->studentUnitFee;
            $totalPaid = $studentUnitFee->installments()->sum('paid_amount');

            $studentUnitFee->total_paid = $totalPaid;

            if ($totalPaid == $studentUnitFee->unit_fee) {
                $studentUnitFee->status = 'paid';
            } elseif ($totalPaid > 0 && $totalPaid < $studentUnitFee->unit_fee) {
                $studentUnitFee->status = 'partial';
            } else {
                $studentUnitFee->status = 'pending';
            }

            $studentUnitFee->save();

            return $payment;
        });

        // Audit — outside transaction so it does not interfere with rollback
        $this->auditService->log(
            'payment_recorded',
            $installment,
            null,
            [
                'amount_paid' => $paymentData['amount_paid'],
                'status'      => $installment->fresh()->status,
            ]
        );

        return $payment;
    }
}
