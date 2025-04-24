const urlController = "../../../controllers/";
const moduleReport = $(".card").data("module");
let modalId;

const columnsEndTable = ["Precio Compra", "Precio Venta", "Precio", "Subtotal", "Total"];
const columnsCenterTable = ["Fecha de Creación", "Fecha de Alta", "Fecha Inicio", "Fecha", "Hora Inicio", "Hora", "Teléfono", "Código", "Cantidad", "Monto Inicial", "Acciones", "Estado"];
$(() => {

   /**  D A T E   F I L T E R  */
   $("#date").daterangepicker({
      locale: {
         format: "DD/MM/YYYY",
      },
      startDate: moment(),
      endDate: moment(),
      maxDate: moment(),
      ranges: {
         Hoy: [moment(), moment()],
         Ayer: [moment().subtract(1, "days"), moment().subtract(1, "days")],
         "Semana actual": [moment().startOf("isoWeek"), moment()],
         "Este mes": [moment().startOf("month"), moment().endOf("month")],
         "Último año": [moment().startOf("year"), moment().endOf("year")]
      },
   });

   /**  F O R M  */
   $("form").submit(function (event) {
      event.preventDefault();

      if (validateForm(event, this)) {
         try {
            /** Cambiar el botón de búsqueda por el botón de carga */
            $("#searchContainer").hide(); /** Oculta el botón de búsqueda */
            $("#loadingContainer").show(); /** Muestra el botón de carga */

            /** Cargamos la información */
            loadDataTable("#module-table-report", moduleReport);
            $("#modalRegister").modal("toggle");
         } catch (error) {
            console.log("Ocurrió un error al generar el reporte en el módulo " + moduleReport);
         } finally {
            /** Volver a mostrar el botón de búsqueda y ocultar el de carga */
            $("#loadingContainer").hide();
            $("#searchContainer").show();
         }
      }
   });
});

/** Valida un formulario antes de enviarlo.
 * Si faltan campos obligatorios, muestra una alerta y evita el envío.
 *
 * @param {Event} event - El evento submit o click que dispara la validación.
 * @param {HTMLFormElement} form - El formulario que se va a validar.
 * @returns {boolean} - Retorna true si el formulario es válido, false si hay errores.
 */
const validateForm = (event, form) => {
   if (!form.checkValidity()) {
      event.preventDefault();
      event.stopPropagation();
      showAlert(false, "Debe completar la información obligatoria");
      form.classList.add("was-validated");
      return false;
   }
   return true;
};

/** Carga datos en una tabla DataTable de manera dinámica.
 * Obtiene los datos desde el servidor usando `fetch` y renderiza las columnas automáticamente.
 *
 * @param {string} tableId - Selector de la tabla donde se mostrarán los datos.
 * @param {string} module - Nombre del módulo para la solicitud al controlador.
 * @param {int} registerId - Identificador si se desea buscar registros de algo en particular.
 */
const loadDataTable = async (tableId, module, registerId = null) => {
   try {
       /** Creamos un objeto FormData para enviar la solicitud */
       let formData = new FormData();
       formData.append("module", module);
       formData.append("operation", "dataTable");
       formData.append("registerId", registerId);

       /** Enviamos la solicitud al servidor */
       const response = await fetch(urlController, {
           method: "POST",
           body: formData,
       });

       /** Verificamos si la respuesta es válida */
       if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

       /** Convertimos la respuesta a JSON */
       const data = await response.json();

       if (data.length === 0) {
           showAlert(false, "No hay información disponibles en el rango de fechas seleccionado.");
           return;
       }

          /** D A T E */
          $('#day').html(data.date);

          /** W I D G E T S */
          $('#total').html(data.total);
          $('#earnings').html(data.earnings);

       /** Generamos dinámicamente las columnas basadas en las claves del primer objeto */
       let columns = Object.keys(data[0]).map((key) => ({
           data: key,
           title: key,
       }));

       let content = initTable();
       content.columns = columns;
       content.data = data;

       /** Aplica alineaciones dinámicas a las filas */
       content.createdRow = (row, rowData) => {
       Object.keys(rowData).forEach((key, index) => {
           if (columnsCenterTable.includes(key)) {
               $(`td:eq(${index})`, row).addClass("text-center");
           } else if (columnsEndTable.includes(key)) {
               $(`td:eq(${index})`, row).addClass("text-end");
           } else {
               $(`td:eq(${index})`, row).addClass("text-start");
           }
       });
       };

       showTable(tableId, content);

       $('#container-report').removeClass('bg-transparent shadow-0');
               $('#response').removeClass('d-none');
   } catch (error) {
      POS.showAlert(data.success, data.message);
      ($('#response').hasClass('d-none')) 
            ? "" : $('#response').addClass('d-none');
      ($('#container-report').hasClass('bg-transparent shadow-0')) 
            ? "" : $('#container-report').addClass('bg-transparent shadow-0');
       console.error("Error en la función loadDataTable del archivo moduleReports:", error);
   }
};







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