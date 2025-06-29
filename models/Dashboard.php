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
         'products'         => "SELECT COUNT(*) AS total FROM productos WHERE estado = 1",
         'brands'           => "SELECT COUNT(*) AS total FROM marcas WHERE estado = 1",
         'customers'        => "SELECT COUNT(*) AS total FROM clientes WHERE estado = 1",
         'users'            => "SELECT COUNT(*) AS total FROM usuarios WHERE estado = 1 AND id <> 1",
      ];

      foreach ($queries as $key => $sql) {
         $result = $this->queryMySQL($sql);
         $this->metrics[$key] = $result[0]['total'];
      }

      /** Métricas adicionales con funciones */
      $this->metrics['monthly_purchases'] = $this->getMonthlyPurchases();
      $this->metrics['daily_sales']       = $this->getDailySales();
      $this->metrics['monthly_sales']     = $this->getMonthlySales();
      $this->metrics['pending_credit']    = $this->getPendingCredit();
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

   /** Total de compras del mes */
   private function getMonthlyPurchases(): string
   {
      $sql = "SELECT SUM(total) AS total FROM compras 
         WHERE MONTH(fecha) = MONTH(CURDATE()) 
         AND YEAR(fecha) = YEAR(CURDATE()) 
         AND estado = 1
      ";

      $total = $this->queryMySQL($sql)[0]['total'] ?? 0;
      return '$' . number_format($total, 2);
   }

   /** Total de ventas del día */
   private function getDailySales(): string
   {
      $sql = "SELECT SUM(total_venta) AS total FROM ventas 
         WHERE DATE(fecha) = CURDATE() 
         AND estado = 1
      ";

      $total = $this->queryMySQL($sql)[0]['total'] ?? 0;
      return '$' . number_format($total, 2);
   }

   /** Total de ventas del mes */
   private function getMonthlySales(): string
   {
      $sql = "SELECT SUM(total_venta) AS total FROM ventas 
         WHERE MONTH(fecha) = MONTH(CURDATE()) 
         AND YEAR(fecha) = YEAR(CURDATE()) 
         AND estado = 1
      ";

      $total = $this->queryMySQL($sql)[0]['total'] ?? 0;
      return '$' . number_format($total, 2);
   }

   /** Total pendiente por cobrar */
   private function getPendingCredit(): string
   {
      $sql = "SELECT SUM(total_venta - total_pagado) AS total FROM ventas 
         WHERE estado = 1 
         AND tipo_venta = 2 
         AND estado_pago IN (2, 3)
      ";

      $total = $this->queryMySQL($sql)[0]['total'] ?? 0;
      return '$' . number_format($total, 2);
   }
}
