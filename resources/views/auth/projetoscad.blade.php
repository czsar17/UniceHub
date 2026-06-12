<!DOCTYPE html>
<html lang="pt-BR">

<head>

    <meta charset="UTF-8">

    <meta name="viewport" content="width=device-width, initial-scale=1.0">

    <title>Editar um Projeto - UniceHub</title>

    <link rel="stylesheet" href="{{ asset('css/projetoscad.css') }}">

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

        <div class="project-layout">

         <div class="project-main">

    <div class="create-project-card">
 <h1>Editar Projeto</h1>

    <form>

        <label>Nome do Projeto</label>
        <input type="text">

        <label>Descrição</label>
        <textarea></textarea>

        <label>Categoria</label>
        <select>
            <option>Desenvolvimento Web</option>
            <option>Desenvolvimento Mobile</option>
            <option>Banco de Dados</option>
            <option>Inteligência Artificial</option>
            <option>Machine Learning</option>
            <option>Deep Learning</option>
            <option>Ciência de Dados</option>
            <option>Big Data</option>
            <option>Cloud Computing</option>
            <option>DevOps</option>
            <option>Cibersegurança</option>
            <option>Redes de Computadores</option>
            <option>Programação Backend</option>
            <option>Programação Frontend</option>
            <option>Full Stack</option>
            <option>Engenharia de Software</option>
            <option>Arquitetura de Sistemas</option>
            <option>Internet das Coisas (IoT)</option>
            <option>Computação em Nuvem</option>
            <option>Blockchain</option>
            <option>Desenvolvimento de Jogos</option>
            <option>Realidade Virtual (VR)</option>
            <option>Realidade Aumentada (AR)</option>
            <option>UX/UI Design</option>
            <option>Design de Interfaces</option>
            <option>Automação de Processos</option>
            <option>Testes de Software</option>
            <option>Quality Assurance (QA)</option>
            <option>Sistemas Embarcados</option>
            <option>Robótica</option>
            <option>Análise de Sistemas</option>
            <option>Computação Quântica</option>
            <option>Java</option>
            <option>Python</option>
            <option>C#</option>
            <option>JavaScript</option>
            <option>React</option>
            <option>Node.js</option>
            <option>Laravel</option>
            <option>Spring Boot</option>
            <option>Docker</option>
            <option>Kubernetes</option>
            <option>MySQL</option>
            <option>PostgreSQL</option>
            <option>MongoDB</option>
            <option>Git e GitHub</option>
            <option>Linux</option>
            <option>Figma</option>
        </select>

        <label>Tecnologias</label>
        <input type="text">

        <label>Imagem de Capa</label>
        <div class="upload-area">

    <i class="fa-regular fa-image"></i>

    <h4>Adicionar capa do projeto</h4>

    <p>PNG, JPG ou SVG (MÁX 5 MB)</p>

    <input type="file">

</div>
        <label>Status</label>

        <select>
            <option>Em desenvolvimento</option>
            <option>Concluído</option>
            <option>Arquivado</option>
        </select>

        <button type="submit">
            Publicar Projeto
        </button>

    </form>
</div>

<section class="project-members">

    <div class="members-header">

        <h3>Membros do projeto</h3>

        <button class="invite-member-btn">
            <i class="fa-solid fa-plus"></i>
            Convidar pessoas
        </button>

    </div>

    <p class="members-description">
        Convide ou gerencie os membros que fazem parte deste projeto.
    </p>

    <div class="member-list">

        <div class="member-card">

            <img src="assets/userx.png">

            <div class="member-info">

                <h4>Nome</h4>

                <span>Curso</span>

                <small>0 conexões em comum</small>

            </div>

            <div class="member-email">
                nome@email.com
            </div>

            <i class="fa-solid fa-ellipsis-vertical"></i>

        </div>

    </div>

</section>

</div> <!-- fecha project-main -->

<aside class="project-sidebar">

        <div class="preview-card">

            <h3>Pré-visualização</h3>

            <div class="preview-project">

                <img src="assets/loading.png">

                <h4>Nome do projeto</h4>

                <p>Descrição do projeto...</p>

            </div>

        </div>

        <div class="summary-card">

            <h3>Resumo do Projeto</h3>

            <p><i class="fa-solid fa-user"></i> Criado por: </p>
            <p><i class="fa-solid fa-circle-check"></i> Status: </p>
            <p><i class="fa-solid fa-calendar"></i> Data: </p>
            <p><i class="fa-solid fa-code"></i> Tecnologia: </p>

        </div>

    </aside>

</div>

</div>

</body>
</html>