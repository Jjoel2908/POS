const TextDelete = '<i class="bx bx-eraser me-1"></i> Sí, Eliminar';
const TextConfirm = '<i class="bx bx-check-circle me-1"></i> Sí, Proceder';
const TextCancel = '<i class="bx bx-arrow-back me-1"></i> No, Salir';
const TextBack = '<i class="bx bx-arrow-back me-1"></i> Regresar';
const colorColumns = "#7128a3";

$(document).ready(function () {
  $(".select").each(function () {
    var $select = $(this);
    var $modal = $select.closest(".modal");

    var placeholderText = "Selecciona una opción";

    if ($modal.length) {
      $select.css("width", "100%").select2({
        dropdownParent: $modal,
        placeholder: placeholderText,
        allowClear: true,
        // ORDENAR REGISTROS DE SELECTS sorter: data => data.sort((a, b) => a.text.localeCompare(b.text))
      });
    }
  });
});

/** Formato de tabla
 * @param {string} idTable Identificador de la tabla a imprimir (.class, #id)
 * @param {object} content Configuración a aplicar a la tabla asi como el contenido (const initTable)
 */
const showTable = (idTable, content) => {
  $(idTable).css("width", "100%").html("").empty().DataTable(content);
};

/**
 * Inicialización de tabla con ( DataTable )
 */
