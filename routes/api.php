<?php

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\Api\LoanTypeController;
use App\Http\Controllers\Api\LoanApplicationController;
use App\Http\Controllers\Api\MpesaPaymentController;

Route::post('/initiate-payment', [MpesaPaymentController::class, 'initiatePayment']);
Route::post('/mpesa-callback', [MpesaPaymentController::class, 'handleCallback']);
Route::get('/loan-status/{id}', [MpesaPaymentController::class, 'checkStatus']);
Route::get('/loan-types', [LoanTypeController::class, 'index']);
