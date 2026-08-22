<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\LoanType;
use App\Models\Payment;
use App\Models\MpesaStkPushRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Validator;

class MpesaPaymentController extends Controller
{
    /**
     * Initiate payment for processing fee
     */
    public function initiatePayment(Request $request)
    {
        try {
            // Validate
            $validator = Validator::make($request->all(), [
                'full_name' => 'required|string|max:255',
                'phone_number' => 'required|string|max:20',
                'national_id' => 'required|string|max:20',
                'amount' => 'required|numeric|min:1',
                'loan_amount' => 'required|numeric|min:1',
                'loan_type' => 'required|string',
                'email' => 'nullable|email|max:255',
            ]);

            if ($validator->fails()) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Validation failed',
                    'errors' => $validator->errors()
                ], 422);
            }

            // Get loan type by name or ID
            $loanType = null;

            if (is_numeric($request->loan_type)) {
                $loanType = LoanType::find($request->loan_type);
            } else {
                $loanType = LoanType::where('name', $request->loan_type)
                    ->orWhere('slug', $request->loan_type)
                    ->first();
            }

            if (!$loanType) {
                return response()->json([
                    'status' => 'error',
                    'message' => 'Loan type not found. Available types: Personal Loan, Business Loan, Education Loan, Emergency Loan, Home Improvement'
                ], 404);
            }

            $processingFee = $request->amount;
            $loanAmount = $request->loan_amount;

            // Check if loan exists
            $loan = LoanApplication::where('national_id', $request->national_id)
                ->whereIn('status', ['pending', 'failed', 'cancelled'])
                ->first();

            if (!$loan) {
                $interestRate = $loanType->interest_rate ?? 12.00;
                $termDays = $loanType->term_days ?? 180;
                $totalRepayment = $loanAmount + $processingFee + ($loanAmount * $interestRate / 100);

                $loan = LoanApplication::create([
                    'full_name' => $request->full_name,
                    'phone_number' => $request->phone_number,
                    'national_id' => $request->national_id,
                    'loan_type_id' => $loanType->id,
                    'amount' => $loanAmount,
                    'interest_rate' => $interestRate,
                    'term_days' => $termDays,
                    'processing_fee' => $processingFee,
                    'total_repayment' => $totalRepayment,
                    'status' => 'pending',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            } else {
                if ($loan->status === 'approved') {
                    return response()->json([
                        'status' => 'error',
                        'message' => 'You already have an approved loan'
                    ], 400);
                }

                $interestRate = $loanType->interest_rate ?? 12.00;
                $termDays = $loanType->term_days ?? 180;
                $totalRepayment = $loanAmount + $processingFee + ($loanAmount * $interestRate / 100);

                $loan->update([
                    'full_name' => $request->full_name,
                    'phone_number' => $request->phone_number,
                    'loan_type_id' => $loanType->id,
                    'amount' => $loanAmount,
                    'interest_rate' => $interestRate,
                    'term_days' => $termDays,
                    'processing_fee' => $processingFee,
                    'total_repayment' => $totalRepayment,
                    'status' => 'pending',
                    'ip_address' => $request->ip(),
                    'user_agent' => $request->userAgent()
                ]);
            }

            // Format phone number
            $phone = $this->formatPhoneNumber($loan->phone_number);

            // Paystack's charge API requires an email even for mobile money
            // charges; the loan form doesn't collect one, so synthesize a
            // stable placeholder from the phone number.
            $email = $request->email ?: $phone . '@kcbmpesaloans.co.ke';

            $reference = 'kcb-' . $loan->id . '-' . now()->timestamp;

            // Call Paystack
            $payload = [
                'email' => $email,
                'amount' => (int) round($processingFee * 100),
                'currency' => 'KES',
                'reference' => $reference,
                'mobile_money' => [
                    'phone' => $phone,
                    'provider' => 'mpesa',
                ],
            ];

            Log::info('Calling Paystack for processing fee', [
                'loan_amount' => $loanAmount,
                'processing_fee' => $processingFee,
                'loan_type' => $loanType->name,
                'phone' => $phone
            ]);

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.paystack.secret_key'),
                'Content-Type' => 'application/json'
            ])->withOptions([
                'timeout' => 30,
            ])->post(rtrim(config('services.paystack.base_url'), '/') . '/charge', $payload);

            Log::info('Paystack response', [
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                throw new \Exception('Paystack API error: ' . $response->body());
            }

            $paystackData = $response->json();

            if (empty($paystackData['status']) || empty($paystackData['data']['reference'])) {
                throw new \Exception('Paystack API error: ' . ($paystackData['message'] ?? $response->body()));
            }

            $paystackReference = $paystackData['data']['reference'];

            // Create payment record
            $payment = Payment::create([
                'loan_application_id' => $loan->id,
                'amount' => $processingFee,
                'payment_method' => 'mpesa',
                'payment_type' => 'processing_fee',
                'phone_number' => $loan->phone_number,
                'reference' => $paystackReference,
                'status' => 'pending',
                'request_payload' => $paystackData,
                'payment_date' => now(),
            ]);

            // Create STK push record
            MpesaStkPushRequest::create([
                'payment_id' => $payment->id,
                'loan_application_id' => $loan->id,
                'reference' => $paystackReference,
                'phone_number' => $loan->phone_number,
                'amount' => $processingFee,
                'status' => 'pending',
                'request_data' => $paystackData,
                'sent_at' => now(),
            ]);

            return response()->json([
                'status' => 'success',
                'message' => 'Processing fee payment initiated. Check your phone for M-Pesa prompt.',
                'data' => [
                    'loan_id' => $loan->id,
                    'loan_type' => $loanType->name,
                    'payment_id' => $payment->id,
                    'checkout_request_id' => $paystackReference,
                    'loan_amount' => $loanAmount,
                    'processing_fee' => $processingFee,
                    'interest_rate' => $loan->interest_rate,
                    'term_days' => $loan->term_days,
                    'total_repayment' => $loan->total_repayment,
                    'status' => 'pending',
                    'next_step' => 'Pay the processing fee of KES ' . number_format($processingFee, 2) . ' to complete your application'
                ]
            ]);

        } catch (\Exception $e) {
            Log::error('Payment error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to initiate payment: ' . $e->getMessage()
            ], 500);
        }
    }

    /**
     * Handle Paystack webhook
     */
    public function handleCallback(Request $request)
    {
        Log::info('Callback received', $request->all());

        $signature = $request->header('x-paystack-signature');
        $expectedSignature = hash_hmac('sha512', $request->getContent(), (string) config('services.paystack.secret_key'));

        if (!$signature || !hash_equals($expectedSignature, $signature)) {
            Log::warning('Paystack webhook signature mismatch');
            return response()->json([
                'status' => 'error',
                'message' => 'Invalid signature'
            ], 401);
        }

        try {
            DB::beginTransaction();

            $data = $request->input('data', []);

            // Find STK push
            $stkPush = MpesaStkPushRequest::where('reference', $data['reference'] ?? null)->first();

            if (!$stkPush) {
                DB::rollBack();
                Log::warning('Transaction not found for callback', [
                    'reference' => $data['reference'] ?? null
                ]);
                return response()->json([
                    'status' => 'error',
                    'message' => 'Transaction not found'
                ], 404);
            }

            $this->applyTransactionResult($stkPush, $data);

            DB::commit();

            return response()->json([
                'status' => 'success',
                'message' => 'Callback processed'
            ]);

        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Callback error: ' . $e->getMessage());

            return response()->json([
                'status' => 'error',
                'message' => 'Failed to process callback'
            ], 500);
        }
    }

    /**
     * Apply a Paystack transaction result (from webhook or a status verify
     * poll) to the STK push, payment, and loan application records.
     */
    private function applyTransactionResult(MpesaStkPushRequest $stkPush, array $data): void
    {
        $paystackStatus = strtolower((string) ($data['status'] ?? ''));
        $resultDesc = $data['gateway_response'] ?? null;

        // Paystack's mobile money charge can sit in intermediate states
        // (e.g. "pay_offline", "ongoing", "pending") while the customer is
        // still entering their PIN. Only terminal statuses should resolve
        // the record; anything else is left pending for the next poll/webhook.
        if (!in_array($paystackStatus, ['success', 'failed', 'abandoned', 'reversed'], true)) {
            Log::info('Paystack transaction not yet resolved, will re-check', [
                'stk_push_id' => $stkPush->id,
                'paystack_status' => $paystackStatus,
                'body' => $data,
            ]);
            return;
        }

        $isCancelled = $paystackStatus === 'abandoned';
        $isSuccess = $paystackStatus === 'success';
        $status = $isSuccess ? 'completed' : ($isCancelled ? 'cancelled' : 'failed');
        $receipt = $data['id'] ?? null;

        Log::info('Processing transaction result', [
            'paystack_status' => $paystackStatus,
            'result_desc' => $resultDesc,
            'status' => $status,
            'is_cancelled' => $isCancelled
        ]);

        // Update STK push
        $stkPush->status = $status;
        $stkPush->result_code = $isSuccess ? 0 : 1;
        $stkPush->result_desc = $resultDesc;
        $stkPush->mpesa_receipt_number = $receipt;
        $stkPush->callback_data = $data;
        $stkPush->completed_at = now();
        $stkPush->save();

        // Update payment
        $payment = Payment::find($stkPush->payment_id);
        if ($payment) {
            $payment->status = $status;
            $payment->mpesa_receipt_number = $receipt;
            $payment->callback_payload = $data;
            $payment->confirmed_at = now();
            $payment->save();
        }

        // Update loan
        $loan = LoanApplication::find($stkPush->loan_application_id);
        if ($loan) {
            if ($isSuccess) {
                // Processing fee paid successfully - APPROVE THE LOAN
                $loan->status = 'approved';
                $loan->approved_at = now();

                Log::info('Processing fee paid. Loan approved.', [
                    'loan_id' => $loan->id,
                    'amount' => $loan->amount,
                    'processing_fee' => $loan->processing_fee,
                    'total_repayment' => $loan->total_repayment,
                    'receipt' => $receipt
                ]);

            } elseif ($isCancelled) {
                $loan->status = 'cancelled';

                Log::info('Payment cancelled by user', [
                    'loan_id' => $loan->id,
                    'result_desc' => $resultDesc ?? 'User abandoned the payment'
                ]);
            } else {
                $loan->status = 'failed';

                Log::warning('Processing fee payment failed', [
                    'loan_id' => $loan->id,
                    'reason' => $resultDesc ?? 'Payment failed'
                ]);
            }
            $loan->save();
        }
    }

    /**
     * Poll Paystack directly for the latest status of a pending charge.
     * Needed as a fallback in case the webhook can't reach a non-public
     * (e.g. localhost) URL, or hasn't arrived yet.
     */
    private function verifyPendingTransaction(LoanApplication $loan): void
    {
        $stkPush = MpesaStkPushRequest::where('loan_application_id', $loan->id)
            ->where('status', 'pending')
            ->whereNotNull('reference')
            ->latest()
            ->first();

        if (!$stkPush) {
            return;
        }

        try {
            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . config('services.paystack.secret_key'),
            ])->withOptions([
                'timeout' => 30,
            ])->get(rtrim(config('services.paystack.base_url'), '/') . '/transaction/verify/' . $stkPush->reference);

            Log::info('Paystack transaction verify response', [
                'loan_id' => $loan->id,
                'status' => $response->status(),
                'body' => $response->body()
            ]);

            if (!$response->successful()) {
                return;
            }

            $body = $response->json();
            $data = $body['data'] ?? null;

            if (!$data) {
                return;
            }

            DB::transaction(function () use ($stkPush, $data) {
                $this->applyTransactionResult($stkPush, $data);
            });

            $loan->refresh();
        } catch (\Exception $e) {
            Log::error('Paystack transaction verify failed: ' . $e->getMessage());
        }
    }

    /**
     * Check loan status
     */
    public function checkStatus($id)
    {
        $loan = LoanApplication::with('loanType')->find($id);

        if (!$loan) {
            return response()->json([
                'status' => 'error',
                'message' => 'Loan not found'
            ], 404);
        }

        if ($loan->status === 'pending') {
            $this->verifyPendingTransaction($loan);
        }

        return response()->json([
            'status' => 'success',
            'data' => [
                'id' => $loan->id,
                'full_name' => $loan->full_name,
                'loan_type' => $loan->loanType->name ?? null,
                'loan_amount' => $loan->amount,
                'interest_rate' => $loan->interest_rate,
                'processing_fee' => $loan->processing_fee,
                'term_days' => $loan->term_days,
                'total_repayment' => $loan->total_repayment,
                'status' => $loan->status,
                'created_at' => $loan->created_at,
                'approved_at' => $loan->approved_at
            ]
        ]);
    }

    /**
     * Format phone number to the local 07XXXXXXXX / 01XXXXXXXX shape
     * Paystack's mobile_money.phone field expects for Kenyan numbers.
     */
    private function formatPhoneNumber($phone)
    {
        $phone = preg_replace('/[^0-9]/', '', $phone);

        if (str_starts_with($phone, '254')) {
            $phone = '0' . substr($phone, 3);
        } elseif (!str_starts_with($phone, '0')) {
            $phone = '0' . $phone;
        }

        return $phone;
    }
}
