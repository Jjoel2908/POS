<?php
require_once '../config/Connection.php';
require '../models/CashboxCount.php';

class Cashbox extends Connection
{
   public function __construct() {}

   public function selectOne($idRegister)
   {
      return $this::queryMySQL("SELECT id, nombre FROM cajas WHERE id = $idRegister AND estado = 1 LIMIT 1");
   }

   public function open(int $cashboxId, int $userId, string $date, float $amount): bool
   {
      try {
         $conexion = self::conectionMySQL();
         $conexion->begin_transaction();

         /** Abrimos la caja */
         $cashbox = $this::executeQueryWithTransaction($conexion, "UPDATE cajas SET abierta = 1 WHERE id = $cashboxId");

         if (!$cashbox)
            throw new Exception('Error al abrir la caja');

         /** Información a registrar */
         $data = [
            'id_caja'       => $cashboxId,
            'creado_por'    => $userId,
            'fecha_inicio'  => $date,
            'monto_inicial' => $amount,
            'estado'        => 0
         ];

         /** Registramos el arqueo de caja */
         $CashboxCount = new CashboxCount();
         $idCount = $CashboxCount::insertAndGetIdWithTransaction($conexion, 'arqueo_caja', $data);

         if (!$idCount)
            throw new Exception('Error al registrar el arqueo de caja');

         $conexion->commit();
         $conexion->close();
         return true;
      } catch (Exception $e) {
         $conexion->rollback();
         $conexion->close();
         return false;
      }
   }

   public function close(int $cashboxId, int $cashboxCountId, string $date): bool
   {
      try {
         $conexion = self::conectionMySQL();
         $conexion->begin_transaction();

         /** Cerramos la caja */
         $cashbox = $this::executeQueryWithTransaction($conexion, "UPDATE cajas SET abierta = 0 WHERE id = $cashboxId");

         if (!$cashbox)
            throw new Exception('Error al abrir la caja');

         /** Actualizamos el arqueo de caja */
         $cashboxCount = $this::executeQueryWithTransaction($conexion, "UPDATE arqueo_caja SET fecha_fin = '$date' WHERE id = $cashboxCountId");

         if (!$cashboxCount)
            throw new Exception('Error al actualizar el arqueo de caja');

         $conexion->commit();
         $conexion->close();
         return true;
      } catch (Exception $e) {
         $conexion->rollback();
         $conexion->close();
         return false;
      }
   }

   public function hasOpen(int $branchId): int
   {
      $openCashbox = $this::queryMySQL("SELECT id FROM cajas WHERE abierta = 1 AND id_sucursal = $branchId AND estado = 1 LIMIT 1");
      return count($openCashbox) > 0 ? $openCashbox[0]['id'] : 0;
   }
}