<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Cadastro em análise | UniceHub</title>
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
    <div class="login-card pending-card">
      <img src="{{ asset($systemTheme['logo_path'] ?? 'images/LOGOUNICEHUB-removebg-preview.png') }}" class="logo" alt="UniceHub">
      <div class="pending-icon"><i class="fa-solid fa-hourglass-half"></i></div>
      <h1>Cadastro em análise</h1>
      <p>Seu cadastro como professor foi recebido e precisa ser aprovado por um administrador antes do acesso ao sistema.</p>
      @isset($email)
        <span class="pending-email">{{ $email }}</span>
      @endisset
      <a class="pending-login-link" href="{{ route('login') }}">Voltar para o login</a>
    </div>
  </div>
</body>
</html>
