<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;


Route::get('/registro', function () {
    return view('auth.registro');
});

Route::post('/registro', [AuthController::class, 'register']);