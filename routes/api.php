<?php

use App\Http\Controllers\Api\AuthController;
use App\Http\Controllers\Api\DoctorController;
use App\Http\Controllers\Api\HospitalController;
use App\Http\Controllers\Api\PatientController;
use App\Http\Controllers\Api\ProductController;
use App\Http\Controllers\Api\PublicController;
use App\Http\Controllers\Api\UserController;
use Illuminate\Support\Facades\Route;

Route::middleware(['api', 'throttle:60'])->group(function () {
    Route::middleware(['guest'])->group(function () {
        Route::post('/login', [AuthController::class, 'login']);
        Route::post('/register', [AuthController::class, 'register']);
    });

    Route::get('/hospitals', [HospitalController::class, 'all']);
    Route::get('/doctors', [DoctorController::class, 'all']);
    Route::get('/products', [ProductController::class, 'all']);

    Route::get('/agreement', [PublicController::class, 'agreement']);
    Route::get('/privacy', [PublicController::class, 'privacy']);
    Route::get('/provinces', [PublicController::class, 'provinces']);
    Route::get('/genders', [PublicController::class, 'genders']);

    Route::middleware(['auth:sanctum'])->group(function () {
        Route::get('/user', [AuthController::class, 'user']);
        Route::post('/logout', [AuthController::class, 'logout']);

        Route::post('/update-password', [UserController::class, 'updatePassword']);

        Route::get('/patient', [PatientController::class, 'patient']);
        Route::post('/patient/update', [PatientController::class, 'update']);
    });
});
