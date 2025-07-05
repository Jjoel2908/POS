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

   public static function updateSaleTotal(mysqli $conexion, int $cashboxId, float $total, string $field = "total_contado"): bool
   {
      return self::executeQueryWithTransaction(
         $conexion,
         "UPDATE 
               arqueo_caja 
            SET 
               monto_fin = monto_fin + $total, 
               $field = $field + $total
            WHERE 
               ISNULL(fecha_fin) 
            AND 
               estado = 0
            AND
               id_caja = $cashboxId"
      );
   }
}
