<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Conexões - UniceHub</title>

    <link rel="stylesheet" href="{{ asset('css/conexoes.css') }}">

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
        <main class="connections-container">

    <!-- HEADER -->
    <section class="connections-header">

        <div>
            <h1>Conexões</h1>
            <p>Conecte-se com pessoas e colabore em projetos juntos.</p>
        </div>

        <button class="connect-btn">
            <i class="fa-solid fa-plus"></i>
            Conectar pessoas
        </button>

    </section>

    <!-- CONTEÚDO -->
    <section class="connections-content">

        <!-- ESQUERDA -->
        <div class="connections-main">

            <!-- TABS -->
           <div class="connections-tabs">

    <button class="tab-btn active" data-tab="todos">Todos</button>

    <button class="tab-btn" data-tab="seguidores">
        Seguidores
    </button>

    <button class="tab-btn" data-tab="seguindo">
        Seguindo
    </button>

    <button class="tab-btn" data-tab="solicitacoes">
        Solicitações
    </button>

    <button class="tab-btn" data-tab="bloqueados">
        Bloqueados
    </button>

</div>

            <!-- TOPO LISTA -->
            <div class="connections-top">

                <h2 id="section-title">Todas as conexões</h2>
                <span class="counter">
                    0 conexões
                </span>

                <div class="search-box">

                    <input
                        type="text"
                        placeholder="Buscar conexão..."
                    >

                    <i class="fa-solid fa-magnifying-glass"></i>

                </div>

            </div>

            <!-- LISTA -->
            <div class="connections-list">

    {{-- TODOS --}}
    <div class="tab-content active" id="todos">

        @php
            $todos = $seguidores->map(fn($f) => $f->seguidor)
                ->merge($seguindo->map(fn($f) => $f->seguido))
                ->unique('id');
        @endphp

        @forelse($todos as $usuario)

        <div class="connection-card">

            <div class="connection-user">

                <img src="{{ asset($usuario->foto) }}">

                <div>

                    <h4>{{ $usuario->name }}</h4>

                    <span>{{ $usuario->curso }}</span>

                </div>

            </div>

            <div class="connection-actions">

                <div class="dropdown">

                    <i class="fa-solid fa-ellipsis"></i>

                    <div class="dropdown-menu">

                        <form action="{{ route('usuario.bloquear', $usuario->id) }}" method="POST">
                            @csrf
                            <button type="submit">
                                Bloquear
                            </button>
                        </form>

                    </div>

                </div>

            </div>

        </div>

        @empty

        <div class="empty-connections">

            <i class="fa-solid fa-user-group"></i>

            <h3>Nenhuma conexão encontrada</h3>

            <p>Comece encontrando pessoas para se conectar.</p>

        </div>

        @endforelse

    </div>

    {{-- SEGUIDORES --}}
    <div class="tab-content" id="seguidores">

        @forelse($seguidores as $follow)

        <div class="connection-card">

            <div class="connection-user">

                <img src="{{ asset($follow->seguidor->foto) }}">

                <div>

                    <h4>{{ $follow->seguidor->name }}</h4>

                    <span>{{ $follow->seguidor->curso }}</span>

                </div>

            </div>

        </div>

        @empty

        <div class="empty-connections">
            <i class="fa-solid fa-users"></i>
            <h3>Nenhum seguidor encontrado</h3>
        </div>

        @endforelse

    </div>

    {{-- SEGUINDO --}}
    <div class="tab-content" id="seguindo">

        @forelse($seguindo as $follow)

        <div class="connection-card">

            <div class="connection-user">

                <img src="{{ asset($follow->seguido->foto) }}">

                <div>

                    <h4>{{ $follow->seguido->name }}</h4>

                    <span>{{ $follow->seguido->curso }}</span>

                </div>

            </div>

        </div>

        @empty

        <div class="empty-connections">
            <i class="fa-solid fa-user-check"></i>
            <h3>Você não segue ninguém</h3>
        </div>

        @endforelse

    </div>

    {{-- SOLICITAÇÕES --}}
    <div class="tab-content" id="solicitacoes">

        @forelse($solicitacoes as $solicitacao)

        <div class="request-card">

            <div class="connection-user">

                <img src="{{ asset($solicitacao->seguidor->foto) }}">

                <div>

                    <h4>{{ $solicitacao->seguidor->name }}</h4>

                    <span>{{ $solicitacao->seguidor->curso }}</span>

                </div>

            </div>

            <div class="request-actions">

                <form action="{{ route('seguir.aceitar', $solicitacao->id) }}" method="POST">
                    @csrf
                    <button class="accept-btn">
                        Aceitar
                    </button>
                </form>

                <form action="{{ route('seguir.recusar', $solicitacao->id) }}" method="POST">
                    @csrf
                    <button class="remove-btn">
                        Remover
                    </button>
                </form>

            </div>

        </div>

        @empty

        <div class="empty-connections">
            <i class="fa-solid fa-user-clock"></i>
            <h3>Nenhuma solicitação pendente</h3>
        </div>

        @endforelse

    </div>

