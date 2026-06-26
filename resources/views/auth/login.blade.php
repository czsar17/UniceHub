<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UniceHub Login</title>

  <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

  <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
  >
    <link rel="stylesheet" href="{{ route('theme.css') }}">
</head>

<body class="auth-page">

    <header class="main-header">
        <div class="logo-area">
            <img src="{{ asset($systemTheme['logo_path'] ?? 'images/LOGOUNICEHUB-removebg-preview.png') }}">
        </div>
    </header>

    <div class="container">

        <div class="login-card">

            <img
              src="{{ asset($systemTheme['logo_path'] ?? 'images/LOGOUNICEHUB-removebg-preview.png') }}"
              class="logo"
            >

            <h1>Bem-vindo!</h1>

            <p>Faça login para continuar.</p>

            @if(session('status'))
                <div class="auth-feedback success">
                    <i class="fa-solid fa-circle-check"></i>
                    <span>{{ session('status') }}</span>
                </div>
            @endif

            @if($errors->any())
                <div class="auth-feedback error">
                    <i class="fa-solid fa-circle-exclamation"></i>
                    <span>{{ $errors->first() }}</span>
                </div>
            @endif

            <!-- EMAIL -->

         <form method="POST" action="/login">

            @csrf

            <div class="input-box">

                <i class="fa-regular fa-envelope"></i>

                <input
                    type="email"
                    name="email"
                    id="email"
                    placeholder="Digite seu email"
                    value="{{ old('email') }}"
                    required
                >

            </div>

            <div class="input-box">

                <i class="fa-solid fa-lock"></i>

                <input
                    type="password"
                    name="password"
                    id="senha"
                    placeholder="Digite sua senha"
                    required
                >

                <i
                    class="fa-regular fa-eye"
                    id="toggleSenha"
                ></i>

            </div>

            <a href="/esqueci-senha" class="forgot">
                Esqueceu a senha?
            </a>

            <button type="submit">
                Entrar
            </button>

        </form>


            <span class="register">

                Não tem uma conta?

                <a href="/registro">
                    Cadastre-se
                </a>

            </span>

        </div>

    </div>


    <script src="{{ asset('js/login.js') }}"></script>
</body>
</html>