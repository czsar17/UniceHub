const tabs = document.querySelectorAll(".tab-btn");
const contents = document.querySelectorAll(".tab-content");
const formPerfil = document.getElementById("formPerfil");
const btnEditar = document.getElementById("btnEditar");
const btnCancelar = document.getElementById("btnCancelar");
const campos = document.querySelectorAll(".campo-edicao");
const sobreMim = document.getElementById("sobre_mim");
const sobreContador = document.getElementById("sobreContador");
const techInput = document.getElementById("techInput");
const btnAddTech = document.getElementById("btnAddTech");
const techList = document.getElementById("techList");
const techCounter = document.getElementById("techCounter");
const fotoInput = document.getElementById("foto");
const fotosPerfil = document.querySelectorAll(".profile-pic");

let editando = false;
let previewFotoUrl = null;

tabs.forEach((tab) => {
    tab.addEventListener("click", () => {
        tabs.forEach((btn) => btn.classList.remove("active"));
        contents.forEach((content) => content.classList.remove("active"));

        tab.classList.add("active");
        document.getElementById(tab.dataset.tab).classList.add("active");
    });
});

function atualizarContadorSobre() {
    sobreContador.textContent = sobreMim.value.length;
}

function getTecnologias() {
    return Array.from(techList.querySelectorAll(".tech-tag")).map((tag) =>
        tag.dataset.value.trim()
    );
}

function atualizarContadorTechs() {
    const total = getTecnologias().length;
    techCounter.textContent = `${total}/8`;
    btnAddTech.disabled = !editando || total >= 8;
    techInput.disabled = !editando || total >= 8;
}

function criarTagTecnologia(valor) {
    const tag = document.createElement("span");
    tag.className = "tech-tag";
    tag.dataset.value = valor;

    const texto = document.createElement("span");
    texto.textContent = `#${valor}`;

    const removeButton = document.createElement("button");
    removeButton.type = "button";
    removeButton.className = "remove-tech";
    removeButton.setAttribute("aria-label", `Remover ${valor}`);
    removeButton.innerHTML = '<i class="fa-solid fa-xmark"></i>';

    const input = document.createElement("input");
    input.type = "hidden";
    input.name = "tecnologias[]";
    input.value = valor;

    tag.append(texto, removeButton, input);
    techList.appendChild(tag);
}

function adicionarTecnologia() {
    const valor = techInput.value.trim().replace(/^#/, "");

    if (!valor) {
        return;
    }

    const tecnologias = getTecnologias();
    const jaExiste = tecnologias.some(
        (tecnologia) => tecnologia.toLowerCase() === valor.toLowerCase()
    );

    if (tecnologias.length >= 8 || jaExiste) {
        techInput.value = "";
        return;
    }

    criarTagTecnologia(valor);
    techInput.value = "";
    atualizarContadorTechs();
}

function alternarEdicao(ativo) {
    editando = ativo;
    document.body.classList.toggle("perfil-editando", ativo);

    campos.forEach((campo) => {
        campo.disabled = !ativo;
    });

    btnCancelar.hidden = !ativo;
    btnEditar.type = "button";
    btnEditar.innerHTML = ativo
        ? '<i class="fa-solid fa-floppy-disk"></i> Salvar'
        : '<i class="fa-solid fa-pen"></i> Editar Perfil';

    atualizarContadorTechs();
}

techList.addEventListener("click", (event) => {
    const removeButton = event.target.closest(".remove-tech");

    if (!removeButton || !editando) {
        return;
    }

    removeButton.closest(".tech-tag").remove();
    atualizarContadorTechs();
});

btnAddTech.addEventListener("click", adicionarTecnologia);

techInput.addEventListener("keydown", (event) => {
    if (event.key === "Enter") {
        event.preventDefault();
        adicionarTecnologia();
    }
});

sobreMim.addEventListener("input", atualizarContadorSobre);

fotoInput.addEventListener("change", () => {
    const arquivo = fotoInput.files[0];

    if (!arquivo || !arquivo.type.startsWith("image/")) {
        return;
    }

    if (previewFotoUrl) {
        URL.revokeObjectURL(previewFotoUrl);
    }

    previewFotoUrl = URL.createObjectURL(arquivo);

    fotosPerfil.forEach((foto) => {
        foto.src = previewFotoUrl;
    });
});

btnEditar.addEventListener("click", (event) => {
    event.preventDefault();

    if (!editando) {
        alternarEdicao(true);
        return;
    }

    formPerfil.requestSubmit();
});

btnCancelar.addEventListener("click", () => {
    window.location.reload();
});

formPerfil.addEventListener("submit", () => {
    adicionarTecnologia();
});

atualizarContadorSobre();
atualizarContadorTechs();
