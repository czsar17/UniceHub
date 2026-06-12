const content = document.getElementById("configContent");
const menuItems = document.querySelectorAll(".menu-item");
function renderPage(page){

   if(page === "perfil"){

    content.innerHTML = `

        <div class="personal-header">

            <h2>Informações pessoais</h2>

            <button class="save-btn">
                Salvar alterações
            </button>

        </div>

        <div class="personal-content">

            <div class="profile-avatar">
                <img src="assets/userx.png" alt="">
            </div>

            <div class="personal-form">

                <div class="input-group">
                    <label>Nome completo</label>
                    <input type="text" maxlength="100">
                    <span class="char-count">0/100</span>
                </div>

                <div class="input-group">
                    <label>Email</label>
                    <input type="email" maxlength="100">
                    <span class="char-count">0/100</span>
                </div>

                <div class="input-group">
                    <label>Curso</label>
                    <input type="text" maxlength="100">
                    <span class="char-count">0/100</span>
                </div>

                <div class="input-group">
                    <label>Data de nascimento</label>
                    <input type="date">
                </div>

                <div class="input-group">
                    <label>Sobre mim</label>
                    <textarea maxlength="500"></textarea>
                    <span class="char-count">0/500</span>
                </div>

                <div class="input-group">
                    <label>Tecnologias</label>
                    <textarea maxlength="500"></textarea>
                    <span class="char-count">0/500</span>
                </div>

            </div>

        </div>
    
    `;
iniciarContadores();
}
if(page === "seguranca"){

    content.innerHTML = `
        <div class="security-section">

            <h2>Segurança da conta</h2>
            <p>Gerencie sua senha e seus dados.</p>

            <div class="security-item">
                <div>
                    <h4>Senha</h4>
                    <span>Altere sua senha.</span>
                </div>

                <button class="save-btn">
                    Alterar senha
                </button>
            </div>

            <div class="security-item">
                <div>
                    <h4>Autenticação de dois fatores</h4>
                    <span>
                        Proteja sua conta com uma camada extra de segurança.
                    </span>
                </div>

                <input type="checkbox">
            </div>

            <div class="security-item">

    <div>
        <h4>CPF</h4>
        <span>Visualize seu CPF.</span>
    </div>

    <div class="cpf-container">

        <input
            type="password"
            value="12345678900"
            id="cpfField"
            readonly
        >

        <button id="toggleCpf" class="eye-btn">
            <i class="fa-regular fa-eye"></i>
        </button>

    </div>

</div>
    `;
    iniciarContadores();
    const cpfField = document.getElementById("cpfField");
const toggleCpf = document.getElementById("toggleCpf");

toggleCpf.addEventListener("click", () => {

    if(cpfField.type === "password"){

        cpfField.type = "text";

        toggleCpf.innerHTML =
        '<i class="fa-regular fa-eye-slash"></i>';

    }else{

        cpfField.type = "password";

        toggleCpf.innerHTML =
        '<i class="fa-regular fa-eye"></i>';

    }

});
}
if(page === "notificacoes"){

content.innerHTML = `

<h2>Preferências de notificação</h2>

<p>
Escolha como e quando deseja ser notificado.
</p>

<div class="notification-grid">

    <div class="notification-card">

        <div>
            <h4>Atualizações de projetos</h4>
            <span>Atividades e atualizações.</span>
        </div>

        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>

    </div>

    <div class="notification-card">

        <div>
            <h4>Conexões</h4>
            <span>Solicitações e conexões.</span>
        </div>

        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>

    </div>

    <div class="notification-card">

        <div>
            <h4>Menções</h4>
            <span>Quando você for mencionado.</span>
        </div>

        <label class="switch">
            <input type="checkbox" checked>
            <span class="slider"></span>
        </label>

    </div>

    <div class="notification-card">

        <div>
            <h4>Notificações</h4>
            <span>Desativar todas.</span>
        </div>

        <label class="switch">
            <input type="checkbox">
            <span class="slider"></span>
        </label>

    </div>

</div>

`;
iniciarContadores();
}

if(page === "tipoPerfil"){

content.innerHTML = `

<h2>Trocar perfil</h2>

<p>
Escolha como acessar sua conta,
seja como aluno ou professor.
</p>

<div class="profile-card active-profile">

    <h3>Aluno 🎓</h3>

    <button class="profile-btn">
        Acessar como aluno
    </button>

</div>

<div class="profile-card">

    <h3>Professor 👨‍🏫</h3>

    <button class="profile-btn">
        Acessar como professor
    </button>

</div>

`;
iniciarContadores();
}
}
function iniciarContadores(){
menuItems.forEach(item => {

    item.addEventListener("click", () => {

        menuItems.forEach(btn =>
            btn.classList.remove("active")
        );

        item.classList.add("active");

        renderPage(item.dataset.page);

    });

});
    const campos = document.querySelectorAll(
        "input[maxlength], textarea[maxlength]"
    );

    campos.forEach(campo => {

        const contador =
            campo.parentElement.querySelector(".char-count");

        campo.addEventListener("input", () => {

            contador.textContent =
                `${campo.value.length}/${campo.maxLength}`;

        });

    });

}

renderPage("perfil");