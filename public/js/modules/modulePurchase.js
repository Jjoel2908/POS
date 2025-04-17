$(() => {
   const formdata = new FormData();

   submitForm(formdata, "droplist", 'Producto', (data) => {
      $("#formPurchase #search").html(data);
      clearForm('#modalRegister');
   }, false);
});

const addPurchase = async () => {
   await loadTemporaryDetails('DetalleCompra');
   openModal('Compra');
};