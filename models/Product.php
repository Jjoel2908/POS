<?php
require_once '../config/Connection.php';

class Product extends Connection
{
   public static $TALLAS = [
      1 => "XXXS",
      2 => "XXS",
      3 => "XS",
      4 => "S",
      5 => "M",
      6 => "L",
      7 => "XL",
      8 => "XXL",
      9 => "XXXL",
      10 => "4XL",
      11 => "5XL",
      12 => "22",
      13 => "22.5",
      14 => "23",
      15 => "23.5",
      16 => "24",
      17 => "24.5",
      18 => "25",
      19 => "25.5",
      20 => "26",
      21 => "26.5",
      22 => "27",
      23 => "27.5",
      24 => "28",
      25 => "28.5",
      26 => "29",
      27 => "29.5",
      28 => "30",
      29 => "30.5",
      30 => "31",
      31 => "32",
      32 => "33",
      33 => "34",
      34 => "35",
      35 => "36",
      36 => "37",
      37 => "38",
      38 => "39",
      39 => "40",
      40 => "15 ML",
      41 => "30 ML",
      42 => "50 ML",
      43 => "75 ML",
      44 => "100 ML",
      45 => "125 ML",
      46 => "150 ML",
      47 => "200 ML",
      48 => "CHICA",
      49 => "MEDIANA",
      50 => "GRANDE",
      51 => "UNITALLA",
      52 => "SIN TALLA",
      53 => "OTRO",
   ];

   public static $COLORES = [
      1 => "NEGRO",
      2 => "BLANCO",
      3 => "ROJO",
      4 => "AZUL",
      5 => "VERDE",
      6 => "AMARILLO",
      7 => "NARANJA",
      8 => "ROSA",
      9 => "MORADO",
      10 => "GRIS",
      11 => "CAFÉ",
      12 => "BEIGE",
      13 => "TURQUESA",
      14 => "VINO",
      15 => "DORADO",
      16 => "PLATEADO",
      17 => "FUCSIA",
      18 => "AQUA",
      19 => "CORAL",
      20 => "LILA",
      21 => "MARFIL",
      22 => "OLIVA",
      23 => "MOSTAZA",
      24 => "CELESTE",
      25 => "LAVANDA",
      26 => "GRANATE",
      27 => "PÚRPURA",
      28 => "TERRACOTA",
      29 => "CIAN",
      30 => "OTRO",
   ];

   public function __construct() {}

   /** Función para obtener los productos con paginación y búsqueda */
   public function dataTable($start, $length, $search): array
   {
      /** Condición base para los productos activos */
      $where = "WHERE p.estado = 1";

      /** Si hay búsqueda, agregarla a la condición */
      if (!empty($search)) {
         /** Protege contra inyecciones básicas */
         $search = addslashes($search);
         $where .= " AND (p.nombre LIKE '%$search%' OR p.codigo LIKE '%$search%' OR m.nombre LIKE '%$search%')";
      }

      /** Consulta SQL para contar los productos filtrados */
      $query = $this->queryMySQL(
         "SELECT 
            p.*, 
            m.nombre AS marca
         FROM 
            productos p 
         INNER JOIN 
            marcas m 
         ON 
            p.id_marca = m.id 
         $where
         LIMIT 
            $start, 
            $length"
      );

      return $query;
   }

   /** Función para contar el total de productos (sin filtros) */
   public function countProducts(): int
   {
      $query = $this->queryMySQL("SELECT COUNT(*) as total FROM productos WHERE estado = 1");
      return $query[0]['total'];
   }

   /** Función para contar el total de productos filtrados por búsqueda */
   public function countFilteredProducts(string $search): int
   {
      $where = "WHERE p.estado = 1";

      /** Si hay búsqueda, agregarla a la condición */
      if (!empty($search)) {
         /** Protege contra inyecciones básicas */
         $search = addslashes($search);
         $where .= " AND (p.nombre LIKE '%$search%' OR p.codigo LIKE '%$search%' OR m.nombre LIKE '%$search%')";
      }

      /** Consulta SQL para contar los productos filtrados */
      $query = $this->queryMySQL("SELECT COUNT(*) as total FROM productos p INNER JOIN marcas m ON p.id_marca = m.id $where");

      return $query[0]['total'];
   }

   /** Función para recuperar el precio compra de un producto */
   public function getPurchasePrice($idRegister): array
   {
      $product = $this::queryMySQL("SELECT precio_compra FROM productos WHERE id = $idRegister AND estado = 1 LIMIT 1");
      return $product[0];
   }

   /** Función para recuperar el precio compra, precio venta y stock actual de un producto */
   public function getSalePrice($idRegister): array
   {
      $product = $this::queryMySQL("SELECT precio_compra, precio_venta, stock FROM productos WHERE id = $idRegister AND estado = 1 LIMIT 1");
      return $product[0];
   }

   /** Buscamos un producto por su nombre o código */
   public function searchProduct(string $text): array
   {
      return $this::queryMySQL("SELECT id, codigo, nombre, stock, imagen FROM productos WHERE codigo LIKE '%$text%' OR nombre LIKE '%$text%'");
   }

   /** Función que guarda la imagen de un producto */
   public function saveImage(?string $oldImage = null, string $folder = "../media/products/"): ?string
   {
      /** Verifica si se ha subido una imagen correctamente */
      if (!isset($_FILES['imagen']['tmp_name']) || !is_uploaded_file($_FILES['imagen']['tmp_name'])) {
         /** No hay imagen subida, así que se devuelve la imagen anterior (si hay) */
         return $oldImage;
      }

      /** Obtiene y convierte la extensión del archivo a minúsculas */
      $ext = strtolower(pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION));

      /** Tipos de imagen permitidos */
      $allowedTypes = ["jpg", "jpeg", "png", "webp"];

      /** Verifica que:
       * - La extensión esté permitida
       * - El archivo realmente sea una imagen
       */
      if (!in_array($ext, $allowedTypes) || !exif_imagetype($_FILES["imagen"]["tmp_name"])) {
         /** Si no pasa la validación, devuelve la imagen anterior */
         return $oldImage;
      }

      /** Crea un nuevo nombre único para la imagen usando microtime */
      $newImageName = round(microtime(true)) . '.' . $ext;

      /** Define la ruta completa donde se guardará la imagen */
      $destinationPath = rtrim($folder, '/') . '/' . $newImageName;

      /** Mueve el archivo subido a la carpeta destino */
      if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $destinationPath)) {
         /** Si hay una imagen anterior, la elimina del servidor */
         if ($oldImage) {
            $oldPath = rtrim($folder, '/') . '/' . $oldImage;
            if (file_exists($oldPath)) {
               unlink($oldPath); /** Borra la imagen anterior */
            }
         }

         /** Devuelve el nombre del nuevo archivo guardado */
         return $newImageName;
      }

      /** Si falló al mover el archivo, devuelve la imagen anterior */
      return $oldImage;
   }
}
