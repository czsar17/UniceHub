<?php

namespace App\Http\Controllers;
use App\Models\Atividade;
use App\Models\Projeto;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
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

        $firstAdmin = !User::where('is_admin', true)->exists();
        $isProfessor = $request->tipo === 'professor';

        // CRIA USUÁRIO
        $user = User::create([
            'name' => $request->name,
            'cpf' => $request->cpf,
            'email' => $request->email,
            'data_nascimento' => $request->data_nascimento,
            'tipo' => $request->tipo,
            'is_admin' => $firstAdmin,
            'approval_status' => $isProfessor && !$firstAdmin ? 'pending' : 'approved',
            'approval_requested_at' => $isProfessor && !$firstAdmin ? now() : null,
            'password' => Hash::make($request->password),
            'foto' => 'images/default-user.png',
        ]);

        if ($isProfessor && !$firstAdmin) {
            return redirect()->route('login')->with('status', 'Cadastro enviado para análise. Você poderá acessar o sistema após aprovação de um administrador.');
        }

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

        if (Auth::attempt($credentials)) {
            $user = Auth::user();

            if ($user->tipo === 'professor' && $user->approval_status === 'pending') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return response()->view('auth.professor-pending', ['email' => $credentials['email']]);
            }

            if ($user->tipo === 'professor' && $user->approval_status === 'rejected') {
                Auth::logout();
                $request->session()->invalidate();
                $request->session()->regenerateToken();

                return back()->withInput($request->only('email'))->withErrors([
                    'email' => 'Seu cadastro de professor foi negado. Entre em contato com a administração.',
                ]);
            }

            $request->session()->regenerate();

            return redirect('/home');
        }

        return back()->withInput($request->only('email'))->withErrors([
            'email' => 'E-mail ou senha inválidos. Verifique os dados e tente novamente.',
        ]);
    }


    public function forgotPasswordForm()
    {
        return view('auth.esqueci-senha', [
            'step' => session('password_reset_verified') ? 'reset' : (session('password_reset_email') ? 'code' : 'email'),
            'email' => session('password_reset_email'),
        ]);
    }

    public function sendPasswordResetCode(Request $request)
    {
        $data = $request->validate([
            'email' => 'required|email|exists:users,email',
        ], [
            'email.exists' => 'Não encontramos uma conta com este e-mail.',
        ]);

        $code = (string) random_int(100000, 999999);

        session([
            'password_reset_email' => $data['email'],
            'password_reset_code_hash' => Hash::make($code),
            'password_reset_expires_at' => now()->addMinutes(15)->timestamp,
            'password_reset_verified' => false,
        ]);

        Mail::raw("Seu código de recuperação UniceHub é: {$code}\n\nEle expira em 15 minutos.", function ($message) use ($data) {
            $message->to($data['email'])->subject('Código de recuperação de senha - UniceHub');
        });

        return redirect()->route('esqueci-senha')->with('status', 'Enviamos um código de 6 dígitos para o seu e-mail.');
    }

    public function verifyPasswordResetCode(Request $request)
    {
        $data = $request->validate([
            'code' => 'required|digits:6',
        ]);

        if (!session('password_reset_email') || !session('password_reset_code_hash')) {
            return redirect()->route('esqueci-senha')->withErrors(['email' => 'Solicite um novo código para continuar.']);
        }

        if (now()->timestamp > (int) session('password_reset_expires_at')) {
            session()->forget(['password_reset_code_hash', 'password_reset_expires_at', 'password_reset_verified']);
            return redirect()->route('esqueci-senha')->withErrors(['code' => 'O código expirou. Solicite um novo código.']);
        }

        if (!Hash::check($data['code'], session('password_reset_code_hash'))) {
            return back()->withErrors(['code' => 'Código inválido. Confira o e-mail e tente novamente.']);
        }

        session(['password_reset_verified' => true]);

        return redirect()->route('esqueci-senha')->with('status', 'Código validado. Agora defina sua nova senha.');
    }

    public function resetPasswordWithCode(Request $request)
    {
        if (!session('password_reset_verified') || !session('password_reset_email')) {
            return redirect()->route('esqueci-senha')->withErrors(['email' => 'Valide o código antes de trocar a senha.']);
        }

        $data = $request->validate([
            'password' => 'required|min:8|confirmed',
        ], [
            'password.min' => 'A senha deve ter no mínimo 8 caracteres.',
            'password.confirmed' => 'As senhas não coincidem.',
        ]);

        $user = User::where('email', session('password_reset_email'))->firstOrFail();
        $user->password = Hash::make($data['password']);
        $user->save();

        session()->forget(['password_reset_email', 'password_reset_code_hash', 'password_reset_expires_at', 'password_reset_verified']);

        return redirect()->route('login')->with('status', 'Senha redefinida com sucesso. Faça login com a nova senha.');
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

public function config()
  {
      return view('auth.config');
  }


private function defaultTheme(): array
{
    return [
        'primary_color' => '#2D6A63',
        'secondary_color' => '#FF7A1A',
        'accent_color' => '#73B98F',
        'background_color' => '#F4FBF8',
        'section_color' => '#FFFFFF',
        'text_color' => '#21433D',
        'text_secondary_color' => '#6D7D78',
        'input_background_color' => '#F8FAFC',
        'font_family' => 'Inter, Segoe UI, Arial, sans-serif',
        'font_size' => '16',
        'title_font_family' => 'Inter, Segoe UI, Arial, sans-serif',
        'layout_style' => 'glass',
        'border_style' => 'solid',
        'contrast' => '50',
        'soft_shadows' => true,
        'smooth_animations' => true,
        'high_contrast' => false,
        'reduce_motion' => false,
        'logo_path' => 'images/LOGOUNICEHUB-removebg-preview.png',
        'background_path' => 'images/themes/image-78.png',
        'auth_background_path' => 'images/themes/image-78.png',
    ];
}

private function systemTheme(): array
{
    if (!\Illuminate\Support\Facades\Schema::hasTable('system_settings')) {
        return $this->defaultTheme();
    }

    $value = DB::table('system_settings')->where('key', 'theme')->value('value');
    $theme = is_string($value) ? json_decode($value, true) : [];

    return array_merge($this->defaultTheme(), is_array($theme) ? $theme : []);
}

private function ensureAdmin(): void
{
    if (!Auth::user()?->is_admin) {
        abort(403);
    }
}

public function themeCss()
{
    $theme = $this->systemTheme();
    $fontSize = max(13, min(20, (int) ($theme['font_size'] ?? 16)));
    $contrast = max(0, min(100, (int) ($theme['contrast'] ?? 50)));
    $contrastPercent = 85 + ($contrast * 0.3);
    $radius = match ($theme['layout_style'] ?? 'glass') {
        'solid' => '18px',
        'compact' => '12px',
        default => '28px',
    };
    $cardOpacity = ($theme['layout_style'] ?? 'glass') === 'solid' ? '1' : (($theme['layout_style'] ?? 'glass') === 'compact' ? '0.92' : '0.80');
    $shadow = !empty($theme['soft_shadows']) ? '0 10px 30px rgba(0, 0, 0, 0.08)' : 'none';
    $motion = (!empty($theme['smooth_animations']) && empty($theme['reduce_motion'])) ? '0.3s' : '0s';
    $borderStyle = $theme['border_style'] ?? 'solid';
    $border = $borderStyle === 'none' ? 'none' : "1px {$borderStyle} rgba(45, 106, 99, 0.22)";
    $logo = asset($theme['logo_path']);
    $background = asset($theme['background_path']);
    $authBackground = asset($theme['auth_background_path']);

    $css = <<<CSS
:root {
    --cor-verde: {$theme['accent_color']};
    --cor-verde-escuro: {$theme['primary_color']};
    --cor-verde-btn: {$theme['accent_color']};
    --cor-verde-hover: {$theme['primary_color']};
    --cor-verde-claro: color-mix(in srgb, {$theme['accent_color']} 28%, white);
    --cor-texto: {$theme['text_color']};
    --auth-primary: {$theme['primary_color']};
    --auth-secondary: {$theme['secondary_color']};
    --auth-text: {$theme['text_color']};
    --auth-muted: {$theme['text_secondary_color']};
    --auth-input-bg: {$theme['input_background_color']};
    --font-principal: {$theme['font_family']};
    --font-titulos: {$theme['title_font_family']};
    --radius-card: {$radius};
    --shadow-card: {$shadow};
    --theme-card-opacity: {$cardOpacity};
    --theme-transition: {$motion};
    --theme-border: {$border};
    --theme-primary: {$theme['primary_color']};
    --theme-secondary: {$theme['secondary_color']};
    --theme-accent: {$theme['accent_color']};
    --theme-bg: {$theme['background_color']};
    --theme-section: {$theme['section_color']};
    --theme-text: {$theme['text_color']};
    --theme-text-muted: {$theme['text_secondary_color']};
    --theme-input-bg: {$theme['input_background_color']};
    font-size: {$fontSize}px;
}
body {
    background-color: {$theme['background_color']};
    background-repeat: no-repeat;
    background-size: cover;
    background-attachment: fixed;
    color: {$theme['text_color']};
    font-family: var(--font-principal);
    filter: contrast({$contrastPercent}%);
}
body:not(.registro):not(.auth-page) { background-image: url('{$background}'); }
body.registro, body.auth-page { background-image: url('{$authBackground}'); }
h1, h2, h3, .cfg-page-title h1, .cfg-breadcrumb a, .cfg-breadcrumb span, .post-content h2, .widget-card h3, .sidebar a, .menu-item { font-family: var(--font-titulos); }
a, .menu-icon, .header-icons i, .notification, .search-box button, .search-box i, .menu-item i, .cfg-section-header h2 i, .sec-icon, .widget-card h3 i { color: var(--theme-primary) !important; }
.header-logo, .logo-area img { content: url('{$logo}'); }
.header, .main-header, .sidebar, .widget-card, .sidebar-card, .post-card, .projects-header, .empty-state, .cfg-card, .cfg-sidebar-menu, .profile-card, .profile-header, .profile-tabs, .profile-project-card, .profile-user-card, .comentarios-card, .connections-main, .connection-card, .request-card, .suggestion, .filter-card, .summary-card, .convite-card, .project-card, .project-mini-card, .project-sidebar, .create-project-card, .member-card, .project-members, .preview-card, .summary-card.projeto, .comment-box, .notification-panel, .notification-card, .cfg-adm-main, .cfg-adm-preview-side, .cfg-adm-card, .cfg-theme-card, .cfg-layout-card, .cfg-effect-item, .cfg-action-card, .cfg-bg-card, .cfg-notif-group, .cfg-profile-type, .cfg-info-box, .cfg-privacy-section, .search-result-card, .search-user-card, .preview-panel, .config-card, .security-section, .notification-section, .profile-section, .project-menu-dropdown {
    border: var(--theme-border);
    box-shadow: var(--shadow-card);
    transition-duration: var(--theme-transition);
}
.header, .main-header, .sidebar, .widget-card, .sidebar-card, .post-card, .projects-header, .empty-state, .cfg-card, .cfg-sidebar-menu, .profile-card, .profile-header, .profile-tabs, .profile-project-card, .profile-user-card, .comentarios-card, .connections-main, .connection-card, .request-card, .suggestion, .filter-card, .summary-card, .convite-card, .project-card, .project-mini-card, .project-sidebar, .create-project-card, .member-card, .project-members, .preview-card, .summary-card.projeto, .comment-box, .notification-panel, .notification-card, .cfg-adm-main, .cfg-adm-preview-side, .cfg-adm-card, .cfg-theme-card, .cfg-layout-card, .cfg-effect-item, .cfg-action-card, .cfg-bg-card, .cfg-notif-group, .cfg-profile-type, .cfg-info-box, .cfg-privacy-section, .search-result-card, .search-user-card, .preview-panel, .connections-tabs, .connections-list, .connections-top, .config-card, .security-section, .notification-section, .profile-section, .project-menu-dropdown {
    background: var(--theme-section) !important;
    background-color: var(--theme-section) !important;
}
body.auth-page .login-card, body.registro .register-card, body.registro .user-selector {
    background-color: color-mix(in srgb, var(--theme-section) 88%, transparent);
    color: var(--theme-text);
}
body.registro .register-card, body.registro .user-selector {
    border: 1px solid rgba(255,255,255,.45);
}
.menu-item.active, .sidebar li.active, .sidebar .active, .sidebar a.active, .nav-link.active, .cfg-profile-type.active-type, .connections-tabs .active {
    background-color: color-mix(in srgb, var(--theme-accent) 18%, var(--theme-section));
}
.sidebar li, .sidebar a, .menu-item, .sidebar-profile h4, .projects-header h1, .empty-state h3, .connections-header h1, .connections-top h2, .card-header h3, .projects-header h1, .page-header h1, .cfg-page-title h1, .cfg-breadcrumb a, .cfg-breadcrumb span, .profile-info h1, .profile-info h2, .profile-card h2, .profile-card h3, .profile-card h4, .profile-tabs button, .filter-card h3, .summary-card h3, .summary-item, .project-card h3, .project-top h3, .create-project-card h1, .preview-card h3, .preview-card h4, .member-card, .cfg-card, .cfg-card h2, .cfg-card h3, .cfg-card h4, .cfg-card strong, .cfg-sidebar-menu .menu-group h3, .cfg-label, .cfg-security-left strong, .cfg-notif-info strong, .cfg-theme-card strong, .cfg-layout-card strong, .notification-card h4, .search-result-card h4, .search-user-card h4 {
    color: var(--theme-text) !important;
}
.sidebar-profile span, .projects-header p, .empty-state p, .connections-header p, .connections-top span, .projects-header p, .page-header p, .cfg-page-title p, .cfg-breadcrumb i, .profile-stats, .profile-card p, .profile-card label, .profile-card span, .section-title-row span, .project-card p, .project-description, .project-footer, .create-project-card label, .preview-card p, .cfg-card p, .cfg-card span, .cfg-card small, .cfg-field-hint, .cfg-security-left span, .cfg-notif-info span, .cfg-theme-card small, .cfg-layout-card small, .notification-card span, .search-result-card p, .search-user-card span {
    color: var(--theme-text-muted) !important;
}
.widget-card, .sidebar-card, .post-card, .projects-header, .empty-state, .project-card, .profile-card, .profile-header, .profile-tabs, .filter-card, .summary-card, .cfg-card, .connections-main {
    backdrop-filter: blur(12px);
}
button, .cfg-btn-primary, .ver-projeto-btn, .conectar-btn, .connect-btn, .btn-primary, .submit-btn, .register-btn { border-color: transparent; }
.cfg-btn-primary, .ver-projeto-btn, .conectar-btn, .connect-btn, .suggestion button, .project-mini-card button, .accept-btn, .new-project-btn, .btn-primary, .submit-btn { background-color: var(--theme-accent); color: #fff; }
input, textarea, select, .cfg-input, .cfg-select, .cfg-readonly-field, .cfg-readonly-badge, .cfg-eye-btn, .profile-field, .nome-input, .curso-input, .search-box input, .connections-search input, .members-search, .filter-card input, .filter-card select, .create-project-card input, .create-project-card textarea, .create-project-card select {
    background-color: var(--theme-input-bg) !important;
    border-color: color-mix(in srgb, var(--theme-primary) 24%, transparent) !important;
    color: var(--theme-text) !important;
}
.search-box {
    background: transparent !important;
    border: none !important;
    box-shadow: none !important;
    border-radius: 999px !important;
}
.search-box input {
    border-radius: 999px !important;
}
.search-box button {
    border-radius: 999px !important;
}
input::placeholder, textarea::placeholder, .search-box input::placeholder, .cfg-input::placeholder {
    color: color-mix(in srgb, var(--theme-text-muted) 72%, transparent) !important;
}
input[type="date"]::-webkit-calendar-picker-indicator { filter: none; }
.tags span, .connections-count, .search-result-tags span, .project-tag, .tech-tag {
    background-color: color-mix(in srgb, var(--theme-accent) 18%, var(--theme-section));
    color: var(--theme-primary);
}
.empty-connections i, .search-empty-state i { color: color-mix(in srgb, var(--theme-primary) 32%, var(--theme-section)); }
.teacher-verified-badge {
    display: inline-flex !important;
    align-items: center !important;
    justify-content: center !important;
    width: fit-content !important;
    min-height: 24px !important;
    margin: 0 !important;
    padding: 4px 9px !important;
    border-radius: 999px !important;
    background: color-mix(in srgb, var(--theme-primary) 10%, var(--theme-section)) !important;
    color: var(--theme-primary) !important;
    border: 1px solid color-mix(in srgb, var(--theme-primary) 24%, transparent) !important;
    font-size: 11px !important;
    font-weight: 800 !important;
    line-height: 1 !important;
    box-shadow: none !important;
}
.teacher-verified-badge i { color: var(--theme-accent) !important; font-size: 12px !important; }
.teacher-verified-badge.compact, .teacher-verified-badge.mini {
    width: 20px !important;
    height: 20px !important;
    min-width: 20px !important;
    min-height: 20px !important;
    padding: 0 !important;
    overflow: hidden !important;
    font-size: 0 !important;
    white-space: nowrap !important;
    background: var(--theme-section) !important;
}
.teacher-verified-badge.profile {
    min-height: 28px !important;
    margin: 6px 0 8px !important;
    padding: 6px 10px !important;
    background: color-mix(in srgb, var(--theme-accent) 14%, var(--theme-section)) !important;
    font-size: 12px !important;
}
CSS;

    $layoutStyle = $theme['layout_style'] ?? 'glass';
    if ($layoutStyle === 'solid') {
        $css .= "
/* Layout: Painel solido */
.header, .main-header, .sidebar, .widget-card, .sidebar-card, .post-card, .projects-header, .empty-state, .project-card, .filter-card, .summary-card, .cfg-card, .cfg-sidebar-menu, .profile-card, .profile-header, .profile-tabs, .connections-main, .search-result-card, .search-user-card {
    backdrop-filter: none !important;
    border-radius: 16px !important;
    box-shadow: 0 8px 18px rgba(0, 0, 0, .06) !important;
}
.main-container { gap: 28px; }
.sidebar { width: 230px; }
.sidebar li { border-radius: 12px; margin-bottom: 8px; }
.profile-body { grid-template-columns: minmax(0, 1fr) 340px; align-items: start; }
.profile-tabs { justify-content: flex-start; gap: 8px; }
.profile-tabs button { border-radius: 10px; }
.projects-container, .connections-layout { gap: 24px; }
.cfg-layout { grid-template-columns: 220px minmax(0, 1fr); gap: 20px; }
.cfg-card { padding: 26px !important; }
";
    }

    if ($layoutStyle === 'compact') {
        $css .= "
/* Layout: Compacto */
.header, .main-header { height: 64px !important; min-height: 64px; border-radius: 18px !important; padding-left: 22px !important; padding-right: 22px !important; }
.search-box input { height: 44px !important; }
.main-container { gap: 14px; padding: 94px 14px 14px !important; }
.sidebar { width: 196px; padding: 12px !important; border-radius: 16px !important; }
.sidebar li { height: 46px; padding: 0 12px; border-radius: 12px; margin-bottom: 6px; font-size: 15px; }
.sidebar-profile { padding: 10px !important; border-radius: 14px !important; }
.logout { min-height: 42px; border-radius: 12px !important; }
.projects-header, .empty-state, .post-card, .project-card, .filter-card, .summary-card, .cfg-card, .cfg-sidebar-menu, .profile-card, .profile-header, .profile-tabs, .search-result-card, .search-user-card {
    padding: 16px !important;
    border-radius: 12px !important;
}
.profile-body { grid-template-columns: 1fr; gap: 12px; }
.profile-tabs { padding: 8px !important; gap: 6px; }
.profile-tabs button { min-height: 36px; border-radius: 9px; }
.cfg-layout { grid-template-columns: 190px minmax(0, 1fr); gap: 14px; }
.cfg-sidebar-menu { width: 190px; }
.cfg-sidebar-menu .menu-group { margin-bottom: 14px; }
.cfg-sidebar-menu .menu-item { min-height: 40px; padding: 9px 10px; }
.cfg-section-header { padding-bottom: 12px; margin-bottom: 14px; }
.project-logo { width: 64px !important; height: 64px !important; min-width: 64px !important; }
";
    }

    if (!empty($theme['high_contrast'])) {
        $css .= "
body { filter: contrast(120%); }
";
    }
    if (!empty($theme['reduce_motion'])) {
        $css .= "
*, *::before, *::after { animation-duration: 0.01ms !important; transition-duration: 0.01ms !important; }
";
    }

    return response($css, 200)->header('Content-Type', 'text/css');
}

public function adminUsuarios()
{
    $this->ensureAdmin();

    return response()->json([
        'usuarios' => User::orderByDesc('is_admin')
            ->orderBy('name')
            ->get(['id', 'name', 'email', 'tipo', 'curso', 'foto', 'is_admin'])
            ->map(fn (User $usuario) => [
                'id' => $usuario->id,
                'name' => $usuario->name,
                'email' => $usuario->email,
                'tipo' => $usuario->tipo,
                'curso' => $usuario->curso,
                'foto' => asset($usuario->foto ?: 'images/default-user.png'),
                'is_admin' => (bool) $usuario->is_admin,
                'is_me' => $usuario->id === Auth::id(),
            ])->values(),
    ]);
}

public function adminAtualizarUsuario(Request $request)
{
    $this->ensureAdmin();

    $data = $request->validate([
        'user_id' => 'required|exists:users,id',
        'is_admin' => 'required|boolean',
    ]);

    $usuario = User::findOrFail($data['user_id']);
    $isAdmin = (bool) $data['is_admin'];

    if (!$isAdmin && $usuario->is_admin && User::where('is_admin', true)->count() <= 1) {
        return response()->json([
            'errors' => ['is_admin' => ['O sistema precisa manter pelo menos um administrador.']],
        ], 422);
    }

    $usuario->is_admin = $isAdmin;
    $usuario->save();

    return response()->json(['message' => 'Permissão atualizada com sucesso.']);
}

public function adminProfessoresPendentes()
{
    $this->ensureAdmin();

    return response()->json([
        'professores' => User::where('tipo', 'professor')
            ->where('approval_status', 'pending')
            ->orderBy('approval_requested_at')
            ->get(['id', 'name', 'email', 'cpf', 'data_nascimento', 'curso', 'telefone', 'approval_requested_at'])
            ->map(fn (User $professor) => [
                'id' => $professor->id,
                'name' => $professor->name,
                'email' => $professor->email,
                'cpf' => $professor->cpf,
                'data_nascimento' => $professor->data_nascimento ? date('d/m/Y', strtotime($professor->data_nascimento)) : 'Não informado',
                'curso' => $professor->curso ?: 'Não informado',
                'telefone' => $professor->telefone ?: 'Não informado',
                'requested_at' => optional($professor->approval_requested_at)->format('d/m/Y H:i') ?: 'Data não informada',
            ])->values(),
    ]);
}

public function adminRevisarProfessor(Request $request)
{
    $this->ensureAdmin();

    $data = $request->validate([
        'user_id' => 'required|exists:users,id',
        'action' => ['required', Rule::in(['approve', 'reject'])],
    ]);

    $professor = User::where('tipo', 'professor')->findOrFail($data['user_id']);

    if ($professor->approval_status !== 'pending') {
        return response()->json([
            'errors' => ['user_id' => ['Este cadastro já foi analisado.']],
        ], 422);
    }

    $professor->approval_status = $data['action'] === 'approve' ? 'approved' : 'rejected';
    $professor->approval_reviewed_at = now();
    $professor->approval_reviewed_by = Auth::id();
    $professor->save();

    return response()->json([
        'message' => $data['action'] === 'approve'
            ? 'Cadastro de professor aprovado.'
            : 'Cadastro de professor negado.',
    ]);
}

public function adminAtualizarTema(Request $request)
{
    $this->ensureAdmin();

    $data = $request->validate([
        'primary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'accent_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'section_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'text_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'text_secondary_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'input_background_color' => ['required', 'regex:/^#[0-9A-Fa-f]{6}$/'],
        'font_family' => [
            'required',
            Rule::in([
                'Inter, Segoe UI, Arial, sans-serif',
                'Poppins, Segoe UI, Arial, sans-serif',
                'Arial, sans-serif',
                'Georgia, serif',
            ]),
        ],
        'font_size' => 'required|integer|min:13|max:20',
        'title_font_family' => [
            'required',
            Rule::in([
                'Inter, Segoe UI, Arial, sans-serif',
                'Poppins, Segoe UI, Arial, sans-serif',
                'Arial, sans-serif',
                'Georgia, serif',
            ]),
        ],
        'layout_style' => ['required', Rule::in(['glass', 'solid', 'compact'])],
        'border_style' => ['required', Rule::in(['solid', 'dashed', 'dotted', 'none'])],
        'contrast' => 'required|integer|min:0|max:100',
        'soft_shadows' => 'nullable|boolean',
        'smooth_animations' => 'nullable|boolean',
        'high_contrast' => 'nullable|boolean',
        'reduce_motion' => 'nullable|boolean',
        'background_path' => 'nullable|string|max:255',
        'auth_background_path' => 'nullable|string|max:255',
        'logo' => 'nullable|file|mimes:png,jpg,jpeg,svg,webp|max:5120',
        'background' => 'nullable|image|max:5120',
        'auth_background' => 'nullable|image|max:5120',
    ]);

    $theme = $this->systemTheme();
    $theme = array_merge($theme, collect($data)->except(['logo', 'background', 'auth_background', 'background_path', 'auth_background_path'])->all());

    foreach (['background_path', 'auth_background_path'] as $pathKey) {
        $submittedPath = ltrim((string) $request->input($pathKey, ''), '/');
        if ($submittedPath !== '' && (str_starts_with($submittedPath, 'images/themes/') || str_starts_with($submittedPath, 'images/admin/') || in_array($submittedPath, ['images/bg-home.png', 'images/bgg-image.png'], true))) {
            $theme[$pathKey] = $submittedPath;
        }
    }

    foreach (['soft_shadows', 'smooth_animations', 'high_contrast', 'reduce_motion'] as $flag) {
        $theme[$flag] = $request->boolean($flag);
    }

    $dir = public_path('images/admin');
    if (!is_dir($dir)) {
        mkdir($dir, 0775, true);
    }

    foreach ([
        'logo' => 'logo_path',
        'background' => 'background_path',
        'auth_background' => 'auth_background_path',
    ] as $input => $key) {
        if ($request->hasFile($input)) {
            $file = $request->file($input);
            $name = $input . '-' . time() . '.' . $file->getClientOriginalExtension();
            $file->move($dir, $name);
            $theme[$key] = 'images/admin/' . $name;
        }
    }

    DB::table('system_settings')->updateOrInsert(
        ['key' => 'theme'],
        ['value' => json_encode($theme), 'updated_at' => now(), 'created_at' => now()]
    );

    return response()->json([
        'message' => 'Personalização salva com sucesso.',
        'theme' => $theme,
    ]);
}

public function adminRestaurarTema()
{
    $this->ensureAdmin();
    $theme = $this->defaultTheme();

    DB::table('system_settings')->updateOrInsert(
        ['key' => 'theme'],
        ['value' => json_encode($theme), 'updated_at' => now(), 'created_at' => now()]
    );

    return response()->json([
        'message' => 'Tema restaurado com sucesso.',
        'theme' => $theme,
    ]);
}

  public function excluirConta(Request $request)
  {
      $request->validate([
          'password' => 'required',
      ], [
          'password.required' => 'Informe sua senha para excluir a conta.',
      ]);

      $user = Auth::user();

      if (!Hash::check($request->password, $user->password)) {
          return response()->json([
              'errors' => ['password' => ['Senha incorreta.']]
          ], 422);
      }

      if ($user->is_admin && !User::where('is_admin', true)->where('id', '!=', $user->id)->exists()) {
          return response()->json([
              'errors' => ['password' => ['Defina outro administrador antes de excluir esta conta.']]
          ], 422);
      }

      DB::transaction(function () use ($user) {
          $user->delete();
      });

      Auth::logout();
      $request->session()->invalidate();
      $request->session()->regenerateToken();

      return response()->json([
          'message' => 'Conta excluida com sucesso.',
          'redirect' => route('login'),
      ]);
  }

  public function trocarSenha(Request $request)
  {
      $request->validate([
          'current_password'      => 'required',
          'password'              => 'required|min:8|confirmed',
      ], [
          'current_password.required' => 'Informe a senha atual.',
          'password.min'              => 'A nova senha deve ter no mínimo 8 caracteres.',
          'password.confirmed'        => 'As senhas não coincidem.',
      ]);

      $user = Auth::user();

      if (!Hash::check($request->current_password, $user->password)) {
          return response()->json([
              'errors' => ['current_password' => ['Senha atual incorreta.']]
          ], 422);
      }

      $user->password = Hash::make($request->password);
      $user->save();

      Atividade::create([
          'user_id'   => $user->id,
          'descricao' => 'Alterou a senha da conta.',
      ]);

      return response()->json(['message' => 'Senha alterada com sucesso.']);
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
        'data_nascimento' => 'nullable|date',
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

    $dadosPerfil = [
        'name' => $request->name,
        'email' => $request->email,
        'telefone' => $request->telefone,
        'curso' => $request->curso,
        'sobre_mim' => $request->sobre_mim,
        'interesses_markdown' => $request->interesses_markdown,
        'tecnologias' => $tecnologias,
    ];

    if ($request->has('data_nascimento')) {
        $dadosPerfil['data_nascimento'] = $request->data_nascimento;
    }

    $user->fill($dadosPerfil);

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

    if ($request->expectsJson()) {
        return response()->json([
            'message' => 'Perfil atualizado com sucesso.',
            'user' => [
                'name' => $user->name,
                'email' => $user->email,
                'telefone' => $user->telefone,
                'curso' => $user->curso,
                'data_nascimento' => $user->data_nascimento,
            ],
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

    $projetosFeed = Projeto::with(['criador', 'curtidas'])
        ->withCount('curtidas')
        ->withCount('comentarios')
        ->latest()
        ->get();

    $projetosDestaque = Projeto::with(['membros' => function ($query) {
            $query->wherePivot('status', 'aceito');
        }])
        ->withCount('curtidas')
        ->withCount('comentarios')
        ->get()
        ->sortByDesc(fn ($projeto) => $projeto->curtidas_count + $projeto->comentarios_count)
        ->take(2)
        ->values();

    $tecnologiasEmAlta = User::query()
        ->whereNotNull('tecnologias')
        ->get(['tecnologias'])
        ->flatMap(fn ($usuario) => $usuario->tecnologias ?? [])
        ->map(fn ($tecnologia) => trim((string) $tecnologia))
        ->filter()
        ->groupBy(fn ($tecnologia) => strtolower($tecnologia))
        ->map(fn ($grupo) => [
            'nome' => $grupo->first(),
            'total' => $grupo->count(),
        ])
        ->sortByDesc('total')
        ->take(4)
        ->values();

    return view('auth.home', compact(
        'sugestoes',
        'projetosFeed',
        'projetosDestaque',
        'tecnologiasEmAlta'
    ));
}

public function notificacoesHeader()
{
    $user = Auth::user();

    $solicitacoesConexao = Follower::where('seguido_id', $user->id)
        ->where('status', 'pendente')
        ->with('seguidor')
        ->latest()
        ->take(4)
        ->get()
        ->map(fn ($follow) => [
            'tipo' => 'Conexão',
            'icone' => 'fa-user-plus',
            'titulo' => optional($follow->seguidor)->name . ' quer se conectar',
            'texto' => optional($follow->seguidor)->curso ?: 'Nova solicitação de conexão.',
            'url' => route('conexoes'),
            'data' => optional($follow->created_at)->diffForHumans(),
            'timestamp' => optional($follow->created_at)->timestamp ?? 0,
        ]);

    $conexoesAceitas = Follower::where('seguidor_id', $user->id)
        ->where('status', 'aceito')
        ->with('seguido')
        ->latest('updated_at')
        ->take(3)
        ->get()
        ->map(fn ($follow) => [
            'tipo' => 'Conexão',
            'icone' => 'fa-user-check',
            'titulo' => optional($follow->seguido)->name . ' aceitou sua conexão',
            'texto' => 'Vocês agora estão conectados.',
            'url' => route('conexoes'),
            'data' => optional($follow->updated_at)->diffForHumans(),
            'timestamp' => optional($follow->updated_at)->timestamp ?? 0,
        ]);

    $convitesProjeto = Projeto::whereHas('membros', function ($query) use ($user) {
            $query->where('users.id', $user->id)
                ->where('projeto_user.status', 'pendente');
        })
        ->with('criador')
        ->latest()
        ->take(4)
        ->get()
        ->map(fn ($projeto) => [
            'tipo' => 'Projeto',
            'icone' => 'fa-folder-open',
            'titulo' => 'Convite para ' . $projeto->nome,
            'texto' => 'Enviado por ' . (optional($projeto->criador)->name ?: 'um usuário'),
            'url' => route('projetos'),
            'data' => optional($projeto->updated_at)->diffForHumans(),
            'timestamp' => optional($projeto->updated_at)->timestamp ?? 0,
        ]);

    $comentarios = \App\Models\ComentarioProjeto::whereHas('projeto', function ($query) use ($user) {
            $query->where('user_id', $user->id);
        })
        ->where('user_id', '!=', $user->id)
        ->with(['user', 'projeto'])
        ->latest()
        ->take(5)
        ->get()
        ->map(fn ($comentario) => [
            'tipo' => 'Comentário',
            'icone' => 'fa-comment',
            'titulo' => optional($comentario->user)->name . ' comentou em ' . optional($comentario->projeto)->nome,
            'texto' => \Illuminate\Support\Str::limit($comentario->comentario, 80),
            'url' => $comentario->projeto ? route('projetos.show', $comentario->projeto) : route('home'),
            'data' => optional($comentario->created_at)->diffForHumans(),
            'timestamp' => optional($comentario->created_at)->timestamp ?? 0,
        ]);

    $curtidas = DB::table('projeto_curtidas')
        ->join('projetos', 'projetos.id', '=', 'projeto_curtidas.projeto_id')
        ->join('users', 'users.id', '=', 'projeto_curtidas.user_id')
        ->where('projetos.user_id', $user->id)
        ->where('projeto_curtidas.user_id', '!=', $user->id)
        ->select(
            'projeto_curtidas.created_at',
            'projetos.id as projeto_id',
            'projetos.nome as projeto_nome',
            'users.name as user_name'
        )
        ->latest('projeto_curtidas.created_at')
        ->take(5)
        ->get()
        ->map(fn ($curtida) => [
            'tipo' => 'Interação',
            'icone' => 'fa-heart',
            'titulo' => $curtida->user_name . ' curtiu ' . $curtida->projeto_nome,
            'texto' => 'Seu projeto recebeu uma nova curtida.',
            'url' => route('projetos.show', $curtida->projeto_id),
            'data' => \Carbon\Carbon::parse($curtida->created_at)->diffForHumans(),
            'timestamp' => \Carbon\Carbon::parse($curtida->created_at)->timestamp,
        ]);

    $notificacoes = collect()
        ->merge($solicitacoesConexao)
        ->merge($conexoesAceitas)
        ->merge($convitesProjeto)
        ->merge($comentarios)
        ->merge($curtidas)
        ->filter(fn ($notificacao) => trim((string) $notificacao['titulo']) !== '')
        ->sortByDesc(fn ($notificacao) => $notificacao['timestamp'])
        ->take(10)
        ->map(function ($notificacao) {
            unset($notificacao['timestamp']);
            return $notificacao;
        })
        ->values();

    return response()->json([
        'total' => $notificacoes->count(),
        'notificacoes' => $notificacoes,
    ]);
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