const initTable = () => {
  return {
    dom: '<"top"Bf>rt<"bottom"ip>',
    processing: true,
    destroy: true,
    responsive: true,
    autoWidth: true,
    lengthChange: true,
    pageLength: 10,
    language: {
      aria: {
        sortAscending: "Activar para ordenar la columna de manera ascendente",
        sortDescending: "Activar para ordenar la columna de manera descendente",
      },
      autoFill: {
        cancel: "Cancelar",
        fill: "Rellene todas las celdas con <i>%d</i>",
        fillHorizontal: "Rellenar celdas horizontalmente",
        fillVertical: "Rellenar celdas verticalmente",
      },
      buttons: {
        collection: "Colección",
        colvis: "Visibilidad",
        colvisRestore: "Restaurar visibilidad",
        copy: "Copiar",
        copyKeys:
          "Presione ctrl o u2318 + C para copiar los datos de la tabla al portapapeles del sistema. <br /> <br /> Para cancelar, haga clic en este mensaje o presione escape.",
        copySuccess: {
          1: "Copiada 1 fila al portapapeles",
          _: "Copiadas %d fila al portapapeles",
        },
        copyTitle: "Copiar al portapapeles",
        csv: "CSV",
        excel: "Excel",
        pageLength: {
          "-1": "Mostrar todas las filas",
          _: "Mostrar %d filas",
        },
        pdf: "PDF",
        print: "Imprimir",
        createState: "Crear Estado",
        removeAllStates: "Borrar Todos los Estados",
        removeState: "Borrar Estado",
        renameState: "Renombrar Estado",
        savedStates: "Guardar Estado",
        stateRestore: "Restaurar Estado",
        updateState: "Actualizar Estado",
      },
      infoThousands: ",",
      loadingRecords: "Cargando...",
      paginate: {
        first: "Primero",
        last: "Último",
        next: "Siguiente",
        previous: "Anterior",
      },
      processing: "Procesando...",
      search: "Buscar:",
      searchBuilder: {
        add: "Añadir condición",
        button: {
          0: "Constructor de búsqueda",
          _: "Constructor de búsqueda (%d)",
        },
        clearAll: "Borrar todo",
        condition: "Condición",
        deleteTitle: "Eliminar regla de filtrado",
        leftTitle: "Criterios anulados",
        logicAnd: "Y",
        logicOr: "O",
        rightTitle: "Criterios de sangría",
        title: {
          0: "Constructor de búsqueda",
          _: "Constructor de búsqueda (%d)",
        },
        value: "Valor",
        conditions: {
          date: {
            after: "Después",
            before: "Antes",
            between: "Entre",
            empty: "Vacío",
            equals: "Igual a",
            not: "Diferente de",
            notBetween: "No entre",
            notEmpty: "No vacío",
          },
          number: {
            between: "Entre",
            empty: "Vacío",
            equals: "Igual a",
            gt: "Mayor a",
            gte: "Mayor o igual a",
            lt: "Menor que",
            lte: "Menor o igual a",
            not: "Diferente de",
            notBetween: "No entre",
            notEmpty: "No vacío",
          },
          string: {
            contains: "Contiene",
            empty: "Vacío",
            endsWith: "Termina con",
            equals: "Igual a",
            not: "Diferente de",
            startsWith: "Inicia con",
            notEmpty: "No vacío",
            notContains: "No Contiene",
            notEndsWith: "No Termina",
            notStartsWith: "No Comienza",
          },
          array: {
            equals: "Igual a",
            empty: "Vacío",
            contains: "Contiene",
            not: "Diferente",
            notEmpty: "No vacío",
            without: "Sin",
          },
        },
        data: "Datos",
      },
      searchPanes: {
        clearMessage: "Borrar todo",
        collapse: {
          0: "Paneles de búsqueda",
          _: "Paneles de búsqueda (%d)",
        },
        count: "{total}",
        emptyPanes: "Sin paneles de búsqueda",
        loadMessage: "Cargando paneles de búsqueda",
        title: "Filtros Activos - %d",
        countFiltered: "{shown} ({total})",
        collapseMessage: "Colapsar",
        showMessage: "Mostrar Todo",
      },
      select: {
        cells: {
          1: "1 celda seleccionada",
          _: "%d celdas seleccionadas",
        },
        columns: {
          1: "1 columna seleccionada",
          _: "%d columnas seleccionadas",
        },
        rows: {
          1: "1 fila seleccionada",
          _: "%d filas seleccionadas",
        },
      },
      thousands: ",",
      datetime: {
        previous: "Anterior",
        hours: "Horas",
        minutes: "Minutos",
        seconds: "Segundos",
        unknown: "-",
        amPm: ["am", "pm"],
        next: "Siguiente",
        months: {
          0: "Enero",
          1: "Febrero",
          10: "Noviembre",
          11: "Diciembre",
          2: "Marzo",
          3: "Abril",
          4: "Mayo",
          5: "Junio",
          6: "Julio",
          7: "Agosto",
          8: "Septiembre",
          9: "Octubre",
        },
        weekdays: [
          "Domingo",
          "Lunes",
          "Martes",
          "Miércoles",
          "Jueves",
          "Viernes",
          "Sábado",
        ],
      },
      editor: {
        close: "Cerrar",
        create: {
          button: "Nuevo",
          title: "Crear Nuevo Registro",
          submit: "Crear",
        },
        edit: {
          button: "Editar",
          title: "Editar Registro",
          submit: "Actualizar",
        },
        remove: {
          button: "Eliminar",
          title: "Eliminar Registro",
          submit: "Eliminar",
          confirm: {
            _: "¿Está seguro que desea eliminar %d filas?",
            1: "¿Está seguro que desea eliminar 1 fila?",
          },
        },
        multi: {
          title: "Múltiples Valores",
          restore: "Deshacer Cambios",
          noMulti:
            "Este registro puede ser editado individualmente, pero no como parte de un grupo.",
          info: "Los elementos seleccionados contienen diferentes valores para este registro. Para editar y establecer todos los elementos de este registro con el mismo valor, haga click o toque aquí, de lo contrario conservarán sus valores individuales.",
        },
        error: {
          system:
            'Ha ocurrido un error en el sistema (<a target="\\" rel="\\ nofollow" href="\\"> Más información</a>).',
        },
      },
      decimal: ".",
      emptyTable: "No hay datos disponibles en la tabla",
      zeroRecords: "No se encontraron coincidencias",
      info: "Mostrando _START_ a _END_ de _TOTAL_ entradas",
      infoFiltered: "(Filtrado de _MAX_ total de entradas)",
      lengthMenu: "Mostrar _MENU_ entradas",
      stateRestore: {
        removeTitle: "Eliminar",
        creationModal: {
          search: "Buscar",
          button: "Crear",
          columns: {
            search: "Columna de búsqueda",
            visible: "Columna de visibilidad",
          },
          name: "Nombre:",
          order: "Ordenar",
          paging: "Paginar",
          scroller: "Posición de desplazamiento",
          searchBuilder: "Creador de búsquedas",
          select: "Selector",
          title: "Crear nuevo",
          toggleLabel: "Incluye:",
        },
        duplicateError: "Ya existe un valor con el mismo nombre",
        emptyError: "No puede ser vacío",
        emptyStates: "No se han guardado",
        removeConfirm: "Esta seguro de eliminar %s?",
        removeError: "Fallo al eliminar",
        removeJoiner: "y",
        removeSubmit: "Eliminar",
        renameButton: "Renombrar",
        renameLabel: "Nuevo nombre para %s:",
        renameTitle: "Renombrar",
      },
      infoEmpty: "No hay datos para mostrar",
    },
    columnDefs: [],
    order: [],
    buttons: [
      {
        extend: "excelHtml5",
        text: '<i class="fa-solid fa-file-excel"></i>',
      },
      {
        extend: "pdfHtml5",
        text: '<i class="fa-solid fa-file-pdf"></i>',
        customize: function (doc) {
          /** Recorrer las tablas en el documento y hacer las modificaciones necesarias */
          doc.content.forEach(function (contentItem) {
            if (contentItem.table) {
              /** Eliminar la última columna de cada fila */
              contentItem.table.body = contentItem.table.body.map(row => row.slice(0, -1));

              /** Asegurarse de que cada tabla ocupe el 100% del ancho */
              contentItem.table.widths = Array(contentItem.table.body[0].length).fill("*"); // Establecer todos los anchos de las columnas al 100% de la página

              /** Cambiar el color de fondo del encabezado de la tabla */
              contentItem.table.headerRows = 1;
              contentItem.table.body[0].forEach(function (cell) {
                cell.fillColor = colorColumns; // Color de fondo del encabezado
              });

              contentItem.table.body.forEach(function (row, rowIndex) {
                if (rowIndex > 0) {
                  /** No modificar el encabezado, solo las filas de datos */
                  row.forEach(function (cell) {
                    cell.fontSize = 12; // Ajustar tamaño de fuente para las celdas de datos
                  });
                }
              });
            }
          });

          doc.content = [
            {
              text: module,
              style: "tableTitle",
              margin: [0, 0, 0, 10], // Espaciado antes de la tabla
            },
            ...doc.content,
          ];

          /** Estilo para los títulos de las tablas */
          doc.styles.tableTitle = {
            fontSize: 16,
            bold: true,
            alignment: "center",
            color: colorColumns,
          };
        },
      },
      {
        extend: "colvis",
        text: '<i class="fa-solid fa-eye"></i>',
      },
    ],
  };
};

