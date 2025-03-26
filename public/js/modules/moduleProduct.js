$(() => {
   const formdata = new FormData();

   submitForm(formdata, "droplist", 'Categoría', (data) => {
      $("#formProduct #id_categoria").html(data);
   }, false);

   submitForm(formdata, "droplist", 'Marca', (data) => {
      $("#formProduct #id_marca").html(data);
   }, false);
});

const addProduct = () => {
   openModal('Producto');
   $('#codigo').prop('readonly', false);
};

const updateProduct = async (module, idProduct) => {
   await updateRegister(module, idProduct);
   $('#codigo').prop('readonly', true);
}