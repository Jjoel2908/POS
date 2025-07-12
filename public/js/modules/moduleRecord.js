const urlController = "../../../controllers/";
const currentModule = $(".card").data("module");
const creditSale = 2;
let modalId;

const columnsEndTable = ["Precio Compra", "Precio Venta", "Monto", "Monto Inicial", "Monto Final", "Precio", "Subtotal", "Total", "Total Venta", "Total Pagado", "Deuda Pendiente", "compra", "venta"];
const columnsCenterTable = ["Fecha de Creación", "Fecha de Alta", "Fecha Creación", "Fecha Vencimiento", "Fecha Inicio", "Fecha de Actualización", "Fecha", "Hora Inicio", "Hora", "Teléfono", "Cantidad", "Acciones", "Estado", "cantidad", "color", "talla", "imagen", "acciones"];

$(() => {
    loadModuleTable(currentModule);

    $("form").submit(async function (event) {
        event.preventDefault();

        if (validateForm(event, this)) {
            try {
                /** Mostrar el spinner */
                $("#loadingSpinner").show();

                /** Deshabilitamos el botón de submit */
                $('#btnSave').prop("disabled", true);
                
                /** Llamamos a submitForm pasando el módulo dinámicamente */
                const moduleRecord = $(this).data("module");
                await submitForm(this, "save", moduleRecord, () => {
                    loadModuleTable(currentModule);
                    $("#modalRegister").modal("toggle");
                });
            } catch (error) {
                console.log("Ocurrió un error al crear/actualizar un nuevo registro en el módulo " + moduleRecord);
            } finally {
                /** Ocultar el spinner después de que se complete la solicitud */
                $("#loadingSpinner").hide();
                $('#btnSave').prop("disabled", false);
            }
        }
    });
});

/** Carga la tabla de datos dependiendo del módulo.
 * Si el módulo es 'Producto', utiliza carga del lado del servidor.
 * En otros casos, utiliza carga normal.
 * @param {string} currentModule - Nombre del módulo a cargar.
 * @param {string} [tableSelector="#module-table"] - Selector de la tabla donde se cargan los datos.
 */
const loadModuleTable = (currentModule, tableSelector = "#module-table") => {
    if (currentModule === "Venta" || currentModule === "Compra")
        runAdditionalStep();
    else if (currentModule === "Producto")
        loadDataTableServerSide(tableSelector, currentModule);
    else
        loadDataTable(tableSelector, currentModule);
};

/** Abre un modal para crear o actualizar un registro.
 * @param {string} [modal=""] - ID del modal a abrir. Por defecto 'modalRegister'.
 * @param {string} title - Título del modal (actualmente no se usa directamente).
 * @param {boolean} isUpdate - Indica si es un registro nuevo (false) o una actualización (true).
 */
const openModal = (title, isUpdate = false, idModal = "", data = null) => {
    /** Si no se especifica un modal, se usa 'modalRegister' */
    modalId = idModal == "" ? "#modalRegister" : idModal;

    /** Determina si el texto es "Nueva" o "Nuevo" dependiendo del módulo */
    let textNew = ["Categoría", "Marca", "Caja", "Compra", "Venta"].includes(title) ? "Nueva " : "Nuevo ";

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
            const $el = $(`#${key}`);
            
            /** Evitamos intentar asignar valor a inputs tipo file */
            if ($el.prop("type") === "file")
                return;

            if ($el.is("select")) {
                $el.val(value).trigger("change");
            } else {
                $el.val(value);
            }
        });

        /** Si data trae imagen, guardamos la ruta en campo oculto */
        if (data.imagen)
            $('#current_image').val(data.imagen);
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

/** Carga datos en una tabla DataTable de manera dinámica.
 * Obtiene los datos desde el servidor usando `fetch` y renderiza las columnas automáticamente.
 *
 * @param {string} tableId - Selector de la tabla donde se mostrarán los datos.
 * @param {string} currentModule - Nombre del módulo para la solicitud al controlador.
 * @param {int} registerId - Identificador si se desea buscar registros de algo en particular.
 */
