<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// LOGIN
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);


// REGISTRO
Route::get('/registro', function () {
    return view('auth.registro');
})->name('registro');

Route::post('/registro', [AuthController::class, 'register']);


// ESQUECI SENHA
Route::get('/esqueci-senha', function () {
    return view('auth.esqueci-senha');
});


// HOME PROTEGIDA
Route::get('/home', function () {
    return view('auth.home');
})->middleware('auth');


// LOGOUT
Route::post('/logout', [AuthController::class, 'logout']);