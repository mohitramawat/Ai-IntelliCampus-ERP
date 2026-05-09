<?php

namespace App\Services\Fee;

use App\Models\FineRule;
use App\Models\StudentUnitInstallment;
use App\Models\InstallmentFine;
use App\Services\AuditService;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;
use Exception;

class FineApplicationService
{
    protected AuditService $auditService;

    public function __construct(AuditService $auditService)
    {
        $this->auditService = $auditService;
    }

    /**
     * Apply a specific fine rule to all eligible unpaid past-due installments securely.
     *
     * @param FineRule $rule
     * @return int Number of fines applied
     * @throws Exception
     */
    public function applyFineNow(FineRule $rule): int
    {
        if (!$rule->is_active) {
            throw new Exception("Cannot apply an inactive fine rule.");
        }

        $today = Carbon::today();

        if ($today->lt(Carbon::parse($rule->effective_from_date))) {
            throw new Exception("Fine rule is not yet effective.");
        }

        $appliedInstallments = [];

        $finesApplied = DB::transaction(function () use ($rule, $today, &$appliedInstallments) {
            $eligibleInstallments = StudentUnitInstallment::whereHas('studentUnitFee.feeStructure', function ($q) use ($rule) {
                    $q->where('course_id', $rule->course_id);
                })
                ->whereHas('studentUnitFee', function ($q) use ($rule) {
                    $q->where('unit_number', $rule->unit_number);
                })
                ->where('status', '!=', 'paid')
                ->whereNotNull('due_date')
                ->where('due_date', '<', $today->toDateString())
                ->whereDoesntHave('fines', function ($q) use ($rule) {
                    $q->where('fine_rule_id', $rule->id);
                })
                ->get();

            $finesApplied = 0;

            foreach ($eligibleInstallments as $installment) {
                InstallmentFine::create([
                    'student_unit_installment_id' => $installment->id,
                    'fine_rule_id' => $rule->id,
                    'fine_amount' => $rule->fine_amount,
                    'applied_on' => $today->toDateString(),
                    'is_paid' => false,
                ]);

                $appliedInstallments[] = $installment;
                $finesApplied++;
            }

            return $finesApplied;
        });

        // Audit — outside transaction, one entry per installment fined
        foreach ($appliedInstallments as $installment) {
            $this->auditService->log(
                'fine_applied',
                $installment,
                null,
                [
                    'fine_amount' => $rule->fine_amount,
                    'rule_id'     => $rule->id,
                ]
            );
        }

        return $finesApplied;
    }
}
