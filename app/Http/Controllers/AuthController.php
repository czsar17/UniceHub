<?php

namespace App\Http\Controllers;
use App\Models\Atividade;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class AuthController extends Controller
{
    // REGISTRO
    public function register(Request $request)
    {
        $request->merge([
            'cpf' => preg_replace('/\D/', '', $request->cpf)
        ]);

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
            'password' => Hash::make($request->password),
            'foto' => 'images/default-user.png',
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

    public function perfil()
{
    $atividades = Auth::user()
        ->atividades()
        ->latest()
        ->take(5)
        ->get();

     return view('auth.perfil', compact('atividades'));
}

public function atualizarPerfil(Request $request)
{
    $user = Auth::user();

    $request->validate([
        'name' => 'required|max:255',
        'email' => [
            'required',
            'email',
            Rule::unique('users')->ignore($user->id),
        ],
        'telefone' => 'nullable|max:20',
        'curso' => [
            'nullable',
            Rule::in([
                'ADS',
                'Análise e Desenvolvimento de Sistemas',
                'Engenharia de Software',
                'Ciência da Computação',
            ]),
        ],
        'sobre_mim' => 'nullable|max:350',
        'interesses_markdown' => 'nullable|string',
        'tecnologias' => 'nullable|array|max:8',
        'tecnologias.*' => 'nullable|string|max:30',
    ]);

    $user->name = $request->name;
    $user->email = $request->email;
    $user->telefone = $request->telefone;
    $user->curso = $request->curso;
    $user->sobre_mim = $request->sobre_mim;
    $user->interesses_markdown = $request->interesses_markdown;

    $tecnologias = collect($request->input('tecnologias', []))
        ->map(fn ($tecnologia) => trim($tecnologia))
        ->filter()
        ->unique()
        ->take(8)
        ->values()
        ->all();

    $user->tecnologias = $tecnologias;

    if($request->hasFile('foto')){

        $arquivo = $request->file('foto');

        $nomeArquivo = time().'.'.$arquivo->getClientOriginalExtension();

        $arquivo->move(public_path('images/perfis'), $nomeArquivo);

        $user->foto = 'images/perfis/'.$nomeArquivo;
    }

    $user->save();

    Atividade::create([
        'user_id' => $user->id,
        'descricao' => 'Atualizou as informações do perfil'
    ]);

    return back();
}
}