</div>
            
        </div>

        <!-- DIREITA -->
        <aside class="connections-sidebar">

            <!-- SUGESTÕES -->
            <div class="sidebar-card">

                <div class="card-header">

                    <h3>Sugestões para você</h3>

                    <a href="#">
                        Ver todas
                    </a>

                </div>

                <div class="suggestions-list">

    @forelse($sugestoes as $usuario)

    <div class="suggestion-item">

        <img src="{{ asset($usuario->foto) }}">

        <div>

            <h4>{{ $usuario->name }}</h4>

            <span>{{ $usuario->curso }}</span>

        </div>

        <form action="{{ route('seguir.enviar', $usuario->id) }}" method="POST">

            @csrf

            <button class="mini-connect-btn">
                Conectar
            </button>

        </form>

    </div>

    @empty

    <p class="empty-sidebar">
        Nenhuma sugestão disponível.
    </p>

    @endforelse

</div>

<a href="#" class="see-more">
    Ver mais sugestões >
</a>

            </div>

            <!-- SOLICITAÇÕES -->
            <div class="sidebar-card">

                <div class="card-header">

                    <h3>Solicitações de conexões</h3>

                </div>

               <div class="requests-list">

    @forelse($solicitacoes->take(3) as $solicitacao)

    <div class="request-item">

        <img src="{{ asset($solicitacao->seguidor->foto) }}">

        <div class="request-info">

            <h4>{{ $solicitacao->seguidor->name }}</h4>

            <span>{{ $solicitacao->seguidor->curso }}</span>

            <div class="request-buttons">

                <form action="{{ route('seguir.aceitar', $solicitacao->id) }}" method="POST">
                    @csrf

                    <button class="accept-btn">
                        Aceitar
                    </button>

                </form>

                <form action="{{ route('seguir.recusar', $solicitacao->id) }}" method="POST">
                    @csrf

                    <button class="remove-btn">
                        Remover
                    </button>

                </form>

            </div>

        </div>

    </div>

    @empty

    <p class="empty-sidebar">
        Nenhuma solicitação.
    </p>

    @endforelse

</div>


                     
                      
                    

                </div>

                <a href="#" class="see-more">
                    Ver todas as solicitações >
                </a>

            </div>

        </aside>

    </section>

</main>
</div>
<script>

const tabs = document.querySelectorAll(".tab-btn");
const contents = document.querySelectorAll(".tab-content");

tabs.forEach(tab => {

    tab.addEventListener("click", () => {

        tabs.forEach(btn =>
            btn.classList.remove("active")
        );

        contents.forEach(content =>
            content.classList.remove("active")
        );

        tab.classList.add("active");

        document
            .getElementById(tab.dataset.tab)
            .classList.add("active");

    });

});

</script>
</body>
</html>