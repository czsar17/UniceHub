function logout(){

    window.location.href = "login.html";

}
// efeito

const menuBtns =
document.querySelectorAll(".menu-btn");

menuBtns.forEach((btn) => {

    btn.addEventListener("click", () => {

        const menu =
        btn.nextElementSibling;

        document
        .querySelectorAll(".post-menu")
        .forEach((m) => {

            if(m !== menu){
                m.classList.remove("active");
            }

        });

        menu.classList.toggle("active");

    });

});


// DISPENSAR

const dismissBtns =
document.querySelectorAll(".dismiss-btn");

dismissBtns.forEach((btn) => {

    btn.addEventListener("click", () => {

        const postCard =
        btn.closest(".post-card");

        postCard.classList.add("remove");

        setTimeout(() => {

            postCard.remove();

        }, 400);

    });

});


// FECHAR MENU AO CLICAR FORA

document.addEventListener("click", (e) => {

    if(!e.target.closest(".menu-container")){

        document
        .querySelectorAll(".post-menu")
        .forEach((menu) => {

            menu.classList.remove("active");

        });

    }

});

const optionBtns =
document.querySelectorAll(".options-btn");

optionBtns.forEach((btn) => {

    btn.addEventListener("click", () => {

        const option =
        btn.nextElementSibling;

        // fecha os outros
        document
        .querySelectorAll(".mini-option")
        .forEach((menu) => {

            if(menu !== option){

                menu.classList.remove("show");

            }

        });

        option.classList.toggle("show");

    });

});


// DISPENSAR POST

const dismissPosts =
document.querySelectorAll(".dismiss-post");

dismissPosts.forEach((btn) => {

    btn.addEventListener("click", () => {

        const card =
        btn.closest(".post-card");

        card.classList.add("remove-post");

        setTimeout(() => {

            card.remove();

        }, 450);

    });

});


// FECHAR AO CLICAR FORA

document.addEventListener("click", (e) => {

    if(!e.target.closest(".options-area")){

        document
        .querySelectorAll(".mini-option")
        .forEach((menu) => {

            menu.classList.remove("show");

        });

    }

});