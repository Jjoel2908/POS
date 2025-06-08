<?php
require '../config/Connection.php';
date_default_timezone_set('America/Mexico_City');
class Dashboard extends Connection
{

   private $totalProducts;
   private $totalCategories;
   private $totalCashboxes;
   private $totalUsers;
   private $totalStockMinimo;
   private $totalPurchasesPerMonth;
   private $totalSalesPerMonth;
   private $totalSalesPerDay;

   public function __construct() {
      $this->fetchData();
   }

   private function fetchData() {
      $this->resultProducts();
      $this->resultCategories();
      $this->resultCashboxes();
      $this->resultUsers();
      // $this->resultStockMinimo();
      $this->resultPurchasesPerMonth();
      $this->resultSalesPerMonth();
      $this->resultSalesPerDay();
   }

   private function resultProducts() {
      $query = $this->queryMySQL("SELECT COUNT(*) AS total FROM productos WHERE estado = 1");
      $this->totalProducts = $query[0]['total'];
   }

   private function resultCategories() {
      $query = $this->queryMySQL("SELECT COUNT(*) AS total FROM categorias WHERE estado = 1");
      $this->totalCategories = $query[0]['total'];
   }

   private function resultCashboxes() {
      $query = $this->queryMySQL("SELECT COUNT(*) AS total FROM cajas WHERE estado = 1");
      $this->totalCashboxes = $query[0]['total'];
   }

   private function resultUsers() {
      $query = $this->queryMySQL("SELECT COUNT(*) AS total FROM usuarios WHERE estado = 1");
      $this->totalUsers = $query[0]['total'];
   }

   // private function resultStockMinimo() {
   //    $query = $this->queryMySQL("SELECT COUNT(*) AS total FROM productos WHERE stock <= stock_minimo");
   //    $this->totalStockMinimo = $query[0]['total'];
   // }

   private function resultPurchasesPerMonth() {
      $query = $this->queryMySQL("SELECT COUNT(*) AS total FROM compras WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())");
      $this->totalPurchasesPerMonth = $query[0]['total'];
   }

   private function resultSalesPerMonth() {
      $query = $this->queryMySQL("SELECT COUNT(*) AS total FROM ventas WHERE MONTH(fecha) = MONTH(CURRENT_DATE()) AND YEAR(fecha) = YEAR(CURRENT_DATE())");
      $this->totalSalesPerMonth = $query[0]['total'];
   }

   private function resultSalesPerDay() {
      $query = $this->queryMySQL("SELECT COUNT(*) AS total FROM ventas WHERE DAY(fecha) = DAY(CURRENT_DATE())  ");
      $this->totalSalesPerDay = $query[0]['total'];
   }

   public function getProductStockMinimo(): array {
      $query = $this->queryMySQL("SELECT * FROM productos WHERE stock <= stock_minimo");
      return $query;
   }

   public function getProductBestSelling(): array {
      $query = $this->queryMySQL("SELECT dv.*, p.nombre AS nombre_producto, SUM(cantidad) AS total_selling FROM detalle_venta dv INNER JOIN productos p ON dv.id_producto = p.id GROUP BY nombre_producto ORDER BY total_selling DESC LIMIT 3");
      return $query;
   }

   private function generateInitialMenu(): string
   {
      return   '<li>
                  <a href="dashboard.php">
                     <div class="parent-icon"><i class="fa-solid fa-gauge-high me-1"></i></div>
                     <div class="menu-title">Inicio</div>
                  </a>
               </li>';
   }

   public function getMenu(): string
   {
      $permissions = self::selectAll("permisos");
      $submodules  = '';

      $modules = $this->generateInitialMenu();

      foreach ($permissions as $permission) {

         if ( !in_array($permission['id'], $_SESSION['permisos']) ) continue;

         if ( $permission['icono'] != 'submodulo' ) {
            $modules .= '<li>
                           <a href="' . $permission['archivo'] . '">
                              <div class="parent-icon"><i class="' . $permission['icono'] . '"></i>
                              </div>
                              <div class="menu-title">' . $permission['nombre'] . '</div>
                           </a>
                        </li>';
         } else {
            $submodules .= '<li> <a href="' . $permission['archivo'] . '"><i class="fa-solid fa-chevron-right me-3"></i>' . $permission['nombre'] . '</a></li>';
         }
      }

      if ( count(array_intersect([11, 12, 13], $_SESSION['permisos'])) > 0 ) {
         $modules .= '<li>
                        <a class="has-arrow" href="javascript:;">
                           <div class="parent-icon"><i class="fa-solid fa-chart-column me-1"></i>
                           </div>
                           <div class="menu-title">Reportes</div>
                        </a>
                        <ul>
                        ' . $submodules . '
                        </ul>
                     </li>';
      }

      return $modules;
   }

   public function getTotalProducts() {
      return $this->totalProducts;
   }

   public function getTotalCategories() {
      return $this->totalCategories;
   }

   public function getTotalCashboxes() {
      return $this->totalCashboxes;
   }

   public function getTotalUsers() {
      return $this->totalUsers;
   }

   public function getTotalStockMinimo() {
      return $this->totalStockMinimo;
   }

   public function getTotalPurchasesPerMonth() {
      return $this->totalPurchasesPerMonth;
   }

   public function getTotalSalesPerMonth() {
      return $this->totalSalesPerMonth;
   }

   public function getTotalSalesPerDay() {
      return $this->totalSalesPerDay;
   }

}
