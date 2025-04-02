<?php
require_once '../config/Connection.php';

class PurchaseDetails extends Connection
{
   public function __construct() {}

   public function dataTable(int $idUser): array
   {
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
            dc.estado = 0 
         AND 
            dc.id_compra IS NULL
         AND
            dc.creado_por = $idUser");
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