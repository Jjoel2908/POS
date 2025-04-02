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








let modulePurchase = {
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