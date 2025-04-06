/** TODO: PONER LOADING CUANDO SE VAYA A REGISTRAR, ACTUALIZAR O ELIMINAR UN REGISTRO */
/** TODO: EN REPORTES PONER LOADING EN LO QUE SE CARGA LA INFORMACIÓN */
const urlController = "../../../controllers/";
const module = $(".card").data("module");
let modalId;
$(() => {
    loadDataTable("#module-table", module);

    $("form").submit(async function (event) {
        event.preventDefault();

        if (validateForm(event, this)) {
            const moduleRecord = $(this).data("module");

            if (moduleRecord == "Compra" || moduleRecord == "Venta") return;
            
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
    let textNew = ["Categoría", "Marca", "Caja", "Compra"].includes(title) ? "Nueva " : "Nuevo ";

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
            if (["Fecha de Creación", "Fecha de Alta", "Teléfono", "Código", "Cantidad", "Acciones", "Estado"].includes(key)) {
                $(`td:eq(${index})`, row).addClass("text-center");
            } else if (["Precio Compra", "Precio Venta"].includes(key)) {
                $(`td:eq(${index})`, row).addClass("text-end");
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

/** Genera una transacción para un módulo específico mediante una confirmación previa.
 * Muestra una alerta de confirmación antes de ejecutar la acción y, si el usuario confirma,
 * llama a la función submitForm para procesar la transacción.
 *
 * @param {string} module - Nombre del módulo para la generación de la transacción.
 */
const saveTransaction = async (module) => {
    try {
        Swal.fire({
            title: 'Generar ' + module,
            text: "¿Estás seguro de generar la siguiente " + module.toLowerCase() + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-cash-register me-1"></i> Sí, Generar ' + module,
            cancelButtonText: TextCancel,
        }).then(async (result) => {
            if (result.isConfirmed) {
                /** Llamamos a submitForm pasando el módulo dinámicamente */
                await submitForm(new FormData(), "save", module, () => {
                    loadDataTable("#module-table", module);
                    $('form #cantidad').prop('disabled', true);
                    $("#modalRegister").modal("toggle");
                });
            }
        });
    } catch (error) {
        console.error("Error en la función saveTransaction del archivo moduleRecord:", error);
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
    const formdata = new FormData();
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
        if (module === "DetalleCompra" || module === "DetalleVenta") {
            await processDelete(module, id);
            return;
        }

        /** Determina si el texto es "la" o "el" dependiendo del módulo */
        let text = ["Categoría", "Marca", "Caja"].includes(module) ? "la" : "el";

        Swal.fire({
            title: '<h3 class="mt-3">Eliminar ' + module + "</h3>",
            html: '<p class="font-size-20 mb-2">¿Estás seguro de eliminar ' +
            text + " siguiente " + module.toLowerCase() + "?</p> <b>" + nombre + "</b>",
            confirmButtonText: TextDelete,
            cancelButtonText: TextCancel,
            showCancelButton: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                await processDelete(module, id);
            }
        });
    } catch (error) {
        console.error("Error en la función deleteRegister del archivo moduleRecord:", error);
    }
};

/** Elimina un registro del módulo especificado enviando una solicitud al servidor.
 * Tras la eliminación, recarga la tabla de datos correspondiente.
 *
 * @param {string} module - Nombre del módulo donde se encuentra el registro a eliminar.
 * @param {number} id - ID del registro que se desea eliminar.
 */
const processDelete = async (module, id) => {
    try {
        let formdata = new FormData();
        formdata.append("id", id);

        /** Llamamos a submitForm pasando el módulo dinámicamente */
        await submitForm(formdata, "delete", module, () => {
            if (module === "DetalleCompra" || module === "DetalleVenta")
                loadDataTableDetails(module);
            else
                loadDataTable("#module-table", module);
        });
    } catch (error) {
        console.error("Error en processDelete:", error);
    }
};

/** Calcula el total de una compra o venta multiplicando el precio unitario por la cantidad.
 * Si los valores ingresados no son números válidos, retorna 0.
 * @returns {number} - Total calculado (precio * cantidad) con dos decimales.
 */
const calculateTotal = () => {
    const price    = $("form #precio_compra").val() || $("form #precio_compra").val() || 0;
    const quantity = $("form #cantidad").val() || 0;

    /** Validar que el precio y la cantidad sean números válidos */
    if (isNaN(price) || isNaN(quantity) || quantity < 0 || price < 0)
        return 0.00;

    /** Multiplicar el precio por la cantidad y redondear a 2 decimales */
    const total = parseFloat((price * quantity).toFixed(2));

    $("form #total").val(total);
};

/** Maneja el evento de presionar "Enter" en formularios dinámicos.
 * Evita el envío automático, obtiene los valores y realiza una solicitud al servidor.
 *
 * @param {Event} e - Evento del teclado.
 * @param {string} formId - ID del formulario que contiene los datos.
 * @param {string} module - Nombre del módulo desde donde se llama la función.
 */
const handleFormKeyPress = async (e, formId, module) => {
    if (e.key === 'Enter') {
        e.preventDefault();

        /** Obtiene los valores del formulario */
        const id       = $(`#${formId} #id`).val();
        const cantidad = $(`#${formId} #cantidad`).val();

        const formdata = new FormData();
        formdata.append("id", id);
        formdata.append("cantidad", cantidad);

        /** Llamamos a submitForm pasando el módulo dinámicamente */
        await submitForm(formdata, "save", module, (data) => {
            clearForm('#modalRegister');
            loadDataTableDetails(data);
            $('#search').select2('open');
        }, false);
    }
};

/** Obtiene y muestra los detalles de una compra/venta en una tabla dentro de un modal.
 * @param {string} module - Nombre del módulo desde donde se llama la función.
 */
const loadDataTableDetails = async (module) => {
    /** Llamamos a submitForm pasando el módulo dinámicamente */
    await submitForm(new FormData(), "dataTable", module, (data) => {
        $('#details').html(data.data);
        $('#total-details').html('$' + data.total);
    }, false);
};

/** Evento que se ejecuta al seleccionar un producto en el campo de búsqueda.
 * Obtiene los datos del producto y los carga en el formulario.
 */
$("form #search").on('select2:select', async e => {
    const id       = e.params.data.id;
    const cantidad = $("form #cantidad");

    if (id > 0) {
        const formdata = new FormData();
        formdata.append("id", id);
        await submitForm(formdata, "update", 'Producto', (data) => {
            $.each(data, (key, value) => $("#" + key).val(value));

            cantidad.prop('disabled', false);
            cantidad.focus();
        }, false);
    }
});

/** Evento que se ejecuta cuando el usuario cambia la cantidad del producto.
 * Llama a la función que calcula el total de la compra.
 */
$("form #cantidad").on('input', () => calculateTotal());