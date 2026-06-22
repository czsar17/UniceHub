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

@forelse($projetosFeed as $projeto)

<div class="post-card">

    <div class="post-top">

        <div class="post-user">
            <img src="{{ asset($projeto->criador->foto) }}">
              <div>
              <h3>{{ $projeto->criador->name }}</h3>

              <span>{{ $projeto->criador->curso }}</span>
            </div>
        </div>

        <div class="post-right">

            <span>{{ $projeto->created_at->diffForHumans() }}</span>

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

        <h2>{{ $projeto->nome }}</h2>

        <p>{{ $projeto->descricao }}</p>

    </div>

    <div class="post-actions">

        <div class="left-actions">

            <form
    action="{{ route('projetos.curtir', $projeto) }}"
    method="POST"
>
    @csrf

    <button type="submit" class="like-btn">
        <i class="fa-regular fa-heart"></i>
        {{ $projeto->curtidas_count }}
    </button>
</form>

            <button class="comment-btn" data-id="{{ $projeto->id }}">
                <i class="fa-regular fa-comment"></i>
                {{ $projeto->comentarios_count }}
                Comentários
            </button>

        </div>

    </div>

</div>

@empty

<div class="post-card">
    <h3>Nenhum projeto encontrado.</h3>
</div>

@endforelse

</section>


    <!-- WIDGETS -->
    <aside class="widgets">

      <!-- Sugestões -->
   <div class="widget-card">

  <div class="widget-title">

    <h3>Sugestões de Conexões</h3>

    <i class="fa-solid fa-ellipsis"></i>

  </div>

<!-- SUGESTÕES DINÂMICAS -->
@if($sugestoes->count() > 0)

    @foreach($sugestoes as $usuario)

    <div class="suggestion">

        <div class="suggestion-user">

            <img src="{{ asset($usuario->foto) }}">

            <div>

                <h4>{{ $usuario->name }}</h4>

                @if($usuario->curso)
                    <span>{{ $usuario->curso }}</span>
                @endif

            </div>

        </div>

        <form action="{{ route('seguir.enviar', $usuario->id) }}" method="POST">

            @csrf

            <button type="submit">
                Conectar
            </button>

        </form>

    </div>

    @endforeach

@else

    <div class="empty-suggestions">

        <i class="fa-solid fa-user-group"></i>

        <p>Nenhum usuário encontrado para se conectar.</p>

    </div>

@endif

</div>    

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

  <div id="commentModal" class="comment-modal">

    <div class="comment-box">

        <div class="comment-header">

            <h3>Comentários</h3>

            <button id="closeComments">
    <i class="fa-solid fa-xmark"></i>
</button>

        </div>

        <div id="commentsContainer">

        </div>

        <form
    id="commentForm"
    method="POST"
>
    @csrf

    <input
        type="text"
        name="comentario"
        id="commentText"
        placeholder="Digite um comentário..."
    >

    <button type="submit">
        Enviar
    </button>
</form>

    </div>

</div>

<script src="{{ asset('js/home.js') }}"></script>

</body>
</body>
</html>