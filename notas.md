LOGO PARTE SUPERIOR IZQUIERDA.

DISEÑO DE IMPRESIÓN DE COMPROBANTES (FACTURAS).






CREAR LLAVE FORANEA PARA ID_USUARIO EN CATEGORIAS.




 {
        extend: "pdfHtml5",
        text: "Generar PDF",
        customize: function (doc) {
          // Modificar el tamaño de fuente general
          doc.defaultStyle.fontSize = 14; // Aumentamos el tamaño de la fuente general

          // Recorrer las tablas en el documento y hacer las modificaciones necesarias
          doc.content.forEach(function (contentItem) {
            if (contentItem.table) {
              // Asegurarse de que cada tabla ocupe el 100% del ancho
              contentItem.table.widths = Array(
                contentItem.table.body[0].length
              ).fill("*"); // Establecer todos los anchos de las columnas al 100% de la página

              // Cambiar el color de fondo del encabezado de la tabla
              contentItem.table.headerRows = 1;
              contentItem.table.body[0].forEach(function (cell) {
                cell.fillColor = "#f2f2f2"; // Color de fondo del encabezado
                cell.fontSize = 14; // Aumentar tamaño de fuente del encabezado
              });

              // Modificar el color del texto en las celdas
              contentItem.table.body.forEach(function (row, rowIndex) {
                if (rowIndex > 0) {
                  // No modificar el encabezado, solo las filas de datos
                  row.forEach(function (cell) {
                    cell.fontSize = 12; // Ajustar tamaño de fuente para las celdas de datos
                  });
                }
              });
            }
          });

          // Añadir un título específico para cada tabla (puedes personalizar el título aquí)
          doc.content = [
            {
              text: "Título de la Tabla", // Personaliza el título de la tabla
              style: "tableTitle",
              margin: [0, 0, 0, 10], // Espaciado antes de la tabla
            },
            ...doc.content,
          ];

          // Estilo para los títulos de las tablas
          doc.styles.tableTitle = {
            fontSize: 16,
            bold: true,
            alignment: "center",
            color: "#333",
          };
        },
      },