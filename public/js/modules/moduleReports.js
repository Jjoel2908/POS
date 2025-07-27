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
                loadDataTableReport("#module-table-report", currentModule);
            } catch (error) {
                console.log("Ocurrió un error al generar el reporte en el módulo " + currentModule);
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
const loadDataTableReport = async (tableId, module, registerId = null) => {
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

        /** Cargamos los datos según el módulo correspondiente */
        if (module === "ReporteGeneral")
            await loadReportGeneralInView(data);
        else 
            await loadTableInView(tableId, data);

    } catch (error) {
        showAlert(false, "Ocurrió un error al cargar los datos. Por favor, inténtalo de nuevo más tarde.");
    }
};

/** Inicializa una tabla DataTable con configuración básica.
 * @param {string} tableId - Selector de la tabla a inicializar.
 * @param {object} data - Datos a mostrar en la tabla.
 * @returns {object} - Objeto de configuración de la tabla.
 */
const loadTableInView = async (tableId, data) => {
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

    let content         = initTable();
        content.columns = columns;
        content.data    = dataTable;

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
}

/**  Carga el reporte general en la vista.
 * Muestra un resumen de ventas, compras, gastos y otros datos relevantes.
 * @param {Object} data - Datos del reporte general.
 * @property {number} data.total_ventas_contado - Total de ventas al contado.
 * @property {number} data.total_ventas_credito - Total de ventas a crédito.
 * @property {number} data.total_pendiente_cobro - Total pendiente por cobrar
 * @property {number} data.total_compras - Total de compras.
 * @property {number} data.total_gastos - Total de gastos.
 * @property {number} data.venta_neta - Venta neta.
 */
const loadReportGeneralInView = async (data) => {
    const container           = document.getElementById("response-widgets");
          container.innerHTML = '';

    try {
        const widgets = [
            {
                titulo: "Ventas a Contado",
                valor: data.total_ventas_contado,
                icono: "fa-solid fa-money-bill-trend-up",
                bgColor: "light-success",
                textColor: "text-success"
            },
            {
                titulo: "   Ventas a Crédito",
                valor: data.total_ventas_credito,
                icono: "fa-solid fa-credit-card",
                bgColor: "light-primary",
                textColor: "text-primary"
            },
            {
                titulo: "Pendiente por Cobrar",
                valor: data.total_pendiente_cobro,
                icono: "fa-solid fa-hand-holding-dollar",
                bgColor: "light-warning",
                textColor: "text-warning"
            },
            {
                titulo: "Total de Compras",
                valor: data.total_compras,
                icono: "fa-solid fa-cart-shopping",
                bgColor: "light-info",
                textColor: "text-info"
            },
            {
                titulo: "Total de Gastos",
                valor: data.total_gastos,
                icono: "fa-solid fa-coins",
                bgColor: "light-danger",
                textColor: "text-danger"
            },
            {
                titulo: "Venta Neta",
                valor: data.venta_neta,
                icono: "fa-solid fa-scale-balanced",
                bgColor: "light-dark",
                textColor: "text-dark"
            }
        ];

        widgets.forEach(widget => {
            container.innerHTML += createWidgetCard(widget);
        });

        await expensesPerType(data.gastos_por_tipo);
        await renderTopProducts(data.top_productos);

        $('#response').removeClass('d-none');
    } catch (error) {
        showAlert(false, "Ocurrió un error al cargar el reporte general. Por favor, inténtalo de nuevo más tarde.");
    }
};

/** Crea una tarjeta de widget para mostrar información resumida.
 * @param {Object} params - Parámetros para la tarjeta.
 * @param {string} params.titulo - Título del widget.
 * @param {number|string} params.valor - Valor a mostrar en el widget.
 * @param {string} params.icono - Clase del icono a mostrar.
 * @param {string} [params.bgColor='light-success'] - Color de fondo del widget (opcional).
 * @param {string} [params.textColor='text-success'] - Color del texto del widget (opcional).
 * @returns {string} - HTML de la tarjeta del widget.
 */
const createWidgetCard = ({ titulo, valor, icono, bgColor = 'light-success', textColor = 'text-success' }) => {
    return `
        <div class="col pe-0">
            <div class="card radius-10">
                <div class="card-body">
                <div class="text-center">
                    <div class="widgets-icons-3 rounded-circle mx-auto bg-${bgColor} ${textColor} mb-3">
                    <i class="${icono}"></i>
                    </div>
                    <h5 class="my-1">$<span>${valor}</span></h5>
                    <p class="mb-0 mt-1">${titulo}</p>
                </div>
                </div>
            </div>
        </div>
    `;
};

