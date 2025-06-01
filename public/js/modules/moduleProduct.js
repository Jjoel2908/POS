$(() => {
   const formdata = new FormData();
   
   submitForm(formdata, "droplist", 'Marca', (data) => {
      $("#formProduct #id_marca").html(data);
   }, false);
});

/** Columnas que tendrá la tabla del módulo */
const moduleColumns = [
    { data: "nombre", title: "Nombre" },
    { data: "marca", title: "Marca" },
    { data: "codigo", title: "Código" },
    { data: "compra", title: "P. Compra" },
    { data: "venta", title: "P. Venta" },
    { data: "cantidad", title: "Stock" },
    { data: "imagen", title: "Imagen" },
    { data: "acciones", title: "Acciones" },
];

/** Abre el modal para registrar un nuevo producto.
 * Habilita el campo 'código' para que el usuario pueda ingresarlo manualmente.
 */
const addProduct = () => {
   openModal('Producto');
   $('#codigo').prop('readonly', false);
};

/** Carga los datos de un producto existente en el formulario para su edición.
 * Bloquea el campo 'código' para evitar su modificación.
 *
 * @param {string} module - Nombre del módulo (por lo general 'Producto').
 * @param {number} idProduct - ID del producto que se desea actualizar.
 */
const updateProduct = async (module, idProduct) => {
   await updateRegister(module, idProduct);
   $('#codigo').prop('readonly', true);
}