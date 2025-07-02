<?php
require_once '../config/Connection.php';

class Color extends Connection
{
   public function __construct() {}

   public function selectOne($idRegister)
   {
      return $this::queryMySQL("SELECT id, nombre FROM colores WHERE id = $idRegister AND estado = 1 LIMIT 1");
   }
}