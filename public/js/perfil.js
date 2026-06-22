/* ==========================================================
   ABAS  (perfil + projeto — compartilhado)
========================================================== */
document.querySelectorAll(".tab-btn").forEach((tab) => {
    tab.addEventListener("click", () => {
        const parent = tab.closest("section, .profile-tabs")?.parentElement
            ?? document;

        parent.querySelectorAll(".tab-btn").forEach((b) => b.classList.remove("active"));
        parent.querySelectorAll(".tab-content").forEach((c) => c.classList.remove("active"));

        tab.classList.add("active");
        const target = document.getElementById(tab.dataset.tab);
        if (target) target.classList.add("active");
    });
});

/* ==========================================================
   EDIÇÃO DE PERFIL
========================================================== */
const formPerfil       = document.getElementById("formPerfil");
const btnEditar        = document.getElementById("btnEditar");
const btnCancelar      = document.getElementById("btnCancelar");
const campos           = document.querySelectorAll(".campo-edicao");
const sobreMim         = document.getElementById("sobre_mim");
const sobreContador    = document.getElementById("sobreContador");
const techInput        = document.getElementById("techInput");
const btnAddTech       = document.getElementById("btnAddTech");
const techList         = document.getElementById("techList");
const techCounter      = document.getElementById("techCounter");
const fotoInput        = document.getElementById("foto");
const fotosPerfil      = document.querySelectorAll(".profile-pic");

let editandoPerfil  = false;
let previewFotoUrl  = null;

function atualizarContadorSobre() {
    if (sobreMim && sobreContador) {
        sobreContador.textContent = sobreMim.value.length;
    }
}

function getTecnologiasPerfil() {
    if (!techList) return [];
    return Array.from(techList.querySelectorAll(".tech-tag"))
        .map((tag) => tag.dataset.value.trim());
}

function atualizarContadorTechs() {
    if (!techCounter) return;
    const total = getTecnologiasPerfil().length;
    techCounter.textContent = `${total}/8`;
    if (btnAddTech) btnAddTech.disabled = !editandoPerfil || total >= 8;
    if (techInput)  techInput.disabled  = !editandoPerfil || total >= 8;
}

function criarTagTecnologiaPerfil(valor) {
    if (!techList) return;
    const tag = document.createElement("span");
    tag.className   = "tech-tag";
    tag.dataset.value = valor;

    const texto = document.createElement("span");
    texto.textContent = `#${valor}`;

    const removeBtn = document.createElement("button");
    removeBtn.type      = "button";
    removeBtn.className = "remove-tech";
    removeBtn.setAttribute("aria-label", `Remover ${valor}`);
    removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';

    const hidden = document.createElement("input");
    hidden.type  = "hidden";
    hidden.name  = "tecnologias[]";
    hidden.value = valor;

    tag.append(texto, removeBtn, hidden);
    techList.appendChild(tag);
}

