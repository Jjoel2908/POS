let POS = new classPOS('Cliente', '#modalAddCustomer');
let URL = "../../../controllers/customer.php?";

$(() => {

   moduleCustomer.tableCustomer();
   
   $("form#formAddCustomer").submit(function (event) {
      event.preventDefault();

      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            let formData = new FormData(this);
            $.ajax({
            url: URL + "op=saveCustomer",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (e) {
               response = JSON.parse(e);
               if (response.success) {
                  POS.showAlert(response.success, response.message);
                  moduleCustomer.tableCustomer();
                  $("#modalAddCustomer").modal("toggle");
               } else POS.showAlert(response.success, response.message);
            },
            });
         }
      });
   });
});

let moduleCustomer = {

   /** M O D A L  A D D  */
   modalAddCustomer: () => {
      POS.viewModal();
   },

   /** T A B L E  */
   tableCustomer: () => {
      $.ajax({
         url: URL + "op=dataTable",
         type: "GET",
         dataType: "JSON",
         success: (data) => {
   
            let content         = initTable();
            content.columns = [  { data: 'id',   title: '#' },
                                    { data: 'nombre',   title: 'Nombre' },
                                    { data: 'telefono', title: 'Teléfono' },
                                    { data: 'correo',   title: 'Correo' },
                                    { data: 'estado',   title: 'Estado' },
                                    { data: 'btn',      title: 'Acciones' } ];
            content.order      = [[0, "desc"]];
            content.columnDefs = [ { targets: [0], visible: false } ];
            content.data       = data;
            content.createdRow = (row, data) => {
               $(`td:eq(0), td:eq(1), td:eq(2)`, row).addClass("text-start");
            };
   
            showTable("#table-customers", content);
         }
      });
	},

   /** U P D A T E */
   updateCustomer: id => {
      let url = URL + "op=updateCustomer";

      POS.update(id, url);
   },

   /** D E L E T E */
   deleteCustomer: (id, nombre) => {
      let url = URL + "op=deleteCustomer";
      POS.delete(id, nombre, url, moduleCustomer.tableCustomer);
   }
}