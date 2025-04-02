$(() => {
   const formdata = new FormData();

   submitForm(formdata, "droplist", 'Producto', (data) => {
      $("#formSale #search").html(data);
      clearForm('#modalRegister');
   }, false);
});

const addSale = async () => {
   await loadDataTableDetails('DetalleVenta');
   openModal('Venta');
};











let POS = new classPOS('Detalle de Venta', '#modalAddSale');
const URL_SALES = "../../../controllers/sales.php?";

$(() => {
   
   /**  A D D   C R E D I T   S A L E  */
   $("form#formAddCreditSale").submit(function (event) {
      event.preventDefault();

      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            let anticipo    = $('form#formAddCreditSale #anticipo').val();
            let container   = $('form#formAddSale #total-sales').text();
            let numberTotal = container.replace(/,/g, '');
            let totalSale   = parseFloat(numberTotal);

            if ( anticipo < totalSale ) {
               moduleSales.saveSale('credito');
            } else POS.showAlert(false, 'El monto de anticipo debe ser menor al total de la venta');
         }
      });
   });

});

let moduleSales = {

   /** M O D A L   A D D   S A L E  */
   modalAddSale: () => {
      let success;
      let error = "Debes abrir caja para realizar ventas";

      $.post(URL_SALES + 'op=existOpenCashbox', e => { 
         
         success = e;
         if (success == 0) {
            POS.showAlert(false, error);
         } else {
            $('#modalAddSale').modal('toggle');
            POS.clearForm();

            $("form#formAddSale #search").css('width', '100%').select2({
               placeholder: 'Buscar Producto...',
               allowClear: true,
               dropdownParent: '#modalAddSale',
               ajax: {
                  url: URL_SALES,
                  dataType: 'json',
                  delay: 250,
                  data: function (params) {
                     return {
                        op: 'selectProducts',
                        q: params.term || '',
                     };
                  },
                  processResults: function (data) {
                     return {
                        results: data.results
                     };
                  },
                  cache: true
               },
               minimumInputLength: 2,
               language: {
                  searching: function () {
                     return 'Buscando...';
                  },
                  inputTooShort: function() {
                     return '';
                  }
               }
            });

            $.post(URL_SALES + "op=selectCustomer", customers => { 
               $("form#formAddSale #cliente").html(customers);
               POS.clearForm();
            })
         }
      });
   },

   /** M O D A L   A D D   C R E D I T   S A L E  */
   modalAddCreditSale: () => {
      let cliente = $('form#formAddSale #cliente option:selected').val();

      if ( cliente == undefined ) {
         POS.showAlert(false, "Debe seleccionar el cliente para realizar la venta a crédito");
         return;
      }

      $('#modalAddSale').modal('hide');
      $('#modalAddCreditSale').modal('show');
   },

   /** C A L C U L A T E   S U B T O T A L  */
   calculateSubtotal: (precio, cantidad) => {
      let subtotal = parseFloat(precio) * parseInt(cantidad);
          subtotal = subtotal.toFixed(2);
      $("form#formAddSale #sub_total").val(subtotal);
   },

   /** H A N D L E   K E Y P R E S S  */
   handleKeyPress: (e) => {
      if (e.key === 'Enter') {

         e.preventDefault();

         let id       = $("form#formAddSale #id").val();
         let cantidad = $("form#formAddSale #cantidad").val();

         let data = {
            id: id,
            cantidad: cantidad
         }
         
         $.post(URL_SALES + "op=addSaleDetails", data, e => {
            response = JSON.parse(e);

            if (response.success) {
               POS.clearForm();
               moduleSales.dataTableSaleDetails();   
               $('form#formAddSale #btnCredit, form#formAddSale #btnSave, form#formAddSale #btnCancel').prop('disabled', false); 
               $('#search').select2('open');
            } else POS.showAlert(response.success, response.message);
         });
      }
   },

   /** D E L E T E   S A L E   D E T A I L  */
   deleteSaleDetail: (id, nombre) => {
      let url = URL_SALES + "op=deleteSaleDetail";
      POS.delete(id, nombre, url, moduleSales.dataTableSaleDetails);
   },

   /** T A B L E  S A L E   D E T A I L */
   dataTableSaleDetails: () => {
      $.post(URL_SALES + "op=dataTableSaleDetails", e => {

         let response = JSON.parse(e);
         let message  = '<tr><td colspan="5">No hay detalles de venta disponibles.</td></tr>';
         let result   = (response.data != "") ? response.data : message;
         let total    = (response.total != 0) ? response.total : '0.00';

         $('#modalAddSale #table-product-details').html(result);
         $('#modalAddSale #total-sales').text(total);

         if (response.data == "") {
            $('#formAddSale #cantidad, #formAddSale #btnCredit, #formAddSale #btnSave, #formAddSale #btnCancel').prop('disabled', true);
         }
      });
   },

   /**  S A V E  S A L E  */
   /** @param {string} pago Método de pago */
   saveSale: pago => {

      if (pago == 'contado') {
         $('#modalAddSale').modal('hide');
      } else $('#modalAddCreditSale').modal('hide');

      let configAlert = {
         title: 'Generar Venta',
         html: `¿Estás seguro de generar la siguiente venta?`,
         icon: 'warning',
         showCancelButton: true,
         confirmButtonText: '<i class="fa-solid fa-bag-shopping me-1"></i> Sí, Generar Venta',
         cancelButtonText: TextCancel,
      };

      Swal.fire(configAlert).then((result) => {
         if (result.isConfirmed) {
            let anticipo = $('form#formAddCreditSale #anticipo').val();
            let cliente = $('form#formAddSale #cliente option:selected').val();

            $.ajax({
               url: URL_SALES + "op=saveSale",
               data: { cliente: cliente, pago: pago, anticipo: anticipo },
               processData: true,
               type: 'POST',
               dataType: 'json',
               success: function(response) {
                  if (response.success) {
                     POS.showAlert(response.success, response.message);
                     moduleSales.dataTableSaleDetails();
                     moduleSales.tableSales();
                     $('#formAddSale #cantidad, #formAddSale #btnCredit, #formAddSale #btnSave, #formAddSale #btnCancel').prop('disabled', true);
                     if (pago == 'credito') $('#modalAddCreditSale').modal('hide');
                     $("#modalAddSale").modal('hide');
                  } else {
                     if (pago == 'credito') $('#modalAddCreditSale').modal('hide');
                     $("#modalAddSale").modal('show');
                     POS.showAlert(response.success, response.message);
                  }
               },
            });
         } else {
            if (pago == 'credito') $('form#formAddCreditSale').get(0).reset().removeClass('was-validated');;
            $("#modalAddSale").modal('show');
         }
      });
   },

   /**  C A N C E L   S A L E  */
   cancelSale: () => {
      $('#modalAddSale').modal('hide');
      Swal.fire({
         title: 'Cancelar Venta',
         text: "Al cancelar esta venta se eliminarán todos los productos agregados. ¿Desea continuar?",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonText: '<i class="bx bx-eraser me-1"></i> Sí, Cancelar Venta',
         cancelButtonText: TextCancel,
      }).then((result) => {
         if (result.isConfirmed) {
            $.ajax({
               url: URL_SALES + "op=cancelSale",
               type: 'POST',
               dataType: 'json',
               success: function(response) {
                  if (response.success) {
                     
                     POS.showAlert(response.success, response.message);
                     POS.clearForm();
                     moduleSales.dataTableSaleDetails();
                     $('#formAddSale #cantidad, #formAddSale #btnCredit, #formAddSale #btnSave, #formAddSale #btnCancel').prop('disabled', true);
                     $("#modalAddSale").modal('hide');
                     
                  } else {
                     $("#modalAddSale").modal('show');
                     POS.showAlert(response.success, response.message);
                  }
               },
            });
         } else $("#modalAddSale").modal('show');
      });
   },

   /** V A L I DA T E   S A L E   D E T A I L S  */
   validateModalSale: () => {
      $.post(URL_SALES + "op=dataTableSaleDetails", e => {

         let response = JSON.parse(e);
         if (response.data != "") {
            let message = "El carrito cuenta con detalles de productos";
            POS.showAlert(false, message);
         } else {
            POS.clearForm();
            $('#modalAddSale').modal('hide');
         }
         
      });
   },

   /** T A B L E  S A L E S */
   tableSales: () => {
      $.ajax({
         url: URL_SALES + "op=dataTable",
         type: "GET",
         dataType: "JSON",
         success: (data) => {
   
            let content = initTable();
            content.columns =    [  { data: 'id',   title: '#' },
                                    { data: 'fecha',     title: 'Fecha' },
                                    { data: 'hora',     title: 'Hora' },
                                    { data: 'usuario',   title: 'Usuario' },
                                    { data: 'btn',      title: 'Acciones' } ];
            content.order = [[0, "desc"]];
            content.data = data;
            content.createdRow = (row, data) => {
               $(`td:eq(3)`, row).addClass("text-start");
            };
   
            showTable("#table-sales", content);
         }
      });
	},

   /** T A B L E   C R E D I T   S A L E S */
   tableCreditSales: () => {
      $.ajax({
         url: URL_SALES + "op=creditDataTable",
         type: "GET",
         dataType: "JSON",
         success: (data) => {
   
            let content = initTable();
            content.columns =    [  { data: 'fecha',     title: 'Fecha' },
                                    { data: 'cliente',   title: 'Cliente' },
                                    { data: 'total',   title: 'Total Venta' },
                                    { data: 'pagado',   title: 'Saldo Pendiente' },
                                    { data: 'ultimo_pago',   title: 'Último Pago' },
                                    { data: 'estatus',   title: 'Estado' },
                                    { data: 'btn',      title: 'Acciones' } ];
            content.order = [[0, "desc"]];
            content.data = data;
            content.createdRow = (row, data) => {
               $(`td:eq(1)`, row).addClass("text-start");
               $(`td:eq(2), td:eq(3)`, row).addClass("text-end");
            };
   
            showTable("#table-credit-sales", content);
         }
      });
	} 
}

