<?php
require '../config/Connection.php';

class Sales extends Connection
{

   public function __construct() {}

   public function dataTable(): array
   {
      return $this->queryMySQL("SELECT v.*, u.nombre AS nombre_usuario FROM ventas v INNER JOIN usuarios u ON v.id_usuario = u.id WHERE v.estado = 1 ORDER BY v.id DESC LIMIT 5");
   }

   public function creditDataTable(): array
   {
      return $this->queryMySQL(
         "SELECT cv.*, c.nombre AS cliente 
         FROM credito cv INNER JOIN clientes c ON cv.id_cliente = c.id ORDER BY cv.id DESC"
      );
   }

   public function getSalesDetails(int $idSale): array {
      $query = "SELECT
                  dv.*, 
                  p.nombre AS nombre_producto,
                  p.estado AS estatus
               FROM detalle_venta dv
               INNER JOIN productos p ON dv.id_producto = p.id
               WHERE dv.id_venta = '{$idSale}' AND dv.estado = 2";
      
      return $this->queryMySQL($query);
   }

   public function addCreditPayment(int $idSale, float $abono, int $estado): bool {
      return $this->queryMySQL("UPDATE credito SET pagado = pagado + $abono, estado = $estado WHERE id = $idSale");
   }

   public function updateStateCashbox(float $monto_fin, int $idArqueo): bool {
      return $this->queryMySQL("UPDATE arqueo_caja SET monto_fin = monto_fin + $monto_fin, total_ventas = total_ventas + 1 WHERE id = $idArqueo");
   }

   public function updateStateSaleDetails($idSale): bool {
      return $this->queryMySQL("UPDATE detalle_venta SET estado = 1 WHERE id_venta = $idSale");
   }

   public function updateStateSale($idSale): bool {
      return $this->queryMySQL("UPDATE ventas SET estado = 1 WHERE id = $idSale");
   }

   public function dataTableSaleDetails(int $id_usuario): array
   {
      return $this->queryMySQL("SELECT dv.*, p.nombre AS nombre_producto, p.precio_venta AS precio FROM detalle_venta dv INNER JOIN productos p ON dv.id_producto = p.id WHERE dv.estado = 0 AND dv.id_usuario = $id_usuario");
   }

   public function existProductDetail(int $id_producto, int $id_usuario): array
   {
      return $this->queryMySQL("SELECT * FROM detalle_venta WHERE id_venta = 0 AND estado = 0 AND id_usuario = $id_usuario AND id_producto = $id_producto");
   }

   public function insertSaleDetail(array $data): bool
   {
      return $this->insert('detalle_venta', $data);
   }

   public function updateSaleDetail(int $id, array $data): bool
   {
      return $this->update('detalle_venta', $id, $data);
   }

   public function deleteSaleDetail(int $id): bool
   {
      return $this->delete('detalle_venta', $id);
   }

   public function getProductDetails(int $id_usuario): array {
      return $this->queryMySQL("SELECT * FROM detalle_venta WHERE id_venta = 0 AND estado = 0 AND id_usuario = $id_usuario");
   }

   public function saveSale(array $data): int
   {
      return $this->insertAndGetId('ventas', $data);
   }

   public function getOpenCashbox(int $idCashbox): array {
      return $this->queryMySQL("SELECT * FROM arqueo_caja WHERE ISNULL(fecha_fin) AND estado = 0 AND id_caja = $idCashbox");
   }

   public function updateTotalCashbox(float $monto_fin, int $total_ventas, int $idCashbox): bool {
      return $this->queryMySQL("UPDATE arqueo_caja SET monto_fin = $monto_fin, total_ventas = $total_ventas WHERE ISNULL(fecha_fin) AND estado = 0 AND id_caja = $idCashbox");
   }

   /** Actualización de detalle_venta con identificador de la venta y método de pago */
   public function updateIdSaleDetails(int $id_venta, int $id_usuario, int $payment): bool
   {
      return $this->queryMySQL("UPDATE detalle_venta SET id_venta = $id_venta, estado = $payment WHERE id_venta = 0 AND estado = 0 AND id_usuario = $id_usuario");
   }

   public function idSaleDetails(int $id_venta): array
   {
      return $this->queryMySQL("SELECT * FROM detalle_venta WHERE id_venta = $id_venta");
   }

   public function decreaseStock(int $id_producto, int $cantidad): bool
   {
      $product = $this->select('productos', $id_producto);
      $stock = $product['stock'] - $cantidad;
      return $this->queryMySQL("UPDATE productos SET stock = $stock WHERE id = $id_producto");
   }

   public function cancelSale(int $id_usuario): bool
   {
      return $this->queryMySQL("DELETE FROM detalle_venta WHERE id_venta = 0 AND estado = 0 AND id_usuario = $id_usuario");
   }
}
