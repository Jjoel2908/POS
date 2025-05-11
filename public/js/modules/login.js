
const sign_in_btn = document.querySelector("#sign-in-btn");
const sign_up_btn = document.querySelector("#sign-up-btn");
const container = document.querySelector(".container");
    
$(() => {
    $("#show_hide_password a").on("click", function (event) {
        event.preventDefault();
        if ($("#show_hide_password input").attr("type") == "text") {
            $("#show_hide_password input").attr("type", "password");
            $("#show_hide_password i").addClass("bx-hide");
            $("#show_hide_password i").removeClass("bx-show");
        } else if ($("#show_hide_password input").attr("type") == "password") {
            $("#show_hide_password input").attr("type", "text");
            $("#show_hide_password i").removeClass("bx-hide");
            $("#show_hide_password i").addClass("bx-show");
        }
    });
});

$("form#formLogin").on("submit", async function (event) {
    event.preventDefault();

    const user     = $("#correo").val();
    const password = $("#password").val();

    if (!user || !password) 
        showAlert(false, !user ? "El correo electrónico es requerido" : "La contraseña es requerida");

    if (!validateForm(event, this)) 
        return;

    try {
        const formData = new FormData(this);
        formData.append("operation", "login");
        formData.append("module", "Login");
        formData.append("id_sucursal", 0);

        const response = await fetch("../../../controllers/", {
            method: "POST",
            body: formData,
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const result = await response.json();
        if (result.success)
            window.location = window.location.origin + result.data;
        else 
            showAlert(false, result.data == null ? "El usuario no se encuentra registrado" : "Las credenciales son incorrectas");
    } catch (error) {
        showAlert(false, "Ocurrió un error al intentar iniciar sesión");
    }
});

/** Mostrar Alerta.
 * @param {boolean} success Identificador para saber si la acción realizada fue exitosa.
 * @param {string} message Mensaje de satisfacción o mensaje de error
 */
const showAlert = (success, message) => {
    const type = success ? "success" : "error"
    const icon = success ? "bx bx-check-circle" : "bx bx-x-circle";

    Lobibox.notify(type, {
        size: "mini",
        icon: icon,
        position: "bottom right",
        msg: '<p class="my-1">' + message + "</p>",
        sound: false,
    });
};

/** Valida un formulario antes de enviarlo.
 * Si faltan campos obligatorios, muestra una alerta y evita el envío.
 *
 * @param {Event} event - El evento submit o click que dispara la validación.
 * @param {HTMLFormElement} form - El formulario que se va a validar.
 * @returns {boolean} - Retorna true si el formulario es válido, false si hay errores.
 */
const validateForm = (event, form) => {
    if (!form.checkValidity()) {
        event.preventDefault();
        event.stopPropagation();
        form.classList.add("was-validated");        
        return false;
    }
    return true;
};