function adicionarTecnologiaPerfil() {
    if (!techInput) return;
    const valor = techInput.value.trim().replace(/^#/, "");
    if (!valor) return;
    const techs   = getTecnologiasPerfil();
    const jaExiste = techs.some((t) => t.toLowerCase() === valor.toLowerCase());
    if (techs.length >= 8 || jaExiste) { techInput.value = ""; return; }
    criarTagTecnologiaPerfil(valor);
    techInput.value = "";
    atualizarContadorTechs();
}

function alternarEdicaoPerfil(ativo) {
    editandoPerfil = ativo;
    document.body.classList.toggle("perfil-editando", ativo);
    campos.forEach((c) => { c.disabled = !ativo; });
    if (btnCancelar) btnCancelar.hidden = !ativo;
    if (btnEditar) {
        btnEditar.type    = "button";
        btnEditar.innerHTML = ativo
            ? '<i class="fa-solid fa-floppy-disk"></i> Salvar'
            : '<i class="fa-solid fa-pen"></i> Editar Perfil';
    }
    atualizarContadorTechs();
}

if (techList) {
    techList.addEventListener("click", (e) => {
        const removeBtn = e.target.closest(".remove-tech");
        if (!removeBtn || !editandoPerfil) return;
        removeBtn.closest(".tech-tag").remove();
        atualizarContadorTechs();
    });
}

if (btnAddTech) btnAddTech.addEventListener("click", adicionarTecnologiaPerfil);

if (techInput) {
    techInput.addEventListener("keydown", (e) => {
        if (e.key === "Enter") { e.preventDefault(); adicionarTecnologiaPerfil(); }
    });
}

if (sobreMim) sobreMim.addEventListener("input", atualizarContadorSobre);

if (fotoInput) {
    fotoInput.addEventListener("change", () => {
        const arquivo = fotoInput.files[0];
        if (!arquivo || !arquivo.type.startsWith("image/")) return;
        if (previewFotoUrl) URL.revokeObjectURL(previewFotoUrl);
        previewFotoUrl = URL.createObjectURL(arquivo);
        fotosPerfil.forEach((f) => { f.src = previewFotoUrl; });
    });
}

if (btnEditar) {
    btnEditar.addEventListener("click", (e) => {
        e.preventDefault();
        if (!editandoPerfil) { alternarEdicaoPerfil(true); return; }
        adicionarTecnologiaPerfil();
        formPerfil.requestSubmit();
    });
}

if (btnCancelar) {
    btnCancelar.addEventListener("click", () => { window.location.reload(); });
}

if (formPerfil && btnEditar) {
    formPerfil.addEventListener("submit", () => { adicionarTecnologiaPerfil(); });
}

atualizarContadorSobre();
atualizarContadorTechs();


/* ==========================================================
   EDIÇÃO DE PROJETO  — mesmo padrão da edição de perfil
========================================================== */
const formProjeto          = document.getElementById("formProjeto");
const btnEditarProjeto     = document.getElementById("btnEditarProjeto");
const btnCancelarProjeto   = document.getElementById("btnCancelarProjeto");
const camposProjeto        = document.querySelectorAll(".campo-projeto");

// elementos específicos do projeto
const capaInput            = document.getElementById("capa");
const capaPreview          = document.getElementById("capaPreview");
const descricaoPreview     = document.getElementById("descricaoPreview");
const descricaoEditor      = document.getElementById("projetoDescricao");
const techInputProjeto     = document.getElementById("techInputProjeto");
const btnAddTechProjeto    = document.getElementById("btnAddTechProjeto");
const techListProjeto      = document.getElementById("techListProjeto");
const techCounterProjeto   = document.getElementById("techCounterProjeto");
const techCountLabel       = document.getElementById("techCountLabel");

let editandoProjeto    = false;
let previewCapaUrl     = null;

/* ── tecnologias do projeto ── */

function getTecnologiasProjeto() {
    if (!techListProjeto) return [];
    return Array.from(techListProjeto.querySelectorAll(".tech-tag"))
        .map((tag) => tag.dataset.value.trim());
}

function atualizarContadorTechsProjeto() {
    if (!techCounterProjeto) return;
    const total = getTecnologiasProjeto().length;
    techCounterProjeto.textContent = `${total}/8`;
    if (techCountLabel) techCountLabel.textContent = total;

    if (btnAddTechProjeto) btnAddTechProjeto.disabled = !editandoProjeto || total >= 8;
    if (techInputProjeto)  techInputProjeto.disabled  = !editandoProjeto || total >= 8;
}

function criarTagTecnologiaProjeto(valor) {
    if (!techListProjeto) return;

    // remove placeholder "Nenhuma tecnologia" se existir
    const placeholder = document.getElementById("semTecnologias");
    if (placeholder) placeholder.remove();

    const tag = document.createElement("span");
    tag.className     = "tech-tag";
    tag.dataset.value = valor;

    const texto = document.createElement("span");
    texto.textContent = `#${valor}`;

    const removeBtn = document.createElement("button");
    removeBtn.type      = "button";
    removeBtn.className = "remove-tech";
    removeBtn.setAttribute("aria-label", `Remover ${valor}`);
    removeBtn.innerHTML = '<i class="fa-solid fa-xmark"></i>';

    const hidden = document.createElement("input");
    hidden.type  = "hidden";
    hidden.name  = "tecnologias[]";
    hidden.value = valor;

    tag.append(texto, removeBtn, hidden);
    techListProjeto.appendChild(tag);
}

function adicionarTecnologiaProjeto() {
    if (!techInputProjeto) return;
    const valor = techInputProjeto.value.trim().replace(/^#/, "");
    if (!valor) return;
    const techs    = getTecnologiasProjeto();
    const jaExiste = techs.some((t) => t.toLowerCase() === valor.toLowerCase());
    if (techs.length >= 8 || jaExiste) { techInputProjeto.value = ""; return; }
    criarTagTecnologiaProjeto(valor);
    techInputProjeto.value = "";
    atualizarContadorTechsProjeto();
}

if (techListProjeto) {
    techListProjeto.addEventListener("click", (e) => {
        const removeBtn = e.target.closest(".remove-tech");
        if (!removeBtn || !editandoProjeto) return;
        removeBtn.closest(".tech-tag").remove();
        atualizarContadorTechsProjeto();
    });
}

if (btnAddTechProjeto) {
    btnAddTechProjeto.addEventListener("click", adicionarTecnologiaProjeto);
}

if (techInputProjeto) {
    techInputProjeto.addEventListener("keydown", (e) => {
        if (e.key === "Enter") { e.preventDefault(); adicionarTecnologiaProjeto(); }
    });
}

/* ── preview da capa ── */
if (capaInput) {
    capaInput.addEventListener("change", () => {
        const arquivo = capaInput.files[0];
        if (!arquivo || !arquivo.type.startsWith("image/")) return;
        if (previewCapaUrl) URL.revokeObjectURL(previewCapaUrl);
        previewCapaUrl = URL.createObjectURL(arquivo);
        if (capaPreview) capaPreview.src = previewCapaUrl;
    });
}

/* ── alternar modo edição do projeto ── */
function alternarEdicaoProjeto(ativo) {
    editandoProjeto = ativo;

    // habilita/desabilita campos (nome, categoria, status, repo, descrição, techInput)
    camposProjeto.forEach((c) => { c.disabled = !ativo; });

    // botão cancelar
    if (btnCancelarProjeto) btnCancelarProjeto.hidden = !ativo;

    // label do botão principal
    if (btnEditarProjeto) {
        btnEditarProjeto.innerHTML = ativo
            ? '<i class="fa-solid fa-floppy-disk"></i> Salvar alterações'
            : '<i class="fa-solid fa-pen"></i> Editar Projeto';
    }

    // alterna visualização vs editor de descrição (igual ao readme do perfil)
    if (descricaoPreview) descricaoPreview.style.display = ativo ? "none"  : "";
    if (descricaoEditor)  descricaoEditor.style.display  = ativo ? "block" : "none";

    // botão de câmera na capa
    document.body.classList.toggle("projeto-editando", ativo);

    // remove-tech visíveis só no modo edição
    document.querySelectorAll("#techListProjeto .remove-tech").forEach((btn) => {
        btn.style.display = ativo ? "inline-flex" : "none";
    });

    atualizarContadorTechsProjeto();
}

if (btnEditarProjeto) {
    btnEditarProjeto.addEventListener("click", (e) => {
        e.preventDefault();
        if (!editandoProjeto) { alternarEdicaoProjeto(true); return; }
        // garante que a tecnologia digitada no input é adicionada antes de salvar
        adicionarTecnologiaProjeto();
        formProjeto.requestSubmit();
    });
}

if (btnCancelarProjeto) {
    btnCancelarProjeto.addEventListener("click", () => { window.location.reload(); });
}

// inicialização
atualizarContadorTechsProjeto();
// esconde botões remove-tech no carregamento (modo leitura)
document.querySelectorAll("#techListProjeto .remove-tech").forEach((btn) => {
    btn.style.display = "none";
});