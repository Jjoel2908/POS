let POS = new classPOS('Detalle', '#modalAddPurchase');
const URL_PURCHASE = "../../../controllers/purchase.php?";

$(() => { modulePurchase.tablePurchase(); });

let modulePurchase = {

   /** M O D A L   A D D   P U R C H A S E  */
   modalAddPurchase: () => {
      $('#modalAddPurchase').modal('toggle');
      $.post(URL_PURCHASE + "op=selectProducts", products => { 
         $("form#formAddPurchase #search").html(products);
         POS.clearForm();
      })
   },

   /** C A L C U L A T E   S U B T O T A L  */
   calculateSubtotal: (precio, cantidad) => {
      let subtotal = parseFloat(precio) * parseInt(cantidad);
          subtotal = subtotal.toFixed(2);
      $("form#formAddPurchase #sub_total").val(subtotal);
   },

   /** H A N D L E   K E Y P R E S S  */
   handleKeyPress: (e) => {
      if (e.key === 'Enter') {

         e.preventDefault();

         let id       = $("form#formAddPurchase #id").val();
         let cantidad = $("form#formAddPurchase #cantidad").val();
   
         let data = {
            id: id,
            cantidad: cantidad
         }
         
         $.post(URL_PURCHASE + "op=addPurchaseDetails", data, e => {
            response = JSON.parse(e);

            if (response.success) {
               POS.clearForm();
               modulePurchase.dataTablePurchaseDetails();
               $('form#formAddPurchase #btnSave, form#formAddPurchase #btnCancel').prop('disabled', false);   
               $('#search').select2('open');        
            } else POS.showAlert(response.success, response.message);
         
         });
      }
   },

   /** D E L E T E   P U R C H A S E   D E T A I L  */
   deletePurchaseDetail: (id, nombre) => {
      let url = URL_PURCHASE + "op=deletePurchaseDetail";
      POS.delete(id, nombre, url, modulePurchase.dataTablePurchaseDetails);
   },

   /** T A B L E  P U R C H A S E   D E T A I L */
   dataTablePurchaseDetails: () => {
      $.post(URL_PURCHASE + "op=dataTablePurchaseDetails", e => {

         let response = JSON.parse(e);
         let message  = '<tr><td colspan="5">No hay detalles de compra disponibles.</td></tr>';
         let result   = (response.data != "") ? response.data : message;
         let total    = (response.total != 0) ? response.total : 0.00;

         $('#modalAddPurchase #table-product-details').html(result);
         $('#modalAddPurchase #total-purchase').html('$' + total);
      });
   },

   /**  S A V E  P U R C H A S E  */
   savePurchase: () => {
      Swal.fire({
         title: 'Generar Compra',
         text: "¿Estás seguro de generar la siguiente compra?",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonText: '<i class="fa-solid fa-bag-shopping me-1"></i> Sí, Generar Compra',
         cancelButtonText: TextCancel,
      }).then((result) => {
         if (result.isConfirmed) {
            $.ajax({
               url: URL_PURCHASE + "op=savePurchase",
               type: 'POST',
               dataType: 'json',
               success: function(response) {
                  if (response.success) {
                     
                     POS.showAlert(response.success, response.message);
                     modulePurchase.dataTablePurchaseDetails();
                     modulePurchase.tablePurchase();
                     $('#formAddPurchase #cantidad, #formAddPurchase #btnSave, #formAddPurchase #btnCancel').prop('disabled', true);
                     $("#modalAddPurchase").modal('hide');
                     
                  } else {
                     $("#modalAddPurchase").modal('show');
                     POS.showAlert(response.success, response.message);
                  }
               },
            });
         } else $("#modalAddPurchase").modal('show');
      });
   },

   /**  C A N C E L   P U R C H A S E  */
   cancelPurchase: () => {
      Swal.fire({
         title: 'Cancelar Compra',
         text: "Al cancelar esta compra se eliminarán todos los productos agregados. ¿Desea continuar?",
         icon: 'warning',
         showCancelButton: true,
         confirmButtonText: '<i class="bx bx-eraser me-1"></i> Sí, Cancelar Compra',
         cancelButtonText: TextCancel,
      }).then((result) => {
         if (result.isConfirmed) {
            $.ajax({
               url: URL_PURCHASE + "op=cancelPurchase",
               type: 'POST',
               dataType: 'json',
               success: function(response) {
                  if (response.success) {
                     
                     POS.showAlert(response.success, response.message);
                     POS.clearForm();
                     modulePurchase.dataTablePurchaseDetails();
                     $('#formAddPurchase #cantidad, #formAddPurchase #btnSave, #formAddPurchase #btnCancel').prop('disabled', true);
                     $("#modalAddPurchase").modal('hide');
                     
                  } else {
                     $("#modalAddPurchase").modal('show');
                     POS.showAlert(response.success, response.message);
                  }
               },
            });
         } else $("#modalAddPurchase").modal('show');
      });
   },

   /** V A L I DA T E   P U R C H A S E   D E T A I L S  */
   validateModalPurchase: () => {
      $.post(URL_PURCHASE + "op=dataTablePurchaseDetails", e => {

         let response = JSON.parse(e);
         if (response.data != "") {
            let message = "El carrito cuenta con detalles de productos";
            POS.showAlert(false, message);
         } else {
            POS.clearForm();
            $('#modalAddPurchase').modal('hide');
         }
         
      });
   },

   /** T A B L E  P U R C H A S E S */
   tablePurchase: () => {
      $.ajax({
         url: URL_PURCHASE + "op=dataTable",
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
   
            showTable("#table-purchases", content);
         }
      });
	} 
}

$("form#formAddPurchase #search").on('select2:select', e => {
   const id = e.params.data.id;
   const url = URL_PURCHASE + "op=infoProduct";
   let cantidad = $("form#formAddPurchase #cantidad");

   const data = { id: id };

   $.post(url, data, info => { 
      const response = JSON.parse(info);
      $.each(response.data, (key, value) => $("#" + key).val(value));
   });

   cantidad.prop('disabled', false);
   // Posicionar el cursor
   cantidad.focus();
});


$("form#formAddPurchase #cantidad").on('input', () => {
   let precio   = $("form#formAddPurchase #precio_compra").val();
   let cantidad = $("form#formAddPurchase #cantidad").val() || 0;
   modulePurchase.calculateSubtotal(precio, cantidad);
});
