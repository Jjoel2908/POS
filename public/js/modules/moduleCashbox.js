let POS = new classPOS('Caja', '#modalAddCashbox');
const URL = "../../../controllers/cashbox.php?";
const URL_CASHBOX = "../../../controllers/handleCashbox.php?";

$(() => {

   moduleCashbox.tableCashbox();
   
   /**  A D D   C A S H B O X  */
   $("form#formAddCashbox").submit(function (event) {
      event.preventDefault();
      
      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            let formData = new FormData(this);
            $.ajax({
            url: URL + "op=saveCashbox",
            type: "POST",
            data: formData,
            contentType: false,
            processData: false,
            success: function (e) {
               response = JSON.parse(e);
               if (response.success) {
                  POS.showAlert(response.success, response.message);
                  moduleCashbox.tableCashbox();
                  $("#modalAddCashbox").modal("toggle");
               } else POS.showAlert(response.success, response.message);
            },
            });
         }
      });
   });
});

let moduleCashbox = {

   /** M O D A L  A D D  C A S H B O X */
   modalAddCashbox: () => {
      POS.viewModal();
   },

   /** M O D A L  A C T I V E  C A S H B O X */
   modalActiveCashbox: () => {
      $('#modalActiveCashbox').modal('toggle');
   },

   /** T A B L E  C A S H B O X S */
   tableCashbox: () => {
      $.ajax({
         url: URL + "op=dataTable",
         type: "GET",
         dataType: "JSON",
         success: (data) => {
   
            let content = initTable();
            content.columns =    [  { data: 'id',   title: '#' },
                                    { data: 'caja',     title: 'Caja' },
                                    { data: 'estatus',   title: 'Estado' },
                                    { data: 'btn',      title: 'Acciones' } ];
            content.data = data;
            content.createdRow = (row, data) => {
               $(`td:eq(1)`, row).addClass("text-start");
            };
   
            showTable("#table-cashboxes", content);
         }
      });
	},

   /** U P D A T E  C A S H B O X */
   updateCashbox: (id) => {
      let url = URL + "op=updateCashbox";
      POS.update(id, url);
   },

   /** D E L E T E  C A S H B O X */
   deleteCashbox: (id, nombre) => {
      let url = URL + "op=deleteCashbox";
      POS.delete(id, nombre, url, moduleCashbox.tableCashbox);
   },

   /** O P E N  C A S H B O X */
   openCashbox: (id, nombre) => {
  
      Swal.fire( {
         title: `<h3 class="mt-3">Abrir Caja</h3>`,
         html: `<p class="font-20 mb-2">Indique el monto inicial para la caja</p>`,
         confirmButtonText: `<i class="bx bx-box me-1"></i> Abrir Caja`,
         cancelButtonText: TextCancel,
         showCancelButton: true,
         input: 'number',
         inputPlaceholder: 'Escribe el Monto Inicial',
         inputAttributes: {
            step: '0.01', // Esto permite introducir números con decimales
            min: '0' // Esto asegura que los números negativos no sean permitidos
         },
         inputValidator: (value) => {
             return new Promise((resolve) => {
                 if (!value) {
                     resolve('Debe proporcionar el Monto Inicial');
                 } else {
                     resolve();
                 }
             });
         }
      } ).then((result) => {
         if (result.isConfirmed) {

            const formdata = new FormData();
            formdata.append("id", id);
            formdata.append("nombre", nombre);
            formdata.append("monto_inicial", result.value);
  
            $.ajax({
               url: URL_CASHBOX + "op=openCashbox",
               type: "POST",
               data: formdata,
               contentType: false,
               processData: false,
               success: (e) => {
                  const response = JSON.parse(e);
                  POS.showAlert(response.success, response.message);
                  moduleCashbox.tableCashbox();
               },
            });
         }
      });
   },

   /** M O D A L  O P E N  C A S H B O X */
   modalOpenCashbox: () => {
      $.ajax({
         url: URL_CASHBOX + "op=dataTableCashbox",
         type: "GET",
         dataType: "JSON",
         success: (data) => {
   
            let content = initTable();
            content.buttons = [];
            content.columns =    [  { data: 'caja',    title: 'Caja' },
                                    { data: 'fecha',   title: 'Fecha Inicio' },
                                    { data: 'hora',    title: 'Hora Inicio' },
                                    { data: 'monto',   title: 'Monto Inicial' },
                                    { data: 'ventas',  title: 'Ventas'},
                                    { data: 'btn',      title: 'Acciones' } ];
            content.data = data;
            content.createdRow = (row, data) => {
               $(`td:eq(0)`, row).addClass("text-start");
            };
   
            showTable("#table-open-cashbox", content);
         }
      });
      $('#modalOpenCashbox').modal('toggle');
   },

   /** C L O S E  C A S H B O X */
   closeCashbox: (id, id_caja, nombre_caja, monto_fin) => {

      $.post(URL_CASHBOX + "op=infoArqueo", {id: id, monto: monto_fin}, totalCreditSale => { 

         let text  = `<p class="font-20 mb-3">Asegúrese que el arqueo de caja sea correcto antes de confirmar.</p>`;
         let table = `  <table class="font-size-15 w-100 table-bordered">
                           <tr>
                              <td class="p-2 bg-primary text-white border-dark">Total de Ventas</td>
                           </tr>
                           <tr>
                              <td class="p-2 border-dark">${totalCreditSale}</td>
                           </tr>
                        </table>`;

         Swal.fire( {
            title: `<h3 class="mt-3">Cerrar Caja</h3>`,
            html: text + table,
            confirmButtonText: `<i class="bx bx-box me-1"></i> Cerrar Caja`,
            cancelButtonText: TextCancel,
            showCancelButton: true,
         } ).then((result) => {
            if (result.isConfirmed) {

               const formdata = new FormData();
               formdata.append("id", id);
               formdata.append("id_caja", id_caja);
               formdata.append("nombre", nombre_caja);
   
               $.ajax({
                  url: URL_CASHBOX + "op=closeCashbox",
                  type: "POST",
                  data: formdata,
                  contentType: false,
                  processData: false,
                  success: (e) => {
                     const response = JSON.parse(e);

                     if (response.success) {

                        POS.showAlert(response.success, response.message);
                        moduleCashbox.tableCashbox();
                        $('#modalOpenCashbox').modal('toggle');

                     } else POS.showAlert(response.success, response.message);
                  },
               });
            }
         });
      });      
   },
}