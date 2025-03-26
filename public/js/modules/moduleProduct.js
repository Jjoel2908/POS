$(() => {
   const formdata = new FormData();
   const dropdowns = ['Categoría', 'Marca'];

   dropdowns.forEach(async (module) => {
      await submitForm(formdata, "droplist", module, (data) => {
         $(`#formProduct #id_${module.toLowerCase()}`).html(data);
      }, false);
   });
});

const addProduct = () => {
   openModal('Producto');
   $('#codigo').prop('readonly', false);
};

const updateProduct = async (module, idProduct) => {
   await updateRegister(module, idProduct);
   $('#codigo').prop('readonly', true);
}