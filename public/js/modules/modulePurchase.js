$(() => {
   const formdata = new FormData();

   submitForm(formdata, "droplist", 'Producto', (data) => {
      $("#formPurchase #search").html(data);
      clearForm('#modalRegister');
   }, false);
});

/** Inicia el proceso de registro de una nueva compra en el módulo de cajas del POS.
 * Carga los productos agregados temporalmente al detalle de compra y abre el modal para finalizar la compra.
 */
const addPurchase = async () => {
   await loadTemporaryDetails('DetalleCompra');
   openModal('Compra');
};