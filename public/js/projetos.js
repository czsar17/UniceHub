document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.project-menu').forEach((menu) => {
        const button = menu.querySelector('.project-menu-btn');
        const dropdown = menu.querySelector('.project-menu-dropdown');

        if (!button || !dropdown) return;

        button.addEventListener('click', (event) => {
            event.stopPropagation();
            document.querySelectorAll('.project-menu').forEach((other) => {
                if (other !== menu) {
                    other.classList.remove('open');
                }
            });
            menu.classList.toggle('open');
        });
    });

    document.addEventListener('click', () => {
        document.querySelectorAll('.project-menu').forEach((menu) => {
            menu.classList.remove('open');
        });
    });
});
