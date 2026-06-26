// ================================================================
// config.js — Configurações UniceHub (refatorado)
// ================================================================

const content    = document.getElementById('configContent');
const menuItems  = document.querySelectorAll('.menu-item');
const breadcrumb = document.getElementById('configBreadcrumb');
const user       = window.cfgUser;
const admin      = window.cfgAdmin || { isAdmin: false };

// ── Navegação ──────────────────────────────────────────────────
menuItems.forEach(item => {
    item.addEventListener('click', () => {
        menuItems.forEach(b => b.classList.remove('active'));
        item.classList.add('active');
        if (breadcrumb && item.dataset.label) breadcrumb.textContent = item.dataset.label;
        renderPage(item.dataset.page);
    });
});

// ── Helpers ────────────────────────────────────────────────────
function flash(msg, type = 'success') {
    const box = document.getElementById('cfgFlash');
    if (!box) return;
    box.className = `cfg-flash ${type}`;
    box.innerHTML = `<i class="fa-solid ${type === 'success' ? 'fa-circle-check' : 'fa-circle-exclamation'}"></i> ${msg}`;
    clearTimeout(box._timer);
    box._timer = setTimeout(() => box.className = 'cfg-flash', 4000);
}

function makeToggle(id, checked = true) {
    return `<label class="cfg-toggle" aria-label="Ativar/desativar">
        <input type="checkbox" id="${id}" ${checked ? 'checked' : ''}>
        <span class="cfg-slider"></span>
    </label>`;
}

function secIcon(icon) {
    return `<span class="sec-icon"><i class="fa-solid ${icon}"></i></span>`;
}

function bindCharCounters() {
    document.querySelectorAll('input[maxlength], textarea[maxlength]').forEach(el => {
        const counter = el.parentElement.querySelector('.char-count');
        if (!counter) return;
        const update = () => counter.textContent = `${el.value.length}/${el.maxLength}`;
        update();
        el.addEventListener('input', update);
    });
}

function formatCpf(digits) {
    return digits.replace(/(\d{3})(\d{3})(\d{3})(\d{2})/, '$1.$2.$3-$4');
}

function escapeHtml(value) {
    return String(value ?? '')
        .replaceAll('&', '&amp;')
        .replaceAll('<', '&lt;')
        .replaceAll('>', '&gt;')
        .replaceAll('"', '&quot;')
        .replaceAll("'", '&#039;');
}

// Envia PATCH/POST via fetch preservando CSRF
async function postForm(url, data) {
    const res = await fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': user.csrfToken,
            'Accept': 'application/json',
            'Content-Type': 'application/json',
        },
        body: JSON.stringify(data),
    });
    return res;
}

async function postMultipart(url, formData) {
    return fetch(url, {
        method: 'POST',
        headers: {
            'X-CSRF-TOKEN': user.csrfToken,
            'Accept': 'application/json',
        },
        body: formData,
    });
}

function refreshThemeCss() {
    const link = document.querySelector('link[href*="theme.css"]');
    if (!link || !admin.themeCssUrl) return;
    link.href = `${admin.themeCssUrl}?v=${Date.now()}`;
}

// ================================================================
// PÁGINAS
// ================================================================

