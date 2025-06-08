<?php
session_start();

if (empty($_SESSION['user'])) header('Location: index.php');
else {
   /** ### [ H E A D E R ] ### */
   require_once 'layout/header.php';

   require_once '../models/Dashboard.php';

   $Dashboard = new Dashboard();

   $products    = $Dashboard->getTotalProducts();
   $brands      = $Dashboard->getTotalBrands();
   $categories  = $Dashboard->getTotalCategories();
   $cashboxes   = $Dashboard->getTotalCashboxes();
   $users       = $Dashboard->getTotalUsers();
   $stockMinimo = $Dashboard->getTotalStockMinimo();
   $purchases   = $Dashboard->getTotalPurchasesPerMonth();
   $sales       = $Dashboard->getTotalSalesPerMonth();
   $salesPerDay = $Dashboard->getTotalSalesPerDay();
?>

   <!-- # [ M O D U L E  |  D A S H B O A R D ] # -->
   <div class="row justify-content-center w-100">
      <div class="col-sm-12 col-lg-12 mb-3">
         <h3 class="mt-4 mb-3 text-center"><span class="text-success">Bienvenido</span>, <?php echo $_SESSION['user_name'] ?>.</h3>
      </div>
      <div class="col-sm-12 col-lg-12">
         <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">

            <!-- # [ P R O D U C T S ] # -->
            <?php if (in_array(4, $_SESSION['permisos'])) { ?>
               <div class="col">
                  <div class="card radius-10 border-bottom border-0 border-5 border-primary">
                     <div class="card-body">
                        <div class="d-flex align-items-center">
                           <div>
                              <p class="mb-0 text-secondary">Productos</p>
                              <h4 class="my-1 text-primary"><?php echo $products ?></h4>
                              <a href="product.php" class="mb-0 font-13">Detalles</a>
                           </div>
                           <div class="widgets-icons-3 rounded-circle bg-gradient-blues text-white ms-auto"><i class="fa-brands fa-slack"></i>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            <?php } ?>

            <!-- # [  B R A N D S  ] # -->
            <?php if (in_array(3, $_SESSION['permisos'])) { ?>
               <div class="col">
                  <div class="card radius-10 border-bottom border-0 border-5 border-primary">
                     <div class="card-body">
                        <div class="d-flex align-items-center">
                           <div>
                              <p class="mb-0 text-secondary">Marcas</p>
                              <h4 class="my-1 text-primary"><?php echo $brands ?></h4>
                              <a href="brand.php" class="mb-0 font-13">Detalles</a>
                           </div>
                           <div class="widgets-icons-3 rounded-circle bg-gradient-blues text-white ms-auto"><i class="fa-solid fa-layer-group"></i>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            <?php } ?>

         </div>
      </div>
   </div>

   <?php require_once 'layout/footer.php'; ?>

   <!-- # [ S C R I P T ] # -->
   <script src="../public/js/modules/moduleDashboard.js"></script>
<?php } ?>