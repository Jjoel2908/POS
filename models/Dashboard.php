<?php
require_once '../config/Connection.php';
class Dashboard extends Connection
{
   /** Almacena las métricas obtenidas de la base de datos 
    * como el total de productos, marcas, usuarios, etc.
    */
   private $metrics = [];

   /** Constructor de la clase.
    * Inicializa la obtención de datos para las métricas del dashboard.
    */
   public function __construct()
   {
      $this->fetchData();
   }

   /** Ejecuta las consultas SQL definidas para obtener las métricas
    * y almacena los resultados en la propiedad $metrics.
    */
   private function fetchData()
   {
      $queries = [
         'products'  => "SELECT COUNT(*) AS total FROM productos WHERE estado = 1",
         'brands'    => "SELECT COUNT(*) AS total FROM marcas WHERE estado = 1",
         'customers' => "SELECT COUNT(*) AS total FROM clientes WHERE estado = 1",
         'users'     => "SELECT COUNT(*) AS total FROM usuarios WHERE estado = 1 AND id <> 1",
         'cashbox'   => "SELECT COUNT(*) AS total FROM cajas WHERE estado = 1",
         // 'totalSalesPerDay' => "SELECT COUNT(*) AS total FROM ventas WHERE DATE(fecha) = CURRENT_DATE()"
         // 'totalStockMinimo'  => "SELECT COUNT(*) AS total FROM productos WHERE stock <= stock_minimo",
         // 'totalPurchasesPerMonth' => "SELECT COUNT(*) AS total FROM compras WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())",
         // 'totalSalesPerMonth'   => "SELECT COUNT(*) AS total FROM ventas WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())",
      ];

      foreach ($queries as $key => $sql) {
         $result = $this->queryMySQL($sql);
         $this->metrics[$key] = $result[0]['total'];
      }
   }

   /** Obtiene el valor de una métrica específica almacenada en $metrics.
    * 
    * @param string $name Nombre de la métrica a obtener.
    * @return mixed Valor de la métrica o null si no existe.
    */
   public function getMetric(string $name)
   {
      return $this->metrics[$name] ?? null;
   }
}