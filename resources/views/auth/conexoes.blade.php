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

    <div class="empty-state">

        <i id="empty-icon" class="fa-solid fa-user-group"></i>

        <h3 id="empty-title">
            Nenhuma conexão encontrada
        </h3>

        <p id="empty-text">
            Comece encontrando pessoas para se conectar.
        </p>

    </div>

</div>

            <!-- BOTÃO -->
            <div class="load-more">

                <button>
                    Carregar mais
                    <i class="fa-solid fa-chevron-down"></i>
                </button>

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

                    <!-- Sugestão -->
                    <div class="suggestion-item">


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

const title = document.getElementById("empty-title");
const text = document.getElementById("empty-text");
const icon = document.getElementById("empty-icon");
const sectionTitle = document.getElementById("section-title");
tabs.forEach(tab => {

    tab.addEventListener("click", () => {

        tabs.forEach(btn =>
            btn.classList.remove("active")
        );

        tab.classList.add("active");

        switch(tab.dataset.tab){

            case "todos":
                icon.className = "fa-solid fa-user-group";
                title.textContent = "Nenhuma conexão encontrada";
                text.textContent = "Comece encontrando pessoas para se conectar.";
                break;

            case "seguidores":
                icon.className = "fa-solid fa-users";
                title.textContent = "Nenhum seguidor encontrado";
                text.textContent = "Quando alguém seguir você aparecerá aqui.";
                break;

            case "seguindo":
                icon.className = "fa-solid fa-user-check";
                title.textContent = "Você não está seguindo ninguém";
                text.textContent = "As pessoas que você seguir aparecerão aqui.";
                break;

            case "solicitacoes":
                icon.className = "fa-solid fa-user-clock";
                title.textContent = "Nenhuma solicitação pendente";
                text.textContent = "Novas solicitações aparecerão aqui.";
                break;

            case "bloqueados":
                icon.className = "fa-solid fa-user-slash";
                title.textContent = "Nenhum usuário bloqueado";
                text.textContent = "Usuários bloqueados aparecerão aqui.";
                break;
        }

    });

});

</script>
</body>
</html>