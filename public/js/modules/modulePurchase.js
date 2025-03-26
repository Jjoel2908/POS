$(() => {
   const formdata = new FormData();

   submitForm(formdata, "droplist", 'Producto', (data) => {
      $("#formPurchase #search").html(data);
      clearForm('#modalRegister');
   }, false);
});

let modulePurchase = {
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
   const total = calculateTotal(precio, cantidad);

   $("form#formAddPurchase #sub_total").val(total);
});