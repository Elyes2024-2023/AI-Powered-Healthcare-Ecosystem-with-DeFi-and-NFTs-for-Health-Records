<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\HomeController;
use App\Http\Controllers\InsurancePolicyController;
use App\Http\Controllers\VaccinationRecordController;
use App\Http\Controllers\HealthTokenController;

/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "web" middleware group. Make something great!
|
*/

Route::middleware([
    'auth:sanctum',
    config('jetstream.auth_middleware'),
    'verified'
])->group(function () {
    Route::get('/', function () {
        return view('welcome');
    });

    // Insurance Policy Routes
    Route::resource('insurance/policies', InsurancePolicyController::class)->names('insurance.policies');
    Route::post('insurance/policies/{policy}/cancel', [InsurancePolicyController::class, 'cancel'])->name('insurance.policies.cancel');

    // Vaccination Record Routes
    Route::resource('vaccination/records', VaccinationRecordController::class)->names('vaccination.records');
    Route::get('vaccination/statistics', [VaccinationRecordController::class, 'statistics'])->name('vaccination.statistics');

    // Health Token Routes
    Route::resource('health-tokens', HealthTokenController::class);
    Route::post('health-tokens/{token}/stake', [HealthTokenController::class, 'stake'])->name('health-tokens.stake');
    Route::post('health-tokens/{token}/unstake', [HealthTokenController::class, 'unstake'])->name('health-tokens.unstake');
    Route::post('health-tokens/{token}/transfer', [HealthTokenController::class, 'transfer'])->name('health-tokens.transfer');
    Route::post('health-tokens/{token}/calculate-rewards', [HealthTokenController::class, 'calculateRewards'])->name('health-tokens.calculate-rewards');
}); 