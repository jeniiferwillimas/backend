<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\LoanApplication;
use App\Models\LoanType;
use Illuminate\Http\Request;

class LoanApplicationController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'full_name' => 'required|string|max:255',
            'phone_number' => 'required|string|regex:/^0[17]\d{8}$/',
            'national_id' => 'required|string|regex:/^\d{7,9}$/',
            'loan_type' => 'required|exists:loan_types,slug',
            'amount' => 'required|numeric|min:1',
        ]);

        $loanType = LoanType::where('slug', $request->loan_type)->first();

        $application = LoanApplication::create([
            'full_name' => $request->full_name,
            'phone_number' => $request->phone_number,
            'national_id' => $request->national_id,
            'loan_type_id' => $loanType->id,
            'amount' => $request->amount,
            'interest_rate' => $loanType->interest_rate,
            'term_days' => 180,
            'processing_fee' => $loanType->processing_fee,
            'total_repayment' => 0,
            'status' => 'pending',
            'ip_address' => $request->ip(),
            'user_agent' => $request->userAgent(),
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Application submitted successfully',
            'data' => $application
        ], 201);
    }
}