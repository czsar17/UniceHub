<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha</title>
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="{{ route('theme.css') }}">
</head>
<body class="auth-page">
    <header class="main-header">
        <div class="logo-area">
            <img src="{{ asset($systemTheme['logo_path'] ?? 'images/LOGOUNICEHUB-removebg-preview.png') }}" alt="UniceHub">
        </div>
    </header>

    <div class="container">
        <div class="login-card recovery-card">
            <img src="{{ asset($systemTheme['logo_path'] ?? 'images/LOGOUNICEHUB-removebg-preview.png') }}" class="logo" alt="UniceHub">

            <h1>{{ $step === 'reset' ? 'Nova senha' : ($step === 'code' ? 'Código de verificação' : 'Recuperar senha') }}</h1>
            <p>
                @if($step === 'reset')
                    Crie uma nova senha para sua conta.
                @elseif($step === 'code')
                    Digite o código enviado para {{ $email }}.
                @else
                    Informe seu e-mail para receber um código de recuperação.
                @endif
            </p>

            @if(session('status'))
                <div class="auth-feedback success"><i class="fa-solid fa-circle-check"></i><span>{{ session('status') }}</span></div>
            @endif

            @if($errors->any())
                <div class="auth-feedback error"><i class="fa-solid fa-circle-exclamation"></i><span>{{ $errors->first() }}</span></div>
            @endif

            @if($step === 'reset')
                <form method="POST" action="{{ route('senha.redefinir') }}">
                    @csrf
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password" placeholder="Nova senha" required minlength="8">
                    </div>
                    <div class="input-box">
                        <i class="fa-solid fa-lock"></i>
                        <input type="password" name="password_confirmation" placeholder="Confirmar senha" required minlength="8">
                    </div>
                    <button type="submit">Redefinir senha</button>
                </form>
            @elseif($step === 'code')
                <form method="POST" action="{{ route('senha.validar-codigo') }}">
                    @csrf
                    <div class="input-box recovery-code-box">
                        <i class="fa-solid fa-key"></i>
                        <input type="text" name="code" inputmode="numeric" pattern="[0-9]{6}" maxlength="6" placeholder="000000" required>
                    </div>
                    <button type="submit">Validar código</button>
                </form>
                <form method="POST" action="{{ route('senha.enviar-codigo') }}" class="recovery-resend-form">
                    @csrf
                    <input type="hidden" name="email" value="{{ $email }}">
                    <button type="submit" class="link-button">Reenviar código</button>
                </form>
            @else
                <form method="POST" action="{{ route('senha.enviar-codigo') }}">
                    @csrf
                    <div class="input-box">
                        <i class="fa-regular fa-envelope"></i>
                        <input type="email" name="email" value="{{ old('email') }}" placeholder="Digite seu email" required>
                    </div>
                    <button type="submit">Enviar código</button>
                </form>
            @endif

            <span class="register">
                Lembrou sua senha?
                <a href="{{ route('login') }}">Fazer login</a>
            </span>
        </div>
    </div>
</body>
</html>
