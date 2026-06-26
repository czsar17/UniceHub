/* ==========================================================
   ABAS — perfil e projeto
========================================================== */
document.querySelectorAll(".profile-tabs .tab-btn").forEach((tab) => {
    tab.addEventListener("click", () => {
        document.querySelectorAll(".profile-tabs .tab-btn").forEach(b => b.classList.remove("active"));
        document.querySelectorAll(".tab-content").forEach(c => c.classList.remove("active"));
        tab.classList.add("active");
        const target = document.getElementById(tab.dataset.tab);
        if (target) target.classList.add("active");
    });
});

/* ── Botão de atalho para ir à aba de comentários ── */
const btnIrComentarios = document.getElementById("btnIrComentarios");
if (btnIrComentarios) {
    btnIrComentarios.addEventListener("click", () => {
        const tabBtn = document.querySelector('.tab-btn[data-tab="comentarios"]');
        if (tabBtn) tabBtn.click();
        document.getElementById("comentarios")?.scrollIntoView({ behavior: "smooth" });
    });
}

/* ── Contador de caracteres no textarea de comentário ── */
document.querySelectorAll(".comentario-textarea").forEach(ta => {
    const footer = ta.closest(".comentario-form")?.querySelector(".comentario-counter");
    if (!footer) return;
    ta.addEventListener("input", () => {
        footer.textContent = `${ta.value.length}/500`;
    });
});

/* ==========================================================
   EDIÇÃO DE PERFIL
========================================================== */
const formPerfil    = document.getElementById("formPerfil");
const btnEditar     = document.getElementById("btnEditar");
const btnCancelar   = document.getElementById("btnCancelar");
const campos        = document.querySelectorAll(".campo-edicao");
const sobreMim      = document.getElementById("sobre_mim");
const sobreContador = document.getElementById("sobreContador");
const techInput     = document.getElementById("techInput");
const btnAddTech    = document.getElementById("btnAddTech");
const techList      = document.getElementById("techList");
const techCounter   = document.getElementById("techCounter");
const fotoInput     = document.getElementById("foto");
const fotosPerfil   = document.querySelectorAll(".profile-pic");

let editandoPerfil = false;
let previewFotoUrl = null;

