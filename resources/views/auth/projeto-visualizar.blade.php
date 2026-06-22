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
    $podeEditar = $projeto->user_id === Auth::id()
        || $projeto->membros->contains('id', Auth::id());

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
                    <li class="{{ request()->routeIs('home') ? 'active' : '' }}">
                        <a href="{{ route('home') }}"><i class="fa-solid fa-house"></i><span>Home</span></a>
                    </li>
                    <li class="{{ request()->routeIs('perfil') ? 'active' : '' }}">
                        <a href="{{ route('perfil') }}"><i class="fa-regular fa-user"></i><span>Perfil</span></a>
                    </li>
                    <li class="{{ request()->routeIs('conexoes') ? 'active' : '' }}">
                        <a href="{{ route('conexoes') }}"><i class="fa-solid fa-user-group"></i><span>Conexões</span></a>
                    </li>
                    <li class="{{ request()->routeIs('projetos*') ? 'active' : '' }}">
                        <a href="{{ route('projetos') }}"><i class="fa-regular fa-folder"></i><span>Projetos</span></a>
                    </li>
                    <li class="{{ request()->routeIs('config') ? 'active' : '' }}">
                        <a href="{{ route('config') }}"><i class="fa-solid fa-gear"></i><span>Configurações</span></a>
                    </li>
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
                    <button class="logout" type="submit">
                        <i class="fa-solid fa-arrow-right-from-bracket"></i>Sair
                    </button>
                </form>
            </div>
        </aside>

        <main class="profile-content">

            {{-- ======================================================
                 FORMULÁRIO ÚNICO de edição do projeto
            ====================================================== --}}
            <form
                id="formProjeto"
                class="project-profile-shell"
                method="POST"
                action="{{ route('projetos.update', $projeto) }}"
                enctype="multipart/form-data"
            >
                @csrf
                @method('PUT')

                {{-- ── CABEÇALHO ── --}}
                <section class="profile-header project-profile-header">

                    {{-- Capa --}}
                    <div class="profile-picture project-profile-picture">
                        <img
                            id="capaPreview"
                            src="{{ $projeto->capa ? asset($projeto->capa) : asset('images/loading.png') }}"
                            class="profile-pic"
                            style="border-radius:18px; width:190px; height:190px; object-fit:cover;"
                        >
                        @if($podeEditar)
                            <label class="photo-edit-action" for="capa" title="Alterar capa">
                                <i class="fa-solid fa-camera"></i>
                            </label>
                            <input type="file" id="capa" name="capa" accept="image/*" hidden>
                        @endif
                    </div>

                    {{-- Informações --}}
                    <div class="profile-info">

                        <input
                            type="text"
                            name="nome"
                            id="projetoNome"
                            value="{{ $projeto->nome }}"
                            class="nome-input campo-projeto"
                            disabled
                        >

                        <input
                            type="text"
                            name="categoria"
                            id="projetoCategoria"
                            value="{{ $projeto->categoria }}"
                            class="curso-input campo-projeto"
                            disabled
                        >

                        <p><i class="fa-regular fa-calendar"></i> Criado em: {{ $projeto->created_at->format('d/m/Y') }}</p>
                        <p>
                            <i class="fa-regular fa-user"></i> Criado por:
                            <a href="{{ route('usuarios.show', $projeto->criador) }}">
                                {{ $projeto->criador->name ?? 'Usuário' }}
                            </a>
                        </p>

                        <div class="profile-stats">
                            <span>
                                Status:
                                <select name="status" id="projetoStatus" class="campo-projeto" disabled>
                                    @foreach(['Planejamento','Em desenvolvimento','Concluído','Arquivado'] as $s)
                                        <option value="{{ $s }}" {{ $projeto->status === $s ? 'selected' : '' }}>
                                            {{ $s }}
                                        </option>
                                    @endforeach
                                </select>
                            </span>
                            <span>Membros: {{ $projeto->membros->count() }}</span>
                            <span>Tecnologias: <span id="techCountLabel">{{ count($projeto->tecnologias ?? []) }}</span></span>
                        </div>

                        {{-- URL do repositório --}}
                        <div id="repoWrap" style="margin-top:10px;">
                            @if($projeto->repo_url || $podeEditar)
                                <label style="color:#2f7a66;font-weight:700;font-size:14px;">
                                    <i class="fa-brands fa-github"></i> Repositório
                                </label>
                                <input
                                    type="url"
                                    name="repo_url"
                                    id="projetoRepo"
                                    value="{{ $projeto->repo_url }}"
                                    placeholder="https://github.com/..."
                                    class="campo-projeto profile-field"
                                    disabled
                                    style="display:block; margin-top:4px; width:100%;"
                                >
                            @endif
                        </div>
                    </div>

                    {{-- Botões de ação --}}
                    <div class="profile-actions" id="projetoActions">

                        @if($podeEditar)
                            <button
                                type="button"
                                id="btnCancelarProjeto"
                                class="cancel-profile-btn"
                                hidden
                            >
                                Cancelar
                            </button>

                            <button
                                type="button"
                                id="btnEditarProjeto"
                                class="edit-project-btn"
                            >
                                <i class="fa-solid fa-pen"></i> Editar Projeto
                            </button>
                        @endif

                        @if($projeto->user_id !== Auth::id() && $projeto->membros->contains('id', Auth::id()))
                            <button
                                type="button"
                                class="leave-project-btn"
                                onclick="document.getElementById('formSair').submit()"
                            >
                                <i class="fa-solid fa-right-from-bracket"></i> Sair do projeto
                            </button>
                        @endif

                    </div>
                </section>

                {{-- ── ABAS ── --}}
                <section class="profile-tabs">
                    <button type="button" class="tab-btn active" data-tab="sobre">Sobre</button>
                    <button type="button" class="tab-btn" data-tab="membros">Membros</button>
                    <button type="button" class="tab-btn" data-tab="relacionados">Relacionados</button>
                    @if($podeEditar)
                        <button type="button" class="tab-btn" data-tab="configuracoes">Configurações</button>
                    @endif
                </section>

                {{-- ── ABA: SOBRE ── --}}
                <section class="tab-content active" id="sobre">
                    <div class="profile-body">

                        {{-- Descrição --}}
                        <div class="left-column">
                            <div class="profile-card readme-card project-readme-card">
                                <h2>Sobre o projeto</h2>

                                {{-- Visualização (modo leitura) --}}
                                <div
                                    id="descricaoPreview"
                                    class="readme-preview markdown-body {{ $descricaoHtml ? '' : 'readme-empty' }}"
                                >
                                    @if($descricaoHtml)
                                        {!! $descricaoHtml !!}
                                    @else
                                        <p>Este projeto ainda não possui descrição.</p>
                                    @endif
                                </div>

                                {{-- Editor (modo edição) --}}
                                <textarea
                                    name="descricao"
                                    id="projetoDescricao"
                                    class="campo-projeto markdown-editor"
                                    placeholder="Descreva o projeto usando Markdown..."
                                    disabled
                                >{{ $projeto->descricao }}</textarea>
                            </div>
                        </div>

                        {{-- Tecnologias --}}
                        <div class="right-column">
                            <div class="profile-card">
                                <div class="section-title-row">
                                    <h2>Tecnologias</h2>
                                    <span id="techCounterProjeto">{{ count($projeto->tecnologias ?? []) }}/8</span>
                                </div>

                                {{-- Editor de tecnologias (visível só no modo edição) --}}
                                <div class="tech-editor" id="techEditorProjeto">
                                    <input
                                        type="text"
                                        id="techInputProjeto"
                                        placeholder="ex: Laravel"
                                        class="profile-field campo-projeto"
                                        disabled
                                    >
                                    <button
                                        type="button"
                                        id="btnAddTechProjeto"
                                        class="add-tech-btn"
                                        disabled
                                        title="Adicionar tecnologia"
                                    >
                                        <i class="fa-solid fa-plus"></i>
                                    </button>
                                </div>

                                <div class="techs" id="techListProjeto">
                                    @forelse(($projeto->tecnologias ?? []) as $tech)
                                        <span class="tech-tag" data-value="{{ $tech }}">
                                            <span>#{{ $tech }}</span>
                                            <button
                                                type="button"
                                                class="remove-tech"
                                                aria-label="Remover {{ $tech }}"
                                            >
                                                <i class="fa-solid fa-xmark"></i>
                                            </button>
                                            <input type="hidden" name="tecnologias[]" value="{{ $tech }}">
                                        </span>
                                    @empty
                                        <p id="semTecnologias">Nenhuma tecnologia cadastrada.</p>
                                    @endforelse
                                </div>
                            </div>
                        </div>
                    </div>
                </section>

                {{-- ── ABA: MEMBROS ── --}}
                <section class="tab-content" id="membros">
                    <div class="profile-card">
                        <h2>Membros do projeto</h2>
                        <div class="profile-users-grid">
                            @forelse($projeto->membros as $membro)
                                <a href="{{ route('usuarios.show', $membro) }}" class="profile-user-card">
                                    <img
                                        src="{{ $membro->foto ? asset($membro->foto) : asset('images/default-user.png') }}"
                                        alt="{{ $membro->name }}"
                                    >
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

                {{-- ── ABA: RELACIONADOS ── --}}
                <section class="tab-content" id="relacionados">
                    <div class="profile-card">
                        <h2>Projetos relacionados</h2>
                        <div class="profile-projects-grid">
                            @forelse($projetosRelacionados as $relacionado)
                                <a href="{{ route('projetos.show', $relacionado) }}" class="profile-project-card">
                                    <img
                                        src="{{ $relacionado->capa ? asset($relacionado->capa) : asset('images/loading.png') }}"
                                        alt=""
                                    >
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

                {{-- ── ABA: CONFIGURAÇÕES (só para quem pode editar) ── --}}
                @if($podeEditar)
                    <section class="tab-content" id="configuracoes">
                        <div class="profile-card">
                            <h2>Convidar membros</h2>
                            <form
                                action="{{ route('projetos.convidar', $projeto) }}"
                                method="POST"
                                style="margin-top:12px;"
                            >
                                @csrf
                                <select
                                    name="membros[]"
                                    multiple
                                    class="profile-field"
                                    style="width:100%; min-height:120px; border:1px solid #cbe8d8; border-radius:12px; padding:8px;"
                                >
                                    @foreach(\App\Models\User::where('id','!=',Auth::id())->orderBy('name')->get() as $u)
                                        @unless($projeto->membros->contains('id', $u->id))
                                            <option value="{{ $u->id }}">{{ $u->name }} — {{ $u->curso ?: 'sem curso' }}</option>
                                        @endunless
                                    @endforeach
                                </select>
                                <button type="submit" class="edit-project-btn" style="margin-top:12px;">
                                    <i class="fa-solid fa-paper-plane"></i> Enviar convite
                                </button>
                            </form>
                        </div>

                        @if($projeto->user_id === Auth::id())
                            <div class="profile-card" style="margin-top:20px; border:1px solid #ffd6d6;">
                                <h2 style="color:#8f1f1f;">Zona de perigo</h2>
                                <p style="margin:10px 0 16px; color:#666;">
                                    Esta ação é permanente e não pode ser desfeita.
                                </p>
                                <form
                                    action="{{ route('projetos.destroy', $projeto) }}"
                                    method="POST"
                                    onsubmit="return confirm('Tem certeza que deseja excluir este projeto? Esta ação não pode ser desfeita.')"
                                >
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" class="leave-project-btn">
                                        <i class="fa-solid fa-trash"></i> Excluir projeto
                                    </button>
                                </form>
                            </div>
                        @endif
                    </section>
                @endif

            </form>

            {{-- Formulário separado para "Sair do projeto" (fora do form principal) --}}
            @if($projeto->user_id !== Auth::id() && $projeto->membros->contains('id', Auth::id()))
                <form
                    id="formSair"
                    action="{{ route('projetos.sair', $projeto) }}"
                    method="POST"
                    onsubmit="return confirm('Deseja realmente sair deste projeto?')"
                    hidden
                >
                    @csrf
                    @method('DELETE')
                </form>
            @endif

        </main>
    </div>

    <script src="{{ asset('js/perfil.js') }}"></script>
</body>
</html>