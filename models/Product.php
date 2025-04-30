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

   public function getPurchasePrice($idRegister): array
   {
      $product = $this::queryMySQL("SELECT precio_compra FROM productos WHERE id = $idRegister AND estado = 1 LIMIT 1");
      return $product[0];
   }

   public function getSalePrice($idRegister): array
   {
      $product = $this::queryMySQL("SELECT precio_compra, precio_venta, stock FROM productos WHERE id = $idRegister AND estado = 1 LIMIT 1");
      return $product[0];
   }

   public function searchProduct(string $text): array
   {
      return $this::queryMySQL("SELECT id, codigo, nombre, stock FROM productos WHERE codigo LIKE '%$text%' OR nombre LIKE '%$text%'");
   }
}