async function renderPage(page) {

    // ── INFORMAÇÕES PESSOAIS ────────────────────────────────────
    if (page === 'perfil' || !page) {
        content.innerHTML = `
        <div id="cfgFlash" class="cfg-flash"></div>

        <div class="cfg-section-header">
            <div>
                <h2><i class="fa-regular fa-user"></i> Informações pessoais</h2>
                <p>Dados visíveis na sua conta. Para editar nome e bio, acesse seu <a href="/perfil">Perfil</a>.</p>
            </div>
            <button class="cfg-btn-primary" id="saveBtn">
                <i class="fa-solid fa-check"></i> Salvar alterações
            </button>
        </div>

        <div class="cfg-personal-layout">
            <div class="cfg-fields">

                <div class="cfg-field-row">
                    <!-- Nome (somente leitura) -->
                    <div class="cfg-field">
                        <label class="cfg-label"><i class="fa-regular fa-id-card"></i> Nome completo</label>
                        <div class="cfg-readonly-field">
                            <span>${user.name}</span>
                            <span class="cfg-readonly-badge"><i class="fa-solid fa-lock"></i> Altere no Perfil</span>
                        </div>
                    </div>

                    <!-- E-mail -->
                    <div class="cfg-field">
                        <label class="cfg-label" for="cfgEmail"><i class="fa-regular fa-envelope"></i> E-mail</label>
                        <input class="cfg-input" type="email" id="cfgEmail"
                            maxlength="100" value="${user.email}" placeholder="seu@email.com">
                        <span class="char-count"></span>
                    </div>
                </div>

                <div class="cfg-field-row">
                    <!-- CPF (visualizar) -->
                    <div class="cfg-field">
                        <label class="cfg-label"><i class="fa-regular fa-address-card"></i> CPF</label>
                        <div class="cfg-cpf-wrap">
                            <input class="cfg-input" type="password" id="cfgCpf"
                                value="${user.cpf}" readonly autocomplete="off">
                            <button class="cfg-eye-btn" id="toggleCpfBtn" type="button">
                                <i class="fa-regular fa-eye" id="cpfEyeIcon"></i>
                            </button>
                        </div>
                        <span class="cfg-field-hint">Somente para visualização</span>
                    </div>

                    <!-- Data de nascimento -->
                    <div class="cfg-field">
                        <label class="cfg-label" for="cfgNasc"><i class="fa-regular fa-calendar"></i> Data de nascimento</label>
                        <input class="cfg-input" type="date" id="cfgNasc"
                            value="${user.data_nascimento ?? ''}">
                    </div>
                </div>

                <div class="cfg-field-row">
                    <!-- Telefone -->
                    <div class="cfg-field">
                        <label class="cfg-label" for="cfgTelefone"><i class="fa-solid fa-phone"></i> Telefone</label>
                        <input class="cfg-input" type="text" id="cfgTelefone"
                            maxlength="20" value="${user.telefone ?? ''}" placeholder="(44) 99999-9999">
                    </div>

                    <!-- Curso -->
                    <div class="cfg-field">
                        <label class="cfg-label" for="cfgCurso"><i class="fa-solid fa-graduation-cap"></i> Curso</label>
                        <select class="cfg-select" id="cfgCurso">
                            <option value="">Selecione...</option>
                            <option value="ADS" ${user.curso === 'ADS' ? 'selected' : ''}>ADS</option>
                            <option value="Análise e Desenvolvimento de Sistemas" ${user.curso === 'Análise e Desenvolvimento de Sistemas' ? 'selected' : ''}>Análise e Desenvolvimento de Sistemas</option>
                            <option value="Engenharia de Software" ${user.curso === 'Engenharia de Software' ? 'selected' : ''}>Engenharia de Software</option>
                            <option value="Ciência da Computação" ${user.curso === 'Ciência da Computação' ? 'selected' : ''}>Ciência da Computação</option>
                        </select>
                    </div>
                </div>

            </div>
        </div>`;

        // Toggle CPF
        document.getElementById('toggleCpfBtn').addEventListener('click', () => {
            const field = document.getElementById('cfgCpf');
            const icon  = document.getElementById('cpfEyeIcon');
            if (field.type === 'password') {
                field.type  = 'text';
                field.value = formatCpf(field.value.replace(/\D/g, ''));
                icon.className = 'fa-regular fa-eye-slash';
            } else {
                field.type  = 'password';
                field.value = field.value.replace(/\D/g, '');
                icon.className = 'fa-regular fa-eye';
            }
        });

        // Salvar
        document.getElementById('saveBtn').addEventListener('click', async () => {
            const btn = document.getElementById('saveBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';

            try {
                const res = await postForm(user.updateUrl, {
                    name:           user.name,      // obrigatório pelo controller
                    email:          document.getElementById('cfgEmail').value,
                    telefone:       document.getElementById('cfgTelefone').value,
                    curso:          document.getElementById('cfgCurso').value,
                    data_nascimento:document.getElementById('cfgNasc').value,
                    sobre_mim:      user.sobre_mim ?? '',
                    interesses_markdown: user.interesses_markdown ?? '',
                    tecnologias:    user.tecnologias ?? [],
                });

                if (res.ok) {
                    flash('Informações salvas com sucesso!', 'success');
                    user.email = document.getElementById('cfgEmail').value;
                    user.telefone = document.getElementById('cfgTelefone').value;
                    user.curso = document.getElementById('cfgCurso').value;
                    user.data_nascimento = document.getElementById('cfgNasc').value;
                } else {
                    const data = await res.json().catch(() => ({}));
                    const msgs = data.errors ? Object.values(data.errors).flat().join(' — ') : 'Erro ao salvar. Tente novamente.';
                    flash(msgs, 'error');
                }
            } catch (e) {
                flash('Erro de conexão. Verifique sua internet.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Salvar alterações';
            }
        });

        bindCharCounters();
    }

    // ── SEGURANÇA ───────────────────────────────────────────────
    else if (page === 'seguranca') {
        content.innerHTML = `
        <div id="cfgFlash" class="cfg-flash"></div>

        <div class="cfg-section-header">
            <div>
                <h2><i class="fa-solid fa-shield-halved"></i> Segurança da conta</h2>
                <p>Gerencie sua senha e proteja seus dados.</p>
            </div>
        </div>

        <div class="cfg-security-list">

            <!-- Alterar senha -->
            <div class="cfg-security-item">
                <div class="cfg-security-left">
                    ${secIcon('fa-key')}
                    <div>
                        <strong>Senha</strong>
                        <span>Altere sua senha de acesso.</span>
                    </div>
                </div>
                <button class="cfg-btn-primary" id="openPwForm">
                    <i class="fa-solid fa-pen"></i> Alterar senha
                </button>
            </div>

            <!-- Form inline -->
            <div class="cfg-inline-form" id="pwChangeForm">
                <div class="cfg-pw-fields">
                    <div class="cfg-field">
                        <label class="cfg-label" for="pwAtual">Senha atual</label>
                        <div class="cfg-cpf-wrap">
                            <input class="cfg-input" type="password" id="pwAtual" placeholder="••••••••" autocomplete="current-password">
                            <button class="cfg-eye-btn" data-target="pwAtual" type="button"><i class="fa-regular fa-eye"></i></button>
                        </div>
                    </div>
                    <div class="cfg-field">
                        <label class="cfg-label" for="pwNova">Nova senha</label>
                        <div class="cfg-cpf-wrap">
                            <input class="cfg-input" type="password" id="pwNova" placeholder="••••••••" autocomplete="new-password">
                            <button class="cfg-eye-btn" data-target="pwNova" type="button"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        <div class="cfg-pw-rules" id="pwRules">
                            <span data-rule="len"><i class="fa-solid fa-circle-xmark"></i> Mínimo 8 caracteres</span>
                            <span data-rule="upper"><i class="fa-solid fa-circle-xmark"></i> Uma letra maiúscula</span>
                            <span data-rule="num"><i class="fa-solid fa-circle-xmark"></i> Um número</span>
                        </div>
                    </div>
                    <div class="cfg-field">
                        <label class="cfg-label" for="pwConfirm">Confirmar nova senha</label>
                        <div class="cfg-cpf-wrap">
                            <input class="cfg-input" type="password" id="pwConfirm" placeholder="••••••••" autocomplete="new-password">
                            <button class="cfg-eye-btn" data-target="pwConfirm" type="button"><i class="fa-regular fa-eye"></i></button>
                        </div>
                        <span class="cfg-match-hint" id="matchHint"></span>
                    </div>
                </div>
                <div class="cfg-pw-actions">
                    <button class="cfg-btn-ghost" id="cancelPwForm">Cancelar</button>
                    <button class="cfg-btn-primary" id="savePwBtn">
                        <i class="fa-solid fa-check"></i> Salvar nova senha
                    </button>
                </div>
                <a href="/esqueci-senha" class="cfg-forgot-link">
                    <i class="fa-regular fa-circle-question"></i> Esqueci minha senha
                </a>
            </div>

            <!-- CPF -->
            <div class="cfg-security-item">
                <div class="cfg-security-left">
                    ${secIcon('fa-address-card')}
                    <div>
                        <strong>CPF</strong>
                        <span>Visualize seu CPF cadastrado.</span>
                    </div>
                </div>
                <div class="cfg-cpf-wrap">
                    <input class="cfg-input cfg-cpf-field" type="password" id="cpfField"
                        value="${user.cpf}" readonly autocomplete="off">
                    <button class="cfg-eye-btn" id="toggleCpfSec" type="button">
                        <i class="fa-regular fa-eye" id="cpfIconSec"></i>
                    </button>
                </div>
            </div>

        </div>`;

        // Abrir/fechar form
        document.getElementById('openPwForm').addEventListener('click', () => {
            document.getElementById('pwChangeForm').classList.toggle('open');
        });
        document.getElementById('cancelPwForm').addEventListener('click', () => {
            document.getElementById('pwChangeForm').classList.remove('open');
        });

        // Regras de senha
        document.getElementById('pwNova').addEventListener('input', function() {
            const v = this.value;
            const check = (rule, ok) => {
                const el = document.querySelector(`[data-rule="${rule}"]`);
                if (!el) return;
                el.classList.toggle('ok', ok);
                el.querySelector('i').className = ok ? 'fa-solid fa-circle-check' : 'fa-solid fa-circle-xmark';
            };
            check('len',   v.length >= 8);
            check('upper', /[A-Z]/.test(v));
            check('num',   /\d/.test(v));
        });

        // Confirmação
        document.getElementById('pwConfirm').addEventListener('input', function() {
            const nova = document.getElementById('pwNova').value;
            const hint = document.getElementById('matchHint');
            if (!this.value) { hint.textContent = ''; return; }
            if (this.value === nova) {
                hint.textContent = '✓ Senhas coincidem';
                hint.className = 'cfg-match-hint ok';
            } else {
                hint.textContent = '✗ Senhas não coincidem';
                hint.className = 'cfg-match-hint err';
            }
        });

        // Salvar senha
        document.getElementById('savePwBtn').addEventListener('click', async () => {
            const atual    = document.getElementById('pwAtual').value;
            const nova     = document.getElementById('pwNova').value;
            const confirma = document.getElementById('pwConfirm').value;

            if (!atual || !nova || !confirma) { flash('Preencha todos os campos de senha.', 'error'); return; }
            if (nova !== confirma)             { flash('As senhas não coincidem.', 'error'); return; }
            if (nova.length < 8)               { flash('A nova senha deve ter pelo menos 8 caracteres.', 'error'); return; }

            const btn = document.getElementById('savePwBtn');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';

            try {
                const res = await postForm(user.passwordUrl, {
                    current_password:      atual,
                    password:              nova,
                    password_confirmation: confirma,
                });

                if (res.ok) {
                    flash('Senha alterada com sucesso!', 'success');
                    document.getElementById('pwChangeForm').classList.remove('open');
                    ['pwAtual','pwNova','pwConfirm'].forEach(id => document.getElementById(id).value = '');
                } else {
                    const data = await res.json().catch(() => ({}));
                    const msgs = data.errors ? Object.values(data.errors).flat().join(' — ') : (data.message || 'Erro ao alterar senha.');
                    flash(msgs, 'error');
                }
            } catch(e) {
                flash('Erro de conexão.', 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Salvar nova senha';
            }
        });

        // Toggle eyes
        document.querySelectorAll('.cfg-eye-btn[data-target]').forEach(btn => {
            btn.addEventListener('click', () => {
                const field = document.getElementById(btn.dataset.target);
                const icon  = btn.querySelector('i');
                field.type  = field.type === 'password' ? 'text' : 'password';
                icon.className = field.type === 'password' ? 'fa-regular fa-eye' : 'fa-regular fa-eye-slash';
            });
        });

        // Toggle CPF
        document.getElementById('toggleCpfSec').addEventListener('click', () => {
            const f = document.getElementById('cpfField');
            const i = document.getElementById('cpfIconSec');
            if (f.type === 'password') {
                f.type  = 'text';
                f.value = formatCpf(f.value.replace(/\D/g,''));
                i.className = 'fa-regular fa-eye-slash';
            } else {
                f.type  = 'password';
                f.value = f.value.replace(/\D/g,'');
                i.className = 'fa-regular fa-eye';
            }
        });
    }

    // ── NOTIFICAÇÕES ────────────────────────────────────────────
    else if (page === 'notificacoes') {
        const grupos = [
            {
                icon: 'fa-folder-open', titulo: 'Projetos',
                itens: [
                    { id: 'n_proj_update',  label: 'Atualizações de projeto',   desc: 'Novos membros, mudanças de status e edições.',  on: true },
                    { id: 'n_proj_invite',  label: 'Convite para projeto',       desc: 'Quando alguém te convidar para colaborar.',     on: true },
                    { id: 'n_proj_comment', label: 'Comentários em projetos',    desc: 'Novos comentários nos seus projetos.',          on: true },
                ],
            },
            {
                icon: 'fa-user-group', titulo: 'Conexões',
                itens: [
                    { id: 'n_conn_req',    label: 'Solicitação de conexão', desc: 'Quando alguém quiser se conectar com você.', on: true },
                    { id: 'n_conn_accept', label: 'Conexão aceita',         desc: 'Quando sua solicitação for aceita.',         on: true },
                ],
            },
            {
                icon: 'fa-at', titulo: 'Interações',
                itens: [
                    { id: 'n_like', label: 'Curtidas', desc: 'Quando seu projeto receber uma curtida.', on: false },
                ],
            },
        ];

        content.innerHTML = `
        <div class="cfg-section-header">
            <div>
                <h2><i class="fa-regular fa-bell"></i> Notificações</h2>
                <p>Escolha o que você quer receber e como quer ser avisado.</p>
            </div>
        </div>
        <div class="cfg-notif-groups">
            ${grupos.map(g => `
            <div class="cfg-notif-group">
                <div class="cfg-notif-group-header"><i class="fa-solid ${g.icon}"></i> ${g.titulo}</div>
                ${g.itens.map(item => `
                <div class="cfg-notif-item">
                    <div class="cfg-notif-info">
                        <strong>${item.label}</strong>
                        <span>${item.desc}</span>
                    </div>
                    ${makeToggle(item.id, item.on)}
                </div>`).join('')}
            </div>`).join('')}
        </div>`;
    }

    // ── TIPO DE PERFIL ──────────────────────────────────────────
    else if (page === 'tipoPerfil') {
        const isAluno = user.tipo === 'aluno';
        content.innerHTML = `
        <div class="cfg-section-header">
            <div>
                <h2><i class="fa-solid fa-user-tag"></i> Tipo de perfil</h2>
                <p>Escolha como você acessa a plataforma.</p>
            </div>
        </div>
        <div class="cfg-profile-types">
            <div class="cfg-profile-type ${isAluno ? 'active-type' : ''}">
                <div class="cfg-type-icon"><i class="fa-solid fa-graduation-cap"></i></div>
                <div>
                    <strong>Aluno</strong>
                    <span>Colabore em projetos, crie portfólio e conecte-se com colegas.</span>
                </div>
                <button class="cfg-btn-primary" ${isAluno ? 'disabled' : ''} data-tipo="aluno">
                    ${isAluno ? 'Ativo' : 'Trocar para aluno'}
                </button>
            </div>
            <div class="cfg-profile-type ${!isAluno ? 'active-type' : ''}">
                <div class="cfg-type-icon"><i class="fa-solid fa-chalkboard-user"></i></div>
                <div>
                    <strong>Professor</strong>
                    <span>Oriente projetos, avalie equipes e acompanhe alunos.</span>
                </div>
                <button class="cfg-btn-${isAluno ? 'ghost' : 'primary'}" ${!isAluno ? 'disabled' : ''} data-tipo="professor">
                    ${!isAluno ? 'Ativo' : 'Trocar para professor'}
                </button>
            </div>
        </div>`;
    }


    // ── PERSONALIZAÇÃO ADM ─────────────────────────────────────
    else if (page === 'personalizacaoAdm') {
        if (!admin.isAdmin) { content.innerHTML = `<p class="cfg-admin-empty">Acesso restrito.</p>`; return; }

        const theme = admin.theme || {};
        const fonts = [
            'Inter, Segoe UI, Arial, sans-serif',
            'Poppins, Segoe UI, Arial, sans-serif',
            'Arial, sans-serif',
            'Georgia, serif',
        ];
        const presets = {
            educacional: {
                primary_color: '#235D4E', secondary_color: '#FF7A1A', accent_color: '#73B98F',
                background_color: '#EAF7F3', section_color: '#FFFFFF', text_color: '#21433D', font_family: fonts[0], title_font_family: fonts[0],
                layout_style: 'glass', border_style: 'solid', contrast: 50, soft_shadows: true, smooth_animations: true, gradients: false, high_contrast: false, reduce_motion: false,
                background_path: 'images/themes/image-78.png', auth_background_path: 'images/themes/image-78.png',
            },
            dark: {
                primary_color: '#38BDF8', secondary_color: '#22C55E', accent_color: '#F97316',
                background_color: '#0B1220', section_color: '#18212F', text_color: '#F8FAFC', font_family: fonts[0], title_font_family: fonts[0],
                layout_style: 'solid', border_style: 'none', contrast: 58, soft_shadows: false, smooth_animations: true, gradients: false, high_contrast: false, reduce_motion: false,
                background_path: 'images/themes/fpreto.png', auth_background_path: 'images/themes/fpreto.png',
            },
            neon: {
                primary_color: '#5B21B6', secondary_color: '#14B8A6', accent_color: '#EC4899',
                background_color: '#F5F3FF', section_color: '#FFFFFF', text_color: '#241B3A', font_family: fonts[1], title_font_family: fonts[1],
                layout_style: 'glass', border_style: 'solid', contrast: 52, soft_shadows: true, smooth_animations: true, gradients: true, high_contrast: false, reduce_motion: false,
                background_path: 'images/themes/froxo.png', auth_background_path: 'images/themes/froxo.png',
            },
        };

        const backgroundPresets = [
            ['images/themes/image-78.png', 'Verde claro', '#2D6A63'],
            ['images/themes/fverdee.png', 'Verde escuro', '#194D30'],
            ['images/themes/fazulc.png', 'Azul claro', '#2C8FB3'],
            ['images/themes/azule.png', 'Azul profundo', '#123B74'],
            ['images/themes/froxo.png', 'Roxo', '#5B2BA0'],
            ['images/themes/image-36.png', 'Galáxia', '#5678C8'],
            ['images/themes/image-34.png', 'Rosa suave', '#C78CB7'],
            ['images/themes/fcoral.png', 'Coral', '#E85C62'],
            ['images/themes/famarelo.png', 'Amarelo', '#D99A00'],
            ['images/themes/fcinza.png', 'Cinza', '#6F7680'],
            ['images/themes/fbranco.png', 'Branco', '#C9CED6'],
            ['images/themes/fpreto.png', 'Preto', '#111827'],
        ];

        const checked = (key, fallback = false) => (theme[key] ?? fallback) ? 'checked' : '';
        const selected = (key, value, fallback = '') => String(theme[key] ?? fallback) === value ? 'selected' : '';
        const radioChecked = (key, value, fallback = '') => String(theme[key] ?? fallback) === value ? 'checked' : '';

        content.innerHTML = `
        <div id="cfgFlash" class="cfg-flash"></div>
        <div class="cfg-adm-shell">
            <div class="cfg-adm-hero">
                <div>
                    <h2><i class="fa-solid fa-palette"></i> Personalização da plataforma</h2>
                    <p>Defina a identidade visual e a experiência do sistema.</p>
                </div>
                <span class="cfg-admin-badge">Administrador</span>
            </div>

            <form class="cfg-adm-grid" id="adminThemeForm">
                <input type="hidden" name="background_path" value="${escapeHtml(theme.background_path || 'images/themes/image-78.png')}">
                <input type="hidden" name="auth_background_path" value="${escapeHtml(theme.auth_background_path || 'images/themes/image-78.png')}">
                <section class="cfg-adm-main">
                    <div class="cfg-adm-tabs" role="tablist">
                        <button type="button" class="active" data-admin-tab="aparencia">Aparência</button>
                        <button type="button" data-admin-tab="temas">Temas</button>
                        <button type="button" data-admin-tab="layout">Layout</button>
                        <button type="button" data-admin-tab="avancado">Avançado</button>
                    </div>

                    <div class="cfg-adm-tab active" data-admin-panel="aparencia">
                        <div class="cfg-adm-section-header">
                            <div>
                                <h3>Identidade visual</h3>
                                <p>Personalize cores, tipografia e imagens principais.</p>
                            </div>
                            <span>Personalização global</span>
                        </div>

                        <div class="cfg-adm-cards two-cols">
                            <article class="cfg-adm-card">
                                <h4><i class="fa-solid fa-palette"></i> Paleta de cores</h4>
                                <div class="cfg-palette-preview">
                                    <span style="--swatch:${escapeHtml(theme.primary_color || '#235D4E')}">Primária</span>
                                    <span style="--swatch:${escapeHtml(theme.secondary_color || '#78B896')}">Secundária</span>
                                    <span style="--swatch:${escapeHtml(theme.accent_color || '#2979FF')}">Destaque</span>
                                </div>
                                <div class="cfg-color-grid">
                                    ${[
                                        ['primary_color', 'Cor primária', '#235D4E'],
                                        ['secondary_color', 'Cor secundária', '#78B896'],
                                        ['accent_color', 'Cor de destaque', '#2979FF'],
                                        ['background_color', 'Cor de fundo', '#F4FBF8'],
                                        ['section_color', 'Cor das seções', '#FFFFFF'],
                                        ['text_color', 'Texto principal', '#1F2937'],
                                    ].map(([key, label, fallback]) => `
                                        <label class="cfg-color-field">
                                            <span>${label}</span>
                                            <input type="color" name="${key}" value="${escapeHtml(theme[key] || fallback)}">
                                            <input class="cfg-input" type="text" value="${escapeHtml(theme[key] || fallback)}" maxlength="7" data-color-text="${key}">
                                        </label>
                                    `).join('')}
                                </div>
                            </article>

                            <article class="cfg-adm-card">
                                <h4><i class="fa-solid fa-font"></i> Tipografia</h4>
                                <div class="cfg-field-row">
                                    <div class="cfg-field">
                                        <label class="cfg-label">Fonte principal</label>
                                        <select class="cfg-select" name="font_family">
                                            ${fonts.map(font => `<option value="${escapeHtml(font)}" ${theme.font_family === font ? 'selected' : ''}>${escapeHtml(font.split(',')[0])}</option>`).join('')}
                                        </select>
                                    </div>
                                    <div class="cfg-field">
                                        <label class="cfg-label">Fonte dos títulos</label>
                                        <select class="cfg-select" name="title_font_family">
                                            ${fonts.map(font => `<option value="${escapeHtml(font)}" ${theme.title_font_family === font ? 'selected' : ''}>${escapeHtml(font.split(',')[0])}</option>`).join('')}
                                        </select>
                                    </div>
                                </div>
                                <div class="cfg-field-row compact-row">
                                    <div class="cfg-field">
                                        <label class="cfg-label">Tamanho da fonte</label>
                                        <input class="cfg-input" type="number" min="13" max="20" name="font_size" value="${escapeHtml(theme.font_size || 16)}">
                                    </div>
                                    <div class="cfg-field">
                                        <label class="cfg-label">Contraste</label>
                                        <input class="cfg-range" type="range" min="0" max="100" name="contrast" value="${escapeHtml(theme.contrast || 50)}">
                                        <span class="cfg-range-value" id="contrastValue">${escapeHtml(theme.contrast || 50)}%</span>
                                    </div>
                                </div>
                                <div class="cfg-font-preview" id="fontPreview">
                                    <h3>Exemplo de título</h3>
                                    <p>Esta é uma pré-visualização de como os textos aparecerão para os usuários.</p>
                                </div>
                            </article>

                            <article class="cfg-adm-card">
                                <h4><i class="fa-solid fa-image"></i> Logo e fundos</h4>
                                <div class="cfg-logo-preview">
                                    <img src="${escapeHtml(theme.logo_path ? `/${theme.logo_path}` : '/images/LOGOUNICEHUB-removebg-preview.png')}" alt="Logo atual">
                                </div>
                                <div class="cfg-upload-grid">
                                    <label class="cfg-upload-box"><span>Alterar logo</span><input type="file" name="logo" accept=".png,.jpg,.jpeg,.svg,.webp"><small>PNG, JPG, SVG ou WEBP até 5MB</small></label>
                                    <label class="cfg-upload-box"><span>Fundo das páginas internas</span><input type="file" name="background" accept="image/*"><small>Home, perfil, projetos e configurações</small></label>
                                    <label class="cfg-upload-box"><span>Fundo login/cadastro</span><input type="file" name="auth_background" accept="image/*"><small>Login, registro e recuperação</small></label>
                                </div>
                            </article>

                            <article class="cfg-adm-card">
                                <h4><i class="fa-solid fa-desktop"></i> Pré-visualização</h4>
                                <div class="cfg-mini-system" id="miniSystemPreview">
                                    <aside></aside>
                                    <main><header></header><div></div><div></div><div></div></main>
                                </div>
                                <button class="cfg-btn-ghost" type="button" id="fullscreenPreviewBtn"><i class="fa-solid fa-expand"></i> Pré-visualizar em tela cheia</button>
                            </article>
                        </div>
                    </div>

                    <div class="cfg-adm-tab" data-admin-panel="temas">
                        <div class="cfg-adm-section-header"><div><h3>Temas e fundos</h3><p>Escolha um preset de cores ou um fundo para aplicar no sistema inteiro.</p></div><span>Global</span></div>
                        <h4 class="cfg-subtitle">Presets rápidos</h4>
                        <div class="cfg-theme-grid compact-themes">
                            ${Object.entries(presets).map(([key, preset]) => `
                                <button type="button" class="cfg-theme-card" data-preset="${key}" style="--p:${preset.primary_color};--s:${preset.secondary_color};--a:${preset.accent_color};">
                                    <span></span><strong>${key === 'educacional' ? 'Educacional' : key === 'dark' ? 'Dark Tech' : 'Neon'}</strong><small>${preset.layout_style}</small>
                                </button>
                            `).join('')}
                        </div>
                        <h4 class="cfg-subtitle">Fundos do sistema</h4>
                        <div class="cfg-bg-grid">
                            ${backgroundPresets.map(([path, label, color]) => `
                                <button type="button" class="cfg-bg-card ${theme.background_path === path ? 'active' : ''}" data-background-path="${path}" style="--bg:url('/${path}');--tone:${color};">
                                    <span></span><strong>${label}</strong>
                                </button>
                            `).join('')}
                        </div>
                    </div>

                    <div class="cfg-adm-tab" data-admin-panel="layout">
                        <div class="cfg-adm-section-header"><div><h3>Padrões de layout</h3><p>Controle densidade, cartões e bordas do sistema.</p></div><span>Layouts disponíveis</span></div>
                        <div class="cfg-layout-grid">
                            ${[
                                ['glass', 'Vidro institucional', 'Transparência suave e cards amplos.'],
                                ['solid', 'Sólido', 'Superfícies opacas e leitura direta.'],
                                ['compact', 'Compacto', 'Menos arredondamento e mais densidade.'],
                            ].map(([value, title, desc]) => `
                                <label class="cfg-layout-card ${radioChecked('layout_style', value, 'glass') ? 'active' : ''}">
                                    <input type="radio" name="layout_style" value="${value}" ${radioChecked('layout_style', value, 'glass')}>
                                    <span class="layout-thumb ${value}"></span>
                                    <strong>${title}</strong>
                                    <small>${desc}</small>
                                </label>
                            `).join('')}
                        </div>
                        <div class="cfg-adm-card inline-card">
                            <div class="cfg-field">
                                <label class="cfg-label">Padrão das bordas</label>
                                <select class="cfg-select" name="border_style" id="borderStyle">
                                    <option value="solid" ${selected('border_style', 'solid', 'solid')}>Padrão</option>
                                    <option value="dashed" ${selected('border_style', 'dashed')}>Tracejado</option>
                                    <option value="dotted" ${selected('border_style', 'dotted')}>Pontilhado</option>
                                    <option value="none" ${selected('border_style', 'none')}>Sem borda</option>
                                </select>
                            </div>
                            <div class="cfg-border-preview" id="borderPreview">Exemplo de card</div>
                        </div>
                    </div>

                    <div class="cfg-adm-tab" data-admin-panel="avancado">
                        <div class="cfg-adm-section-header"><div><h3>Avançado</h3><p>Efeitos visuais, acessibilidade e restauração.</p></div></div>
                        <div class="cfg-advanced-grid">
                            ${[
                                ['soft_shadows', 'Sombras suaves', 'Aplica profundidade em menus, cards e modais.', true, 'fa-layer-group'],
                                ['smooth_animations', 'Animações suaves', 'Ativa transições modernas.', true, 'fa-wand-magic-sparkles'],
                                ['gradients', 'Gradientes', 'Permite fundos e botões com gradiente.', false, 'fa-fill-drip'],
                                ['high_contrast', 'Alto contraste', 'Aumenta diferenciação para melhorar legibilidade.', false, 'fa-circle-half-stroke'],
                                ['reduce_motion', 'Reduzir animações', 'Diminui movimentos para conforto visual.', false, 'fa-person-walking'],
                            ].map(([key, title, desc, fallback, icon]) => `
                                <label class="cfg-effect-item">
                                    <span class="effect-icon"><i class="fa-solid ${icon}"></i></span>
                                    <span><strong>${title}</strong><small>${desc}</small></span>
                                    <span class="cfg-toggle"><input type="checkbox" name="${key}" value="1" ${checked(key, fallback)}><span class="cfg-slider"></span></span>
                                </label>
                            `).join('')}
                            <button class="cfg-action-card danger" type="button" id="resetThemeBtn"><i class="fa-solid fa-rotate-left"></i><span><strong>Restaurar padrão</strong><small>Voltar para as configurações originais.</small></span></button>
                        </div>
                    </div>
                </section>

                <aside class="cfg-adm-preview-side">
                    <h3><i class="fa-regular fa-eye"></i> Pré-visualização</h3>
                    <div class="cfg-home-preview" id="themePreview">
                        <header><span class="hamb"><i class="fa-solid fa-bars"></i></span><strong>UniceHub</strong><span class="search">Pesquisar pessoas e projetos...</span><i class="fa-regular fa-bell"></i><span class="avatar"></span></header>
                        <div class="home-body">
                            <nav><span class="active"><i class="fa-solid fa-house"></i> Home</span><span><i class="fa-regular fa-user"></i> Perfil</span><span><i class="fa-solid fa-users"></i> Conexões</span><span><i class="fa-regular fa-folder"></i> Projetos</span></nav>
                            <main><article><div class="post-top"><span class="avatar"></span><strong>Projeto exemplo</strong></div><h4>Protótipo colaborativo</h4><p>Feed com projeto, tags e ações usando as cores do tema.</p><div class="tags"><span>#laravel</span><span>#design</span></div><button type="button">Ver projeto</button></article></main>
                            <aside><section><h4>Projetos em Destaque</h4><p>KARVAN PC BUILD</p><p>Dashboard Acadêmico</p></section><section><h4>Tecnologias em alta</h4><span>#php</span><span>#figma</span></section></aside>
                        </div>
                    </div>
                    <div class="cfg-tip-box"><h4><i class="fa-regular fa-lightbulb"></i> Dica</h4><p>As mudanças salvas afetam Home, Perfil, Conexões, Projetos, Configurações e telas de autenticação.</p></div>
                    <div class="cfg-presets-box"><h4>Presets rápidos</h4><button type="button" data-preset="educacional">Educacional</button><button type="button" data-preset="dark">Dark Tech</button><button type="button" data-preset="neon">Neon</button></div>
                    <button class="cfg-btn-primary" type="submit"><i class="fa-solid fa-check"></i> Salvar alterações</button>
                </aside>
            </form>
        </div>
        <div class="cfg-preview-modal" id="cfgPreviewModal"><div><button type="button" id="closePreviewModal"><i class="fa-solid fa-xmark"></i></button><div class="cfg-home-preview full" id="fullHomePreview"><header><span class="hamb"><i class="fa-solid fa-bars"></i></span><strong>UniceHub</strong><span class="search">Pesquisar pessoas e projetos...</span><i class="fa-regular fa-bell"></i><span class="avatar"></span></header><div class="home-body"><nav><span class="active"><i class="fa-solid fa-house"></i> Home</span><span><i class="fa-regular fa-user"></i> Perfil</span><span><i class="fa-solid fa-users"></i> Conexões</span><span><i class="fa-regular fa-folder"></i> Projetos</span><span><i class="fa-solid fa-gear"></i> Configurações</span></nav><main><article><div class="post-top"><span class="avatar"></span><strong>sla</strong><small>18 horas ago</small></div><h4>Projeto acadêmico</h4><p>Pré-visualização da Home com feed, botão, tags e cartões laterais.</p><div class="tags"><span>#laravel</span><span>#unicehub</span></div><button type="button">Ver projeto</button></article><article><h4>Segundo projeto</h4><p>Cards e textos herdando as cores globais.</p></article></main><aside><section><h4>Projetos em Destaque</h4><p>KARVAN PC BUILD</p><p>Dashboard Acadêmico</p></section><section><h4>Tecnologias em alta</h4><span>#php</span><span>#javascript</span><span>#figma</span></section></aside></div></div></div></div>
        <div class="cfg-confirm-modal" id="restoreConfirm" aria-hidden="true"><div><i class="fa-solid fa-triangle-exclamation"></i><h3>Restaurar personalização?</h3><p>Isso vai voltar cores, fontes, bordas, efeitos, logo e fundos para o padrão do sistema.</p><div><button type="button" class="cfg-btn-ghost" id="cancelRestoreTheme">Cancelar</button><button type="button" class="cfg-btn-primary danger" id="confirmRestoreTheme">Restaurar padrão</button></div></div></div>`;

        const form = document.getElementById('adminThemeForm');
        const preview = document.getElementById('themePreview');
        const fullPreview = document.getElementById('fullHomePreview');
        const mini = document.getElementById('miniSystemPreview');
        const contrastValue = document.getElementById('contrastValue');
        const borderPreview = document.getElementById('borderPreview');

        const syncColorTexts = () => {
            form.querySelectorAll('input[type="color"]').forEach(color => {
                const text = form.querySelector(`[data-color-text="${color.name}"]`);
                if (text) text.value = color.value.toUpperCase();
            });
        };

        const applyPreset = (key) => {
            const preset = presets[key];
            if (!preset) return;
            Object.entries(preset).forEach(([name, value]) => {
                const field = form.elements[name];
                if (!field) return;
                if (field instanceof RadioNodeList) {
                    [...field].forEach(radio => { radio.checked = radio.value === value; });
                } else if (field.type === 'checkbox') {
                    field.checked = Boolean(value);
                } else {
                    field.value = value;
                }
            });
            syncColorTexts();
            updatePreview();
        };

        const updatePreview = () => {
            const data = new FormData(form);
            const primary = data.get('primary_color');
            const secondary = data.get('secondary_color');
            const accent = data.get('accent_color');
            const bg = data.get('background_color');
            const section = data.get('section_color');
            const text = data.get('text_color');
            const border = data.get('border_style');
            const contrast = data.get('contrast') || 50;
            const backgroundPath = data.get('background_path') || 'images/themes/image-78.png';
            const previewTargets = [preview, fullPreview].filter(Boolean);
            const borderValue = border === 'none' ? 'none' : `1px ${border} rgba(45, 106, 99, 0.24)`;

            previewTargets.forEach(target => {
                target.style.setProperty('--preview-primary', primary);
                target.style.setProperty('--preview-secondary', secondary);
                target.style.setProperty('--preview-accent', accent);
                target.style.setProperty('--preview-bg', bg);
                target.style.setProperty('--preview-section', section);
                target.style.setProperty('--preview-text', text);
                target.style.setProperty('--preview-border', borderValue);
                target.style.backgroundColor = bg;
                target.style.backgroundImage = `url('/${backgroundPath}')`;
                target.style.color = text;
                target.style.fontFamily = data.get('font_family');
                target.style.fontSize = `${data.get('font_size')}px`;
                target.style.filter = `contrast(${85 + Number(contrast) * 0.3}%)`;
                target.querySelectorAll('button').forEach(button => {
                    button.style.background = data.get('gradients') ? `linear-gradient(135deg, ${accent}, ${primary})` : accent;
                });
            });

            mini.style.setProperty('--mini-primary', primary);
            mini.style.setProperty('--mini-bg', bg);
            mini.style.setProperty('--mini-section', section);
            mini.style.setProperty('--mini-accent', accent);
            mini.style.backgroundImage = `url('/${backgroundPath}')`;
            borderPreview.style.border = borderValue;
            contrastValue.textContent = `${contrast}%`;

            form.querySelectorAll('.cfg-layout-card').forEach(card => card.classList.toggle('active', card.querySelector('input').checked));
            form.querySelectorAll('.cfg-bg-card').forEach(card => card.classList.toggle('active', card.dataset.backgroundPath === backgroundPath));
        };

        form.querySelectorAll('.cfg-adm-tabs button').forEach(btn => {
            btn.addEventListener('click', () => {
                form.querySelectorAll('.cfg-adm-tabs button').forEach(b => b.classList.remove('active'));
                form.querySelectorAll('.cfg-adm-tab').forEach(panel => panel.classList.remove('active'));
                btn.classList.add('active');
                form.querySelector(`[data-admin-panel="${btn.dataset.adminTab}"]`)?.classList.add('active');
            });
        });

        form.querySelectorAll('input[type="color"]').forEach(color => {
            color.addEventListener('input', () => { syncColorTexts(); updatePreview(); });
        });

        form.querySelectorAll('[data-color-text]').forEach(text => {
            text.addEventListener('input', () => {
                const color = form.querySelector(`input[type="color"][name="${text.dataset.colorText}"]`);
                if (/^#[0-9A-Fa-f]{6}$/.test(text.value) && color) color.value = text.value;
                updatePreview();
            });
        });

        form.querySelectorAll('[data-preset]').forEach(btn => btn.addEventListener('click', () => applyPreset(btn.dataset.preset)));
        form.querySelectorAll('[data-background-path]').forEach(btn => {
            btn.addEventListener('click', () => {
                form.elements.background_path.value = btn.dataset.backgroundPath;
                form.elements.auth_background_path.value = btn.dataset.backgroundPath;
                updatePreview();
            });
        });
        form.addEventListener('input', updatePreview);
        syncColorTexts();
        updatePreview();

        document.getElementById('fullscreenPreviewBtn').addEventListener('click', () => document.getElementById('cfgPreviewModal').classList.add('active'));
        document.getElementById('closePreviewModal').addEventListener('click', () => document.getElementById('cfgPreviewModal').classList.remove('active'));

        form.addEventListener('submit', async (event) => {
            event.preventDefault();
            const btn = form.querySelector('button[type="submit"]');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Salvando...';

            try {
                const formData = new FormData(form);
                ['soft_shadows', 'smooth_animations', 'gradients', 'high_contrast', 'reduce_motion'].forEach(key => {
                    formData.set(key, form.elements[key]?.checked ? '1' : '0');
                });
                const res = await postMultipart(admin.themeSaveUrl, formData);
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw data;
                admin.theme = data.theme;
                refreshThemeCss();
                flash(data.message || 'Personalização salva com sucesso.', 'success');
            } catch (error) {
                const msgs = error.errors ? Object.values(error.errors).flat().join(' — ') : 'Erro ao salvar personalização.';
                flash(msgs, 'error');
            } finally {
                btn.disabled = false;
                btn.innerHTML = '<i class="fa-solid fa-check"></i> Salvar alterações';
            }
        });

        const restoreConfirm = document.getElementById('restoreConfirm');
        const closeRestoreConfirm = () => {
            restoreConfirm.classList.remove('active');
            restoreConfirm.setAttribute('aria-hidden', 'true');
        };

        document.getElementById('resetThemeBtn').addEventListener('click', () => {
            restoreConfirm.classList.add('active');
            restoreConfirm.setAttribute('aria-hidden', 'false');
        });

        document.getElementById('cancelRestoreTheme').addEventListener('click', closeRestoreConfirm);
        restoreConfirm.addEventListener('click', (event) => {
            if (event.target === restoreConfirm) closeRestoreConfirm();
        });

        document.getElementById('confirmRestoreTheme').addEventListener('click', async () => {
            const btn = document.getElementById('confirmRestoreTheme');
            btn.disabled = true;
            btn.innerHTML = '<i class="fa-solid fa-spinner fa-spin"></i> Restaurando...';
            const res = await postForm(admin.themeResetUrl, {});
            const data = await res.json().catch(() => ({}));
            if (res.ok) {
                admin.theme = data.theme;
                refreshThemeCss();
                closeRestoreConfirm();
                flash(data.message || 'Tema restaurado.', 'success');
                renderPage('personalizacaoAdm');
            } else {
                flash('Erro ao restaurar tema.', 'error');
                btn.disabled = false;
                btn.innerHTML = 'Restaurar padrão';
            }
        });
    }

    // ── USUÁRIOS ADM ───────────────────────────────────────────
    else if (page === 'usuariosAdm') {
        if (!admin.isAdmin) { content.innerHTML = `<p class="cfg-admin-empty">Acesso restrito.</p>`; return; }

        content.innerHTML = `
        <div id="cfgFlash" class="cfg-flash"></div>
        <div class="cfg-section-header">
            <div>
                <h2><i class="fa-solid fa-users-gear"></i> Usuários</h2>
                <p>Conceda ou remova acesso administrativo. Vários usuários podem ser administradores; o sistema só impede ficar sem nenhum ADM.</p>
            </div>
        </div>
        <div class="cfg-admin-users" id="adminUsersList">
            <div class="cfg-admin-empty">Carregando usuários...</div>
        </div>`;

        const list = document.getElementById('adminUsersList');

        const renderUsers = (usuarios) => {
            if (!usuarios.length) {
                list.innerHTML = '<div class="cfg-admin-empty">Nenhum usuário encontrado.</div>';
                return;
            }

            list.innerHTML = usuarios.map(usuario => `
                <div class="cfg-admin-user">
                    <img src="${escapeHtml(usuario.foto)}" alt="">
                    <div>
                        <strong>${escapeHtml(usuario.name)}</strong>
                        <span>${escapeHtml(usuario.email)} • ${escapeHtml(usuario.tipo || 'usuário')}</span>
                    </div>
                    <label class="cfg-toggle" title="Permissão de administrador">
                        <input type="checkbox" data-user-id="${usuario.id}" ${usuario.is_admin ? 'checked' : ''}>
                        <span class="cfg-slider"></span>
                    </label>
                </div>
            `).join('');
        };

        try {
            const res = await fetch(admin.usersUrl, { headers: { 'Accept': 'application/json' } });
            const data = await res.json();
            if (!res.ok) throw data;
            renderUsers(data.usuarios || []);
        } catch (error) {
            list.innerHTML = '<div class="cfg-admin-empty">Erro ao carregar usuários.</div>';
        }

        list.addEventListener('change', async (event) => {
            const input = event.target.closest('input[data-user-id]');
            if (!input) return;

            const previous = !input.checked;
            input.disabled = true;

            try {
                const res = await postForm(admin.userUpdateUrl, {
                    user_id: input.dataset.userId,
                    is_admin: input.checked,
                });
                const data = await res.json().catch(() => ({}));
                if (!res.ok) throw data;
                flash(data.message || 'Permissão atualizada.', 'success');
            } catch (error) {
                input.checked = previous;
                const msgs = error.errors ? Object.values(error.errors).flat().join(' — ') : 'Erro ao atualizar permissão.';
                flash(msgs, 'error');
            } finally {
                input.disabled = false;
            }
        });
    }

    // ── SOBRE ───────────────────────────────────────────────────
    else if (page === 'sobre') {
        content.innerHTML = `
        <div class="cfg-section-header">
            <div>
                <h2><i class="fa-solid fa-circle-info"></i> Sobre o UniceHub</h2>
                <p>Plataforma de colaboração acadêmica do Unicesumar.</p>
            </div>
        </div>
        <div class="cfg-info-grid">
            <div class="cfg-info-box"><i class="fa-solid fa-code-branch"></i><strong>v1.0.0</strong><span>Versão</span></div>
            <div class="cfg-info-box"><i class="fa-brands fa-laravel"></i><strong>Laravel 11</strong><span>Framework</span></div>
            <div class="cfg-info-box"><i class="fa-solid fa-database"></i><strong>MySQL 8</strong><span>Banco de dados</span></div>
            <div class="cfg-info-box"><i class="fa-solid fa-server"></i><strong>Online</strong><span>Status</span></div>
        </div>`;
    }

    // ── PRIVACIDADE ─────────────────────────────────────────────
    else if (page === 'privacidade') {
        content.innerHTML = `
        <div class="cfg-section-header">
            <div>
                <h2><i class="fa-solid fa-file-shield"></i> Política de privacidade</h2>
                <p>Como tratamos seus dados dentro da plataforma.</p>
            </div>
        </div>
        <div class="cfg-privacy-section">
            <h3><i class="fa-solid fa-lock"></i> Dados coletados</h3>
            <p>O UniceHub coleta nome, e-mail, CPF e data de nascimento para identificação do usuário. Esses dados são usados exclusivamente para o funcionamento da plataforma e não são compartilhados com terceiros.</p>
        </div>
        <div class="cfg-privacy-section">
            <h3><i class="fa-solid fa-eye-slash"></i> Privacidade do perfil</h3>
            <p>Informações como telefone, curso e tecnologias são visíveis apenas para usuários conectados na plataforma. Seu CPF nunca é exibido publicamente.</p>
        </div>
        <div class="cfg-privacy-section">
            <h3><i class="fa-solid fa-trash-can"></i> Exclusão de dados</h3>
            <p>Para solicitar a exclusão da sua conta e de todos os dados associados, entre em contato com o suporte da plataforma através do e-mail institucional.</p>
        </div>`;
    }

    else {
        content.innerHTML = `<p style="color:#6d7d78;padding:20px 0;">Seção em desenvolvimento.</p>`;
    }
}

// ── Init ───────────────────────────────────────────────────────
renderPage('perfil');
