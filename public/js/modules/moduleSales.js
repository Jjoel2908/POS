let customerLoaded = false; 
$(() => {
    initializeSalesDropdown();
    loadTemporaryDetails("DetalleVenta");
});

/** Inicializa el componente Select2 para realizar búsquedas de productos en tiempo real.
 *
 * Características:
 * - Muestra resultados con un retraso de 250ms para evitar múltiples peticiones simultáneas.
 * - Muestra sugerencias cuando el usuario escribe al menos 4 caracteres.
 * - Utiliza AJAX para obtener productos desde el backend (op=selectProducts).
 */
const initializeSalesDropdown = () => {
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
};

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

/** Ejecuta una acción adicional complementaria a un proceso principal.
 *
 * Esta función se utiliza para realizar tareas secundarias que deben llevarse a cabo
 * después (o como consecuencia) de una acción principal, como actualizar la interfaz,
 * mostrar mensajes, realizar validaciones extra o disparar eventos personalizados.
 *
 * Su propósito es mantener el código modular y evitar mezclar responsabilidades dentro
 * de la lógica principal.
 */
const runAdditionalStep = () => {
  const formdata = new FormData();
         submitForm(formdata, "droplist", 'Cliente', (data) => {
            $("#formAdd #id_cliente").html(data);
            customerLoaded = true; // Marcar como ya cargados
         }, false);
}

/** Detecta la opción seleccionada por el usuario en el campo de tipo de venta.
 * y realiza acciones dinámicas en el formulario según el valor seleccionado.
 */
$("form#formAdd #tipo_venta").on("select2:select", (e) => {
   const id = e.params.data.id;
   const customerField = $("#customerField");
   const customerId = $("#id_cliente");

   if (id == creditSale.toString()) {
    if (!customerLoaded)
      runAdditionalStep();

    customerField.show(); // Mostrar el campo
    customerId.prop("required", true); // Agregar el atributo requerido
   } else {
      customerField.hide(); // Ocultar el campo
      customerId.prop("required", false); // Quitar el atributo requerido
   }
});