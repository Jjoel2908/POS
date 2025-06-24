# PARA HABILITAR NUEVAMENTE LAS CATEGORIAS DEBEMOS IR A LA BASE DE DATOS PARA PONER ID_CATEGORIA COMO OBLIGATORIO SIN QUE SEA NULLO, ADEMAS AGREGAR RELACION CON TABLA DE CATEGORIAS. TAMBIEN EN EL MODAL PARA AGREGAR PRODUCTO, AGREGAR EL CAMPO SELECT DE CATEGORIA Y EN EL MODULEPRODUCT CONSULTAR EL DROPLIST DE LA MISMA. CUANDO SE GUARDE EL PRODUCTO DEBEMOS VERIFICAR QUE LA CATEGORIA SE ESTE ENVIANDO CORRECTAMENTE.

/** Función para obtener los productos con paginación y búsqueda */
public function dataTable($start, $length, $search): array
{
   /** Condición base para los productos activos */
   $where = "WHERE p.estado = 1";

   /** Si hay búsqueda, agregarla a la condición */
   if (!empty($search)) {
      /** Protege contra inyecciones básicas */
      $search = addslashes($search);
      $where .= " AND (
         p.nombre LIKE '%$search%' OR 
         p.codigo LIKE '%$search%' OR 
         m.nombre LIKE '%$search%' OR 
         c.nombre LIKE '%$search%' OR 
         t.nombre LIKE '%$search%'
      )";
   }

   /** Consulta SQL para contar los productos filtrados */
   $query = $this->queryMySQL(
      "SELECT 
         p.*, 
         m.nombre AS marca,
         c.nombre AS color,
         t.nombre AS talla
      FROM 
         productos p 
      INNER JOIN 
         marcas m ON p.id_marca = m.id
      LEFT JOIN 
         colores c ON p.id_color = c.id
      LEFT JOIN 
         tallas t ON p.id_talla = t.id
      $where
      LIMIT 
         $start, 
         $length"
   );

   return $query;
}




### CADA QUE SE HAGA UNA NUEVA VENTA, HABILITAR NUEVAMENTE LA VERIFICACION DE CAJA ABIERTA.
### PONER EN ACCION SPINNER CUANDO SE GUARDO UN REGISTRO, ACTUALICE, O ELIMINE 
### SPINNER DE CARGA


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