<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\FormController;
use App\Http\Controllers\SubmissionController;
use App\Http\Controllers\AuthController;

Route::prefix('auth')->group(function () {
    Route::post('register', [AuthController::class, 'Register']);
    Route::post('login', [AuthController::class, 'Login']);
    Route::middleware('auth:api')->group(function () {
        Route::get('my-profile', [AuthController::class, 'MyProfile']);
        Route::post('logout', [AuthController::class, 'Logout']);
    });
});

Route::prefix('forms')->group(function () {
    Route::get('/', [FormController::class, 'index']);
    Route::get('{id}', [FormController::class, 'show']);
    Route::middleware(['auth:api', 'role:admin'])->group(function () {
        Route::post('/', [FormController::class, 'store']);
        Route::put('{id}', [FormController::class, 'update']);
        Route::delete('{id}', [FormController::class, 'destroy']);
    });
});

Route::middleware('auth:api')->prefix('submissions')->group(function () {
    Route::post('/', [SubmissionController::class, 'store']);
    Route::get('/', [SubmissionController::class, 'index']);
    Route::get('{id}', [SubmissionController::class, 'show']);
    Route::put('{id}', [SubmissionController::class, 'update']);
});

Route::prefix('payments')->group(function () {
    Route::middleware('auth:api')->group(function () {
        Route::post('initiate', [PaymentController::class, 'initiate']);
        Route::get('{paymentId}/receipt', [PaymentController::class, 'receipt']);
        Route::post('sample-receipt', [PaymentController::class, 'sampleReceipt']);
    });
    Route::post('webhook/razorpay', [PaymentController::class, 'razorpayWebhook']);
});

