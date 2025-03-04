const urlController = "../../../controllers/";
$(() =>  { 
    $("form").submit(async function (event) {
        event.preventDefault();

        if (validateForm(event, this)) {
            /** Obtenemos el módulo desde el atributo data-module */
            const module = $(this).data("module");

            /** Llamamos a submitForm pasando el módulo dinámicamente */
            await submitForm(this, "save", module, () => POS.loadTable('#module-table'));
        }
    });
});

/** Abre un modal para crear o actualizar un registro.
 * @param {string} [modal=""] - ID del modal a abrir. Por defecto 'modalRegister'.
 * @param {string} title - Título del modal (actualmente no se usa directamente).
 * @param {boolean} isUpdate - Indica si es un registro nuevo (false) o una actualización (true).
 */
const openModal = (title, isUpdate = false, idModal = "") => {

    /** Si no se especifica un modal, se usa 'modalRegister' */
    const modalId = idModal == "" ? "#modalRegister" : idModal;

    /** Determina si el texto es "Nueva" o "Nuevo" dependiendo del módulo */
    let textNew = ["Categoría", "Caja"].includes(title)
        ? "Nueva "
        : "Nuevo ";

    /** Define el texto de acción: "Actualizar" o "Nuevo/Nueva". */
    let actionText = isUpdate ? "Actualizar " : textNew;

    /** Actualiza el título dinámicamente. */
    $(`${modalId} #modalTitle`).html(`${actionText} ${title}`);

    /** Actualiza el texto del botón dependiendo si es nuevo o actualización */
    $(`${modalId} #btnSave`).html(
        `<i class="fa-regular fa-floppy-disk me-1"></i> ${
        isUpdate ? "Actualizar" : "Guardar"
        }`
    );

    /** Limpia los campos del formulario */
    clearForm(modalId);

    /** Muestra u oculta el modal */
    $(modalId).modal("toggle");
};

/** Limpia el formulario dentro de un modal específico.
 * Restablece todos los campos de entrada y selects, y elimina cualquier validación previa.
 * 
 * @param {string} modalId - El ID o selector del modal que contiene el formulario.
 */
const clearForm = modalId => {
    $(`${modalId} form`).get(0).reset();
    $(`${modalId} input`).val("");
    $(`${modalId} select`).val("").trigger("change");
    $(`${modalId} form`).removeClass("was-validated");
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
        showAlert(false, "Debe completar la información obligatoria");
        form.classList.add("was-validated");
        return false;
    }
    return true;
}

/** Envía el formulario al servidor usando async/await y FormData.
 * Muestra una alerta con el resultado y ejecuta un callback opcional en caso de éxito.
 * 
 * @param {HTMLFormElement} form - El formulario a enviar.
 * @param {string} operation - Nombre de la operación que se va a realizar (crear, actualizar, etc.).
 * @param {function} callback - Función opcional que se ejecuta tras un envío exitoso.
 */
const submitForm = async (form, operation, view, callback) => {
    try {
        let formData = new FormData(form);
        formData.append('operation', operation);
        formData.append('module', view);

        const response = await fetch(urlController, { method: 'POST', body: formData });

        if (!response.ok)
            throw new Error(`HTTP error! status: ${response.status}`);

        const result = await response.json();

        showAlert(result.success, result.message);

        if (result.success && callback)
            callback();

    } catch (error) {
        console.error('Error en la función submitForm del archivo moduleRecord:', error);
    }
};

/** Muestra una notificación en pantalla indicando el resultado de una acción.
 * Utiliza la librería Lobibox para mostrar mensajes tipo toast.
 * 
 * @param {boolean} success - Indica si la operación fue exitosa (true) o fallida (false).
 * @param {string} message - Mensaje que se mostrará al usuario.
 */
const showAlert = (success, message) => {
    Lobibox.notify(success ? "success" : "error", {
        pauseDelayOnHover: true,
        size: "mini",
        icon: success ? "bx bx-check-circle" : "bx bx-x-circle",
        position: "bottom right",
        msg: `<p class="my-1">${message}</p>`,
        sound: false
    });
}