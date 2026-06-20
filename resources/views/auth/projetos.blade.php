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
                    <a href="{{ route('projetoscad') }}">
                        <button class="edit-project-btn" type="button">
                            <i class="fa-solid fa-pen"></i>
                            Editar Projetos
                        </button>
                    </a>

                    <a href="{{ route('projetoscad') }}">
                        <button class="new-project-btn" type="button">
                            <i class="fa-solid fa-plus"></i>
                            Novo Projeto
                        </button>
                    </a>
                </div>

            </div>

            <div class="projects-list">
                @forelse($projetos as $projeto)
                    <div class="project-card">
                        <img
                            src="{{ $projeto->capa ? asset($projeto->capa) : asset('images/loading.png') }}"
                            class="project-logo">

                        <div class="project-info">
                            <div class="project-top">
                                <h3>{{ $projeto->nome }}</h3>
                                <span class="status {{ $projeto->status === 'Concluído' ? 'active' : 'pending' }}">
                                    {{ $projeto->status }}
                                </span>
                            </div>

                            <p class="project-description">
                                {{ Str::limit(strip_tags($projeto->descricao), 180) }}
                            </p>

                            <div class="tech-list">
                                @foreach(($projeto->tecnologias ?? []) as $tech)
                                    <span>{{ $tech }}</span>
                                @endforeach
                            </div>

                            <div class="project-footer">
                                <span>👥 {{ $projeto->membros->count() }} membros</span>
                                <span>📅 {{ $projeto->created_at->format('d/m/Y') }}</span>
                            </div>
                        </div>

                        @if($projeto->user_id === Auth::id())
                            <div class="project-menu">
                                <button class="project-menu-btn" type="button" aria-label="Mais opções">⋯</button>
                                <div class="project-menu-dropdown">
                                    <form action="{{ route('projetos.destroy', $projeto) }}" method="POST" onsubmit="return confirm('Tem certeza que deseja excluir este projeto?')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit">Excluir projeto</button>
                                    </form>
                                </div>
                            </div>
                        @endif
                    </div>
                @empty
                    <div class="empty-state">
                        Nenhum projeto encontrado.
                    </div>
                @endforelse
            </div>

        </div>

        <div class="projects-right">
            <div class="filter-card">
                <h3>Filtros</h3>
                <input type="text" placeholder="Buscar projeto">
                <select>
                    <option>Status</option>
                </select>
                <select>
                    <option>Tecnologia</option>
                </select>
                <button type="button">Aplicar filtros</button>
            </div>

            <div class="summary-card">
                <h3>Resumo</h3>
                <div class="summary-item">
                    <span>Projetos</span>
                    <strong>{{ $resumo['total'] }}</strong>
                </div>
                <div class="summary-item">
                    <span>Em andamento</span>
                    <strong>{{ $resumo['em_andamento'] }}</strong>
                </div>
                <div class="summary-item">
                    <span>Concluídos</span>
                    <strong>{{ $resumo['concluidos'] }}</strong>
                </div>
                <div class="summary-item">
                    <span>Arquivados</span>
                    <strong>{{ $resumo['arquivados'] }}</strong>
                </div>
            </div>
        </div>

    </div>

</div>

<script src="{{ asset('js/projetos.js') }}"></script>

</body>
</html>