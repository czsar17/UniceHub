const nomeInput = document.getElementById('nome');
const descricaoInput = document.getElementById('descricao');
const categoriaSelect = document.getElementById('categoria');
const statusSelect = document.getElementById('status');
const techInput = document.getElementById('techInput');
const techTags = document.getElementById('techTags');
const techInputsContainer = document.getElementById('techInputsContainer');
const repoInput = document.getElementById('repo_url');
const capaInput = document.getElementById('capa');
const previewNome = document.getElementById('previewNome');
const previewMarkdown = document.getElementById('previewMarkdown');
const previewTecnologias = document.getElementById('previewTecnologias');
const previewCapa = document.getElementById('previewCapa');
const previewRepoLink = document.getElementById('previewRepoLink');
const summaryStatus = document.getElementById('summaryStatus');
const summaryTecnologia = document.getElementById('summaryTecnologia');
const membersCount = document.getElementById('membersCount');
const membersSearch = document.querySelector('.members-search');
const memberCards = document.querySelectorAll('.member-card');

function escaparHtml(texto) {
    return texto
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#39;');
}

function formatarMarkdownSimples(texto) {
    const linhas = texto.split(/\r?\n/);
    const html = [];
    let listaAberta = false;

    const formatarLinha = (linha) => {
        let linhaFormatada = escaparHtml(linha);
        linhaFormatada = linhaFormatada
            .replace(/`([^`]+)`/g, '<code>$1</code>')
            .replace(/\*\*([^*]+)\*\*/g, '<strong>$1</strong>')
            .replace(/\*([^*]+)\*/g, '<em>$1</em>')
            .replace(/\[([^\]]+)\]\(([^)]+)\)/g, '<a href="$2" target="_blank" rel="noopener noreferrer">$1</a>');
        return linhaFormatada;
    };

    for (let i = 0; i < linhas.length; i++) {
        const linha = linhas[i].trimEnd();
        const linhaTrim = linha.trim();

        if (!linhaTrim) {
            if (listaAberta) {
                html.push('</ul>');
                listaAberta = false;
            }
            continue;
        }

        if (/^#{1,6}\s+/.test(linhaTrim)) {
            if (listaAberta) {
                html.push('</ul>');
                listaAberta = false;
            }
            const nivel = linhaTrim.match(/^#+/)[0].length;
            const conteudo = linhaTrim.replace(/^#{1,6}\s+/, '');
            html.push(`<h${nivel}>${formatarLinha(conteudo)}</h${nivel}>`);
        } else if (/^[-*]\s+/.test(linhaTrim)) {
            if (!listaAberta) {
                html.push('<ul>');
                listaAberta = true;
            }
            html.push(`<li>${formatarLinha(linhaTrim.replace(/^[-*]\s+/, ''))}</li>`);
        } else if (/^>\s+/.test(linhaTrim)) {
            if (listaAberta) {
                html.push('</ul>');
                listaAberta = false;
            }
            html.push(`<blockquote>${formatarLinha(linhaTrim.replace(/^>\s+/, ''))}</blockquote>`);
        } else if (/^```/.test(linhaTrim)) {
            if (listaAberta) {
                html.push('</ul>');
                listaAberta = false;
            }
            let bloco = '';
            i++;
            while (i < linhas.length && !/^```/.test(linhas[i].trim())) {
                bloco += escaparHtml(linhas[i]) + '\n';
                i++;
            }
            html.push(`<pre><code>${bloco.trim()}</code></pre>`);
        } else if (/^\d+\.\s+/.test(linhaTrim)) {
            if (listaAberta) {
                html.push('</ul>');
                listaAberta = false;
            }
            html.push(`<p>${formatarLinha(linhaTrim.replace(/^\d+\.\s+/, ''))}</p>`);
        } else {
            if (listaAberta) {
                html.push('</ul>');
                listaAberta = false;
            }
            html.push(`<p>${formatarLinha(linhaTrim)}</p>`);
        }
    }

    if (listaAberta) {
        html.push('</ul>');
    }

    return html.join('');
}

function obterTecnologias() {
    return Array.from(techTags.querySelectorAll('.tech-tag'))
        .map((tag) => tag.dataset.value)
        .filter(Boolean);
}

function atualizarInputsTecnologias() {
    techInputsContainer.innerHTML = '';

    obterTecnologias().forEach((tecnologia) => {
        const input = document.createElement('input');
        input.type = 'hidden';
        input.name = 'tecnologias[]';
        input.value = tecnologia;
        techInputsContainer.appendChild(input);
    });
}

function adicionarTech(valor) {
    const texto = valor.trim().replace(/^#/, '');
    if (!texto) return;

    const tecnologias = obterTecnologias();
    if (tecnologias.includes(texto)) return;

    const tag = document.createElement('span');
    tag.className = 'tech-tag';
    tag.dataset.value = texto;
    tag.innerHTML = `#${texto} <button type="button" class="tech-remove">×</button>`;

    techTags.appendChild(tag);
    atualizarInputsTecnologias();
    atualizarPreview();
}

function atualizarPreview() {
    const nome = nomeInput.value.trim() || 'Nome do projeto';
    const descricao = descricaoInput.value.trim() || 'Descrição do projeto...';
    const tecnologias = obterTecnologias();

    previewNome.textContent = nome;
    previewMarkdown.innerHTML = formatarMarkdownSimples(descricao);
    summaryStatus.textContent = statusSelect.value;

    const tecnologia = tecnologias[0] || '-';
    summaryTecnologia.textContent = tecnologia;

    previewTecnologias.innerHTML = '';
    tecnologias.forEach((tag) => {
        const span = document.createElement('span');
        span.textContent = tag;
        previewTecnologias.appendChild(span);
    });

    const repo = repoInput.value.trim();
    if (repo) {
        previewRepoLink.href = repo;
        previewRepoLink.textContent = repo;
        previewRepoLink.hidden = false;
    } else {
        previewRepoLink.hidden = true;
        previewRepoLink.removeAttribute('href');
        previewRepoLink.textContent = '';
    }
}

function atualizarContadorMembros() {
    if (!membersCount) return;

    const selecionados = document.querySelectorAll('.member-checkbox:checked').length;
    membersCount.textContent = `${selecionados} ${selecionados === 1 ? 'selecionado' : 'selecionados'}`;
}

function filtrarMembros() {
    if (!membersSearch) return;

    const termo = membersSearch.value.trim().toLowerCase();

    memberCards.forEach((card) => {
        const nome = card.dataset.memberName || '';
        const email = card.dataset.memberEmail || '';
        const corresponde = !termo || nome.includes(termo) || email.includes(termo);
        card.style.display = corresponde ? '' : 'none';
    });
}

if (capaInput) {
    capaInput.addEventListener('change', () => {
        const file = capaInput.files[0];
        if (file && file.type.startsWith('image/')) {
            previewCapa.src = URL.createObjectURL(file);
        }
    });
}

if (techInput) {
    techInput.addEventListener('keydown', (event) => {
        if (event.key === 'Enter') {
            event.preventDefault();
            adicionarTech(techInput.value);
            techInput.value = '';
        }
    });
}

if (techTags) {
    techTags.addEventListener('click', (event) => {
        if (event.target.classList.contains('tech-remove')) {
            event.target.closest('.tech-tag').remove();
            atualizarInputsTecnologias();
            atualizarPreview();
        }
    });
}

if (nomeInput && descricaoInput && categoriaSelect && statusSelect && repoInput) {
    [nomeInput, descricaoInput, categoriaSelect, statusSelect, repoInput].forEach((element) => {
        element.addEventListener('input', atualizarPreview);
        element.addEventListener('change', atualizarPreview);
    });
}

if (membersSearch) {
    membersSearch.addEventListener('input', filtrarMembros);
}

document.querySelectorAll('.member-checkbox').forEach((checkbox) => {
    checkbox.addEventListener('change', atualizarContadorMembros);
});

atualizarInputsTecnologias();
atualizarPreview();
atualizarContadorMembros();
