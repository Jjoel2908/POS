<?php
require_once '../config/Connection.php';

class SaleDetails extends Connection
{
   public function __construct() {}

   public function dataTable(int $userId, ?int $saleId = null): array
   {
      /** Estado para el detalle de compra */
      $estado = $saleId ? 1 : 0;

      /** Identificador de la compra */
      $idSale = $saleId ? "= $saleId" : "IS NULL";

      /** Identificador del usuario */
      $idUser = $saleId ? "" : " AND dv.creado_por = $userId";

      return $this->queryMySQL(
         "SELECT 
            dv.id,
            dv.precio_venta AS precio,
            dv.cantidad, 
            p.nombre AS producto 
         FROM 
            detalle_venta dv 
         INNER JOIN 
            productos p 
         ON 
            dv.id_producto = p.id 
         WHERE 
            dv.estado = $estado
         AND 
            dv.id_venta $idSale
         $idUser");
   }

   public function existSaleDetails(int $idProduct, int $idUser): array
   {
      return $this->queryMySQL("SELECT id, cantidad FROM detalle_venta WHERE id_venta IS NULL AND estado = 0 AND creado_por = $idUser AND id_producto = $idProduct");
   }

   public function updateSaleDetail(int $idSale, int $quantity): bool
   {
      return $this->queryMySQL("UPDATE detalle_venta SET cantidad = cantidad + $quantity WHERE id = $idSale");
   }

   public function getSaleDetails(int $idUser): array {
      return $this->queryMySQL(
         "SELECT 
            id, 
            id_producto,
            precio_venta AS precio,
            cantidad
         FROM 
            detalle_venta
         WHERE 
            estado = 0 
         AND 
            id_venta IS NULL
         AND
            creado_por = $idUser"
      );
   }
}