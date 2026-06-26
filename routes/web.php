<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\AuthController;
use App\Http\Controllers\ProjetoController;

// ROOT -> LOGIN
Route::get('/', function () {
    return redirect()->route('login');
});

// LOGIN
Route::get('/login', function () {
    return view('auth.login');
})->name('login');

Route::post('/login', [AuthController::class, 'login']);

Route::get('/theme.css', [AuthController::class, 'themeCss'])->name('theme.css');


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

Route::get('/usuarios/{user}', [AuthController::class, 'visualizarUsuario'])
    ->middleware('auth')
    ->name('usuarios.show');

Route::get(
    '/api/projetos/{projeto}/comentarios',
    [ProjetoController::class, 'listarComentarios']
)->middleware('auth');
    
Route::get('/buscar', [AuthController::class, 'buscar'])
    ->middleware('auth')
    ->name('buscar');

Route::get('/notificacoes/header', [AuthController::class, 'notificacoesHeader'])
    ->middleware('auth')
    ->name('notificacoes.header');


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
Route::get('/projetos', [ProjetoController::class, 'index'])
    ->middleware('auth')
    ->name('projetos');

    Route::delete('/projetos/{projeto}/sair', [ProjetoController::class, 'sairProjeto'])
    ->middleware('auth')
    ->name('projetos.sair');

    Route::get(
    '/projetos/{projeto}/comentarios',
    [ProjetoController::class, 'comentarios']
)->middleware('auth');

Route::post(
    '/projetos/{projeto}/comentar',
    [ProjetoController::class, 'comentar']
)->middleware('auth')
->name('projetos.comentar');

Route::post(
    '/projetos/{projeto}/comentar',
    [ProjetoController::class, 'comentar']
)->middleware('auth')
->name('projetos.comentar');

Route::delete(
    '/comentarios/{comentario}',
    [ProjetoController::class, 'excluirComentario']
)->middleware('auth')
->name('comentarios.excluir');

    Route::post(
    '/projetos/{projeto}/curtir',
    [ProjetoController::class,'curtir']
)->name('projetos.curtir');

// cadastro de projetos
Route::get('/projetoscad', [ProjetoController::class, 'create'])
    ->middleware('auth')
    ->name('projetoscad');

Route::post('/projetos', [ProjetoController::class, 'store'])
    ->middleware('auth')
    ->name('projetos.store');

Route::get('/projetos/{projeto}/editar', [ProjetoController::class, 'edit'])
    ->middleware('auth')
    ->name('projetos.edit');

Route::get('/projetos/{projeto}', [ProjetoController::class, 'show'])
    ->middleware('auth')
    ->name('projetos.show');

Route::put('/projetos/{projeto}', [ProjetoController::class, 'update'])
    ->middleware('auth')
    ->name('projetos.update');

Route::post('/projetos/{projeto}/convidar', [ProjetoController::class, 'convidar'])
    ->middleware('auth')
    ->name('projetos.convidar');

Route::post('/projetos/{projeto}/aceitar', [ProjetoController::class, 'aceitarConvite'])
    ->middleware('auth')
    ->name('projetos.aceitar');

Route::post('/projetos/{projeto}/recusar', [ProjetoController::class, 'recusarConvite'])
    ->middleware('auth')
    ->name('projetos.recusar');

Route::delete('/projetos/{projeto}', [ProjetoController::class, 'destroy'])
    ->middleware('auth')
    ->name('projetos.destroy');

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


Route::get('/config', [AuthController::class, 'config'])
    ->middleware('auth')
    ->name('config');

Route::post('/config/senha', [AuthController::class, 'trocarSenha'])
    ->middleware('auth')
    ->name('config.senha');

Route::get('/config/admin/usuarios', [AuthController::class, 'adminUsuarios'])
    ->middleware('auth')
    ->name('admin.usuarios');

Route::post('/config/admin/usuarios', [AuthController::class, 'adminAtualizarUsuario'])
    ->middleware('auth')
    ->name('admin.usuarios.atualizar');

Route::post('/config/admin/tema', [AuthController::class, 'adminAtualizarTema'])
    ->middleware('auth')
    ->name('admin.tema.salvar');

Route::post('/config/admin/tema/restaurar', [AuthController::class, 'adminRestaurarTema'])
    ->middleware('auth')
    ->name('admin.tema.restaurar');

Route::post('/perfil/atualizar', [AuthController::class, 'atualizarPerfil'])
    ->middleware('auth')
    ->name('perfil.atualizar');