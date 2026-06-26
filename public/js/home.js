function logout() {
    window.location.href = "login.html";
}

// =========================
// MENU POSTS
// =========================

const optionBtns = document.querySelectorAll(".options-btn");

optionBtns.forEach((btn) => {

    btn.addEventListener("click", (e) => {

        e.stopPropagation();

        const option = btn.nextElementSibling;

        if (!option) return;

        document
            .querySelectorAll(".mini-option")
            .forEach((menu) => {

                if (menu !== option) {
                    menu.classList.remove("show");
                }

            });

        option.classList.toggle("show");

    });

});

// =========================
// DISPENSAR POST
// =========================

const dismissPosts =
document.querySelectorAll(".dismiss-post");

dismissPosts.forEach((btn) => {

    btn.addEventListener("click", () => {

        const card =
        btn.closest(".post-card");

        if (!card) return;

        card.classList.add("remove-post");

        setTimeout(() => {

            card.remove();

        }, 450);

    });

});

// =========================
// FECHAR MENUS AO CLICAR FORA
// =========================

document.addEventListener("click", (e) => {

    if (!e.target.closest(".options-area")) {

        document
            .querySelectorAll(".mini-option")
            .forEach((menu) => {

                menu.classList.remove("show");

            });

    }

});

// =========================
// MODAL DE COMENTÁRIOS
// =========================

const modal =
document.getElementById("commentModal");

const closeBtn =
document.getElementById("closeComments");

const commentForm =
document.getElementById("commentForm");

const commentsContainer =
document.getElementById("commentsContainer");

// =========================
// CARREGAR COMENTÁRIOS
// =========================

function carregarComentarios(projetoId) {

    if (!commentsContainer) return;

    commentsContainer.innerHTML = `
        <div style="padding:20px;text-align:center;">
            Carregando comentários...
        </div>
    `;

    fetch(`/api/projetos/${projetoId}/comentarios`)
    .then(response => {

        if (!response.ok) {
            throw new Error("Erro");
        }

        return response.json();

    })
    .then(comentarios => {

        if (comentarios.length === 0) {

            commentsContainer.innerHTML = `
                <div style="
                    padding:20px;
                    text-align:center;
                    color:#777;
                ">
                    Nenhum comentário ainda.
                </div>
            `;

            return;
        }

        commentsContainer.innerHTML = "";

        comentarios.forEach(comentario => {

            const foto =
                comentario.user?.foto ??
                "images/default-user.png";

            const nome =
                comentario.user?.name ??
                "Usuário";

            commentsContainer.innerHTML += `
                <div class="comment-item">

                    <img
                        src="/${foto}"
                        class="comment-avatar"
                    >

                    <div class="comment-content">

                        <strong>
                            ${nome}
                        </strong>

                        <p>
                            ${comentario.comentario}
                        </p>

                    </div>

                </div>
            `;

        });

    })
    .catch(error => {

        console.error(error);

        commentsContainer.innerHTML = `
            <div style="
                padding:20px;
                text-align:center;
                color:red;
            ">
                Erro ao carregar comentários.
            </div>
        `;

    });

}

// =========================
// ABRIR MODAL
// =========================

document
.querySelectorAll(".comment-btn")
.forEach((btn) => {

    btn.addEventListener("click", () => {

        const projetoId =
        btn.dataset.id;

        console.log("Projeto:", projetoId);

        if (modal) {
            modal.classList.add("active");
        }

        if (commentForm) {

            commentForm.action =
            `/projetos/${projetoId}/comentar`;

        }

        carregarComentarios(projetoId);

    });

});

// =========================
// FECHAR MODAL
// =========================

if (closeBtn) {

    closeBtn.addEventListener("click", () => {

        if (modal) {
            modal.classList.remove("active");
        }

    });

}

// =========================
// FECHAR CLICANDO FORA
// =========================

if (modal) {

    modal.addEventListener("click", (e) => {

        if (e.target === modal) {

            modal.classList.remove("active");

        }

    });

}
// =========================
// RECOLHER WIDGETS LATERAIS
// =========================

document.querySelectorAll('.widget-toggle').forEach((btn) => {
    btn.addEventListener('click', (event) => {
        event.stopPropagation();
        const card = btn.closest('.widget-card');
        if (!card) return;

        const collapsed = !card.classList.contains('collapsed');
        card.classList.toggle('collapsed', collapsed);
        btn.setAttribute('aria-expanded', collapsed ? 'false' : 'true');
        btn.setAttribute('aria-label', collapsed ? 'Expandir card' : 'Recolher card');
    });
});
