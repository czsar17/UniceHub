const alunoBtn = document.getElementById("alunoBtn");
const professorBtn = document.getElementById("professorBtn");

const password = document.getElementById("password");
const confirmPassword = document.getElementById("confirmPassword");

const togglePassword = document.getElementById("togglePassword");

const ruleLength = document.getElementById("ruleLength");
const ruleNumber = document.getElementById("ruleNumber");
const ruleSpecial = document.getElementById("ruleSpecial");

const cpfInput = document.getElementById("cpf");

const birthDate = document.getElementById("birthDate");

const form = document.getElementById("registerForm");

const tipoUsuario = document.getElementById("tipoUsuario");

const calendarIcon = document.querySelector(".calendar-icon");
const dateInput = document.querySelector("#birthDate");

const imageBase = "/images";

/* CPF */

cpfInput.addEventListener("input", () => {

    cpfInput.value =
        cpfInput.value.replace(/\D/g, "");

    cpfInput.value =
        cpfInput.value.slice(0, 11);

});

/* SUBMIT */

form.addEventListener("submit", (e) => {

    const today = new Date();

    const currentDate =
        today.getFullYear() + "-" +
        String(today.getMonth() + 1).padStart(2, "0") + "-" +
        String(today.getDate()).padStart(2, "0");

    if (birthDate.value === currentDate) {

        e.preventDefault();

        alert("A data de nascimento não pode ser a data atual.");

        return;
    }

    if (!birthDate.value) {

        e.preventDefault();

        alert("Selecione sua data de nascimento.");

        return;
    }

    if (password.value !== confirmPassword.value) {

        e.preventDefault();

        alert("As senhas não coincidem.");

        return;
    }

});

/* BACKGROUND */

function setBackground(image){

    document.body.style.backgroundImage =
        `url('${imageBase}/${image}')`;

}

/* TROCA PERFIL */

alunoBtn.addEventListener("click", () => {

    alunoBtn.classList.add("active");

    professorBtn.classList.remove("active");

    tipoUsuario.value = "aluno";

    setBackground("bg-aluno1.png");

});

professorBtn.addEventListener("click", () => {

    professorBtn.classList.add("active");

    alunoBtn.classList.remove("active");

    tipoUsuario.value = "professor";

    setBackground("bg-professor1.png");

});

/* MOSTRAR SENHA */

togglePassword.addEventListener("click", () => {

    const isPassword =
        password.type === "password";

    password.type =
        isPassword ? "text" : "password";

    togglePassword.innerHTML = isPassword
        ? `<i class="fa-regular fa-eye-slash"></i>`
        : `<i class="fa-regular fa-eye"></i>`;

});

/* VALIDAÇÃO SENHA */

password.addEventListener("keyup", () => {

    const value = password.value;

    value.length >= 8
        ? ruleLength.classList.add("valid")
        : ruleLength.classList.remove("valid");

    /\d/.test(value)
        ? ruleNumber.classList.add("valid")
        : ruleNumber.classList.remove("valid");

    /[!@#$%^&*(),.?":{}|<>]/.test(value)
        ? ruleSpecial.classList.add("valid")
        : ruleSpecial.classList.remove("valid");

});

/* DATE PICKER */

calendarIcon.addEventListener("click", () => {

    if (dateInput.showPicker) {

        dateInput.showPicker();

    } else {

        dateInput.focus();

    }

});