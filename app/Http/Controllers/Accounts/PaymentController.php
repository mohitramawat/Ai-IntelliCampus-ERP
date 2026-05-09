<?php

namespace App\Http\Controllers\Accounts;

use App\Http\Controllers\Controller;
use App\Models\StudentUnitInstallment;
use App\Services\Fee\FeePaymentService;
use Illuminate\Http\Request;
use Exception;

class PaymentController extends Controller
{
    protected FeePaymentService $paymentService;

    public function __construct(FeePaymentService $paymentService)
    {
        $this->paymentService = $paymentService;
    }

    public function store(Request $request, $installmentId)
    {
        $paymentData = $request->validate([
            'amount_paid' => 'required|numeric|min:1',
            'payment_mode' => 'required|in:cash,online,upi,bank_transfer,cheque,other',
            'payment_date' => 'required|date',
            'transaction_reference' => 'nullable|string|max:100',
            'remarks' => 'nullable|string',
        ]);

        $paymentData['created_by'] = auth()->id() ?? 1; // Assuming 1 for tinker/testing if no auth

        $installment = StudentUnitInstallment::findOrFail($installmentId);

        try {
            $payment = $this->paymentService->recordPayment($installment, $paymentData);

            return response()->json([
                'success' => true,
                'message' => 'Payment recorded successfully.',
                'data' => $payment
            ]);
        } catch (Exception $e) {
            return response()->json([
                'success' => false,
                'message' => $e->getMessage(),
            ], 422);
        }
    }
}
