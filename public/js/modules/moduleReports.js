let POS = new classPOS('Reportes', '#modalDetalle');
let URL_REPORT = "../../../controllers/reports.php?";

$(() => {

   $('#date').daterangepicker({
      locale: {
         format: 'DD/MM/YYYY'
      },
      startDate: moment().subtract(29, 'days'),
      endDate: moment(),
      maxDate: moment(),
      ranges: {
         'Hoy': [moment(), moment()],
         'Ayer': [moment().subtract(1, 'days'), moment().subtract(1, 'days')],
         'Últimos 7 días': [moment().subtract(6, 'days'), moment()],
         'Últimos 30 días': [moment().subtract(29, 'days'), moment()],
         'Este mes': [moment().startOf('month'), moment().endOf('month')],
         'Mes pasado': [moment().subtract(1, 'month').startOf('month'), moment().subtract(1, 'month').endOf('month')]
      }
   });

   /**  F O R M   R E P O R T   P U R C H A S E  */
   $("form#formReportPurchase").submit(function (event) {
      event.preventDefault();

      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            modulePurchase.getReportPurchase(this);
         }
      });
   });

   /**  F O R M   R E P O R T   S A L E  */
   $("form#formReportSales").submit(function (event) {
      event.preventDefault();

      POS.validateForm(event, this).then((isValid) => {
         if (isValid) {
            moduleSales.getReportSales(this);
         }
      });
   });
});

