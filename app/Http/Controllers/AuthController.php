<?php

namespace App\Http\Controllers;
use App\Models\Atividade;
use App\Models\Projeto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

use App\Models\Follower;

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

    private function projetosDoUsuario(User $user)
{
    return Projeto::where(function ($query) use ($user) {
        $query->where('user_id', $user->id)
            ->orWhereHas('membros', function ($query) use ($user) {
                $query->where('users.id', $user->id)
                    ->where('projeto_user.status', 'aceito');
            });
    })
        ->with(['criador', 'membros' => function ($query) {
            $query->wherePivot('status', 'aceito');
        }])
        ->latest()
        ->get();
}

public function perfil()
{
    $perfilUser = Auth::user();

    $atividades = $perfilUser
        ->atividades()
        ->latest()
        ->take(5)
        ->get();

    $projetosPerfil = $this->projetosDoUsuario($perfilUser);
    $perfilFollowStatus = null;

    return view('auth.perfil', compact('atividades', 'perfilUser', 'projetosPerfil', 'perfilFollowStatus'));
}

public function visualizarUsuario(User $user)
{
    if ($user->id === Auth::id()) {
        return redirect()->route('perfil');
    }

    $perfilUser = $user;

    $atividades = $perfilUser
        ->atividades()
        ->latest()
        ->take(5)
        ->get();

    $projetosPerfil = $this->projetosDoUsuario($perfilUser);
    $perfilFollowStatus = Follower::where('seguidor_id', Auth::id())
        ->where('seguido_id', $perfilUser->id)
        ->value('status');

    return view('auth.perfil', compact('atividades', 'perfilUser', 'projetosPerfil', 'perfilFollowStatus'));
}

public function buscar(Request $request)
{
    $termo = trim((string) $request->query('q', ''));

    $usuarios = collect();
    $projetos = collect();

    if ($termo !== '') {
        $usuarios = User::where('id', '!=', Auth::id())
            ->where(function ($query) use ($termo) {
                $query->where('name', 'like', "%{$termo}%")
                    ->orWhere('curso', 'like', "%{$termo}%")
                    ->orWhere('tipo', 'like', "%{$termo}%");
            })
            ->orderBy('name')
            ->get();

        $projetos = Projeto::with(['criador', 'membros'])
            ->where(function ($query) use ($termo) {
                $query->where('nome', 'like', "%{$termo}%")
                    ->orWhere('categoria', 'like', "%{$termo}%")
                    ->orWhere('status', 'like', "%{$termo}%")
                    ->orWhere('descricao', 'like', "%{$termo}%");
            })
            ->latest()
            ->get();
    }

    return view('auth.busca', compact('termo', 'usuarios', 'projetos'));
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

    $tecnologias = collect($request->input('tecnologias', []))
        ->map(fn ($tecnologia) => trim($tecnologia))
        ->filter()
        ->unique()
        ->take(8)
        ->values()
        ->all();

    $user->fill([
        'name' => $request->name,
        'email' => $request->email,
        'telefone' => $request->telefone,
        'curso' => $request->curso,
        'sobre_mim' => $request->sobre_mim,
        'interesses_markdown' => $request->interesses_markdown,
        'tecnologias' => $tecnologias,
    ]);

    if($request->hasFile('foto')){
        $arquivo = $request->file('foto');
        $nomeArquivo = time().'.'.$arquivo->getClientOriginalExtension();
        $arquivo->move(public_path('images/perfis'), $nomeArquivo);
        $user->foto = 'images/perfis/'.$nomeArquivo;
    }

    if ($user->isDirty() || $request->hasFile('foto')) {
        $user->save();

        Atividade::create([
            'user_id' => $user->id,
            'descricao' => 'Atualizou as informações do perfil'
        ]);
    }

    return back();
}

public function seguir($id)
{
    $user = Auth::user();

    if($user->id == $id){
        return back();
    }

    $existe = Follower::where('seguidor_id', $user->id)
        ->where('seguido_id', $id)
        ->exists();

    if(!$existe){

        Follower::create([
            'seguidor_id' => $user->id,
            'seguido_id' => $id,
            'status' => 'pendente'
        ]);
    }

    return back();
}

public function aceitarSeguidor($id)
{
    $follow = Follower::findOrFail($id);

    if($follow->seguido_id != Auth::id()){
        abort(403);
    }

    $follow->status = 'aceito';

    $follow->save();

    return back();
}

public function recusarSeguidor($id)
{
    $follow = Follower::findOrFail($id);

    if($follow->seguido_id != Auth::id()){
        abort(403);
    }

    $follow->delete();

    return back();
}


public function home()
{
    $user = Auth::user();

    $usuariosIgnorados = Follower::where('seguidor_id', $user->id)
        ->pluck('seguido_id');

    $sugestoes = User::where('id', '!=', $user->id)
        ->whereNotIn('id', $usuariosIgnorados)
        ->inRandomOrder()
        ->take(2)
        ->get();

    return view('auth.home', compact('sugestoes'));
}

