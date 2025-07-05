<?php
require_once '../config/Connection.php';
require_once '../models/Cashbox.php';
require_once '../models/CashboxCount.php';

class Expense extends Connection
{
   public function __construct() {}

   public function selectOne($idRegister)
   {
      return $this::queryMySQL("SELECT * FROM gastos WHERE id = $idRegister AND estado = 1 LIMIT 1");
   }

   public function dataTable(int $idSucursal): array
   {
      return $this->queryMySQL(
         "SELECT 
            g.*, 
            tg.nombre AS tipo_gasto 
         FROM 
            gastos g 
         INNER JOIN 
            tipos_gasto tg
         ON 
            g.id_tipo_gasto = tg.id 
         WHERE 
            g.estado = 1
         AND
            g.id_sucursal = $idSucursal
         AND 
            DATE(g.fecha) = CURDATE() 
         ORDER BY g.id DESC"
      );
   }
}
