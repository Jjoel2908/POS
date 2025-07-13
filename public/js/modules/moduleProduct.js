$(() => {
   const droplists = [
      { label: 'Marca', action: "droplist", target: '#id_marca' },
      { label: 'Producto', action: "droplistPresentations", target: '#id_presentacion' },
      { label: 'Producto', action: "droplistColors", target: '#id_color' },
   ];

   droplists.forEach(({ label, action, target }) => {
      const formdata = new FormData();
   
      submitForm(formdata, action, label, (data) => {
         $(target).html(data);
      }, false);
   });
});

/** Columnas que tendrá la tabla del módulo */
const MODULE_COLUMNS = [
   { 
      data: "imagen", 
      title: "Producto", 
      width: "320px", 
      orderable: false, 
      searchable: false 
   },
   { 
      data: "descripcion", 
      title: "Detalles",
      orderable: false, 
   },
   { 
      data: "acciones", 
      title: "Acciones", 
      orderable: false, 
      searchable: false 
   },
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
 * @param {string} currentModule - Nombre del módulo (por lo general 'Producto').
 * @param {number} idProduct - ID del producto que se desea actualizar.
 */
const updateProduct = async (currentModule, idProduct) => {
   await updateRegister(currentModule, idProduct);
   $('#codigo').prop('readonly', true);
}

/** Carga los datos de un producto existente en el formulario para su duplicación.
 *
 * @param {string} currentModule - Nombre del módulo (por lo general 'Producto').
 * @param {number} idProduct - ID del producto que se desea actualizar.
 */
const duplicateProduct = async (currentModule, idProduct) => {
   await updateRegister(currentModule, idProduct);
   $('#modalTitle').html('Nueva Variante');
   $('#id').val('');
   $('#codigo').val('').prop('readonly', false);
}