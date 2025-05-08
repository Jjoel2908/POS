<?php
require '../config/Connection.php';

class Test extends Connection
{
   public function __construct() {}

   public function selectOne($idRegister)
   {
      return $this::queryMySQL("SELECT id, nombre, precio_venta FROM test WHERE id = $idRegister AND estado = 1 LIMIT 1");
   }
}
