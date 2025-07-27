<?php
require_once '../config/Connection.php';
require '../models/PurchaseDetails.php';

class Purchase extends Connection
{
   public function __construct() {}

   /** Obtiene las últimas 5 compras registradas
    *
    * @return array - Lista de compras con detalles
    */
   public function dataTable(): array
   {
      return $this->queryMySQL(
         "SELECT 
            c.id,
            c.fecha, 
            c.total, 
            u.nombre AS usuario 
         FROM 
            compras c 
         INNER JOIN 
            usuarios u 
         ON c.creado_por = u.id 
         WHERE 
            c.estado = 1 
         ORDER BY 
            c.id DESC 
         LIMIT 5"
      );
   }

   /** Actualiza los detalles de compra pendientes
    *
    * @param mysqli $conexion - Conexión a la base de datos
    * @param int $idPurchase - ID de la compra a actualizar
    * @param int $idUser - ID del usuario que genera la compra
    * @return bool - Resultado de la operación
    */
   public function updatePurchaseDetails(mysqli $conexion, int $idPurchase, int $idUser): bool
   {
      return $conexion->query(
         "UPDATE 
            detalle_compra 
         SET 
            id_compra = $idPurchase, 
            estado = 1 
         WHERE 
            estado = 0 
         AND 
            id_compra IS NULL
         AND
            creado_por = $idUser"
      );
   }

   /** Actualiza el stock de un producto
    *
    * @param mysqli $conexion - Conexión a la base de datos
    * @param int $idProduct - ID del producto a actualizar
    * @param int $quantity - Cantidad a sumar al stock
    * @return bool - Resultado de la operación
    */
   public function updateProductStock(mysqli $conexion, int $idProduct, int $quantity): bool
   {
      return $this->executeQueryWithTransaction(
         $conexion,
         "UPDATE 
            productos
         SET 
            stock = stock + $quantity
         WHERE 
            id = $idProduct"
      );
   }
   
   /** Actualiza el stock de un producto al eliminar una compra
    *
    * @param mysqli $conexion - Conexión a la base de datos
    * @param int $idProduct - ID del producto a actualizar
    * @param int $quantity - Cantidad a restar del stock
    * @return bool - Resultado de la operación
    */
   public function updateProductStockOnDelete(mysqli $conexion, int $idProduct, int $quantity): bool
   {
      return $this->executeQueryWithTransaction(
         $conexion,
         "UPDATE 
            productos
         SET 
            stock = stock - $quantity
         WHERE 
            id = $idProduct"
      );
   }

   /** Registra una nueva compra y actualiza stock de productos
    *
    * @param int $idUser - ID del usuario que genera la compra
    * @param int $idSucursal - ID de la sucursal donde se realiza la compra
    * @param float $total - Total de la compra
    * @return array - Respuesta de éxito o error
    */
   public function insertPurchase(int $idUser, int $idSucursal, float $total): bool
   {
      try {
         $conexion = self::conectionMySQL();
         $conexion->begin_transaction();

         /** Información a registrar */
         $data = [
            'id_sucursal' => $idSucursal,
            'total'       => $total,
            'fecha'       => date('Y-m-d H:i:s'),
            'creado_por'  => $idUser
         ];

         /** Registramos la compra */
         $idPurchase = $this::insertAndGetIdWithTransaction($conexion, 'compras', $data);

         if (!$idPurchase)
            throw new Exception('Error al registrar la compra');

         /** Obtenemos los productos pendientes en detalle_compra */
         $details = PurchaseDetails::getPurchaseDetails($idUser);

         if (empty($details))
            throw new Exception('No hay productos pendientes para registrar la compra');

         /** Actualizamos los detalles de compra */
         $updateDetails = $this->updatePurchaseDetails($conexion, $idPurchase, $idUser);

         if (!$updateDetails)
            throw new Exception('Error al actualizar los detalles de compra');

         /** Actualizar stock en productos */
         foreach ($details as $detail) {
            $updateStock = $this->updateProductStock($conexion, $detail['id_producto'], $detail['cantidad']);

            if (!$updateStock)
               throw new Exception('Error al actualizar el stock del producto ID: ' . $detail['id_producto']);
         }

         $conexion->commit();
         $conexion->close();
         return true;
      } catch (Exception $e) {
         $conexion->rollback();
         $conexion->close();
         return false;
      }
   }

   /** Actualiza el estado de una compra a inactiva
    *
    * @param mysqli $conexion - Conexión a la base de datos
    * @param int $idPurchase - ID de la compra a actualizar
    * @return bool - Resultado de la operación
    */
   public function updatePurchaseStatus(mysqli $conexion, int $idPurchase): bool
   {
      return $conexion->query(
         "UPDATE 
            compras 
         SET 
            estado = 0 
         WHERE 
            id = $idPurchase"
      );
   }

   /** Elimina una compra y sus detalles
    *
    * @param int $idPurchase - ID de la compra a eliminar
    * @param array $details - Detalles de la compra a eliminar
    * @return bool - Resultado de la operación
    */
   public function deletePurchase(int $idPurchase, array $details): bool
   {
      if (empty($idPurchase) || !filter_var($idPurchase, FILTER_VALIDATE_INT))
         return false;

      $conexion = self::conectionMySQL();
      $conexion->begin_transaction();

      try {
         /** Actualizamos el estado de la compra */
         $updatePurchase = $this->updatePurchaseStatus($conexion, $idPurchase);

         if (!$updatePurchase)
            throw new Exception('Error al actualizar el estado de la compra');

         $updateDetails = PurchaseDetails::updatePurchaseDetailStatus($conexion, $idPurchase);

         if (!$updateDetails)
            throw new Exception('Error al actualizar los detalles de compra');

         /** Actualizar stock en productos */
         foreach ($details as $detail) {
            $updateStock = $this->updateProductStockOnDelete($conexion, $detail['id_producto'], $detail['cantidad']);

            if (!$updateStock)
               throw new Exception('Error al actualizar el stock del producto ID: ' . $detail['id_producto']);
         }

         $conexion->commit();
         return true;
      } catch (Exception $e) {
         $conexion->rollback();
         return false;
      } finally {
         $conexion->close();
      }
   }
}
