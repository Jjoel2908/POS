<?php
session_start();

if (empty($_SESSION['id']) || empty($_SESSION['user']))
   header('Location: index.php');

require_once 'layout/header.php';

require_once '../models/Dashboard.php';
$Dashboard = new Dashboard();

/** Tarjetas a mostrar en el Dashboard */
$generalInformation = [
   [
      'key'   => 'brands',
      'perm'  => 3,
      'title' => 'Marcas',
      'icon'  => 'fa-solid fa-layer-group',
   ],
   [
      'key'   => 'categories',
      'perm'  => 2,
      'title' => 'Categorías',
      'icon'  => 'bx bx-category',
   ],
   [
      'key'   => 'products',
      'perm'  => 4,
      'title' => 'Productos',
      'icon'  => 'fa-brands fa-slack',
   ],
   [
      'key'   => 'cashboxes',
      'perm'  => 6,
      'title' => 'Cajas',
      'icon'  => 'fa-solid fa-cash-register',
   ],
   [
      'key'   => 'customers',
      'perm'  => 30,
      'title' => 'Clientes',
      'icon'  => 'fa-solid fa-users-line',
   ],
   [
      'key'   => 'users',
      'perm'  => 31,
      'title' => 'Usuarios',
      'icon'  => 'fa-solid fa-users',
   ],
];

$metrics = [
   [
      'key'    => 'daily_sales',
      'perm'   => 7,
      'title'  => 'Ventas Por Día',
      'icon'   => 'fa-solid fa-cart-shopping',
   ],
   [
      'key'    => 'daily_expenses',
      'perm'   => 10,
      'title'  => 'Gastos Por Día',
      'icon'   => 'fa-solid fa-coins',
   ],
   [
      'key'    => 'monthly_sales',
      'perm'   => 7,
      'title'  => 'Ventas Por Mes',
      'icon'   => 'fa-solid fa-cart-shopping',
   ],
   [
      'key'    => 'monthly_purchases',
      'perm'   => 7,
      'title'  => 'Compras Por Mes',
      'icon'   => 'fa-solid fa-bag-shopping',
   ],
   [
      'key'    => 'pending_credit',
      'perm'   => 7,
      'title'  => 'Pendiente por Cobrar',
      'icon'   => 'fa-solid fa-money-bill-wave',
   ],
];
?>

<!-- # [ M O D U L E  |  D A S H B O A R D ] # -->
<div class="row justify-content-center mx-auto w-100">
   <div class="col-sm-12 mb-4">
      <h3 class="mt-1 mb-2 text-center"><span class="text-success">Bienvenido</span>, <?php echo $_SESSION['user_name'] ?>.</h3>
   </div>

   <div class="col-sm-12 col-lg-3">
      <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-1">
         <div class="col">
            <div class="card bg-gradient-deepblue mb-0 radius-10">
               <div class="card-header pt-3">
                  <p class="m-0 text-white font-18 text-center pt-1">Datos Generales</p>
               </div>
               <div class="card-body px-0 pt-1 pb-3">
                  <ul class="list-group list-group-flush">
                     <?php foreach ($generalInformation as $moduleData): ?>
                        <?php if (in_array($moduleData['perm'], $_SESSION['permisos'])): ?>
                           <li class="list-group-item bg-transparent text-white">
                              <div class="d-flex justify-content-between py-1">
                                 <p class="m-0 font-17">
                                    <i class="<?= $moduleData['icon'] ?> me-2"></i>
                                    <?= $moduleData['title'] ?>
                                 </p>
                                 <p class="m-0 font-17"><?= $Dashboard->getMetric($moduleData['key']) ?></p>
                              </div>
                           </li>
                        <?php endif; ?>
                     <?php endforeach; ?>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>

   <!-- # [   P R O D U C T S   B E S T   S E L L I  N G   ] # -->
   <div class="col-12 col-lg-5">
      <div class="card radius-10 mb-0">
         <div class="card-body">
            <div class="d-flex align-items-center justify-content-center">
               <h6 class="mb-0 mt-1 font-18">Los 3 Productos Más Vendidos</h6>
            </div>
            <div class="chart-container-1">
               <div class="text-center font-17 mt-4 text-danger" id="message-product"></div>
               <canvas id="products-best-selling"></canvas>
            </div>
         </div>
      </div>
   </div>

   <div class="col-sm-12 col-lg-4">
      <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-1">
         <div class="col">
            <div class="card bg-gradient-moonlit mb-0 radius-10">
               <div class="card-header pt-3">
                  <p class="m-0 text-white font-18 text-center pt-1">Estadísticas Operativas</p>
               </div>
               <div class="card-body px-0 pt-1 pb-3">
                  <ul class="list-group list-group-flush">
                     <?php foreach ($metrics as $moduleData): ?>
                        <?php if (in_array($moduleData['perm'], $_SESSION['permisos'])): ?>
                           <li class="list-group-item bg-transparent text-white">
                              <div class="d-flex justify-content-between py-1">
                                 <p class="m-0 font-17">
                                    <i class="<?= $moduleData['icon'] ?> me-2"></i>
                                    <?= $moduleData['title'] ?>
                                 </p>
                                 <p class="m-0 font-17"><?= $Dashboard->getMetric($moduleData['key']) ?></p>
                              </div>
                           </li>
                        <?php endif; ?>
                     <?php endforeach; ?>
                  </ul>
               </div>
            </div>
         </div>
      </div>
   </div>

</div>

<?php require_once 'layout/footer.php'; ?>

<!-- # [ S C R I P T ] # -->
<script src="../public/js/modules/moduleDashboard.js"></script>