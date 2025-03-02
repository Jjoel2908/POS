let POS = new classPOS('Devolución', '#modalAddReturn');
const URL_RETURN = "../../../controllers/return.php?";

$(() => {

   moduleReturns.tableReturn();

   /**  F O R M  A D D   R E T U R N */
   $("form#formAddReturn").submit(function (event) {
      event.preventDefault();
   
      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            let formData = new FormData(this);
            $.ajax({
               url: URL_RETURN + "op=saveReturn",
               type: "POST",
               data: formData,
               contentType: false,
               processData: false,
               success: function (e) {
                  response = JSON.parse(e);
                  if (response.success) {
                     POS.showAlert(response.success, response.message);
                     moduleReturns.tableReturn();
                     $("#modalAddReturn").modal("toggle");
                  } else POS.showAlert(response.success, response.message); 
               },
            });
         }
      });
   });
});

let moduleReturns = {
   modalAddReturn: () => {
      $('#modalAddReturn').modal('toggle');

      $.post(URL_RETURN + "op=selectSales", sales => { 
         $("form#formAddReturn #id_venta").html(sales);
         POS.clearForm();
      })
   }, 

   /** T A B L E  R E T U R N S */
   tableReturn: () => {
      $.ajax({
         url: URL_RETURN + "op=dataTable",
         type: "GET",
         dataType: "JSON",
         success: (data) => {
   
            let content = initTable();
            content.columns =    [  { data: 'fecha',     title: 'Fecha' },
                                    { data: 'usuario',      title: 'Recibió' },
                                    { data: 'producto',   title: 'Producto' },
                                    { data: 'cantidad',      title: 'Cantidad' },
                                    { data: 'motivo',      title: 'Motivo' },
                                    { data: 'total',      title: 'Total' }];
            content.data = data;
            content.columnDefs  = [ { targets: [3], visible: false } ]
            content.createdRow = (row, data) => {
               $(`td:eq(1), td:eq(2), td:eq(3)`, row).addClass("text-start");
               $(`td:eq(4)`, row).addClass("text-end");
            };

            showTable("#table-returns", content);
         }
      });
	},
};

$('#formAddReturn #id_venta').on('select2:select', e => {
   const id = e.params.data.id;
   const url = URL_RETURN + "op=selectProducts";

   $.post(url, { id: id }, products => {
      $("form#formAddReturn #id_detail").html(products);
      $("form#formAddReturn #id_detail").val('');
   });
});

$('#formAddReturn #id_detail').on('select2:select', e => {
   const id = e.params.data.id;  
   const url = URL_RETURN + "op=selectQuantity";

   $.post(url, { id: id }, response => {

      let detail = JSON.parse(response);
      let max = parseInt(detail.quantity); 
      $('#formAddReturn #cantidad').attr('max', max); 
      $('#formAddReturn #cantidad').val(1); 
      $('#formAddReturn #precio').val(detail.price);
      $('#formAddReturn #id_producto').val(detail.id_producto);
   });
});

$('#formAddReturn #cantidad').on('input', () => {
   let max = parseInt($('#formAddReturn #cantidad').attr('max'));
   let currentValue = parseInt($('#formAddReturn #cantidad').val());

   if (currentValue > max) {
      $('#formAddReturn #cantidad').val(max);
   }
});