const loadDataTable = async (tableId, currentModule, registerId = null) => {
    try {
        /** Creamos un objeto FormData para enviar la solicitud */
        let formData = new FormData();
        formData.append("module", currentModule);
        formData.append("operation", "dataTable");
        formData.append("registerId", registerId);

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
            $("#table-empty").removeClass("d-none");
            $("#table-data").addClass("d-none");
            return;
        } else {
            $("#table-empty").addClass("d-none");
            $("#table-data").removeClass("d-none");
        }

        /** Generamos dinámicamente las columnas basadas en las claves del primer objeto */
        let columns = Object.keys(data[0]).map((key) => ({
            data: key,
            title: key,
        }));

        let content = initTable();
        content.columns = columns;
        content.buttons = [];
        content.data = data;

        /** Aplica alineaciones dinámicas a las filas */
        content.createdRow = (row, rowData) => {
            Object.keys(rowData).forEach((key, index) => {
                if (columnsCenterTable.includes(key)) {
                    $(`td:eq(${index})`, row).addClass("text-center");
                } else if (columnsEndTable.includes(key)) {
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

/** Carga datos en una tabla utilizando DataTables con procesamiento en servidor (serverSide).
 * Es una función genérica reutilizable para cualquier módulo, renderizando columnas de forma dinámica.
 *
 * @param {string} tableId - Selector de la tabla donde se mostrarán los datos (ej: "#tabla").
 * @param {string} currentModule - Nombre del módulo (se envía al controlador como identificador).
 * @param {int|null} registerId - ID opcional si se requiere buscar un registro específico.
 */
const loadDataTableServerSide = (tableId, currentModule, registerId = null) => {
    /** Visulización de tablas */
    $("#table-empty").addClass("d-none");
    $("#table-data").removeClass("d-none");

    let content = initTable();

    /** Activamos el procesamiento en servidor */
    content.serverSide = true;
    content.processing = true;

    /** Definimos el origen de datos desde el backend */
    content.ajax = {
        url: urlController,
        type: "POST",
        data: function(d) {
            d.module     = currentModule;
            d.operation  = "dataTable";
            d.registerId = registerId;
            return d;
        }
    };
    
    content.buttons = [];
    content.columns = MODULE_COLUMNS;

    /** Aplica alineaciones dinámicas a las filas */
    content.createdRow = (row, rowData) => {
        Object.keys(rowData).forEach((key, index) => {
            if (columnsCenterTable.includes(key)) {
                $(`td:eq(${index})`, row).addClass("text-center");
            } else if (columnsEndTable.includes(key)) {
                $(`td:eq(${index})`, row).addClass("text-end");
            } else {
                $(`td:eq(${index})`, row).addClass("text-start");
            }
        });
    };

    /** Recargamos la tabla ya con columnas dinámicas */
    showTable(tableId, content);
};

/** Genera una transacción para un módulo específico mediante una confirmación previa.
 * Muestra una alerta de confirmación antes de ejecutar la acción y, si el usuario confirma,
 * llama a la función submitForm para procesar la transacción.
 */
const saveTransaction = async () => {
    try {
        Swal.fire({
            title: 'Generar ' + currentModule,
            text: "¿Estás seguro de generar la siguiente " + currentModule.toLowerCase() + "?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonText: '<i class="fa-solid fa-cash-register me-1"></i> Sí, Generar ' + currentModule,
            cancelButtonText: TextCancel,
        }).then(async (result) => {
            if (result.isConfirmed) {
                try {
                    /** Mostramos el spinner y ocultamos botón de guardar */
                    $("#loadingSpinner").removeClass('d-none');
                    $('#btnSaveTransaction').addClass('d-none');
                    
                    const formData = new FormData();
                    /** Información adicional para venta */
                    if (currentModule === "Venta") {
                        const saleType   = $(`#tipo_venta option:selected`).val();
                        const customerId = $(`#id_cliente option:selected`).val();
                        formData.append("saleType", saleType);

                        if (customerId)
                            formData.append("customerId", customerId);
                    }

                    /** Llamamos a submitForm pasando el módulo dinámicamente */
                    await submitForm(formData, "save", currentModule, (data) => {
                        loadTemporaryDetails("Detalle" + currentModule);
                        $('#cantidad').prop('disabled', true);
                        $('select#tipo_venta').val(1).trigger('change');
                        $("#customerField").hide();
                    });
                } catch (error) {
                    console.error("Error en la función saveTransaction del archivo moduleRecord:", error);
                } finally {
                    $("#loadingSpinner").addClass('d-none');
                    $('#btnSaveTransaction').removeClass('d-none');
                }
            }
        });
    } catch (error) {
        console.error("Error en la función saveTransaction del archivo moduleRecord:", error);
    }
};

/** Carga los datos de un registro en un formulario para su edición.
 * Realiza una solicitud al servidor para obtener la información del registro y llena los campos del formulario.
 *
 * @param {string} currentModule - Nombre del módulo para la solicitud al controlador.
 * @param {number} id - ID del registro que se desea actualizar.
 * @param {string} [idModal=""] - ID del modal que se abrirá (opcional).
 */
const updateRegister = async (currentModule, id, idModal = "") => {
    const formdata = new FormData();
    formdata.append("id", id);

    /** Llamamos a submitForm pasando el módulo dinámicamente */
    await submitForm(formdata, "update", currentModule, (data) => {
        openModal(currentModule, true, idModal, data);
    }, false);
};

/** Elimina un registro tras la confirmación del usuario.
 * Muestra una alerta de confirmación con SweetAlert antes de proceder con la eliminación.
 *
 * @param {string} currentModule - Nombre del módulo para la solicitud al controlador.
 * @param {number} id - ID del registro que se desea eliminar.
 * @param {string} nombre - Nombre del registro para mostrar en la alerta.
 */
const deleteRegister = async (currentModule, id, nombre) => {
    try {
        if (currentModule === "DetalleCompra" || currentModule === "DetalleVenta") {
            await processDelete(currentModule, id);
            return;
        }

        /** Determina si el texto es "la" o "el" dependiendo del módulo */
        let text = ["Categoría", "Marca", "Caja"].includes(currentModule) ? "la" : "el";

        Swal.fire({
            title: '<h3 class="mt-3">Eliminar ' + currentModule + "</h3>",
            html: '<p class="font-size-20 mb-2">¿Estás seguro de eliminar ' +
            text + " siguiente " + currentModule.toLowerCase() + "?</p> <b>" + nombre + "</b>",
            confirmButtonText: TextDelete,
            cancelButtonText: TextCancel,
            showCancelButton: true,
        }).then(async (result) => {
            if (result.isConfirmed) {
                await processDelete(currentModule, id);
            }
        });
    } catch (error) {
        console.error("Error en la función deleteRegister del archivo moduleRecord:", error);
    }
};

/** Elimina un registro del módulo especificado enviando una solicitud al servidor.
 * Tras la eliminación, recarga la tabla de datos correspondiente.
 *
 * @param {string} currentModule - Nombre del módulo donde se encuentra el registro a eliminar.
 * @param {number} id - ID del registro que se desea eliminar.
 */
const processDelete = async (currentModule, id) => {
    try {
        let formdata = new FormData();
        formdata.append("id", id);

        /** Llamamos a submitForm pasando el módulo dinámicamente */
        await submitForm(formdata, "delete", currentModule, () => {
            if (currentModule === "DetalleCompra" || currentModule === "DetalleVenta")
                loadTemporaryDetails(currentModule);
            else
                loadModuleTable(currentModule);
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
    const price    = $("form #precio_compra").val() || $("form #precio_venta").val() || 0;
    const quantity = $("form #cantidad").val() || 0;

    /** Validar que el precio y la cantidad sean números válidos */
    if (isNaN(price) || isNaN(quantity) || quantity < 0 || price < 0)
        return 0.00;

    /** Multiplicar el precio por la cantidad y redondear a 2 decimales */
    const total = parseFloat(price * quantity);

    $("form #total").val(total.toLocaleString("es-MX", { minimumFractionDigits: 2, maximumFractionDigits: 2 }));
};

/** Maneja el evento de presionar "Enter" en formularios dinámicos.
 * Evita el envío automático, obtiene los valores y realiza una solicitud al servidor.
 *
 * @param {Event} e - Evento del teclado.
 * @param {string} formId - ID del formulario que contiene los datos.
 * @param {string} currentModule - Nombre del módulo desde donde se llama la función.
 */
const handleFormKeyPress = async (e, formId, currentModule) => {
    if (e.key === 'Enter') {
        e.preventDefault();

        /** Obtiene los valores del formulario */
        const id        = $(`#${formId} #id`).val();
        const quantity  = $(`#${formId} #cantidad`).val();
        const saleType  = $(`#${formId} #tipo_venta option:selected`).val();
        const customer  = $(`#${formId} #id_cliente option:selected`).val();
        const cantidad = $("form #cantidad");

        const formdata = new FormData();
        formdata.append("id", id);
        formdata.append("cantidad", quantity);

        /** Llamamos a submitForm pasando el módulo dinámicamente */
        await submitForm(formdata, "save", currentModule, (data) => {

            /** Limpiamos el formulario */
            $('#container-image').html("");
            $('#container-image-void').removeClass('d-none');   
            clearForm('');

            /** Cargamos los detalles de la venta */
            loadTemporaryDetails(data);

            /** Abrimos el buscador para nuevo producto */
            $('#search').select2('open');
            $('select#tipo_venta').val(saleType).trigger('change');
            $(`select#id_cliente`).val(customer).trigger('change');
            cantidad.prop('disabled', true);
        }, false);
    }
};

/** Carga y muestra los detalles de productos (compra/venta) *antes* de ser registrados,
 * es decir, en un estado provisional o temporal (por ejemplo, dentro de un formulario).
 * 
 * @param {string} currentModule - Nombre del módulo desde donde se llama la función.
 */
const loadTemporaryDetails = async (currentModule) => {
    /** Llamamos a submitForm pasando el módulo dinámicamente */
    const btnSaveTransaction = $("#btnSaveTransaction");
    const formData = new FormData();

    await submitForm(formData, "temporaryDataTable", currentModule, (data) => {
        $('#details').html(data.data);
        $('#total-details').html('$' + data.total);

        if (Number(data.count) === 0)
            btnSaveTransaction.prop('disabled', true);
        else
            btnSaveTransaction.prop('disabled', false);
    }, false);
};

/** Carga y muestra los detalles de una compra/venta ya registrada en el sistema.
 * Muestra los detalles en un modal si se pasa un ID de registro.
 *
 * @param {string} currentModule - Nombre del módulo desde donde se llama la función.
 * @param {number|null} registerId - ID del registro a cargar (opcional).
 * @param {string|null} date - Fecha en formato 'DD/MM/YYYY' (opcional).
 * @param {string} modalSelector - Selector del modal a mostrar (por defecto '#modalViewDetails').
 */
const loadRegisteredDetails = async (currentModule, registerId = null, date = null, modalSelector = "#modalViewDetails", tableSelector = "#table-details") => {
    const formattedDate = formatDateForTitle(date);
    const title = getTitleByModuleAndDate(currentModule, formattedDate);

    /** Asignamos el título para el modal */
    $(`${modalSelector} #modalTitle`).html(title);

    /** Cargamos la información */
    loadDataTable(tableSelector, currentModule, registerId);

    /** Mostramos el modal */
    $(modalSelector).modal("toggle");
};

/** Inicializa el componente Select2 para realizar búsquedas de productos en tiempo real.
 *
 * Características:
 * - Muestra resultados con un retraso de 250ms para evitar múltiples peticiones simultáneas.
 * - Muestra sugerencias cuando el usuario escribe al menos 4 caracteres.
 * - Utiliza AJAX para obtener productos desde el backend (op=selectProducts).
 */
const initializeProductsDropdown = () => {
    const $select = $("form#formAdd #search")
        .css("width", "100%")
        .select2({
        placeholder: "Ingrese el código de barras o el nombre del producto...",
        allowClear: true,
        minimumInputLength: 4,
        ajax: {
            url: urlController,
            type: "POST",
            dataType: "json",
            delay: 250,
            data: function (params) {
            return {
                module: "Producto",
                operation: "droplist",
                search: params.term || "",
            };
            },
            processResults: function (data) {
            return {
                results: data.results,
            };
            },
            cache: true,
        },
        language: {
            searching: () => "Buscando...",
            inputTooShort: () => "",
        },
        templateResult: formatProductResult,
        templateSelection: formatProductSelection,
        });

    $select.select2("open");
};

/** Renderiza cada opción del Select2 con imagen y texto.
 * Esta función se usa en el templateResult de Select2.
 *
 * @param {Object} product - El producto que llega desde el backend.
 * @returns {jQuery|String} - Elemento jQuery con la imagen y texto o solo texto si está cargando.
 */
const formatProductResult = (product) => {
    if (product.loading) return product.text;

    const $container = $(`
                <div style="display: flex; align-items: center;">
                    <img src="${product.imagen}" style="width: 80px; height: 80px; object-fit: cover; margin-right: 18px; border-radius: 4px;" />
                    <span>${product.text}</span>
                </div>
            `);

    return $container;
};

/** Renderiza el producto seleccionado en el input del Select2.
 * Aquí solo devolvemos el texto (no la imagen) para mantener el input limpio.
 *
 * @param {Object} product - El producto seleccionado.
 * @returns {String} - El texto a mostrar en el campo.
 */
const formatProductSelection = (product) => {
    return product.text || product.id;
};

/** Evento que se ejecuta al seleccionar un producto en el campo de búsqueda.
 * Obtiene los datos del producto y los carga en el formulario.
 */
$("form #search").on('select2:select', async e => {
    const id       = e.params.data.id;
    const cantidad = $("form #cantidad");

    if (Number(id) > 0) {
        const formdata = new FormData();
        formdata.append("id", id);
        await submitForm(formdata, "getRecord", 'Producto', (data) => {
            $.each(data, (key, value) => $("#" + key).val(value));

            /** Visualización de la imagen de producto */
            if (data?.pathImage) {
                $('#container-image-void').addClass('d-none');
                $('#container-image').html(data.pathImage);
            }
            else {
                $('#container-image').html("");
                $('#container-image-void').removeClass('d-none');
            }

            /** Activamos el input de cantidad e insertamos una pieza automáticamente */
            cantidad.val(1);
            cantidad.prop('disabled', false);
            cantidad.focus();
            calculateTotal();
        }, false);
    }
});

/** Evento que se ejecuta cuando el usuario cambia la cantidad del producto.
 * Llama a la función que calcula el total de la compra.
 */
$("form #cantidad").on('input', () => calculateTotal());