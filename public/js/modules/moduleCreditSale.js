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


const loadDetailsPayment = async (saleId, customerName) => {
    
}