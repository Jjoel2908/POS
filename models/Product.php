<?php
require '../config/Connection.php';

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
            c.nombre AS categoria,
            m.nombre AS marca
         FROM 
            productos p 
         INNER JOIN 
            categorias c ON p.id_categoria = c.id 
         INNER JOIN 
            marcas m ON p.id_marca = m.id 
         WHERE 
            p.estado = 1"
      );
   }
}
