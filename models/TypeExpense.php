<?php
require_once '../config/Connection.php';

class TypeExpense extends Connection
{
   public function __construct() {}

   public function selectOne($idRegister)
   {
      return $this::queryMySQL("SELECT * FROM tipos_gastos WHERE id = $idRegister AND estado = 1 LIMIT 1");
   }
}