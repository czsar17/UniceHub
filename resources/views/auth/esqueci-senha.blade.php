<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport"
    content="width=device-width, initial-scale=1.0">

    <title>Recuperar Senha</title>

    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <link
    rel="stylesheet"
    href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"
    >

    <link rel="stylesheet" href="{{ route('theme.css') }}">
</head>

<body class="auth-page">

    <!-- HEADER -->

    <header class="main-header">

        <div class="logo-area">

            <img
            src="{{ asset($systemTheme['logo_path'] ?? 'images/LOGOUNICEHUB-removebg-preview.png') }}"
            >

        </div>

    </header>


    <!-- CONTAINER -->

    <div class="container">

        <div class="login-card">

            <img
              src="{{ asset($systemTheme['logo_path'] ?? 'images/LOGOUNICEHUB-removebg-preview.png') }}"
              class="logo"
            >

            <h1>Recuperar senha</h1>

            <p>
                Digite seu email para
                recuperar sua senha.
            </p>


            <!-- EMAIL -->

            <div class="input-box">

                <i class="fa-regular fa-envelope"></i>

                <input
                  type="email"
                  id="email"
                  placeholder="Digite seu email"
                >

            </div>


            <!-- BOTÃO -->

            <button onclick="enviarCodigo()">

                Enviar código

            </button>


            <!-- VOLTAR LOGIN -->

            <span class="register">

                Lembrou sua senha?

                <a href="login.html">

                    Fazer login

                </a>

            </span>

        </div>

    </div>


<script>

function enviarCodigo(){

    const email =
    document.getElementById("email").value;

    const emailRegex =
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;


    if(emailRegex.test(email)){

        alert(
            "Código enviado para: " + email
        );

    }else{

        alert(
            "Digite um email válido."
        );

    }

}

</script>

</body>
</html>