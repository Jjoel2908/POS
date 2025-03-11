<?php
require '../config/Connection.php';

class Product extends Connection
{

   public function __construct()
   {
   }

   public function dataTable(): array
   {
      return $this->queryMySQL("SELECT p.*, c.categoria AS nombre_categoria FROM productos p INNER JOIN categorias c ON p.id_categoria = c.id WHERE p.estado = 1");
   }
}
