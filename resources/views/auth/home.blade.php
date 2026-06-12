@php
use Illuminate\Support\Facades\Auth;
@endphp

<!DOCTYPE html>
<html lang="pt-BR">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>UniceHub - Home</title>

  <link rel="stylesheet" href="{{ asset('css/home.css') }}">

  <link rel="stylesheet"
  href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css"/>
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

  <!-- CONTEÚDO -->
  <main class="main-container">

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

    <!-- FEED -->
    <section class="feed">

      <!-- POST -->
      <div class="post-card">

    <div class="post-top">

        <!-- USUÁRIO -->
        <div class="post-user">

            <img src="assets/user5.png">

            <div>

                <h3>Thiago Vatira</h3>

                <span>Eng. de Software</span>

            </div>

        </div>

        <!-- LADO DIREITO -->
        <div class="post-right">

            <span>há 10 horas</span>

            <!-- 3 PONTOS -->
            <div class="options-area">

                <i class="fa-solid fa-ellipsis options-btn"></i>

                <div class="mini-option">

                    <div class="dismiss-post">

                        <i class="fa-solid fa-xmark"></i>

                        Dispensar

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- CONTEÚDO -->
    <div class="post-content">

        <h2>Projeto Mobile em Python</h2>

        <p>
            Iniciei um novo projeto de aplicativo mobile voltado para
            organização de estudos. Preciso de ajuda com interface
            e integração de funcionalidades.
        </p>

        <div class="post-images">

            <img src="assets/pythin.png">

            <img src="assets/code.png">

        </div>

    </div>

</div>
      <!-- parte dp segundo card (jp) -->
<div class="post-card">

  <div class="post-top">

    <div class="post-user">

      <img src="assets/user4.png">

      <div>

        <h3>João Pedro</h3>

        <span>Eng. de Software</span>

      </div>

    </div>

    <div class="post-time">

      <span>há 7 horas</span>

     <div class="options-area">

                <i class="fa-solid fa-ellipsis options-btn"></i>

                <div class="mini-option">

                    <div class="dismiss-post">

                        <i class="fa-solid fa-xmark"></i>

                        Dispensar

                    </div>

                </div>

            </div>

        </div>

    </div>
  <div class="post-content">

    <h2>Projeto Laravel + Vue.js</h2>

    <p>
      Iniciei um novo projeto open source usando Laravel e Vue.js
      para o desenvolvimento de um sistema de gerenciamento
      colaborativo. Preciso de ajuda com frontend.
      Alguém interessado em colaborar?
    </p>

    <div class="post-images">

      <img src="assets/vue.png">

      <img src="assets/code3.png">

    </div>

  </div>

  <div class="post-actions">

    <div class="left-actions">

      <span>
        <i class="fa-regular fa-heart"></i>
        24
      </span>

      <span>
        <i class="fa-regular fa-comment"></i>
        9 Comentários
      </span>

      <span>
        <i class="fa-solid fa-users"></i>
        4 membros
      </span>

    </div>

    <button class="connect-btn">

      Conectar

    </button>

  </div>

</div>
    </section>

    <!-- WIDGETS -->
    <aside class="widgets">

      <!-- Sugestões -->
   <div class="widget-card">

  <div class="widget-title">

    <h3>Sugestões de Conexões</h3>

    <i class="fa-solid fa-ellipsis"></i>

  </div>

  <!-- PRIMEIRA SUGESTÃO -->
  <div class="suggestion">

    <div class="suggestion-user">

      <img src="assets/user2.png">

      <div>

        <h4>Jason Stan</h4>

        <span>Ciência da Computação</span>

      </div>

    </div>

    <button>Conectar</button>

  </div>

  <!-- SEGUNDA SUGESTÃO -->
  <div class="suggestion">

    <div class="suggestion-user">

      <img src="assets/user3.png">

      <div>

        <h4>Cristiano Ronaldo</h4>

        <span>ADS</span>

      </div>

    </div>

    <button>Conectar</button>

  </div>

</div>
      <!-- Projetos -->
      <!-- Projetos -->
<div class="widget-card">

  <div class="widget-title">

    <h3>
      <i class="fa-solid fa-laptop-code"></i>
      Projetos em Destaque
    </h3>

    <i class="fa-solid fa-ellipsis"></i>

  </div>

  <!-- PROJETO 1 -->
  <div class="project-mini-card">

    <div>

      <h4>Unicehub</h4>

      <p>Plataforma de colaboração acadêmica.</p>

      <div class="project-info">

        <span>
          <i class="fa-regular fa-calendar"></i>
          Iniciado em 20/02/2026
        </span>

        <span>
          <i class="fa-solid fa-users"></i>
          4 membros
        </span>

      </div>

    </div>

    <button>Ver projeto</button>

  </div>

  <!-- PROJETO 2 -->
  <div class="project-mini-card">

    <div>

      <h4>API de recomendação</h4>

      <p>Tudo com base no seu perfil.</p>

      <div class="project-info">

        <span>
          <i class="fa-regular fa-calendar"></i>
          Iniciado em 17/04/2026
        </span>

        <span>
          <i class="fa-solid fa-users"></i>
          7 membros
        </span>

      </div>

    </div>

    <button>Ver projeto</button>

  </div>

  <div class="see-more">

    <a href="#">Ver mais ></a>

  </div>

</div>

      <!-- TAGS -->
      <div class="widget-card">

        <div class="widget-title">

          <h3>Tecnologias em alta</h3>

          <i class="fa-solid fa-ellipsis"></i>

        </div>

        <div class="tags">

          <span>#JavaScript</span>

          <span>#Python</span>

          <span>#Java</span>

          <span>#PHP</span>

        </div>

      </div>

    </aside>

  </main>
  
  <script src="{{ asset('js/home.js') }}"></script>

</body>
</body>
</html>