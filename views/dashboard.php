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
      'key'        => 'brands',
      'perm'       => 3,
      'title'      => 'Marcas',
      'icon'       => 'fa-solid fa-layer-group',
      'background' => 'bg-gradient-deepblue',
   ],
   [
      'key'        => 'categories',
      'perm'       => 2,
      'title'      => 'Categorías',
      'icon'       => 'bx bx-category',
      'link'       => 'category.php',
      'background' => 'bg-gradient-orange',
   ],
   [
      'key'        => 'products',
      'perm'       => 4,
      'title'      => 'Productos',
      'icon'       => 'fa-brands fa-slack',
      'link'       => 'product.php',
      'background' => 'bg-gradient-ibiza',
   ],
   [
      'key'        => 'cashboxes',
      'perm'       => 6,
      'title'      => 'Cajas',
      'icon'       => 'fa-solid fa-cash-register',
      'link'       => 'cashbox.php',
      'background' => 'bg-gradient-burning',
   ],
   [
      'key'   => 'open_cashboxes',
      'perm'  => 6,
      'title' => 'Cajas Abiertas',
      'icon'  => 'fa-solid fa-cash-register',
      'link'  => 'cashbox.php',
      'background' => 'bg-gradient-cosmic',
   ],
   [
      'key'   => 'customers',
      'perm'  => 30,
      'title' => 'Clientes',
      'icon'  => 'fa-solid fa-users-line',
      'link'  => 'customer.php',
      'background' => 'bg-gradient-lush',
   ],
   [
      'key'   => 'users',
      'perm'  => 31,
      'title' => 'Usuarios',
      'icon'  => 'fa-solid fa-users',
      'link'  => 'user.php',
      'background' => 'bg-gradient-kyoto',
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
      'key'    => 'monthly_expenses',
      'perm'   => 10,
      'title'  => 'Gastos Por Mes',
      'icon'   => 'fa-solid fa-coins',
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
   <div class="col-sm-12 mb-3">
      <h3 class="mt-1 mb-2 text-center"><span class="text-success">Bienvenido</span>, <?php echo $_SESSION['user_name'] ?>.</h3>
   </div>

   <div class="col-sm-12 col-lg-8 mb-3">
      <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2">
         <?php foreach ($generalInformation as $card): ?>
            <?php if (in_array($card['perm'], $_SESSION['permisos'])): ?>
               <div class="col pe-0">
                  <div class="card radius-10 mx-sm-0 mx-lg-4 <?= $card['background'] ?>">
                     <div class="card-body">
                        <div class="d-flex align-items-center p-1">
                           <div>
                              <h2 class="my-1 text-white">
                                 <?= $Dashboard->getMetric($card['key']) ?>
                              </h2>
                              <p class="mb-0 font-16 text-white"><?= $card['title'] ?></p>
                           </div>
                           <div class="font-50 text-white ms-auto">
                              <i class="<?= $card['icon'] ?>"></i>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            <?php endif; ?>
         <?php endforeach; ?>
      </div>
   </div>

   <div class="col-sm-12 col-lg-4 mb-3">
      <div class="row row-cols-1 row-cols-lg-2 row-cols-xl-1">
         <div class="col pe-0">
            <div class="card bg-gradient-moonlit mb-0 radius-10">
               <div class="card-header pt-3">
                  <p class="m-0 text-white font-18 text-center py-2">Estadísticas Operativas</p>
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