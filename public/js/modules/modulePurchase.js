$(() => {
   const formdata = new FormData();

   submitForm(formdata, "droplist", 'Producto', (data) => {
      $("#formPurchase #search").html(data);
      clearForm('#modalRegister');
   }, false);
});

const addPurchase = async () => {
   await loadDataTableDetails('DetalleCompra');
   openModal('Compra');
};