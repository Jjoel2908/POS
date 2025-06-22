/** Inicia el proceso de registro de una nueva venta */
const addSale = async (saleType) => {
    /** Llamamos a submitForm pasando el módulo dinámicamente */
    /** TODO: HABILITAR CUANDO YA ESTÉ LISTO EL MODULO DE CAJAS */
    // await submitForm(new FormData(), "hasOpenCashbox", "Caja", async () => {

    /** Removemos todo lo que haya en el contenedor de la imagen */
    // $('#container-image').html("");

    /** Cargamos los detalles de venta y abrimos el modal */
    await loadTemporaryDetails("DetalleVenta");
    openModal("Venta");

    /** Maneja la UI según el tipo de venta */
    await handleSaleTypeUI(saleType);

    // }, false);
};

/** Configura la interfaz según el tipo de venta.
 * Si es venta a crédito (2), muestra el selector de cliente y lo inicializa.
 *
 * @param {number} saleType - Tipo de venta (1: contado, 2: crédito).
 */
const handleSaleTypeUI = async (saleType) => {
    if (saleType == 2) {
        $("#container-search").addClass("col-lg-7");
        $("#container-customer").removeClass("d-none");

        const formData = new FormData();
        submitForm(formData, "droplist", "Cliente", (data) => {
            $("#formSale #cliente").html(data);
            $("#formSale #cliente").val("").trigger("change");

            $("#formSale #cliente").css("width", "100%").select2({
                dropdownParent: "#modalRegister",
                placeholder: "Selecciona el cliente",
                allowClear: true,
            });
        }, false);
    }

    /** Actualiza el valor de tipo_venta */
    $("#tipo_venta").val(saleType);
};

/** Inicializa el componente Select2 para realizar búsquedas de productos en tiempo real
 * dentro del formulario de ventas (form#formAddSale).
 *
 * Características:
 * - Muestra resultados con un retraso de 250ms para evitar múltiples peticiones simultáneas.
 * - Muestra sugerencias cuando el usuario escribe al menos 2 caracteres.
 * - Utiliza AJAX para obtener productos desde el backend (op=selectProducts).
 */
$("#modalRegister").on("shown.bs.modal", function () {
    const $select = $("form#formSale #search")
        .css("width", "100%")
        .select2({
        placeholder: "Buscar Producto...",
        allowClear: true,
        dropdownParent: $("#modalRegister"),
        minimumInputLength: 2,
        ajax: {
            url: urlController,
            type: "POST",
            dataType: "json",
            delay: 250,
            data: function (params) {
            return {
                module: "Producto",
                operation: "droplistSales",
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
});

/** Función encargada de mostrar un input para agregar un pago a venta de crédito.
 * @param {int} saleId - Identificador de la venta a crédito.
 */
const addPayment = async (saleId) => {
    Swal.fire({
        title: '<h4 class="mt-3">Ingrese el monto del pago</h4>',
        input: "number",
        inputAttributes: {
        min: 0,
        step: 0.01,
        placeholder: "Monto del pago",
        },
        showCancelButton: true,
        confirmButtonText:
        '<i class="fa-solid fa-circle-check me-1"></i> Confirmar Pago',
        cancelButtonText: TextCancel,
        preConfirm: (paymentAmount) => {
        if (paymentAmount === null || paymentAmount.trim() === "") {
            Swal.showValidationMessage("Por favor, ingrese un número válido");
            return false;
        }

        const parsedAmount = parseFloat(paymentAmount);

        /** Validar números inválidos como --33, -.34, 4.43--, etc. */
        if (
            isNaN(parsedAmount) ||
            parsedAmount <= 0 ||
            !/^\d*\.?\d+$/.test(paymentAmount)
        ) {
            Swal.showValidationMessage(
            "Por favor, ingrese un número válido y positivo"
            );
            return false;
        }

        return parsedAmount;
        },
    }).then(async (result) => {
        if (result.isConfirmed) {
        /** Deshabilitar el botón para evitar doble envío */
        Swal.getConfirmButton().disabled = true;

        /** Cantidad ingresada por el usuario */
        const paymentAmount = result.value;

        const formData = new FormData();
        formData.append("id", saleId);
        formData.append("monto", paymentAmount);

        /** Llamamos a submitForm pasando el módulo dinámicamente */
        await submitForm(
            formData,
            "processCustomerPayment",
            "VentaCredito",
            async () => {
            loadDataTable("#module-table", "VentaCredito");
            }
        );
        }
    });
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