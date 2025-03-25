$(() => {
   const formdata = new FormData();

   submitForm(formdata, "droplist", 'Categoría', (data) => {
      $("#formProduct #id_categoria").html(data);
   }, false);

   submitForm(formdata, "droplist", 'Marca', (data) => {
      $("#formProduct #id_marca").html(data);
   }, false);
});

const addProduct = async () => {
   openModal('Producto');
   if ( $('#codigo').attr('readonly') ) $('#codigo').removeAttr('readonly');
}

const updateProduct = async (module, idProduct) => {
   await updateRegister(module, idProduct);
   $('#codigo').prop('readonly', true);
}