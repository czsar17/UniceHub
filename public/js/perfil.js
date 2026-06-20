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
    if (sobreMim && sobreContador) {
        sobreContador.textContent = sobreMim.value.length;
    }
}

function getTecnologias() {
    if (!techList) return [];

    return Array.from(techList.querySelectorAll(".tech-tag")).map((tag) =>
        tag.dataset.value.trim()
    );
}

function atualizarContadorTechs() {
    if (!techCounter) return;

    const total = getTecnologias().length;
    techCounter.textContent = `${total}/8`;

    if (btnAddTech) {
        btnAddTech.disabled = !editando || total >= 8;
    }

    if (techInput) {
        techInput.disabled = !editando || total >= 8;
    }
}

function criarTagTecnologia(valor) {
    if (!techList) return;

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
    if (!techInput) return;

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

    if (btnCancelar) {
        btnCancelar.hidden = !ativo;
    }

    if (btnEditar) {
        btnEditar.type = "button";
        btnEditar.innerHTML = ativo
            ? '<i class="fa-solid fa-floppy-disk"></i> Salvar'
            : '<i class="fa-solid fa-pen"></i> Editar Perfil';
    }

    atualizarContadorTechs();
}

if (techList) {
    techList.addEventListener("click", (event) => {
        const removeButton = event.target.closest(".remove-tech");

        if (!removeButton || !editando) {
            return;
        }

        removeButton.closest(".tech-tag").remove();
        atualizarContadorTechs();
    });
}

if (btnAddTech) {
    btnAddTech.addEventListener("click", adicionarTecnologia);
}

if (techInput) {
    techInput.addEventListener("keydown", (event) => {
        if (event.key === "Enter") {
            event.preventDefault();
            adicionarTecnologia();
        }
    });
}

if (sobreMim) {
    sobreMim.addEventListener("input", atualizarContadorSobre);
}

if (fotoInput) {
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
}

if (btnEditar) {
    btnEditar.addEventListener("click", (event) => {
        event.preventDefault();

        if (!editando) {
            alternarEdicao(true);
            return;
        }

        formPerfil.requestSubmit();
    });
}

if (btnCancelar) {
    btnCancelar.addEventListener("click", () => {
        window.location.reload();
    });
}

if (formPerfil && btnEditar) {
    formPerfil.addEventListener("submit", () => {
        adicionarTecnologia();
    });
}

atualizarContadorSobre();
atualizarContadorTechs();
