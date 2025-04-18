/** Abre una caja específica del punto de venta (POS).
 * Se ejecuta al seleccionar una caja desde la interfaz.
 *
 * @param {number} cashboxId - ID de la caja que se va a abrir.
 * @param {string} cashboxName - Nombre de la caja, útil para mostrar en la vista.
 */
const openCashbox = async (cashboxId, cashboxName) => {
    try {
        Swal.fire( {
            title: `<h3 class="mt-3">Abrir Caja</h3>`,
            html: `<p class="font-20 mb-2">Indique el efectivo inicial para comenzar operaciones con la caja ${cashboxName.toLowerCase()}.</p>`,
            confirmButtonText: `<i class="bx bx-box me-1"></i> Abrir Caja`,
            cancelButtonText: TextCancel,
            showCancelButton: true,
            input: 'number',
            inputPlaceholder: 'Escribe el Monto Inicial',
            inputValidator: value => !value ? 'Debe proporcionar el Monto Inicial' : null,
        }).then(async (result) => {
            if (result.isConfirmed) {
               const formData = new FormData();
               formData.append("id", cashboxId);
               formData.append("monto", result.value);

               /** Llamamos a submitForm pasando el módulo dinámicamente */
               await submitForm(formData, "openCashbox", "Caja", () => {
                  loadDataTable("#module-table", "Caja");
               });
            }
        });
    } catch (error) {
        console.error("Error en la función openCashbox del archivo moduleCashbox:", error);
    }
};

/** Cierra una caja específica del punto de venta (POS).
 * Se ejecuta al seleccionar el botón para cerrar la caja.
 *
 * @param {number} cashboxCountId - ID del conteo actual de la caja (registro de apertura).
 * @param {number} cashboxId - ID de la caja que se va a cerrar.
 * @param {number} finalAmount - Monto final contado al cierre de la caja.
 */
const closeCashbox = async (cashboxCountId, cashboxId, finalAmount) => {
   const text  = `<p class="font-20 mb-3">Asegúrese que el arqueo de caja sea correcto antes de confirmar.</p>`;
   const table = `<table class="font-size-15 w-100 table-bordered">
                     <tr>
                        <td class = "p-2 bg-primary text-white border-dark">Total de Ventas</td>
                     </tr>
                     <tr>
                        <td class = "p-2 border-dark">${finalAmount}</td>
                     </tr>
                  </table>`;

   Swal.fire({
      title: `<h3 class="mt-3">Cerrar Caja</h3>`,
      html: text + table,
      confirmButtonText: `<i class="bx bx-box me-1"></i> Cerrar Caja`,
      cancelButtonText: TextCancel,
      showCancelButton: true,
   }).then(async (result) => {
      if (result.isConfirmed) {

         const formData = new FormData();
         formData.append("cashboxCountId", cashboxCountId);
         formData.append("id", cashboxId);

         /** Llamamos a submitForm pasando el módulo dinámicamente */
         await submitForm(formData, "closeCashbox", "Caja", () => {
            /** Recargamos la tabla del módulo */
            loadDataTable("#module-table", "Caja");

            /** Ocultamos el modal */
            $("#modalViewDetails").modal("toggle");
         });
      }
   }); 
}