/** * Genera un gráfico de tipo "pie" para mostrar los gastos por tipo.
 * Utiliza colores dinámicos y muestra los datos en un canvas con ID "chart-expenses-type".
 *
 * @param {Array} expenses - Array de objetos con información de gastos, cada objeto debe tener 'tipo' y 'total'.
 */
const expensesPerType = async (expenses) => {
    if (expenses.length > 0) {
        const ctx = document.getElementById("chart-expenses-type").getContext("2d");

        /** Generar colores dinámicos (gradientes simples o sólidos) */
        const generateColors = (n) => {
            const baseColors = [
                "#FF6384", "#36A2EB", "#FFCE56", "#4BC0C0",
                "#9966FF", "#FF9F40", "#00c292", "#e44a00",
                "#607d8b", "#795548"
            ];
            const colors = [];
            for (let i = 0; i < n; i++) {
                colors.push(baseColors[i % baseColors.length]);
            }
            return colors;
        };

        const labels = expenses.map(item => item.tipo);
        const data   = expenses.map(item => parseFloat(item.total.replace(",", "")));
        const colors = generateColors(expenses.length);

        /** Eliminar gráfico anterior si existe */
        if (window.expensesChart) {
            window.expensesChart.destroy();
        }

        /** Crear nuevo gráfico */
        window.expensesChart = new Chart(ctx, {
            type: "pie",
            data: {
                labels,
                datasets: [{
                    data,
                    backgroundColor: colors,
                    hoverBackgroundColor: colors
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            return `${context.label}: $${value.toLocaleString("es-MX", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                            })}`;
                        }
                    }
                }
                }
            }
        });

        $("#message-expenses-type").addClass('d-none').removeClass('mt-4');
        $("#container-expenses-chart").removeClass('d-none');
    } else {
        $("#message-expenses-type").html("No hay información para mostrar");
        $("#message-expenses-type").removeClass('d-none').addClass('mt-4');
        $("#container-expenses-chart").addClass('d-none');
    }
};

/** Renderiza un gráfico de barras horizontales para mostrar los productos más vendidos.
 * Utiliza el canvas con ID "chart-top-products" y muestra los datos de productos vendidos.
 * @param {Array} productos - Array de objetos con información de productos, cada objeto debe tener 'producto' y 'total_vendido'.
 */
const renderTopProducts = async (productos) => {
    if (productos.length > 0) {
        const ctx = document.getElementById("chart-top-products").getContext("2d");

        const labels = productos.map(p => p.producto);
        const data = productos.map(p => parseInt(p.total_vendido));

        const backgroundColors = [
            "#008cff", "#15ca20", "#ff3e3e", "#ffb02c", "#a461d8"
        ];

        if (window.topProductsChart) {
            window.topProductsChart.destroy();
        }

        window.topProductsChart = new Chart(ctx, {
            type: "doughnut",
            data: {
                labels,
                datasets: [{
                    label: "Cantidad vendida",
                    data,
                    backgroundColor: backgroundColors,
                    borderRadius: 8
                }]
            },
            options: {
                maintainAspectRatio: false,
                plugins: {
                legend: {
                    display: true,
                    position: 'bottom'
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            const value = context.parsed;
                            return `${context.label}: $${value.toLocaleString("es-MX", {
                            minimumFractionDigits: 2,
                            maximumFractionDigits: 2
                            })}`;
                        }
                    }
                }
                }
            }
        });

        $("#message-top-products").addClass('d-none').removeClass('mt-4');
        $("#container-top-products-chart").removeClass('d-none');
    } else {
        $("#message-top-products").html("No hay productos vendidos en el periodo");
        $("#message-top-products").removeClass('d-none').addClass('mt-4');
        $("#container-top-products-chart").addClass('d-none');
    }
};

/** Ejecuta una acción adicional complementaria a un proceso principal.
 *
 * Esta función se utiliza para realizar tareas secundarias que deben llevarse a cabo
 * después (o como consecuencia) de una acción principal, como actualizar la interfaz,
 * mostrar mensajes, realizar validaciones extra o disparar eventos personalizados.
 *
 * Su propósito es mantener el código modular y evitar mezclar responsabilidades dentro
 * de la lógica principal.
 */
const runAdditionalStep = () => {
    /** Cargamos la información */
    loadDataTableReport("#module-table-report", currentModule);
}