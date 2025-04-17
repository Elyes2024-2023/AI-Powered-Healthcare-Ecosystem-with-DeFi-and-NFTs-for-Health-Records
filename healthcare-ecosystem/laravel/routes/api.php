<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\HealthRecordController;
use App\Http\Controllers\Api\InsuranceClaimController;
use App\Http\Controllers\Api\InsurancePolicyController;
use App\Http\Controllers\Api\IotDeviceDataController;
use App\Http\Controllers\Api\SymptomCheckerController;
use App\Http\Controllers\Api\SymptomRecordController;
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

// Public routes
Route::post('/register', [AuthController::class, 'register']);
Route::post('/login', [AuthController::class, 'login']);

// Protected routes
Route::middleware('auth:sanctum')->group(function () {
    Route::post('/logout', [AuthController::class, 'logout']);
    Route::get('/user', [AuthController::class, 'user']);
    
    // Health Records
    Route::apiResource('health-records', HealthRecordController::class);
    
    // Insurance Policies
    Route::apiResource('insurance-policies', InsurancePolicyController::class);
    
    // Insurance Claims
    Route::apiResource('insurance-claims', InsuranceClaimController::class);
    
    // Symptom Records
    Route::apiResource('symptom-records', SymptomRecordController::class);
    
    // Symptom Checker
    Route::post('/symptom-checker/analyze', [SymptomCheckerController::class, 'analyze']);
    Route::get('/symptom-checker/history', [SymptomCheckerController::class, 'history']);
    
    // IoT Device Data
    Route::apiResource('iot-device-data', IotDeviceDataController::class);
}); 