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

















   
  

   public function insertPurchaseDetail(array $data): bool
   {
      return $this->insert('detalle_compra', $data);
   }

   public function deletePurchaseDetail(int $id): bool
   {
      return $this->delete('detalle_compra', $id);
   }

   public function getProductDetails(int $id_usuario): array {
      return $this->queryMySQL("SELECT * FROM detalle_compra WHERE id_compra = 0 AND estado = 0 AND id_usuario = $id_usuario");
   }

   public function savePurchase(array $data): int
   {
      return $this->insertAndGetId('compras', $data);
   }

   public function updateIdPurchaseDetails(int $id_compra, int $id_usuario): bool
   {
      return $this->queryMySQL("UPDATE detalle_compra SET id_compra = $id_compra, estado = 1 WHERE id_compra = 0 AND estado = 0 AND id_usuario = $id_usuario");
   }

   public function idPurchaseDetails(int $id_compra): array
   {
      return $this->queryMySQL("SELECT * FROM detalle_compra WHERE id_compra = $id_compra");
   }

   public function addStock(int $id_producto, int $cantidad): bool
   {
      $product = $this->select('productos', $id_producto);
      $newStock = $product['stock'] + $cantidad;
      return $this->queryMySQL("UPDATE productos SET stock = $newStock WHERE id = $id_producto");
   }

   public function cancelPurchase(int $id_usuario): bool
   {
      return $this->queryMySQL("DELETE FROM detalle_compra WHERE id_compra = 0 AND estado = 0 AND id_usuario = $id_usuario");
   }
}