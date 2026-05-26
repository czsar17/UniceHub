<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // REGISTRO
    public function register(Request $request)
    {
        $request->validate([
    'name' => 'required|min:3',

    'cpf' => [
        'required',
        'digits:11',
        'unique:users',
        'regex:/^\d{11}$/'
    ],

    'email' => 'required|email|unique:users',

    'data_nascimento' => 'required',

    'password' => 'required|min:8|confirmed',

    'tipo' => 'required|in:aluno,professor'
], [

    // CPF
    'cpf.required' => 'O CPF é obrigatório.',
    'cpf.unique' => 'Este CPF já está cadastrado.',
    'cpf.digits' => 'O CPF deve ter 11 dígitos.',
    'cpf.regex' => 'Digite um CPF válido.',

    // EMAIL
    'email.required' => 'O email é obrigatório.',
    'email.email' => 'Digite um email válido.',
    'email.unique' => 'Este email já está cadastrado.',

    // SENHA
    'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
    'password.confirmed' => 'As senhas não coincidem.'
]);

        // CRIA USUÁRIO
        $user = User::create([
            'name' => $request->name,
            'cpf' => $request->cpf,
            'email' => $request->email,
            'data_nascimento' => $request->data_nascimento,
            'tipo' => $request->tipo,
            'password' => Hash::make($request->password)
        ]);

        // LOGIN AUTOMÁTICO
        Auth::login($user);

        // REGENERA SESSÃO
        $request->session()->regenerate();

        // REDIRECIONA PRA HOME
        return redirect('/home');
    }

    // LOGIN
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email' => 'required|email',
            'password' => 'required'
        ]);

        if(Auth::attempt($credentials)){

            $request->session()->regenerate();

            return redirect('/home');
        }

        return back()->withErrors([
            'email' => 'Email ou senha inválidos'
        ]);
    }

    // LOGOUT
    public function logout(Request $request)
    {
        Auth::logout();

        $request->session()->invalidate();

        $request->session()->regenerateToken();

        return redirect('/login');
    }
}