<?php
require_once '../config/Connection.php';

class CashboxCount extends Connection
{
   public function __construct() {}

   /** Obtiene el arqueo de caja abierto por ID
    *
    * @param int $cashboxId - ID de la caja abierta
    * @return array - Detalles del arqueo de caja
    */
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

   /** Actualiza el total de una venta en arqueo_caja
    *
    * @param mysqli $conexion - Conexión a la base de datos
    * @param int $cashboxId - ID de la caja abierta
    * @param float $total - Total de la venta
    * @param string $field - Campo a actualizar (default: "total_contado")
    * @return bool - Resultado de la actualización
    */
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

   /** Actualiza el total de una venta eliminada en arqueo_caja
    *
    * @param mysqli $conexion - Conexión a la base de datos
    * @param int $cashboxId - ID de la caja abierta
    * @param float $total - Total de la venta eliminada
    * @param string $date - Fecha de la venta
    * @param string $field - Campo a actualizar (default: "total_contado")
    * @return bool - Resultado de la actualización
    */
   public static function updateSaleTotalOnDelete(mysqli $conexion, int $cashboxId, float $total, string $dateTime, string $field = "total_contado"): bool
   {
      /** Extraer solo la fecha en formato 'Y-m-d' para hacer el rango del día completo */
      $date = date('Y-m-d', strtotime($dateTime));

      /** Crear los límites del día: 00:00:00 a 23:59:59 */
      $startOfDay = "$date 00:00:00";
      $endOfDay   = "$date 23:59:59";

      return self::executeQueryWithTransaction(
         $conexion,
         "UPDATE 
            arqueo_caja 
         SET 
            monto_fin = monto_fin - $total, 
            $field = $field - $total
         WHERE
            id_caja = $cashboxId
         AND 
            fecha_inicio BETWEEN '$startOfDay' AND '$endOfDay'"
      );
   }
}
