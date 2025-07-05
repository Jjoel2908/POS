<?php
require_once '../config/Connection.php';

class ExpenseType extends Connection
{
   public function __construct() {}

   public function selectOne($idRegister)
   {
      return $this::queryMySQL("SELECT id, nombre FROM tipos_gasto WHERE id = $idRegister AND estado = 1 LIMIT 1");
   }
}