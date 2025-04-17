<?php
require_once '../config/Connection.php';

class Product extends Connection
{

   public function __construct()
   {
   }

   public function dataTable(): array
   {
      return $this->queryMySQL(
         "SELECT 
            p.*, 
            m.nombre AS marca
         FROM 
            productos p 
         INNER JOIN 
            marcas m 
         ON 
            p.id_marca = m.id 
         WHERE 
            p.estado = 1"
      );
   }

   public function getPurchasePrice($idRegister): array
   {
      $product = $this::queryMySQL("SELECT precio_compra FROM productos WHERE id = $idRegister AND estado = 1");
      return $product[0];
   }
}
