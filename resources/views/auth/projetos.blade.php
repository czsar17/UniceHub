<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Projetos - UniceHub</title>

    <link rel="stylesheet" href="{{ asset('css/projetos.css') }}">

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

    <div class="search-box">

      <input type="text" placeholder="Pesquisar...">

      <i class="fa-solid fa-magnifying-glass"></i>

    </div>

    <div class="header-icons">

      <i class="fa-regular fa-bell notification"></i>

      <div class="header-profile">

        <img src="{{ asset(Auth::user()->foto) }}" class="profile-pic">

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
    <!-- CONTEÚDO -->
    <div class="projects-content">

        <div class="projects-left">

            <div class="projects-header">

                <div>
                    <h1>Meus Projetos</h1>
                    <p>Gerencie e acompanhe seus projetos.</p>
                </div>

                <div class="project-actions">

    <a href="projetoscad.html">
        <button class="edit-project-btn">
            <i class="fa-solid fa-pen"></i>
            Editar Projetos
        </button>
    </a>

    <a href="projetosced.html">
        <button class="new-project-btn">
            <i class="fa-solid fa-plus"></i>
            Novo Projeto
        </button>
    </a>

</div>

            </div>

            <div class="projects-list">

                <!-- CARD 1 -->

                <div class="project-card">

                    <img
                    src="assets/loading.png"
                    class="project-logo">

                    <div class="project-info">

                        <div class="project-top">

                            <h3>Projeto</h3>

                            <span class="status active">
                                Status
                            </span>

                        </div>

                        <p class="project-description">
                            Descrição
                        </p>

                        <div class="tech-list">
                            <span>Tec1</span>
                            <span>Tec2</span>
                            <span>Tec3</span>
                        </div>

                        <div class="project-footer">
                            <span>👥 0 membros</span>
                            <span>📅 00/00/0000</span>
                        </div>

                    </div>

                </div>

                <!-- CARD 2 -->

                <div class="project-card">

                    <img
                    src="assets/loading.png"
                    class="project-logo">

                    <div class="project-info">

                        <div class="project-top">

                            <h3>Projeto Exemplo 2</h3>

                            <span class="status active">
                                Status
                            </span>

                        </div>

                        <p class="project-description">
                            Descrição.
                        </p>

                        <div class="tech-list">
                            <span>Tec1</span>
                            <span>Tec2</span>
                            <span>Tec3</span>
                        </div>

                        <div class="project-footer">
                            <span>👥 0 membros</span>
                            <span>📅 00/00/0000</span>
                        </div>

                    </div>

                </div>

            </div>

        </div>

        <!-- PAINEL DIREITO -->

        <div class="projects-right">

            <div class="filter-card">

                <h3>Filtros</h3>

                <input
                type="text"
                placeholder="Buscar projeto">

                <select>
                    <option>Status</option>
                </select>

                <select>
                    <option>Tecnologia</option>
                </select>

                <button>
                    Aplicar filtros
                </button>

            </div>

            <div class="summary-card">

                <h3>Resumo</h3>

                <div class="summary-item">
                    <span>Projetos</span>
                    <strong>00</strong>
                </div>

                <div class="summary-item">
                    <span>Em andamento</span>
                    <strong>0</strong>
                </div>

                <div class="summary-item">
                    <span>Concluídos</span>
                    <strong>0</strong>
                </div>

                <div class="summary-item">
                    <span>Arquivados</span>
                    <strong>0</strong>
                </div>

            </div>

        </div>

    </div>

</div>

    <!-- MAIN -->

    <main class="content">

        <header class="topbar">

            <div>

                <h1>Meus Projetos</h1>

                <p>
                    Gerencie e acompanhe seus projetos.
                </p>

            </div>

            <button class="new-project">
                <i class="fa-solid fa-plus"></i>
                Novo Projeto
            </button>

        </header>

        <div class="projects-layout">

            <!-- LISTA -->

            <section class="projects">

                <div class="project-card">

                    <img
                    class="project-logo"
                    src="assets/loading.png">

                    <div class="project-info">

                        <div class="project-header">

                            <h2>Projeto</h2>

                            <span class="status active-status">
                                Status
                            </span>

                        </div>

                        <p>
                            Descrição
                        </p>

                        <div class="techs">

                            <span>Tec1</span>
                            <span>Tec2</span>
                            <span>Tec3</span>

                        </div>

                        <div class="project-footer">

                            <span>
                                <i class="fa-regular fa-calendar"></i>
                                00/00/0000
                            </span>

                            <span>
                                <i class="fa-solid fa-users"></i>
                                0 membros
                            </span>

                        </div>

                    </div>

                </div>

                <div class="project-card">

                    <img
                    class="project-logo"
                    src="assets/loading.png">

                    <div class="project-info">

                        <div class="project-header">

                            <h2>Projeto</h2>

                            <span class="status active-status">
                                Status
                            </span>

                        </div>

                        <p>
                            Descrição
                        </p>

                        <div class="techs">

                            <span>Tec1</span>
                            <span>Tec2</span>
                            <span>Tec3</span>

                        </div>

                        <div class="project-footer">

                            <span>
                                <i class="fa-regular fa-calendar"></i>
                                00/00/0000
                            </span>

                            <span>
                                <i class="fa-solid fa-users"></i>
                                0 membros
                            </span>

                        </div>

                    </div>

                </div>

            </section>

            <!-- LATERAL -->

            <aside class="right-panel">

                <div class="filter-box">

                    <h3>Filtros</h3>

                    <input
                    type="text"
                    placeholder="Buscar projeto">

                    <select>
                        <option>Status</option>
                    </select>

                    <select>
                        <option>Tecnologia</option>
                    </select>

                    <button>
                        Aplicar filtros
                    </button>

                </div>

                <div class="summary-box">

                    <h3>Resumo</h3>

                    <div class="summary-item">
                        <strong>0</strong>
                        <span>Projetos</span>
                    </div>

                    <div class="summary-item">
                        <strong>0</strong>
                        <span>Em andamento</span>
                    </div>

                    <div class="summary-item">
                        <strong>0</strong>
                        <span>Concluídos</span>
                    </div>

                    <div class="summary-item">
                        <strong>0</strong>
                        <span>Arquivado</span>
                    </div>

                </div>

            </aside>

        </div>

    </main>

</div>

<script src="projetos.js"></script>

</body>
</html>