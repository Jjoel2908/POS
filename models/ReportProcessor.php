<?php
require '../config/Connection.php';

class ReportProcessor extends Connection
{

   private string $startDate = '';
   private string $endDate   = '';

   private array $meses = array(
      1 => "Enero", 
      2 => "Febrero", 
      3 => "Marzo", 
      4 => "Abril", 
      5 => "Mayo", 
      6 => "Junio", 
      7 => "Julio", 
      8 => "Agosto", 
      9 => "Septiembre", 
      10 => "Octubre", 
      11 => "Noviembre", 
      12 => "Diciembre"
   );

   public function __construct(private ?string $dateRange = '')
   {
      $this->setRange($dateRange);
   }

   private function setRange(string $dateRange): void {
      if (!empty($dateRange)) {
         $param = explode(' - ', $dateRange);
         $this->startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $param[0])));
         $this->endDate   = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $param[1])));
      }
   }  

   /* ===================================================================================  */
   /* -------------------------- R E P O R T   P U R C H A S E -------------------------- */
   /* ===================================================================================  */
   /**
    * Obtiene las compras y sus detalles dentro de un rango de fechas específico.
    * Utiliza una consulta SQL para seleccionar información de las tablas 'productos', 'compras' y 'detalle_compra'.
    *
    * @return array Arreglo de resultados de la consulta SQL que contiene información detallada de las compras.
    */
   public function getReportPurchases(): array {

      $totalWidget = $this->queryMySQL("SELECT 
                                          FORMAT(IFNULL(SUM(total), 0.00), 2) AS total_purchase
                                       FROM compras  
                                       WHERE fecha >= '{$this->startDate}' AND fecha <= '{$this->endDate}' AND estado = 1");
      
      $table =  $this->queryMySQL("SELECT 
                                    c.*,
                                    u.nombre AS nombre_usuario
                                 FROM compras c
                                    INNER JOIN usuarios u ON c.id_usuario = u.id
                                 WHERE
                                    c.fecha >= '{$this->startDate}' AND c.fecha <= '{$this->endDate}' AND c.estado = 1");

      list($dayStart, $hourStart) = explode(" ", $this->startDate);
      list($dayEnd, $hourEnd)     = explode(" ", $this->endDate);

      $monthStart = date("n", strtotime($dayStart));
      $monthEnd   = date("n", strtotime($dayEnd));

      $name_monthStart = $this->meses[$monthStart];
      $name_monthEnd   = $this->meses[$monthEnd];

      $dateStart = date('d \d\e ', strtotime($dayStart)) . $name_monthStart . date(' \d\e Y', strtotime($dayStart));
      $dateEnd   = date('d \d\e ', strtotime($dayEnd)) . $name_monthEnd . date(' \d\e Y', strtotime($dayEnd));

      $date = $dateStart . "<span class='mx-1'> al </span>" . $dateEnd;

      return [
         "total"    => $totalWidget[0]['total_purchase'],
         "table"    => $table,
         "date"     => $date
      ];
   }

   public function getPurchaseDetails(int $idPurchase): array {
      $query = "SELECT
                  dc.*, 
                  p.nombre AS nombre_producto,
                  p.estado AS estatus
               FROM detalle_compra dc
               INNER JOIN productos p ON dc.id_producto = p.id
               WHERE dc.id_compra = '{$idPurchase}' AND dc.estado = 1";
      
      return $this->queryMySQL($query);
   }

   /* ===================================================================================  */
   /* ------------------------------ R E P O R T   S A L E S ------------------------------ */
   /* ===================================================================================  */

   /**
    * Obtiene las ventas y sus detalles dentro de un rango de fechas específico.
    * Utiliza una consulta SQL para seleccionar información de las tablas 'productos', 'ventas' y 'detalle_venta'.
    *
    * @return array Arreglo de resultados de la consulta SQL que contiene información detallada de las ventas.
    */
   public function getReportSales(): array {

      $totalWidget = $this->queryMySQL("SELECT 
                                          FORMAT(IFNULL(SUM(total), 0.00), 2) AS total_selling
                                       FROM ventas  
                                       WHERE fecha >= '{$this->startDate}' AND fecha <= '{$this->endDate}' AND estado = 1");

      $Earnings = $this->queryMySQL("SELECT 
                                       FORMAT(IFNULL(SUM(dv.cantidad * (dv.precio - dv.precio_compra)), 0.00), 2) AS earnings
                                    FROM detalle_venta dv
                                       INNER JOIN ventas v ON dv.id_venta = v.id
                                       INNER JOIN productos p ON dv.id_producto = p.id
                                    WHERE 
                                       v.fecha >= '{$this->startDate}' AND v.fecha <= '{$this->endDate}' 
                                       AND v.estado = 1 AND dv.estado = 1");
      
      $table =  $this->queryMySQL("SELECT 
                                    v.*,
                                    u.nombre AS nombre_usuario,
                                    cj.caja AS nombre_caja,
                                    ct.nombre AS nombre_cliente
                                 FROM ventas v
                                    INNER JOIN usuarios u ON v.id_usuario = u.id
                                    INNER JOIN cajas cj ON v.id_caja = cj.id
                                    INNER JOIN clientes ct ON v.id_cliente = ct.id
                                 WHERE
                                    v.fecha >= '{$this->startDate}' AND v.fecha <= '{$this->endDate}' AND v.estado = 1");

      list($dayStart, $hourStart) = explode(" ", $this->startDate);
      list($dayEnd, $hourEnd)     = explode(" ", $this->endDate);

      $monthStart = date("n", strtotime($dayStart));
      $monthEnd   = date("n", strtotime($dayEnd));

      $name_monthStart = $this->meses[$monthStart];
      $name_monthEnd   = $this->meses[$monthEnd];

      $dateStart = date('d \d\e ', strtotime($dayStart)) . $name_monthStart . date(' \d\e Y', strtotime($dayStart));
      $dateEnd   = date('d \d\e ', strtotime($dayEnd)) . $name_monthEnd . date(' \d\e Y', strtotime($dayEnd));

      $date = $dateStart . "<span class='mx-1'> al </span>" . $dateEnd;

      return [
         "total"    => $totalWidget[0]['total_selling'],
         "earnings" => $Earnings[0]['earnings'],
         "table"    => $table,
         "date"     => $date
      ];
   }

   public function getSalesDetails(int $idSale): array {
      $query = "SELECT
                  dv.*, 
                  p.nombre AS nombre_producto,
                  p.estado AS estatus
               FROM detalle_venta dv
               INNER JOIN productos p ON dv.id_producto = p.id
               WHERE dv.id_venta = '{$idSale}' AND dv.estado = 1";
      
      return $this->queryMySQL($query);
   }

   /* ===================================================================================  */
   /* ------------------------------ B E S T   S E L L I N G ------------------------------ */
   /* ===================================================================================  */
   /**
    * Obtiene un top 10 de los productos más vendidos
    */
    public function getReportProductBestSelling(): array {
      $query = "  SELECT 
                     dv.*, 
                     p.nombre AS nombre_producto, 
                     p.codigo AS codigo_producto,
                     p.precio_venta AS precio_producto,
                     ct.categoria AS nombre_categoria,
                     SUM(cantidad) AS total_selling 
                  FROM detalle_venta dv 
                     INNER JOIN productos p ON dv.id_producto = p.id 
                     INNER JOIN categorias ct ON p.id_categoria = ct.id
                     WHERE dv.estado = 1 
                     GROUP BY nombre_producto 
                     ORDER BY total_selling DESC 
                     LIMIT 10";

      return $this->queryMySQL($query);
   }
}
