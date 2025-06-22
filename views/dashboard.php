<?php
session_start();

if (empty($_SESSION['id']) || empty($_SESSION['user']))
   header('Location: index.php');

require_once 'layout/header.php';

require_once '../models/Dashboard.php';
$Dashboard = new Dashboard();
?>

<!-- # [ M O D U L E  |  D A S H B O A R D ] # -->
<div class="row justify-content-center mx-auto w-100">
   <div class="col-sm-12 mb-3">
      <h3 class="mt-1 mb-2 text-center"><span class="text-success">Bienvenido</span>, <?php echo $_SESSION['user_name'] ?>.</h3>
   </div>
   <div class="col-sm-12 col-lg-12">
      <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">

         <!-- # [ P R O D U C T S ] # -->
         <?php if (in_array(4, $_SESSION['permisos'])) { ?>
            <div class="col pe-0">
               <div class="card radius-10 border-start border-0 border-5 border-primary">
                  <div class="card-body">
                     <div class="d-flex align-items-center">
                        <div>
                           <p class="mb-0 text-secondary font-16">Productos</p>
                           <h3 class="my-1 text-primary"><?= $Dashboard->getMetric('products') ?></h3>
                           <a href="product.php" class="mb-0 font-12">Detalles</a>
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
            <div class="col pe-0">
               <div class="card radius-10 border-start border-0 border-5 border-primary">
                  <div class="card-body">
                     <div class="d-flex align-items-center">
                        <div>
                           <p class="mb-0 text-secondary font-16">Marcas</p>
                           <h3 class="my-1 text-primary"><?= $Dashboard->getMetric('brands') ?></h3>
                           <a href="brand.php" class="mb-0 font-12">Detalles</a>
                        </div>
                        <div class="widgets-icons-3 rounded-circle bg-gradient-blues text-white ms-auto"><i class="fa-solid fa-layer-group"></i>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         <?php } ?>

         <!-- # [  C U S T O M E R S ] # -->
         <?php if (in_array(10, $_SESSION['permisos'])) { ?>
            <div class="col pe-0">
               <div class="card radius-10 border-start border-0 border-5 border-primary">
                  <div class="card-body">
                     <div class="d-flex align-items-center">
                        <div>
                           <p class="mb-0 text-secondary font-16">Clientes</p>
                           <h3 class="my-1 text-primary"><?= $Dashboard->getMetric('customers') ?></h3>
                           <a href="customer.php" class="mb-0 font-12">Detalles</a>
                        </div>
                        <div class="widgets-icons-3 rounded-circle bg-gradient-blues text-white ms-auto"><i class="fa-solid fa-users-line"></i>
                        </div>
                     </div>
                  </div>
               </div>
            </div>
         <?php } ?>

         <!-- # [  U S E R S  ] # -->
         <?php if (in_array(10, $_SESSION['permisos'])) { ?>
            <div class="col pe-0">
               <div class="card radius-10 border-start border-0 border-5 border-primary">
                  <div class="card-body">
                     <div class="d-flex align-items-center">
                        <div>
                           <p class="mb-0 text-secondary font-16">Usuarios</p>
                           <h3 class="my-1 text-primary"><?= $Dashboard->getMetric('users') ?></h3>
                           <a href="user.php" class="mb-0 font-12">Detalles</a>
                        </div>
                        <div class="widgets-icons-3 rounded-circle bg-gradient-blues text-white ms-auto"><i class="fa-solid fa-users"></i>
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