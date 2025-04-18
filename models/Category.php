<?php
require '../config/Connection.php';

class Category extends Connection
{
   public function __construct() {}

   public function selectOne($idRegister)
   {
      return $this::queryMySQL("SELECT id, nombre FROM categorias WHERE id = $idRegister AND estado = 1 LIMIT 1");
   }
}
