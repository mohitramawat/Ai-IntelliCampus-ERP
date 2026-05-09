<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\FineRule;
use App\Services\Fee\FineApplicationService;
use Exception;

class FineController extends Controller
{
    protected FineApplicationService $fineService;

    public function __construct(FineApplicationService $fineService)
    {
        $this->fineService = $fineService;
    }

    public function apply($ruleId)
    {
        $rule = FineRule::findOrFail($ruleId);

        try {
            $finesApplied = $this->fineService->applyFineNow($rule);

            return response()->json([
                'success' => true,
                'message' => "Fine applied successfully to {$finesApplied} eligible installment(s)."
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage()
            ], 422);
        }
    }
}
