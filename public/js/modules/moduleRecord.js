const urlController = "../../../controllers/";
const module = $(".card").data("module");
let modalId;
$(() => {
    loadDataTable("#module-table", module);

    $("form").submit(async function (event) {
        event.preventDefault();

        if (validateForm(event, this)) {
        const moduleRecord = $(this).data("module");
        /** Llamamos a submitForm pasando el módulo dinámicamente */
        await submitForm(this, "save", moduleRecord, () => {
            loadDataTable("#module-table", module);
            $("#modalRegister").modal("toggle");
        });
        }
    });
});

/** Abre un modal para crear o actualizar un registro.
 * @param {string} [modal=""] - ID del modal a abrir. Por defecto 'modalRegister'.
 * @param {string} title - Título del modal (actualmente no se usa directamente).
 * @param {boolean} isUpdate - Indica si es un registro nuevo (false) o una actualización (true).
 */
const openModal = (title, isUpdate = false, idModal = "", data = null) => {
    /** Si no se especifica un modal, se usa 'modalRegister' */
    modalId = idModal == "" ? "#modalRegister" : idModal;

    /** Determina si el texto es "Nueva" o "Nuevo" dependiendo del módulo */
    let textNew = ["Categoría", "Caja"].includes(title) ? "Nueva " : "Nuevo ";

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

    if (data != null) {
        $.each(data, (key, value) => {
            $(`select#${key}`).val(value).trigger('change');
            $(`#${key}`).val(value);
        });
    }

    /** Muestra u oculta el modal */
    $(modalId).modal("toggle");
};

/** Limpia el formulario dentro de un modal específico.
 * Restablece todos los campos de entrada y selects, y elimina cualquier validación previa.
 *
 * @param {string} modalId - El ID o selector del modal que contiene el formulario.
 */
const clearForm = (modalId) => {
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
};

/** Envía el formulario al servidor usando async/await y FormData.
 * Muestra una alerta con el resultado y ejecuta un callback opcional en caso de éxito.
 *
 * @param {HTMLFormElement} form - El formulario a enviar.
 * @param {string} operation - Nombre de la operación que se va a realizar (crear, actualizar, etc.).
 * @param {function} callback - Función opcional que se ejecuta tras un envío exitoso.
 */
const submitForm = async (form, operation, view, callback, showAlertResponse = true) => {
    try {
        let formData;

        if (form instanceof FormData)
            formData = form;
        else 
            formData = new FormData(form)

        formData.append("operation", operation);
        formData.append("module", view);

        const response = await fetch(urlController, {
            method: "POST",
            body: formData,
        });

        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        const result = await response.json();

        if (showAlertResponse || !result.success) showAlert(result.success, result.message);

        if (result.success && callback) callback(result?.data);
    } catch (error) {
        console.error("Error en la función submitForm del archivo moduleRecord:", error);
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
        sound: false,
    });
};

/** Carga datos en una tabla DataTable de manera dinámica.
 * Obtiene los datos desde el servidor usando `fetch` y renderiza las columnas automáticamente.
 *
 * @param {string} tableId - Selector de la tabla donde se mostrarán los datos.
 * @param {string} module - Nombre del módulo para la solicitud al controlador.
 */
const loadDataTable = async (tableId, module) => {
    try {
        /** Creamos un objeto FormData para enviar la solicitud */
        let formData = new FormData();
        formData.append("module", module);
        formData.append("operation", "dataTable");

        /** Enviamos la solicitud al servidor */
        const response = await fetch(urlController, {
        method: "POST",
        body: formData,
        });

        /** Verificamos si la respuesta es válida */
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        /** Convertimos la respuesta a JSON */
        const data = await response.json();

        if (data.length === 0) {
        showAlert(false, "No hay datos disponibles.");
        return;
        }

        /** Generamos dinámicamente las columnas basadas en las claves del primer objeto */
        let columns = Object.keys(data[0]).map((key) => ({
        data: key,
        title: key,
        }));

        let content = initTable();
        content.columns = columns;
        content.data = data;

        /** Aplica alineaciones dinámicas a las filas */
        content.createdRow = (row, rowData) => {
        Object.keys(rowData).forEach((key, index) => {
            if (["FECHA DE CREACIÓN", "ACCIONES", "ESTADO"].includes(key)) {
            $(`td:eq(${index})`, row).addClass("text-center");
            } else {
            $(`td:eq(${index})`, row).addClass("text-start");
            }
        });
        };

        showTable(tableId, content);
    } catch (error) {
        console.error("Error en la función loadDataTable del archivo moduleRecord:", error);
    }
};

/** Carga los datos de un registro en un formulario para su edición.
 * Realiza una solicitud al servidor para obtener la información del registro y llena los campos del formulario.
 *
 * @param {string} module - Nombre del módulo para la solicitud al controlador.
 * @param {number} id - ID del registro que se desea actualizar.
 * @param {string} [idModal=""] - ID del modal que se abrirá (opcional).
 */
const updateRegister = async (module, id, idModal = "") => {
    let formdata = new FormData();
    formdata.append("id", id);

    /** Llamamos a submitForm pasando el módulo dinámicamente */
    await submitForm(formdata, "update", module, (data) => {
     

        openModal(module, true, idModal, data);
    }, false);
};

/** Elimina un registro tras la confirmación del usuario.
 * Muestra una alerta de confirmación con SweetAlert antes de proceder con la eliminación.
 *
 * @param {string} module - Nombre del módulo para la solicitud al controlador.
 * @param {number} id - ID del registro que se desea eliminar.
 * @param {string} nombre - Nombre del registro para mostrar en la alerta.
 */
const deleteRegister = async (module, id, nombre) => {
    try {
        /** Determina si el texto es "la" o "el" dependiendo del módulo */
        let text = ["Categoría", "Caja"].includes(module) ? "la" : "el";

        Swal.fire({
            title: '<h3 class="mt-3">Eliminar ' + module + "</h3>",
            html: '<p class="font-size-20 mb-2">¿Estás seguro de eliminar ' +
            text + " siguiente " + module.toLowerCase() + "?</p> <b>" + nombre + "</b>",
            confirmButtonText: TextDelete,
            cancelButtonText: TextCancel,
            showCancelButton: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                let formdata = new FormData();
                formdata.append("id", id);

                /** Llamamos a submitForm pasando el módulo dinámicamente */
                await submitForm(formdata, "delete", module, () => {
                    loadDataTable("#module-table", module);
                });
            }
        });
    } catch (error) {
        console.error("Error en la función deleteRegister del archivo moduleRecord:", error);
    }
};
