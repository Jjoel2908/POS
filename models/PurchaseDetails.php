<?php
require_once '../config/Connection.php';

class PurchaseDetails extends Connection
{
   public function __construct() {}

   public function existPurchaseDetails(int $idProduct, int $idUser): array
   {
      return $this->queryMySQL("SELECT 1 FROM detalle_compra WHERE id_compra = 0 AND estado = 0 AND id_usuario = $idUser AND id_producto = $idProduct");
   }

   public function dataTablePurchaseDetails(int $id_usuario): array
   {
      return $this->queryMySQL("SELECT dc.*, p.nombre AS nombre_producto, p.precio_compra AS precio FROM detalle_compra dc INNER JOIN productos p ON dc.id_producto = p.id WHERE dc.estado = 0 AND dc.id_usuario = $id_usuario");
   }

   public function insertPurchaseDetail(array $data): bool
   {
      return $this->insert('detalle_compra', $data);
   }

   public function updatePurchaseDetail(int $id, array $data): bool
   {
      return $this->update('detalle_compra', $id, $data);
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