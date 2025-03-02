<?php
require '../config/Connection.php';
class ReturnProduct extends Connection {


   public function dataTable(): array
   {
      return $this->queryMySQL("SELECT 
                                       d.*, 
                                       p.nombre AS nombre_producto,
                                       c.nombre AS nombre_cliente,
                                       u.nombre AS nombre_usuario
                              FROM devoluciones d 
                                 INNER JOIN productos p ON d.id_producto = p.id
                                 INNER JOIN clientes c ON d.id_cliente = c.id
                                 INNER JOIN usuarios u ON d.id_usuario = u.id
                              ORDER BY d.fecha DESC");
   }

   public function insertReturn(array $data): bool {
      return $this::insert('devoluciones', $data);
   }

   public function decreaseDetailProduct(int $id_detail, int $quantity): bool 
   {
      $detail         = $this::select("detalle_venta", $id_detail);
      $idSale         = $detail['id_venta'];
      $quantityDetail = (int) $detail['cantidad'];
      $priceDetail    = (float) $detail['precio'];
      $updateTotal    = ($quantityDetail - $quantity) * $priceDetail;

      $query = ($quantityDetail == $quantity || $quantityDetail == 1)
         ? $this::delete("detalle_venta", $id_detail)
         : $this::queryMySQL("UPDATE detalle_venta SET cantidad = cantidad - $quantity WHERE id = $id_detail");

      if ($query) return $this::queryMySQL("UPDATE ventas SET total = $updateTotal WHERE id = $idSale");

      return false;
   }  

   public function addStockProduct(int $idProduct, int $quantity): bool {
      return $this::queryMySQL("UPDATE productos SET stock = stock + $quantity WHERE id = $idProduct");
   }

   public static function selectSales(): array {
      return self::queryMySQL(
                              "SELECT 
                                 v.*,
                                 c.nombre AS nombre_cliente
                              FROM   ventas v
                                 INNER JOIN clientes c ON v.id_cliente = c.id
                              WHERE DATEDIFF(CURDATE(), v.fecha) <= 10 AND v.estado = 1
                                 ORDER BY v.fecha DESC");
   }

   public static function selectProducts(int $idSale): array {
      return self::queryMySQL(
                              "SELECT dv.*,
                                       p.nombre AS nombre_producto,
                                       p.codigo AS codigo_producto
                               FROM detalle_venta dv
                                 INNER JOIN productos p ON dv.id_producto = p.id
                              WHERE id_venta = $idSale");
   }

   public static function detailSale(int $id_detail): array {
      return self::queryMySQL("SELECT * FROM detalle_venta WHERE id = $id_detail");
   }
}
?>