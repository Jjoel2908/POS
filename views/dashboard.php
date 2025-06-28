<?php
session_start();

if (empty($_SESSION['id']) || empty($_SESSION['user']))
   header('Location: index.php');

require_once 'layout/header.php';

require_once '../models/Dashboard.php';
$Dashboard = new Dashboard();

/** Tarjetas a mostrar en el Dashboard */
$cards = [
   [
      'key'   => 'products',
      'perm'  => 4,
      'title' => 'Productos',
      'icon'  => 'fa-brands fa-slack',
      'link'  => 'product.php',
      'color' => 'primary',
   ],
   [
      'key'   => 'brands',
      'perm'  => 3,
      'title' => 'Marcas',
      'icon'  => 'fa-solid fa-layer-group',
      'link'  => 'brand.php',
      'color' => 'primary',
   ],
   [
      'key'   => 'customers',
      'perm'  => 10,
      'title' => 'Clientes',
      'icon'  => 'fa-solid fa-users-line',
      'link'  => 'customer.php',
      'color' => 'primary',
   ],
   [
      'key'   => 'users',
      'perm'  => 10,
      'title' => 'Usuarios',
      'icon'  => 'fa-solid fa-users',
      'link'  => 'user.php',
      'color' => 'primary',
   ],
];
?>

<!-- # [ M O D U L E  |  D A S H B O A R D ] # -->
<div class="row justify-content-center mx-auto w-100">
   <div class="col-sm-12 mb-3">
      <h3 class="mt-1 mb-2 text-center"><span class="text-success">Bienvenido</span>, <?php echo $_SESSION['user_name'] ?>.</h3>
   </div>
   <div class="col-sm-12 col-lg-12">
      <div class="row row-cols-1 row-cols-md-2 row-cols-xl-4">
         <?php foreach ($cards as $card): ?>
            <?php if (in_array($card['perm'], $_SESSION['permisos'])): ?>
               <div class="col pe-0">
                  <div class="card radius-10 border-start border-0 border-5 border-<?= $card['color'] ?>">
                     <div class="card-body">
                        <div class="d-flex align-items-center">
                           <div>
                              <p class="mb-0 text-secondary font-16"><?= $card['title'] ?></p>
                              <h3 class="my-1 text-<?= $card['color'] ?>">
                                 <?= $Dashboard->getMetric($card['key']) ?>
                              </h3>
                              <a href="<?= $card['link'] ?>" class="mb-0 font-12">Detalles</a>
                           </div>
                           <div class="widgets-icons-3 rounded-circle bg-gradient-blues text-white ms-auto">
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
</div>

<?php require_once 'layout/footer.php'; ?>