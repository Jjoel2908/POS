<?php
require_once '../config/Connection.php';
require '../models/QouteDetails.php';

class Qoute extends Connection
{
   public function __construct() {}

   /** Estados de cotizaciones */
   public static $QUOTE_STATUS = [
      1 => 'pendiente',     // Cotización creada, aún no convertida
      2 => 'aceptada',      // Aceptada por el cliente (opcional)
      3 => 'convertida',    // Convertida en venta/factura
      4 => 'cancelada',     // Cancelada manualmente
      5 => 'expirada',      // Venció su fecha de validez
      6 => 'rechazada',     // Rechazada explícitamente por el cliente
   ];

   public static $BADGES_STATUS = [
      1 => ['texto' => 'Pendiente',  'clase' => 'bg-warning'],
      2 => ['texto' => 'Aceptada',   'clase' => 'bg-primary'],
      3 => ['texto' => 'Convertida', 'clase' => 'bg-success'],
      4 => ['texto' => 'Cancelada',  'clase' => 'bg-danger'],
      5 => ['texto' => 'Expirada',   'clase' => 'bg-secondary'],
      6 => ['texto' => 'Rechazada',  'clase' => 'bg-dark'],
   ];

    // id (PK)
    // numero_cotizacion (único)
    // id_cliente (FK)
    // usuario_id (FK)
    // sucursal_id (FK)
    // fecha_creacion
    // fecha_vencimiento
    // estado (ENUM: 'pendiente', 'convertida', 'cancelada')
    // total
    // notas
    // terminos
    // created_at
    // updated_at

   public function dataTable(): array
   {
        return $this->queryMySQL(
            "SELECT 
                q.id,
                q.fecha_creacion, 
                q.fecha_vencimiento, 
                q.estado_cotizacion, 
                q.total, 
                q.descripcion,
                c.nombre AS cliente 
            FROM 
                cotizaciones q 
            INNER JOIN 
                clientes c 
            ON 
                q.id_cliente = c.id 
            WHERE 
                q.estado = 1
        ");
   }

   public function calculateTotal(array $details): float
    {
        $total = 0;
        foreach ($details as $detail) {
            $total += $detail['cantidad'] * $detail['precio'];
        }

        return number_format(floatval($total), 2, '.', '');
    }





   /** Actualizamos las compras que estan pendientes con respecto al usuario */
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

   /** Registra una nueva cotización.
    *
    * @param int $idUser - ID del usuario que genera la cotización.
    * @param int $idSucursal - ID de la sucursal donde se realiza la cotización.
    * @param float $total - Total de la compra.
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
         $PurchaseDetails = new PurchaseDetails();
         $details         = $PurchaseDetails->getPurchaseDetails($idUser);

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
}