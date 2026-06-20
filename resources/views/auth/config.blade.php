<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Configurações - UniceHub</title>

    <link rel="stylesheet" href="{{ asset('css/config.css') }}">

    <link rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">

</head>

<body>
 <!-- HEADER -->
  <header class="header">

    <div class="header-left">

      <i class="fa-solid fa-bars menu-icon"></i>

      <img src="{{ asset('images/LOGOUNICEHUB-removebg-preview.png') }}" class="header-logo">

    </div>

    <form class="search-box" action="{{ route('buscar') }}" method="GET">

    <input
        type="text"
        name="q"
        value="{{ request('q') }}"
        placeholder="Pesquisar pessoas e projetos..."
    >

    <button type="submit">
        <i class="fa-solid fa-magnifying-glass"></i>
    </button>

</form>

    <div class="header-icons">

      <i class="fa-regular fa-bell notification"></i>

      <div class="header-profile">

        <img src="assets/userx.png" class="profile-pic">

      </div>

    </div>

  </header>

    <div class="main-container">

        <!-- SIDEBAR -->
        <aside class="sidebar">

    <div class="sidebar-content">

        <ul>

            <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                <a href="{{ route('home') }}">
                    <i class="fa-solid fa-house"></i>
                    <span>Home</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('perfil') ? 'active' : '' }}">
                <a href="{{ route('perfil') }}">
                    <i class="fa-regular fa-user"></i>
                    <span>Perfil</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('conexoes') ? 'active' : '' }}">
                <a href="{{ route('conexoes') }}">
                    <i class="fa-solid fa-user-group"></i>
                    <span>Conexões</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('projetos') ? 'active' : '' }}">
                <a href="{{ route('projetos') }}">
                    <i class="fa-regular fa-folder"></i>
                    <span>Projetos</span>
                </a>
            </li>

            <li class="{{ request()->routeIs('config') ? 'active' : '' }}">
                <a href="{{ route('config') }}">
                    <i class="fa-solid fa-gear"></i>
                    <span>Configurações</span>
                </a>
            </li>

        </ul>

    </div>

    <!-- PERFIL INFERIOR -->
    <div class="sidebar-bottom">

        <div class="sidebar-profile">

             <img src="{{ asset(Auth::user()->foto) }}" class="profile-pic">

            <div>
                <h4>{{ Auth::user()->name }}</h4>
                @if(Auth::user()->curso)
                <span>{{ Auth::user()->curso }}</span>
                @endif
            </div>

        </div>

        <form method="POST" action="/logout">

            @csrf

            <button class="logout" type="submit">

                <i class="fa-solid fa-arrow-right-from-bracket"></i>

                Sair

            </button>

        </form>

    </div>

</aside>
     <main class="connections-container">

    <section class="settings-container">

        <aside class="settings-menu">

            <div class="menu-group">
                <h3>Conta</h3>

                <button class="menu-item active" data-page="perfil">
                    Informações pessoais
                </button>

                <button class="menu-item" data-page="seguranca">
                    Segurança
                </button>

                <button class="menu-item" data-page="notificacoes">
                    Notificações
                </button>

                <button class="menu-item" data-page="tipoPerfil">
                    Tipo de perfil
                </button>
            </div>

            <div class="menu-group">
                <h3>Sistema</h3>

                <button class="menu-item" data-page="sobre">
                    Sobre o UniceHub
                </button>

                <button class="menu-item">
                    Status do sistema
                </button>

                <button class="menu-item">
                    Política de privacidade
                </button>
            </div>

            <div class="menu-group">
                <h3>Admin</h3>

                <button class="menu-item">
                    Personalização
                </button>
            </div>

        </aside>

        <div class="settings-content">

            <div class="page-header">
                <h1>Configurações</h1>
                <p>Gerencie suas preferências e configurações da conta.</p>
            </div>

            
            <div class="config-card" id="configContent"></div>

        </div>

    </section>

</main>
    <script src="{{ asset('js/config.js') }}"></script>
    </main>

</section>