const validateInt = (event) => {
  // Obtenemos el valor actual del campo de entrada
  const valor = event.target.value;
  // Usamos una expresión regular para reemplazar cualquier carácter que no sea un dígito
  event.target.value = valor.replace(/[^0-9]/g, "");
};

/** Función para validar la entrada de texto en un campo de entrada HTML.
 * Permite únicamente letras y espacios, eliminando números y caracteres especiales.
 *
 * @param {Event} event - El evento de entrada asociado al campo de texto.
 */
const validateInputText = (event) => {
  /** Obtiene el valor actual del campo de entrada.
   * Este valor será procesado para eliminar caracteres no deseados.
   */
  const valor = event.target.value;

  /** Reemplaza cualquier carácter que no sea una letra o un espacio.
   * - \p{L}: Permite letras de cualquier idioma (compatibles con Unicode).
   * - Espacio ( ): Permite espacios entre palabras.
   * - El modificador 'u' habilita soporte para Unicode.
   */
  event.target.value = valor.replace(/[^\p{L} ]/gu, "");
};

/** Función para validar la entrada de un campo numérico y eliminar caracteres no deseados.
 * Permite solo números, un punto decimal y restringe símbolos como '+' o '-'.
 *
 * @param {Event} event - El evento de entrada asociado al campo numérico.
 */
