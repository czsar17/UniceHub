<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $projeto->nome }} - UniceHub</title>
    <link rel="stylesheet" href="{{ asset('css/perfil.css') }}">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
</head>

@php
    $user = Auth::user();
    $descricaoHtml = trim($projeto->descricao ?? '')
        ? \Illuminate\Support\Str::markdown($projeto->descricao, [
            'html_input' => 'allow',
            'allow_unsafe_links' => false,
        ])
        : '';
@endphp

<body>
    <header class="header">
        <div class="header-left">
            <i class="fa-solid fa-bars menu-icon"></i>
            <img src="{{ asset('images/LOGOUNICEHUB-removebg-preview.png') }}" class="header-logo">
        </div>

        <form class="search-box" action="{{ route('buscar') }}" method="GET">
            <input type="text" name="q" value="{{ request('q') }}" placeholder="Pesquisar pessoas e projetos...">
            <button type="submit" aria-label="Pesquisar">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </form>

        <div class="header-icons">
            <i class="fa-regular fa-bell notification"></i>
            <div class="header-profile">
                <img src="{{ asset($user->foto) }}" class="profile-pic">
            </div>
        </div>
    </header>

    <div class="main-container">
        <aside class="sidebar">
            <div class="sidebar-content">
                <ul>
                    <li class="{{ request()->routeIs('home') ? 'active' : '' }}"><a href="{{ route('home') }}"><i class="fa-solid fa-house"></i><span>Home</span></a></li>
                    <li class="{{ request()->routeIs('perfil') ? 'active' : '' }}"><a href="{{ route('perfil') }}"><i class="fa-regular fa-user"></i><span>Perfil</span></a></li>
                    <li class="{{ request()->routeIs('conexoes') ? 'active' : '' }}"><a href="{{ route('conexoes') }}"><i class="fa-solid fa-user-group"></i><span>Conexões</span></a></li>
                    <li class="{{ request()->routeIs('projetos*') ? 'active' : '' }}"><a href="{{ route('projetos') }}"><i class="fa-regular fa-folder"></i><span>Projetos</span></a></li>
                    <li class="{{ request()->routeIs('config') ? 'active' : '' }}"><a href="{{ route('config') }}"><i class="fa-solid fa-gear"></i><span>Configurações</span></a></li>
                </ul>
            </div>

            <div class="sidebar-bottom">
                <div class="sidebar-profile">
                    <img src="{{ asset($user->foto) }}" class="profile-pic">
                    <div>
                        <h4>{{ $user->name }}</h4>
                        @if($user->curso)<span>{{ $user->curso }}</span>@endif
                    </div>
                </div>
                <form method="POST" action="/logout">
                    @csrf
                    <button class="logout" type="submit"><i class="fa-solid fa-arrow-right-from-bracket"></i>Sair</button>
                </form>
            </div>
        </aside>

        <main class="profile-content">
            <div id="formPerfil" class="project-profile-shell">
                <section class="profile-header project-profile-header">
                    <div class="profile-picture project-profile-picture">
                        <img src="{{ $projeto->capa ? asset($projeto->capa) : asset('images/loading.png') }}" class="profile-pic">
                    </div>

                    <div class="profile-info">
                        <h1 class="profile-title-text">{{ $projeto->nome }}</h1>
                        <h2>{{ $projeto->categoria ?: 'Projeto' }}</h2>
                        <p><i class="fa-regular fa-calendar"></i> Criado em: {{ $projeto->created_at->format('d/m/Y') }}</p>
                        <p><i class="fa-regular fa-user"></i> Criado por: <a href="{{ route('usuarios.show', $projeto->criador) }}">{{ $projeto->criador->name ?? 'Usuário' }}</a></p>

                        <div class="profile-stats">
                            <span>Status: {{ $projeto->status }}</span>
                            <span>Membros: {{ $projeto->membros->count() }}</span>
                            <span>Tecnologias: {{ count($projeto->tecnologias ?? []) }}</span>
                        </div>
                    </div>

                    <div class="profile-actions">
                        @if(
                            $projeto->user_id !== Auth::id()
                            && $projeto->membros->contains('id', Auth::id())
                        )
                            <form action="{{ route('projetos.sair', $projeto) }}"
                                method="POST"
                                onsubmit="return confirm('Deseja realmente sair deste projeto?')">

                                @csrf
                                @method('DELETE')

                                <button type="submit" class="leave-project-btn">
                                    <i class="fa-solid fa-right-from-bracket"></i>
                                    Sair do projeto
                                </button>
                            </form>
                        @endif
                        @if($projeto->repo_url)
                            <a href="{{ $projeto->repo_url }}" target="_blank" rel="noopener" class="edit-project-btn">
                                <i class="fa-brands fa-github"></i>
                                Repositório
                            </a>
                        @endif
                    </div>
                </section>

                <section class="profile-tabs">
                    <button type="button" class="tab-btn active" data-tab="sobre">Sobre</button>
                    <button type="button" class="tab-btn" data-tab="membros">Membros</button>
                    <button type="button" class="tab-btn" data-tab="relacionados">Relacionados</button>
                </section>

                <section class="tab-content active" id="sobre">
                    <div class="profile-body">
                        <div class="left-column">
                            <div class="profile-card readme-card project-readme-card">
                                <h2>Sobre o projeto</h2>
                                <div class="readme-preview markdown-body {{ $descricaoHtml ? '' : 'readme-empty' }}">
                                    @if($descricaoHtml)
                                        {!! $descricaoHtml !!}
                                    @else
                                        <p>Este projeto ainda não possui descrição.</p>
                                    @endif
                                </div>
                            </div>
                        </div>

                        <div class="right-column">
                            <div class="profile-card">
                                <h2>Tecnologias</h2>
                                <div class="techs">
                                    @forelse(($projeto->tecnologias ?? []) as $tech)
                                        <span>#{{ $tech }}</span>
                                    @empty
                                        <p>Nenhuma tecnologia cadastrada.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                <section class="tab-content" id="membros">
                    <div class="profile-card">
                        <h2>Membros do projeto</h2>
                        <div class="profile-users-grid">
                            @forelse($projeto->membros as $membro)
                                <a href="{{ route('usuarios.show', $membro) }}" class="profile-user-card">
                                    <img src="{{ asset($membro->foto) }}" alt="">
                                    <div>
                                        <h3>{{ $membro->name }}</h3>
                                        <span>{{ $membro->curso ?: 'Curso não informado' }}</span>
                                    </div>
                                </a>
                            @empty
                                <p>Este projeto ainda não possui membros aceitos.</p>
                            @endforelse
                        </div>
                    </div>
                </section>

                <section class="tab-content" id="relacionados">
                    <div class="profile-card">
                        <h2>Projetos relacionados</h2>
                        <div class="profile-projects-grid">
                            @forelse($projetosRelacionados as $relacionado)
                                <a href="{{ route('projetos.show', $relacionado) }}" class="profile-project-card">
                                    <img src="{{ $relacionado->capa ? asset($relacionado->capa) : asset('images/loading.png') }}" alt="">
                                    <div>
                                        <div class="profile-project-title">
                                            <h3>{{ $relacionado->nome }}</h3>
                                            <span>{{ $relacionado->status }}</span>
                                        </div>
                                        <p>{{ Str::limit(strip_tags($relacionado->descricao), 120) }}</p>
                                    </div>
                                </a>
                            @empty
                                <p>Nenhum projeto relacionado encontrado.</p>
                            @endforelse
                        </div>
                    </div>
                </section>
            </div>
        </main>
    </div>

    <script src="{{ asset('js/perfil.js') }}"></script>
</body>
</html>
