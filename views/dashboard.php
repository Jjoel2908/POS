<?php
session_start();

if ( empty($_SESSION['user']) ) header('Location: index.php');
else {
   /** ### [ H E A D E R ] ### */
   require_once 'layout/header.php';

   require_once '../models/Dashboard.php';

   $Dashboard = new Dashboard();

   $products    = $Dashboard->getTotalProducts();
   $categories  = $Dashboard->getTotalCategories();
   $cashboxes   = $Dashboard->getTotalCashboxes();
   $users       = $Dashboard->getTotalUsers();
   $stockMinimo = $Dashboard->getTotalStockMinimo();
   $purchases   = $Dashboard->getTotalPurchasesPerMonth();
   $sales       = $Dashboard->getTotalSalesPerMonth();
   $salesPerDay = $Dashboard->getTotalSalesPerDay();
?>

   <!-- # [ M O D U L E  |  D A S H B O A R D ] # -->
   <div class="row row justify-content-center">
      <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">

         <!-- # [ P R O D U C T S ] # -->
         <?php if ( in_array(3, $_SESSION['permisos']) ) { ?>
         <div class="col">
            <div class="card radius-10 border-bottom border-0 border-5 border-info">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div>
                        <p class="mb-0 text-secondary">Productos</p>
                        <h4 class="my-1 text-info"><?php echo $products ?></h4>
                        <a href="product.php" class="mb-0 font-13">Detalles</a>
                     </div>
                     <div class="widgets-icons-2 rounded-circle bg-gradient-blues text-white ms-auto"><i class="fa-brands fa-slack"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>

         <!-- # [  C A T E G O R I E S  ] # -->
         <?php if ( in_array(2, $_SESSION['permisos']) ) { ?>
         <div class="col">
            <div class="card radius-10 border-bottom border-0 border-5 border-danger">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div>
                        <p class="mb-0 text-secondary">Categorías</p>
                        <h4 class="my-1 text-danger"><?php echo $categories ?></h4>
                        <a href="category.php" class="mb-0 font-13">Detalles</a>
                     </div>
                     <div class="widgets-icons-2 rounded-circle bg-gradient-burning text-white ms-auto"><i class="bx bx-category"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>

         <!-- # [  C A S H B O X E S  ] # -->
         <?php if ( in_array(5, $_SESSION['permisos']) ) { ?>
         <div class="col">
            <div class="card radius-10 border-bottom border-0 border-5 border-success">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div>
                        <p class="mb-0 text-secondary">Cajas</p>
                        <h4 class="my-1 text-success"><?php echo $cashboxes ?></h4>
                        <a href="cashbox.php" class="mb-0 font-13">Detalles</a>
                     </div>
                     <div class="widgets-icons-2 rounded-circle bg-gradient-ohhappiness text-white ms-auto"><i class="fa-solid fa-bag-shopping"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>

         <!-- # [  U S E R S  ] # -->
         <?php if ( in_array(9, $_SESSION['permisos']) ) { ?>
         <div class="col">
            <div class="card radius-10 border-bottom border-0 border-5 border-warning">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div>
                        <p class="mb-0 text-secondary">Usuarios</p>
                        <h4 class="my-1 text-warning"><?php echo $users ?></h4>
                        <a href="user.php" class="mb-0 font-13">Detalles</a>
                     </div>
                     <div class="widgets-icons-2 rounded-circle bg-gradient-orange text-white ms-auto"><i class="bx bxs-group"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>

         <!-- # [  S T O C K   M I N I M U M  ] # -->
         <?php if ( in_array(3, $_SESSION['permisos']) ) { ?>
         <div class="col">
            <div class="card radius-10 border-bottom border-0 border-3 view-border">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div>
                        <p class="mb-0 text-secondary">Productos con Stock Mínimo</p>
                        <h4 class="my-1 view-text"><?php echo $stockMinimo ?></h4>
                        <a href="product.php" class="mb-0 font-13">Detalles</a>
                     </div>
                     <div class="widgets-icons view-bg-light view-text ms-auto"><i class="fa-solid fa-arrow-trend-down"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>

         <!-- # [  P U R C H A S E S   P E R   M O N T H  ] # -->
         <?php if ( in_array(4, $_SESSION['permisos']) ) { ?>
         <div class="col">
            <div class="card radius-10 border-bottom border-0 border-3 view-border">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div>
                        <p class="mb-0 text-secondary">Compras Por Mes</p>
                        <h4 class="my-1 view-text"><?php echo $purchases ?></h4>
                        <a href="purchase.php" class="mb-0 font-13">Detalles</a>
                     </div>
                     <div class="widgets-icons view-bg-light view-text ms-auto"><i class="fa-solid fa-calendar-days"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>

         <!-- # [  S A L E S   P E R   M O N T H  ] # -->
         <?php if ( in_array(6, $_SESSION['permisos']) ) { ?>
         <div class="col">
            <div class="card radius-10 border-bottom border-0 border-3 view-border">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div>
                        <p class="mb-0 text-secondary">Ventas Por Mes</p>
                        <h4 class="my-1 view-text"><?php echo $sales ?></h4>
                        <a href="sales.php" class="mb-0 font-13">Detalles</a>
                     </div>
                     <div class="widgets-icons view-bg-light view-text ms-auto"><i class="fa-regular fa-calendar-days"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>

         <!-- # [  S A L E S   P E R   D A Y  ] # -->
         <?php if ( $_SESSION['id'] == 1 ) { ?>
         <div class="col">
            <div class="card radius-10 border-bottom border-0 border-3 view-border">
               <div class="card-body">
                  <div class="d-flex align-items-center">
                     <div>
                        <p class="mb-0 text-secondary">Ventas Por Día</p>
                        <h4 class="my-1 view-text"><?php echo $salesPerDay ?></h4>
                        <a href="sales.php" class="mb-0 font-13">Detalles</a>
                     </div>
                     <div class="widgets-icons view-bg-light view-text ms-auto"><i class="fa-solid fa-cart-shopping"></i>
                     </div>
                  </div>
               </div>
            </div>
         </div>
         <?php } ?>

      </div>
   </div>

   <?php if ( $_SESSION['id'] == 1 ) { ?>
   <div class="row">
      <!-- # [    P R O D U C T S   S T O C K   M I N    ] # -->
      <div class="col-12 col-lg-6">
         <div class="card radius-10">
            <div class="card-body">
               <div class="d-flex align-items-center">
                  <div>
                     <h6 class="mb-0">Productos con Stock Mínimo</h6>
                  </div>
                  <div class="dropdown ms-auto">
                     <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class='bx bx-dots-horizontal-rounded font-22 text-option'></i></a>
                     <ul class="dropdown-menu">
                        <li>
                           <a class="dropdown-item" href="../reports/productsWithMinimunStock.php" target="_blank"><i class="fas fa-file-pdf text-danger me-1"></i> Generar Reporte</a>
                        </li>
                     </ul>
                  </div>
               </div>
               <div class="chart-container-2 mt-4">
                  <div class="text-center font-16" id="message"></div>
                  <canvas id="chart-stock-minimo"></canvas>
               </div>
            </div>
         </div>
      </div>

      <!-- # [   P R O D U C T S   B E S T   S E L L I  N G   ] # -->
      <div class="col-12 col-lg-6">
         <div class="card radius-10">
            <div class="card-body">
               <div class="d-flex align-items-center">
                  <div>
                     <h6 class="mb-0 mt-1">Los 3 Productos Más Vendidos</h6>
                  </div>
               </div>
               <div class="chart-container-2 mt-4">
                  <div class="text-center font-16" id="message-product"></div>
                  <canvas id="products-best-selling"></canvas>
               </div>
            </div>
         </div>
      </div>
   </div>
   <?php } ?>
   
   <?php
   /** ### [ F O O T E R ] ### */
   require_once 'layout/footer.php';
   ?>

   <!-- # [ S C R I P T ] # -->
   <script src="../public/js/modules/moduleDashboard.js"></script>

<?php } ?>