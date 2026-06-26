document.addEventListener('DOMContentLoaded', () => {
    const menuIcon = document.querySelector('.menu-icon');
    const sidebar = document.querySelector('.sidebar');

    if (menuIcon && sidebar) {
        const collapsed = localStorage.getItem('unicehub.sidebar.collapsed') === '1';
        document.body.classList.toggle('sidebar-collapsed', collapsed);
        menuIcon.setAttribute('role', 'button');
        menuIcon.setAttribute('tabindex', '0');
        menuIcon.setAttribute('aria-label', 'Recolher menu lateral');

        const toggleSidebar = () => {
            const nextState = !document.body.classList.contains('sidebar-collapsed');
            document.body.classList.toggle('sidebar-collapsed', nextState);
            localStorage.setItem('unicehub.sidebar.collapsed', nextState ? '1' : '0');
        };

        menuIcon.addEventListener('click', toggleSidebar);
        menuIcon.addEventListener('keydown', (event) => {
            if (event.key === 'Enter' || event.key === ' ') {
                event.preventDefault();
                toggleSidebar();
            }
        });
    }

    const bell = document.querySelector('.notification');
    const headerIcons = document.querySelector('.header-icons');

    if (!bell || !headerIcons) return;

    const trigger = document.createElement('button');
    trigger.type = 'button';
    trigger.className = 'notification-trigger';
    trigger.setAttribute('aria-label', 'Abrir notificações');

    bell.replaceWith(trigger);
    trigger.appendChild(bell);

    const badge = document.createElement('span');
    badge.className = 'notification-badge';
    badge.hidden = true;
    trigger.appendChild(badge);

    const panel = document.createElement('div');
    panel.className = 'notification-panel';
    panel.innerHTML = `
        <div class="notification-panel-header">
            <strong>Notificações</strong>
            <span>Atualizações recentes</span>
        </div>
        <div class="notification-list">
            <div class="notification-empty">Carregando...</div>
        </div>
    `;
    headerIcons.appendChild(panel);

    const list = panel.querySelector('.notification-list');
    let loaded = false;

    function escapeHtml(value) {
        return String(value ?? '')
            .replaceAll('&', '&amp;')
            .replaceAll('<', '&lt;')
            .replaceAll('>', '&gt;')
            .replaceAll('"', '&quot;')
            .replaceAll("'", '&#039;');
    }

    function renderEmpty(message) {
        list.innerHTML = `<div class="notification-empty">${escapeHtml(message)}</div>`;
    }

    function renderNotifications(data) {
        const notifications = Array.isArray(data.notificacoes) ? data.notificacoes : [];

        if (data.total > 0) {
            badge.textContent = data.total > 9 ? '9+' : data.total;
            badge.hidden = false;
        } else {
            badge.hidden = true;
        }

        if (!notifications.length) {
            renderEmpty('Nenhuma notificação por enquanto.');
            return;
        }

        list.innerHTML = notifications.map(item => `
            <a class="notification-item" href="${escapeHtml(item.url || '#')}">
                <span class="notification-item-icon"><i class="fa-solid ${escapeHtml(item.icone || 'fa-bell')}"></i></span>
                <span class="notification-item-body">
                    <strong>${escapeHtml(item.titulo)}</strong>
                    <small>${escapeHtml(item.texto)}</small>
                    <em>${escapeHtml(item.data)}</em>
                </span>
            </a>
        `).join('');
    }

    async function loadNotifications() {
        try {
            const response = await fetch('/notificacoes/header', {
                headers: { 'Accept': 'application/json' },
            });

            if (!response.ok) throw new Error('Erro ao buscar notificações');

            renderNotifications(await response.json());
            loaded = true;
        } catch (error) {
            renderEmpty('Não foi possível carregar as notificações.');
        }
    }

    trigger.addEventListener('click', (event) => {
        event.stopPropagation();
        panel.classList.toggle('open');

        if (panel.classList.contains('open') && !loaded) {
            loadNotifications();
        }
    });

    document.addEventListener('click', (event) => {
        if (!panel.contains(event.target) && !trigger.contains(event.target)) {
            panel.classList.remove('open');
        }
    });

    loadNotifications();
});
