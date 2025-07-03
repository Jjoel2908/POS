<?php
require_once '../config/Connection.php';

class PurchaseDetails extends Connection
{
   public function __construct() {}

    // id (PK)
    // cotizacion_id (FK)
    // producto_id (FK)
    // descripcion
    // cantidad
    // precio_unitario
    // descuento
    // impuesto
    // total
    // created_at
    // updated_at


   public function dataTable(int $userId, ?int $purchaseId = null): array
   {
      /** Estado para el detalle de compra */
      $estado = $purchaseId ? 1 : 0;

      /** Identificador de la compra */
      $idPurchase = $purchaseId ? "= $purchaseId" : "IS NULL";

      /** Identificador del usuario */
      $idUser = $purchaseId ? "" : " AND dc.creado_por = $userId";

      return $this->queryMySQL(
         "SELECT 
            dc.id,
            dc.precio,
            dc.cantidad, 
            p.nombre AS producto 
         FROM 
            detalle_compra dc 
         INNER JOIN 
            productos p 
         ON 
            dc.id_producto = p.id 
         WHERE 
            dc.estado = $estado
         AND 
            dc.id_compra $idPurchase
         $idUser");
   }

   public function existPurchaseDetails(int $idProduct, int $idUser): array
   {
      return $this->queryMySQL("SELECT id FROM detalle_compra WHERE id_compra IS NULL AND estado = 0 AND creado_por = $idUser AND id_producto = $idProduct");
   }

   public function updatePurchaseDetail(int $idPurchase, int $quantity): bool
   {
      return $this->queryMySQL("UPDATE detalle_compra SET cantidad = cantidad + $quantity WHERE id = $idPurchase");
   }

   public function getPurchaseDetails(int $idUser): array {
      return $this->queryMySQL(
         "SELECT 
            id, 
            id_producto,
            precio,
            cantidad
         FROM 
            detalle_compra 
         WHERE 
            estado = 0 
         AND 
            id_compra IS NULL
         AND
            creado_por = $idUser");
   }
}