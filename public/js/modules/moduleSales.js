let customerLoaded = false; 
$(() => {
    initializeProductsDropdown();
    loadTemporaryDetails("DetalleVenta");
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