public function conexoes()
{
    $user = Auth::user();

    // QUEM SEGUE VOCÊ
    $seguidores = Follower::where('seguido_id', $user->id)
        ->where('status', 'aceito')
        ->with('seguidor')
        ->get();

    // QUEM VOCÊ SEGUE
    $seguindo = Follower::where('seguidor_id', $user->id)
        ->where('status', 'aceito')
        ->with('seguido')
        ->get();

    // SOLICITAÇÕES PENDENTES
    $solicitacoes = Follower::where('seguido_id', $user->id)
        ->where('status', 'pendente')
        ->with('seguidor')
        ->latest()
        ->get();

    // CONVITES PENDENTES PARA PARTICIPAR DE PROJETOS
    $solicitacoesProjeto = Projeto::whereHas('membros', function ($query) use ($user) {
        $query->where('users.id', $user->id)
            ->where('projeto_user.status', 'pendente');
    })
        ->with('criador')
        ->latest()
        ->get();

    // BLOQUEADOS
    $bloqueados = Follower::where('seguidor_id', $user->id)
        ->where('status', 'bloqueado')
        ->with('seguido')
        ->get();

    // SUGESTÕES
    $ignorados = Follower::where('seguidor_id', $user->id)
        ->pluck('seguido_id');

    $sugestoes = User::where('id', '!=', $user->id)
        ->whereNotIn('id', $ignorados)
        ->inRandomOrder()
        ->take(3)
        ->get();

    $usuariosPesquisa = User::where('id', '!=', $user->id)
        ->orderBy('name')
        ->get();

    $projetosPesquisa = Projeto::with(['criador', 'membros'])
        ->latest()
        ->get();

    $relacoesUsuario = Follower::where(function ($query) use ($user) {
        $query->where('seguidor_id', $user->id)
            ->orWhere('seguido_id', $user->id);
    })->get();

    $statusUsuario = function (User $usuario) use ($relacoesUsuario, $user) {
        $enviada = $relacoesUsuario->first(fn ($relacao) => (int) $relacao->seguidor_id === (int) $user->id && (int) $relacao->seguido_id === (int) $usuario->id);
        $recebida = $relacoesUsuario->first(fn ($relacao) => (int) $relacao->seguidor_id === (int) $usuario->id && (int) $relacao->seguido_id === (int) $user->id);

        if (($enviada && $enviada->status === 'bloqueado') || ($recebida && $recebida->status === 'bloqueado')) {
            return ['label' => 'Bloqueado', 'can_follow' => false];
        }

        if (($enviada && $enviada->status === 'aceito') || ($recebida && $recebida->status === 'aceito')) {
            return ['label' => 'Conectado', 'can_follow' => false];
        }

        if ($enviada && $enviada->status === 'pendente') {
            return ['label' => 'Solicitação enviada', 'can_follow' => false];
        }

        if ($recebida && $recebida->status === 'pendente') {
            return ['label' => 'Solicitação recebida', 'can_follow' => false];
        }

        return ['label' => 'Conectar', 'can_follow' => true];
    };

    $usuariosPreview = $usuariosPesquisa->map(function (User $usuario) use ($statusUsuario) {
        $status = $statusUsuario($usuario);

        return [
            'id' => $usuario->id,
            'type' => 'usuario',
            'name' => $usuario->name,
            'course' => $usuario->curso ?: 'Curso não informado',
            'role' => ucfirst($usuario->tipo ?? 'usuário'),
            'photo' => asset($usuario->foto ?: 'images/default-user.png'),
            'about' => $usuario->sobre_mim ?: 'Este usuário ainda não adicionou uma descrição.',
            'email' => $usuario->email,
            'technologies' => $usuario->tecnologias ?? [],
            'search' => trim($usuario->name . ' ' . $usuario->curso . ' ' . $usuario->tipo . ' ' . implode(' ', $usuario->tecnologias ?? [])),
            'follow_url' => route('seguir.enviar', $usuario->id),
            'follow_label' => $status['label'],
            'can_follow' => $status['can_follow'],
        ];
    })->values();

    $projetosPreview = $projetosPesquisa->map(function (Projeto $projeto) {
        return [
            'id' => $projeto->id,
            'type' => 'projeto',
            'name' => $projeto->nome,
            'course' => $projeto->categoria ?: 'Projeto',
            'role' => $projeto->status,
            'photo' => $projeto->capa ? asset($projeto->capa) : asset('images/loading.png'),
            'about' => strip_tags($projeto->descricao ?: 'Este projeto ainda não possui descrição.'),
            'creator' => $projeto->criador->name ?? 'Criador não informado',
            'technologies' => $projeto->tecnologias ?? [],
            'members_count' => $projeto->membros->count(),
            'created_at' => optional($projeto->created_at)->format('d/m/Y'),
            'search' => trim($projeto->nome . ' ' . $projeto->categoria . ' ' . $projeto->status . ' ' . implode(' ', $projeto->tecnologias ?? [])),
            'view_url' => route('projetos'),
        ];
    })->values();

    return view('auth.conexoes', compact(
        'seguidores',
        'seguindo',
        'solicitacoes',
        'solicitacoesProjeto',
        'bloqueados',
        'sugestoes',
        'usuariosPesquisa',
        'projetosPesquisa',
        'relacoesUsuario',
        'usuariosPreview',
        'projetosPreview'
    ));
}

public function bloquear($id)
{
    $follow = Follower::where(function($q) use ($id){

        $q->where('seguidor_id', Auth::id())
          ->where('seguido_id', $id);

    })->orWhere(function($q) use ($id){

        $q->where('seguidor_id', $id)
          ->where('seguido_id', Auth::id());

    })->first();

    if($follow){

        $follow->status = 'bloqueado';
        $follow->save();

    }

    return back();
}

}
