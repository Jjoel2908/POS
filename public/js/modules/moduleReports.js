const urlController = "../../../controllers/";
const moduleReport = $(".card").data("module");
let modalId;

const columnsEndTable = ["Precio Compra", "Precio Venta", "Precio", "Monto", "Subtotal", "Total"];
const columnsCenterTable = ["Folio", "Fecha de Creación", "Fecha de Alta", "Fecha Inicio", "Fecha", "Hora Inicio", "Hora", "Teléfono", "Código", "Cantidad", "Monto Inicial", "Acciones", "Estatus", "Tipo de Venta"];

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
            "Hoy": [moment(), moment()],
            "Ayer": [moment().subtract(1, "days"), moment().subtract(1, "days")],
            "Semana actual": [moment().startOf("isoWeek"), moment()],
            "Semana pasada": [moment().subtract(1, "week").startOf("isoWeek"), moment().subtract(1, "week").endOf("isoWeek")],
            "Este mes": [moment().startOf("month"), moment().endOf("month")],
            "Mes pasado": [moment().subtract(1, "month").startOf("month"), moment().subtract(1, "month").endOf("month")],
            "Este año": [moment().startOf("year"), moment().endOf("year")],
            "Año pasado": [moment().subtract(1, "year").startOf("year"), moment().subtract(1, "year").endOf("year")],
        }
    });

    /**  F O R M  */
    $("form#formReports").submit(function (event) {
        event.preventDefault();

        if (validateForm(event, this)) {
            try {
                /** Cambiar el botón de búsqueda por el botón de carga */
                $("#searchContainer").hide(); /** Oculta el botón de búsqueda */
                $("#loadingContainer").show(); /** Muestra el botón de carga */

                /** Cargamos la información */
                loadDataTable("#module-table-report", moduleReport);
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

        /** Obtenemos el valor del campo de fecha */
        const dateRange = $("form#formReports #date").val();
        formData.append("dateRange", dateRange);

        /** Enviamos la solicitud al servidor */
        const response = await fetch(urlController, {
            method: "POST",
            body: formData,
        });

        /** Verificamos si la respuesta es válida */
        if (!response.ok) throw new Error(`HTTP error! status: ${response.status}`);

        /** Convertimos la respuesta a JSON */
        const data = await response.json();

        /** Verificamos si la respuesta está vacía */
        if (data.length === 0) {
            $('#response').addClass('d-none');
            $('#container-report').addClass('bg-transparent shadow-0');
            $('#container-report').removeClass('border-start border-end border-0 border-3 border-primary');
            return showAlert(false, "El sistema no encontró resultados para los filtros aplicados.");
        }

        /** Generamos dinámicamente las columnas basadas en las claves del primer objeto */
        const dataTable = data?.table ?? data;
        let columns = Object.keys(dataTable[0]).map((key) => ({
            data: key,
            title: key,
        }));

        let content = initTable();
        content.columns = columns;
        content.data = dataTable;

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

        /** Actualizamos el total en el widget */
        data?.total ? $('#total').html(data.total) : "";
        $('#container-report').removeClass('bg-transparent shadow-0');
        $('#container-report').addClass('border-start border-end border-0 border-3 border-primary');
        $('#response').removeClass('d-none');
    } catch (error) {
        showAlert(false, "Ocurrió un error al cargar los datos. Por favor, inténtalo de nuevo más tarde.");
    }
};

/** Carga y muestra los detalles de una compra/venta ya registrada en el sistema.
 * Muestra los detalles en un modal si se pasa un ID de registro.
 *
 * @param {string} currentModule - Nombre del módulo desde donde se llama la función.
 * @param {number|null} registerId - ID del registro a cargar (opcional).
 * @param {string|null} date - Fecha en formato 'DD/MM/YYYY' (opcional).
 * @param {string} modalSelector - Selector del modal a mostrar (por defecto '#modalViewDetails').
 */
const loadRegisteredDetails = async (currentModule, registerId = null, date = null, modalSelector = "#modalViewDetails", tableSelector = "#table-details") => {
    const formattedDate = formatDateForTitle(date);
    const title = getTitleByModuleAndDate(currentModule, formattedDate);

    /** Asignamos el título para el modal */
    $(`${modalSelector} #modalTitle`).html(title);

    /** Cargamos la información */
    loadDataTable(tableSelector, currentModule, registerId);

    /** Mostramos el modal */
    $(modalSelector).modal("toggle");
};