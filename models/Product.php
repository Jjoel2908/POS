<?php
require_once '../config/Connection.php';

class Product extends Connection
{

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
      return $this::queryMySQL("SELECT id, codigo, nombre, stock FROM productos WHERE codigo LIKE '%$text%' OR nombre LIKE '%$text%'");
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