function atualizarContadorSobre() {
    if (sobreMim && sobreContador) sobreContador.textContent = sobreMim.value.length;
}
function getTecnologiasPerfil() {
    if (!techList) return [];
    return Array.from(techList.querySelectorAll(".tech-tag")).map(t => t.dataset.value.trim());
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
    tag.className = "tech-tag"; tag.dataset.value = valor;
    const texto = document.createElement("span"); texto.textContent = `#${valor}`;
    const rm = document.createElement("button");
    rm.type = "button"; rm.className = "remove-tech";
    rm.setAttribute("aria-label", `Remover ${valor}`);
    rm.innerHTML = '<i class="fa-solid fa-xmark"></i>';
    const inp = document.createElement("input");
    inp.type = "hidden"; inp.name = "tecnologias[]"; inp.value = valor;
    tag.append(texto, rm, inp);
    techList.appendChild(tag);
}
function adicionarTecnologiaPerfil() {
    if (!techInput) return;
    const valor = techInput.value.trim().replace(/^#/, "");
    if (!valor) return;
    const techs = getTecnologiasPerfil();
    if (techs.length >= 8 || techs.some(t => t.toLowerCase() === valor.toLowerCase())) {
        techInput.value = ""; return;
    }
    criarTagTecnologiaPerfil(valor);
    techInput.value = "";
    atualizarContadorTechs();
}
function alternarEdicaoPerfil(ativo) {
    editandoPerfil = ativo;
    document.body.classList.toggle("perfil-editando", ativo);
    campos.forEach(c => { c.disabled = !ativo; });
    if (btnCancelar) btnCancelar.hidden = !ativo;
    if (btnEditar) {
        btnEditar.innerHTML = ativo
            ? '<i class="fa-solid fa-floppy-disk"></i> Salvar'
            : '<i class="fa-solid fa-pen"></i> Editar Perfil';
    }
    atualizarContadorTechs();
}
if (techList) {
    techList.addEventListener("click", e => {
        const rm = e.target.closest(".remove-tech");
        if (!rm || !editandoPerfil) return;
        rm.closest(".tech-tag").remove();
        atualizarContadorTechs();
    });
}
if (btnAddTech) btnAddTech.addEventListener("click", adicionarTecnologiaPerfil);
if (techInput) {
    techInput.addEventListener("keydown", e => {
        if (e.key === "Enter") { e.preventDefault(); adicionarTecnologiaPerfil(); }
    });
}
if (sobreMim) sobreMim.addEventListener("input", atualizarContadorSobre);
if (fotoInput) {
    fotoInput.addEventListener("change", () => {
        const f = fotoInput.files[0];
        if (!f || !f.type.startsWith("image/")) return;
        if (previewFotoUrl) URL.revokeObjectURL(previewFotoUrl);
        previewFotoUrl = URL.createObjectURL(f);
        fotosPerfil.forEach(el => { el.src = previewFotoUrl; });
    });
}
if (btnEditar) {
    btnEditar.addEventListener("click", e => {
        e.preventDefault();
        if (!editandoPerfil) { alternarEdicaoPerfil(true); return; }
        adicionarTecnologiaPerfil();
        formPerfil.requestSubmit();
    });
}
if (btnCancelar) btnCancelar.addEventListener("click", () => window.location.reload());
if (formPerfil && btnEditar) formPerfil.addEventListener("submit", () => adicionarTecnologiaPerfil());
atualizarContadorSobre();
atualizarContadorTechs();


/* ==========================================================
   EDIÇÃO DE PROJETO
========================================================== */
const formProjeto        = document.getElementById("formProjeto");
const btnEditarProjeto   = document.getElementById("btnEditarProjeto");
const btnCancelarProjeto = document.getElementById("btnCancelarProjeto");
const camposProjeto      = document.querySelectorAll(".campo-projeto");

const capaInput          = document.getElementById("capa");
const capaPreview        = document.getElementById("capaPreview");
const descricaoPreview   = document.getElementById("descricaoPreview");
const descricaoEditor    = document.getElementById("projetoDescricao");
const repoLinkWrap       = document.getElementById("repoLinkWrap");
const repoInput          = document.getElementById("repoInput");
const techInputProjeto   = document.getElementById("techInputProjeto");
const btnAddTechProjeto  = document.getElementById("btnAddTechProjeto");
const techListProjeto    = document.getElementById("techListProjeto");
const techCounterProjeto = document.getElementById("techCounterProjeto");
const techCountLabel     = document.getElementById("techCountLabel");
const techEditorProjeto  = document.getElementById("techEditorProjeto");

let editandoProjeto = false;
let previewCapaUrl  = null;

function getTecnologiasProjeto() {
    if (!techListProjeto) return [];
    return Array.from(techListProjeto.querySelectorAll(".tech-tag")).map(t => t.dataset.value.trim());
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
    const placeholder = document.getElementById("semTecnologias");
    if (placeholder) placeholder.remove();
    const tag = document.createElement("span");
    tag.className = "tech-tag"; tag.dataset.value = valor;
    const texto = document.createElement("span"); texto.textContent = `#${valor}`;
    const rm = document.createElement("button");
    rm.type = "button"; rm.className = "remove-tech";
    rm.setAttribute("aria-label", `Remover ${valor}`);
    rm.innerHTML = '<i class="fa-solid fa-xmark"></i>';
    if (editandoProjeto) rm.style.display = "inline-flex";
    const inp = document.createElement("input");
    inp.type = "hidden"; inp.name = "tecnologias[]"; inp.value = valor;
    tag.append(texto, rm, inp);
    techListProjeto.appendChild(tag);
}
function adicionarTecnologiaProjeto() {
    if (!techInputProjeto) return;
    const valor = techInputProjeto.value.trim().replace(/^#/, "");
    if (!valor) return;
    const techs = getTecnologiasProjeto();
    if (techs.length >= 8 || techs.some(t => t.toLowerCase() === valor.toLowerCase())) {
        techInputProjeto.value = ""; return;
    }
    criarTagTecnologiaProjeto(valor);
    techInputProjeto.value = "";
    atualizarContadorTechsProjeto();
}
function alternarEdicaoProjeto(ativo) {
    editandoProjeto = ativo;
    camposProjeto.forEach(c => { c.disabled = !ativo; });
    if (btnCancelarProjeto) btnCancelarProjeto.hidden = !ativo;
    if (btnEditarProjeto) {
        btnEditarProjeto.innerHTML = ativo
            ? '<i class="fa-solid fa-floppy-disk"></i> Salvar alterações'
            : '<i class="fa-solid fa-pen"></i> Editar Projeto';
    }
    // descrição: preview ↔ editor
    if (descricaoPreview) descricaoPreview.style.display = ativo ? "none"  : "";
    if (descricaoEditor)  descricaoEditor.style.display  = ativo ? "block" : "none";
    // repo: link ↔ input
    if (repoLinkWrap) repoLinkWrap.style.display = ativo ? "none"  : "";
    if (repoInput)    repoInput.style.display     = ativo ? "block" : "none";
    // editor de techs
    if (techEditorProjeto) techEditorProjeto.style.display = ativo ? "grid" : "none";
    // botões remove
    document.querySelectorAll("#techListProjeto .remove-tech").forEach(btn => {
        btn.style.display = ativo ? "inline-flex" : "none";
    });
    // ícone câmera
    document.body.classList.toggle("projeto-editando", ativo);
    atualizarContadorTechsProjeto();
}
if (capaInput) {
    capaInput.addEventListener("change", () => {
        const f = capaInput.files[0];
        if (!f || !f.type.startsWith("image/")) return;
        if (previewCapaUrl) URL.revokeObjectURL(previewCapaUrl);
        previewCapaUrl = URL.createObjectURL(f);
        if (capaPreview) capaPreview.src = previewCapaUrl;
    });
}
if (techListProjeto) {
    techListProjeto.addEventListener("click", e => {
        const rm = e.target.closest(".remove-tech");
        if (!rm || !editandoProjeto) return;
        rm.closest(".tech-tag").remove();
        atualizarContadorTechsProjeto();
    });
}
if (btnAddTechProjeto) btnAddTechProjeto.addEventListener("click", adicionarTecnologiaProjeto);
if (techInputProjeto) {
    techInputProjeto.addEventListener("keydown", e => {
        if (e.key === "Enter") { e.preventDefault(); adicionarTecnologiaProjeto(); }
    });
}
if (btnEditarProjeto) {
    btnEditarProjeto.addEventListener("click", e => {
        e.preventDefault();
        if (!editandoProjeto) { alternarEdicaoProjeto(true); return; }
        adicionarTecnologiaProjeto();
        formProjeto.requestSubmit();
    });
}
if (btnCancelarProjeto) btnCancelarProjeto.addEventListener("click", () => window.location.reload());

// init
atualizarContadorTechsProjeto();
document.querySelectorAll("#techListProjeto .remove-tech").forEach(btn => {
    btn.style.display = "none";
});


/* ==========================================================
   MODAL DE CONVITE
========================================================== */
const modalConvite  = document.getElementById("modalConvite");
const btnConvidar   = document.getElementById("btnConvidar");
const fecharModal   = document.getElementById("fecharModal");
const cancelarModal = document.getElementById("cancelarModal");
const buscaConvite  = document.getElementById("buscaConvite");
const listaConvite  = document.getElementById("listaConvite");
const modalNenhum   = document.getElementById("modalNenhum");

function abrirModal() {
    if (!modalConvite) return;
    modalConvite.hidden = false;
    if (buscaConvite) {
        buscaConvite.value = "";
        filtrarLista("");
        setTimeout(() => buscaConvite.focus(), 50);
    }
}
function fecharModalFn() {
    if (modalConvite) modalConvite.hidden = true;
}
function filtrarLista(termo) {
    if (!listaConvite) return;
    const rows = listaConvite.querySelectorAll(".modal-user-row");
    let visiveis = 0;
    rows.forEach(row => {
        const match = (row.dataset.busca ?? "").includes(termo.toLowerCase());
        row.style.display = match ? "" : "none";
        if (match) visiveis++;
    });
    if (modalNenhum) {
        modalNenhum.style.display = (visiveis === 0 && termo !== "") ? "block" : "none";
    }
}

if (btnConvidar)   btnConvidar.addEventListener("click",   abrirModal);
if (fecharModal)   fecharModal.addEventListener("click",   fecharModalFn);
if (cancelarModal) cancelarModal.addEventListener("click", fecharModalFn);
if (modalConvite) {
    modalConvite.addEventListener("click", e => {
        if (e.target === modalConvite) fecharModalFn();
    });
}
document.addEventListener("keydown", e => {
    if (e.key === "Escape" && modalConvite && !modalConvite.hidden) fecharModalFn();
});
if (buscaConvite) {
    buscaConvite.addEventListener("input", () => filtrarLista(buscaConvite.value.trim()));
}