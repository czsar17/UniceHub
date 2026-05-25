const senha =
document.getElementById("senha");

const email =
document.getElementById("email");

const toggleSenha =
document.getElementById("toggleSenha");


// OLHINHO

toggleSenha.addEventListener("click", () => {

    if(senha.type === "password"){

        senha.type = "text";

        toggleSenha.classList.replace(
            "fa-eye",
            "fa-eye-slash"
        );

    }else{

        senha.type = "password";

        toggleSenha.classList.replace(
            "fa-eye-slash",
            "fa-eye"
        );

    }

});


// REGRAS

const ruleLength =
document.getElementById("rule-length");

const ruleNumber =
document.getElementById("rule-number");

const ruleSpecial =
document.getElementById("rule-special");

const ruleEmail =
document.getElementById("rule-email");


// SENHA

senha.addEventListener("input", () => {

    const valor = senha.value;

    // 8 caracteres

    if(valor.length >= 8){

        ruleLength.classList.add("valid");

    }else{

        ruleLength.classList.remove("valid");

    }

    // número

    if(/[0-9]/.test(valor)){

        ruleNumber.classList.add("valid");

    }else{

        ruleNumber.classList.remove("valid");

    }

    // especial

    if(/[!@#$%^&*]/.test(valor)){

        ruleSpecial.classList.add("valid");

    }else{

        ruleSpecial.classList.remove("valid");

    }

});


// EMAIL

email.addEventListener("input", () => {

    const emailRegex =
    /^[^\s@]+@[^\s@]+\.[^\s@]+$/;

    if(emailRegex.test(email.value)){

        ruleEmail.classList.add("valid");

    }else{

        ruleEmail.classList.remove("valid");

    }

});
