<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;

// ROOT -> LOGIN
Route::get('/', function () {
    return redirect()->route('login');
});

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
})->name('esqueci-senha');


// HOME PROTEGIDA
Route::get('/home', function () {
    return view('auth.home');
})->middleware('auth')->name('home');

// perfil
Route::get('/perfil', [AuthController::class, 'perfil'])
->middleware('auth')
->name('perfil');


    Route::post(
    '/perfil/atualizar',
    [AuthController::class, 'atualizarPerfil']
    )->middleware('auth');;


// configurações
Route::get('/config', function () {
    return view('auth.config');
})->middleware('auth')->name('config');

// conexões
Route::get('/conexoes', function () {
    return view('auth.conexoes');
})->middleware('auth')->name('conexoes');

// projetos
Route::get('/projetos', function () {
    return view('auth.projetos');
})->middleware('auth')->name('projetos');

// cadastro de projetos
Route::get('/projetoscad', function () {
    return view('auth.projetoscad');
})->middleware('auth')->name('projetoscad');



// LOGOUT
Route::post('/logout', [AuthController::class, 'logout']);