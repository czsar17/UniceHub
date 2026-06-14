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
Route::get('/home', [AuthController::class, 'home'])
    ->middleware('auth')
    ->name('home');
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
Route::get('/conexoes', [AuthController::class, 'conexoes'])
    ->middleware('auth')
    ->name('conexoes');

// projetos
Route::get('/projetos', function () {
    return view('auth.projetos');
})->middleware('auth')->name('projetos');

// cadastro de projetos
Route::get('/projetoscad', function () {
    return view('auth.projetoscad');
})->middleware('auth')->name('projetoscad');

// seguir usuário
Route::post(
    '/seguir/{id}',
    [AuthController::class, 'seguir']
)->middleware('auth')->name('seguir.enviar');


Route::post(
    '/seguir/aceitar/{id}',
    [AuthController::class, 'aceitarSeguidor']
)->middleware('auth')->name('seguir.aceitar');

Route::post(
    '/seguir/recusar/{id}',
    [AuthController::class, 'recusarSeguidor']
)->middleware('auth')->name('seguir.recusar');


Route::post('/bloquear/{id}', [AuthController::class, 'bloquear'])
    ->middleware('auth')
    ->name('usuario.bloquear');
    
// LOGOUT
Route::post('/logout', [AuthController::class, 'logout']);