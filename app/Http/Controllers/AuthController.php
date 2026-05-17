<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name' => 'required|min:3',
            'cpf' => 'required|unique:users',
            'email' => 'required|email|unique:users',
            'data_nascimento' => 'required',
            'password' => 'required|min:8|confirmed',
            'tipo' => 'required|in:aluno,professor'
        ]);

        User::create([
            'name' => $request->name,
            'cpf' => $request->cpf,
            'email' => $request->email,
            'data_nascimento' => $request->data_nascimento,
            'tipo' => $request->tipo,
            'password' => Hash::make($request->password)
        ]);

        return redirect('/registro')
            ->with('success', 'Usuário cadastrado com sucesso!');
    }
}