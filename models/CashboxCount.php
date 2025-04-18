<?php
require_once '../config/Connection.php';

class CashboxCount extends Connection
{
   public function __construct() {}

   public function dataTable(int $purchaseId): array
   {
      return $this->queryMySQL(
         "SELECT 
            ac.*, 
            c.nombre AS caja 
         FROM 
            arqueo_caja ac 
         INNER JOIN 
            cajas c 
         ON 
            ac.id_caja = c.id 
         WHERE 
            id_caja = $purchaseId
         AND
            fecha_fin IS NULL"
      );
   }
}