let moduleCreditSales = {
   viewDetails: (id, total) => {

      $.ajax({
         url: URL_SALES + "op=viewDetailsSale&id=" + id,
         type: "GET",
         dataType: "JSON",
         contentType: false,
         processData: false,
         success: (data) => {

            $('#detalle').html('Venta');
            $('#totalDetails').html(total);
   
            let content = initTable();
            content.buttons = [];
            content.columns =    [  { data: 'nombre',     title: 'Producto' },
                                    { data: 'cantidad',   title: 'Cantidad' },
                                    { data: 'precio',     title: 'Precio' },
                                    { data: 'subtotal',   title: 'Subtotal' }
                                 ];
            content.data = data;
            content.createdRow = (row, data) => {
               $(`td:eq(0)`, row).addClass("text-start");
               $(`td:eq(3), td:eq(4)`, row).addClass("text-end");
            };
   
            showTable("#table-view-details", content);

            $('#modalViewDetails').modal('toggle');
         }
      });
   },
   /** O P E N  C A S H B O X */
   addPayment: (id, pendiente, idSale) => {
  
      Swal.fire( {
         title: `<h3 class="mt-3">Agregar Pago</h3>`,
         html: `<p class="font-20 mb-2">Indique el monto a abonar</p>`,
         confirmButtonText: `<i class="fa-solid fa-money-bill-1 me-1"></i> Agregar Pago`,
         cancelButtonText: TextCancel,
         showCancelButton: true,
         input: 'number',
         inputPlaceholder: 'Escribe el Monto Inicial',
         inputAttributes: {
            step: '0.01', // Esto permite introducir números con decimales
            min: '0', // Esto asegura que los números negativos no sean permitidos
            max: pendiente
         },
         inputValidator: (value) => {
             return new Promise((resolve) => {
                 if (!value) {
                     resolve('Debe proporcionar el monto a abonar');
                 } else {
                     resolve();
                 }
             });
         }
      } ).then((result) => {
         if (result.isConfirmed) {

            const formdata = new FormData();
            formdata.append("id", id);
            formdata.append("idSale", idSale);
            formdata.append("abono", result.value);
            formdata.append("pendiente", pendiente);
  
            $.ajax({
               url: URL_SALES + "op=addPayment",
               type: "POST",
               data: formdata,
               contentType: false,
               processData: false,
               success: (e) => {
                  const response = JSON.parse(e);
                  POS.showAlert(response.success, response.message);
                  moduleSales.tableCreditSales();
               },
            });
         }
      });
   },
}

$("form#formAddSale #search").on('select2:select', e => {
   const id = e.params.data.id;
   const url = URL_SALES + "op=infoProduct";
   let cantidad = $("form#formAddSale #cantidad");

   const data = { id: id };

   $.post(url, data, info => { 
      const response = JSON.parse(info);
      $.each(response.data, (key, value) => $("#" + key).val(value));
   });

   cantidad.prop('disabled', false);

   // Posicionar el cursor
   cantidad.focus();
});

$("form#formAddSale #cantidad").on('input', () => {
   let precio   = $("form#formAddSale #precio_venta").val();
   let cantidad = $("form#formAddSale #cantidad").val() || 0;
   moduleSales.calculateSubtotal(precio, cantidad);
});

// Manejar el clic en el botón de cancelar
$('#btnCreditCancel').on('click', function () {
   // Muestra el modal principal
   $('#modalAddSale').modal('show');
});

// $('#modalAddCreditSale').on('hidden.bs.modal', function () {
//    // Muestra nuevamente el modal principal cuando el modal de crédito se cierra
//    $('#modalAddSale').modal('show');
// });