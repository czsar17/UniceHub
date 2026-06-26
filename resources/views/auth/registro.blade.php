<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />

    <title>Cadastro | UniceHub</title>

    <!-- CSS -->
    <link rel="stylesheet" href="{{ asset('css/auth.css') }}">

    <!-- Icons -->
    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    />
    <link rel="stylesheet" href="{{ route('theme.css') }}">
</head>
<body class="registro auth-page">

    <header class="main-header">
        <div class="logo-area">
            <img src="{{ asset($systemTheme['logo_path'] ?? 'images/LOGOUNICEHUB-removebg-preview.png') }}">
        </div>
    </header>

    <main class="container registro">

        <!-- SELEÇÃO -->
        <aside class="user-selector">

            <h2>Você é:</h2>

            <button type="button" class="user-card active aluno" id="alunoBtn">

                <div class="icon-box">
                    <i class="fa-solid fa-user-graduate"></i>
                </div>

                <div class="user-info">
                    <h3>Aluno</h3>
                    <span>Sou aluno</span>
                </div>

            </button>

            <button type="button" class="user-card professor" id="professorBtn">

                <div class="icon-box">
                    <i class="fa-solid fa-chalkboard-user"></i>
                </div>

                <div class="user-info">
                    <h3>Professor</h3>
                    <span>Sou professor</span>
                </div>

            </button>

        </aside>

        <!-- FORMULÁRIO -->
        <section class="register-card">

            <div class="logo-title">
                <h1>
                    Cadastre-se no
                    <span>UniceHub</span>
                </h1>

                <p>
                    Preencha os campos abaixo para criar sua conta.
                </p>
            </div>

            <form id="registerForm" method="POST" action="/registro">

            @csrf

            <!-- NOME -->
            <div class="input-group">

                <label>
                    Nome completo
                    <span>*</span>
                </label>

                <div class="input-box registro">

                    <i class="fa-regular fa-user"></i>

                    <input
                        type="text"
                        name="name"
                        value="{{ old('name') }}"
                        placeholder="Digite seu nome completo"
                        required
                    />

                </div>

                @error('name')
                    <small>{{ $message }}</small>
                @enderror

            </div>

            <!-- CPF -->
            <div class="input-group">

            <label>
                CPF
                <span>*</span>
            </label>

            <div class="input-box registro @error('cpf') error @enderror">

                <i class="fa-regular fa-id-card"></i>

                <input
                    type="text"
                    id="cpf"
                    name="cpf"
                    value="{{ old('cpf') }}"
                    placeholder="000-000-000-00"
                    maxlength="14"
                    inputmode="numeric"
                    required
                />

            </div>

            <small
                    class="input-error"
                    id="cpfError"
            ></small>

                @error('cpf')
                    <small class="input-error">
                        {{ $message }}
                    </small>
                @enderror

            </div>

            <!-- EMAIL -->
            <div class="input-group">

            <label>
                Email
                <span>*</span>
            </label>

            <div class="input-box registro @error('email') error @enderror">

                <i class="fa-regular fa-envelope"></i>

                <input
                    type="email"
                    name="email"
                    value="{{ old('email') }}"
                    placeholder="Digite seu melhor email"
                    required
                />

            </div>

                @error('email')
                    <small class="input-error">
                        {{ $message }}
                    </small>
                @enderror

            </div>
            <!-- NASCIMENTO -->
            <div class="input-group">

                <label>
                    Data de nascimento
                    <span>*</span>
                </label>

                <div class="input-box registro date-box">

                    <i class="fa-regular fa-calendar calendar-icon"></i>

                    <input
                        type="date"
                        id="birthDate"
                        name="data_nascimento"
                        value="{{ old('data_nascimento') }}"
                        required
                    />

                </div>

                @error('data_nascimento')
                    <small>{{ $message }}</small>
                @enderror

            </div>

            <!-- SENHA -->
            <div class="input-group">

                <label>
                    Senha
                    <span>*</span>
                </label>

                <div class="input-box registro">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        name="password"
                        placeholder="Digite sua senha"
                        id="password"
                        required
                    />

                    <button
                        type="button"
                        class="show-password"
                        id="togglePassword"
                    >
                        <i class="fa-regular fa-eye"></i>
                    </button>

                </div>

                @error('password')
                    <small>{{ $message }}</small>
                @enderror

            </div>

            <!-- REGRAS -->
            <div class="password-rules registro">

                <p id="ruleLength">
                    <i class="fa-solid fa-circle"></i>
                    Mínimo de 8 caracteres
                </p>

                <p id="ruleNumber">
                    <i class="fa-solid fa-circle"></i>
                    Pelo menos 1 número
                </p>

                <p id="ruleSpecial">
                    <i class="fa-solid fa-circle"></i>
                    1 caractere especial
                </p>

            </div>

            <!-- CONFIRMAR -->
            <div class="input-group">

                <label>
                    Confirmar senha
                    <span>*</span>
                </label>

                <div class="input-box registro">

                    <i class="fa-solid fa-lock"></i>

                    <input
                        type="password"
                        name="password_confirmation"
                        placeholder="Confirme sua senha"
                        id="confirmPassword"
                        required
                    />

                </div>

            </div>

            <!-- TIPO -->
            <input
                type="hidden"
                name="tipo"
                id="tipoUsuario"
                value="aluno"
            >

            <!-- BOTÃO -->
            <button type="submit" class="register-btn">
                Criar conta
            </button>

        </form>

            <div class="login-link">

                <span>Já possui uma conta?</span>

                <a href="/login" class="login-btn">
                    Faça login
                </a>

            </div>

        </section>

    </main>

    <script src="{{ asset('js/scriptRegistro.js') }}"></script>

</body>
</html>
