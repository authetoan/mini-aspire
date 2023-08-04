<?php

use App\Http\Controllers\AuthController;
use App\Http\Controllers\LoanController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('login', [AuthController::class,'login']);

Route::post('loans', [LoanController::class, 'store'])
    ->middleware('auth:sanctum');
Route::put('loans/{loanId}/approve', [LoanController::class, 'approve'])
    ->middleware('auth:sanctum,can:approve,loan');
Route::get('loans', [LoanController::class, 'viewCustomerLoans'])
    ->middleware('auth:sanctum');
Route::get('loans/{loanId}', [LoanController::class, 'show'])
    ->middleware('auth:sanctum');
Route::put(
    'loans/{loanId}/scheduled-repayments/{scheduledRepaymentId}/repayment',
    [LoanController::class, 'addRepayment']
)
    ->middleware('auth:sanctum');