/* ===================================================================================  */
/* -------------------------- R E P O R T   P U R C H A S E -------------------------- */
/* ===================================================================================  */
let modulePurchase = {

   getReportPurchase: form => {

      let formData = new FormData(form);
      $.ajax({
         url: URL_REPORT + "op=getReportPurchase",
         type: "POST",
         data: formData,
         dataType: "JSON",
         contentType: false,
         processData: false,
         success: (data) => {

            if (data.success) {

               /** D A T E */
               $('#day').html(data.date);

               /** W I D G E T S */
               $('#total').html(data.total);
                         
               /** T A B L E */
               let content = initTable();
               content.columns =    [  { data: 'id',        title: '#' },
                                       { data: 'fecha',     title: 'Fecha' },
                                       { data: 'proveedor',     title: 'Proveedor' },
                                       { data: 'usuario',   title: 'Comprador' },
                                       { data: 'total',     title: 'Total' },
                                       { data: 'btn',       title: 'Acciones' } ];
               content.order = [[0, "desc"]];
               content.buttons = [];
               content.data = data.table;
               content.createdRow = (row, data) => {
                  $(`td:eq(2), td:eq(3)`, row).addClass("text-start");
                  $('td:eq(4)', row).addClass("text-end");  
               };
      
               showTable("#table-report-purchase", content);

               $('#container-report').removeClass('bg-transparent shadow-0');
               $('#response').removeClass('d-none');
            } else {
               POS.showAlert(data.success, data.message);
               ($('#response').hasClass('d-none')) 
                     ? "" : $('#response').addClass('d-none');
               ($('#container-report').hasClass('bg-transparent shadow-0')) 
                     ? "" : $('#container-report').addClass('bg-transparent shadow-0');
            }
         }
      });

   },

   viewDetails: (id, total) => {

      $.ajax({
         url: URL_REPORT + "op=viewDetailsPurchase&id=" + id,
         type: "GET",
         dataType: "JSON",
         contentType: false,
         processData: false,
         success: (data) => {

            $('#detalle').html('Compra');
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
   }
}

/* ===================================================================================  */
/* ------------------------------ R E P O R T   S A L E S ------------------------------ */
/* ===================================================================================  */
let moduleSales = {

   getReportSales: form => {

      let formData = new FormData(form);
      $.ajax({
         url: URL_REPORT + "op=getReportSales",
         type: "POST",
         data: formData,
         dataType: "JSON",
         contentType: false,
         processData: false,
         success: (data) => {

            if (data.success) {

               /** D A T E */
               $('#day').html(data.date);

               /** W I D G E T S */
               $('#total').html(data.total);
               $('#earnings').html(data.earnings);
                         
               /** T A B L E */
               let content = initTable();
               content.columns =    [  { data: 'id',        title: '#' },
                                       { data: 'fecha',     title: 'Fecha' },
                                       { data: 'caja',     title: 'Caja' },
                                       { data: 'cliente',   title: 'Cliente' },
                                       { data: 'usuario',   title: 'Vendedor' },
                                       { data: 'total',     title: 'Total' },
                                       { data: 'btn',       title: 'Acciones' } ];
               content.order = [[0, "desc"]];
               content.buttons = [];
               content.data = data.table;
               content.createdRow = (row, data) => {
                  $(`td:eq(2), td:eq(3), td:eq(4)`, row).addClass("text-start");
                  $('td:eq(5)', row).addClass("text-end");  
               };
      
               showTable("#table-report-sales", content);

               $('#container-report').removeClass('bg-transparent shadow-0');
               $('#response').removeClass('d-none');
            } else {
               POS.showAlert(data.success, data.message);
               ($('#response').hasClass('d-none')) 
                     ? "" : $('#response').addClass('d-none');
               ($('#container-report').hasClass('bg-transparent shadow-0')) 
                     ? "" : $('#container-report').addClass('bg-transparent shadow-0');
            }
         }
      });

   },

   viewDetails: (id, total) => {

      $.ajax({
         url: URL_REPORT + "op=viewDetailsSale&id=" + id,
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
   }
}

/* ===================================================================================  */
/* ------------------------------ B E S T   S E L L I N G ------------------------------ */
/* ===================================================================================  */
let moduleBestSelling = {

   productBestSelling: () => {
      $.post(URL_REPORT + 'op=productBestSelling', e => {

         let response = JSON.parse(e);
         if (response.success) {

            var ctx = document.getElementById("products-best-selling").getContext("2d");

            var gradientStroke1 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke1.addColorStop(0, "#f093fb");
            gradientStroke1.addColorStop(1, "#f5576c");

            var gradientStroke2 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke2.addColorStop(0, "#00c6fb");
            gradientStroke2.addColorStop(1, "#005bea");

            var gradientStroke3 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke3.addColorStop(0, "#2af598");
            gradientStroke3.addColorStop(1, "#009efd");

            var gradientStroke4 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke4.addColorStop(0, "#ffb8c6");
            gradientStroke4.addColorStop(1, "#ff81a8");

            var gradientStroke5 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke5.addColorStop(0, "#8c7ae6");
            gradientStroke5.addColorStop(1, "#7158e2");

            var gradientStroke6 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke6.addColorStop(0, "#ffda79");
            gradientStroke6.addColorStop(1, "#ffd5ab");

            var gradientStroke7 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke7.addColorStop(0, "#55efc4");
            gradientStroke7.addColorStop(1, "#00b894");

            var gradientStroke8 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke8.addColorStop(0, "#fdcb6e");
            gradientStroke8.addColorStop(1, "#e17055");

            var gradientStroke9 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke9.addColorStop(0, "#74b9ff");
            gradientStroke9.addColorStop(1, "#0984e3");

            var gradientStroke10 = ctx.createLinearGradient(0, 0, 0, 300);
            gradientStroke10.addColorStop(0, "#ff7675");
            gradientStroke10.addColorStop(1, "#d63031");
            
            let product  = response.product;
            let quantity = response.quantity;

            new Chart(ctx, {
               type: 'pie',
               data: {
                  labels: product,
                  datasets: [{
                     backgroundColor: [
                           gradientStroke1,
                           gradientStroke2,
                           gradientStroke3,
                           gradientStroke4,
                           gradientStroke5,
                           gradientStroke6,
                           gradientStroke7,
                           gradientStroke8,
                           gradientStroke9,
                           gradientStroke10
                     ],
                     hoverBackgroundColor: [
                           gradientStroke1,
                           gradientStroke2,
                           gradientStroke3,
                           gradientStroke4,
                           gradientStroke5,
                           gradientStroke6,
                           gradientStroke7,
                           gradientStroke8,
                           gradientStroke9,
                           gradientStroke10
                     ],
                     data: quantity
                  }]
               },
               options: {
                  maintainAspectRatio: false,
                  legend: {
                     display: true,
                     position: 'right', // Aquí se define la posición de las etiquetas
                     align: 'start', // Opcional: alineación de las etiquetas
                     labels: {
                         boxWidth: 20, // Ancho de la caja de color de las etiquetas
                         padding: 20, // Espaciado entre las etiquetas
                         usePointStyle: true, // Usa el estilo de punto para las muestras de color
                         fontSize: 14 // Tamaño de letra de las etiquetas
                     }
                    },
                  tooltips: {
                     displayColors: false
                  }
               }
            });

            $('#info-products').html(response.data);

         } else POS.showAlert(response.success, response.message);
      })
   }
}