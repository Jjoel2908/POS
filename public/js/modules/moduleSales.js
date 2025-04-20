/** Inicia el proceso de registro de una nueva venta */
const addSale = async (saleType) => {
   /** Llamamos a submitForm pasando el módulo dinámicamente */
   await submitForm(new FormData(), "hasOpenCashbox", "Caja", async () => {
      /** Cargamos los detalles de venta y abrimos el modal */
      await loadTemporaryDetails('DetalleVenta');
      openModal('Venta');

      /** Maneja la UI según el tipo de venta */
      await handleSaleTypeUI(saleType);

      /** Búsqueda en tiempo real */
      await initProductSearch();
   }, false);
};

/** Configura la interfaz según el tipo de venta.
 * Si es venta a crédito (2), muestra el selector de cliente y lo inicializa.
 *
 * @param {number} saleType - Tipo de venta (1: contado, 2: crédito).
 */
const handleSaleTypeUI = async (saleType) => {
   if (saleType == 2) {
      $('#container-search').addClass('col-lg-7');
      $('#container-customer').removeClass('d-none');

      const formData = new FormData();
      submitForm(formData, "droplist", 'Cliente', (data) => {
         $('#formSale #cliente').html(data);
         $('#formSale #cliente').val("").trigger("change");

         $("#formSale #cliente").css('width', '100%').select2({
            dropdownParent: '#modalRegister',
            placeholder: 'Selecciona el cliente',
            allowClear: true,
         });
      }, false);
   }

   /** Actualiza el valor de tipo_venta */
   $('#tipo_venta').val(saleType);
};

/** Inicializa el componente Select2 para realizar búsquedas de productos en tiempo real
 * dentro del formulario de ventas (form#formAddSale).
 * 
 * Características:
 * - Muestra resultados con un retraso de 250ms para evitar múltiples peticiones simultáneas.
 * - Muestra sugerencias cuando el usuario escribe al menos 2 caracteres.
 * - Utiliza AJAX para obtener productos desde el backend (op=selectProducts).
 */
const initProductSearch = async () => {
   $("form#formSale #search").css("width", "100%").select2({  
      placeholder: "Buscar Producto...",
      allowClear: true,
      dropdownParent: "#modalRegister",
      minimumInputLength: 2,
      ajax: {
         url: urlController,
         type: 'POST',
         dataType: "json",
         delay: 250,
         data: function (params) {
            return {
               module: 'Producto',
               operation: 'droplistSales',
               search: params.term || '',
            };
         },
         processResults: function (data) {
            return {
               results: data.results
            };
         },
         cache: true,
      },
      language: {
         searching: () => "Buscando...",
         inputTooShort: () => "", // No muestra texto cuando el input es muy corto
      },
   });
};