const validateInputNumber = (event) => {
  /** Obtiene el valor actual del campo de entrada */
  const valor = event.target.value;

  /** Reemplaza cualquier carácter que no sea un dígito o un punto decimal.
   * - \d: Permite dígitos (0-9).
   * - .: Permite el punto decimal.
   * - ^[^0-9.]: Elimina cualquier carácter que no coincida con números o el punto.
   */
  event.target.value = valor.replace(/[^0-9.]/g, "");
};

const validateString = (event) => {
  // Esta función se ejecuta cada vez que el usuario ingresa un valor en un campo de entrada.
  // Utiliza una expresión regular para filtrar y eliminar cualquier carácter que no sea una letra, un número o un espacio.
  // Los caracteres no permitidos se reemplazan por una cadena vacía, asegurando que solo se ingresen letras, números y espacios.

  // Obtenemos el valor actual del campo de entrada
  const valor = event.target.value;
  // Usamos una expresión regular para reemplazar cualquier carácter que no sea un dígito
  // replace(/[^A-Za-z0-9 ]/g, '');
  event.target.value = valor.replace(/[^\p{L}\p{N} ]/gu, "");
};

const validateAndConvertToUppercase = (event) => {
  let valor = event.target.value;
  // Convertir a mayúsculas y permitir letras con acentos, Ñ, números y espacios
  event.target.value = valor.toUpperCase().replace(/[^A-ZÁÉÍÓÚÑ0-9 ]/g, "");
};

const validateAndConvertToLowercase = (event) => {
  let valor = event.target.value;
  // Convertir a minúsculas y filtrar solo letras minúsculas y números
  event.target.value = valor.toLowerCase().replace(/[^a-z0-9]/g, "");
};

const validateWithCommasAndDots = (event) => {
  const valor = event.target.value;
  // Expresión regular para mantener letras, números, espacios, comas y puntos
  event.target.value = valor.replace(/[^\p{L}\p{N} ,.]/gu, "");
};

const validateLength = (event) => {
  // Obtenemos el valor actual del campo de entrada
  const valor = event.target.value;

  // Verificamos la longitud del valor
  if (valor.length >= 14) {
    return true;
  } else {
    return false;
  }
};

/** Función para validar un número de teléfono.
 * Permite ingresar únicamente números y limita el máximo a 10 dígitos.
 * No se permiten letras, símbolos o caracteres especiales.
 *
 * @param {Event} event - El evento de entrada asociado al campo de texto.
 */
const validatePhoneNumber = (event) => {
  /** Remover caracteres no numéricos */
  let value = event.target.value.replace(/\D/g, "");

  /** Limitar a 10 caracteres */
  if (value.length > 10) {
    value = value.slice(0, 10);
  }

  event.target.value = value;
};

function parseFechaDMY(fechaStr) {
  const [dia, mes, año] = fechaStr.split("/").map(Number);
  return new Date(año, mes - 1, dia); // mes - 1 porque enero = 0
}

function getFechaActualLetras(fecha = new Date()) {
  const fechaFija = new Date(fecha.getFullYear(), fecha.getMonth(), fecha.getDate());

  return fechaFija.toLocaleDateString("es-ES", {
    day: "numeric",
    month: "long",
    year: "numeric",
  });
}

// Función para   cambiar el formato de la fecha
function formatearFecha(fecha) {
  if (!fecha) return ""; // Verifica si la fecha está vacía
  let partes = fecha.split("-"); // Divide la fecha en partes
  return `${partes[2]}/${partes[1]}/${partes[0]}`; // Reordena a DD/MM/YYYY
}

function formatCurrency(value) {
  return value > 0
    ? new Intl.NumberFormat("es-MX", {
        style: "currency",
        currency: "MXN",
      }).format(value)
    : "PENDIENTE";
}