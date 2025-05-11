<?php

session_start();

if (empty($_SESSION['user']) || !in_array(15, $_SESSION['permisos'])) {
   header('Location: index.php');
   exit;
}

require_once 'layout/header.php';
?>

<div class="card bg-transparent shadow-0" data-module="Test">
   <div class="card-body pt-1">

      <!-- # [ T I T L E ] # -->
      <div class="row">
         <div class="col-sm-12">
            <h5 class="view-title">
               <i class="fa-solid fa-chevron-right me-1"></i>
               Productos
            </h5>
         </div>
      </div>

      <!-- # [ A C T I O N S ] # -->
         <div class="row">
            <div class="col-sm-12">
               <div class="d-flex justify-content-end">
                  <button class="btn btn-warning radius-30 px-4 fs-14" onclick="openModal('Producto')">
                     <i class="fa-solid fa-plus me-1"></i>
                     Agregar Producto
                  </button>
               </div>
            </div>
         </div>

      <!-- # [ C O N T E N T ] # -->
      <div id="table-empty" class="row mt-5 d-none">
         <table class="table table-striped table-bordered view-table text-center">
            <thead>
               <tr>
                  <th>Producto</th>
               </tr>
            </thead>
            <tbody>
               <tr>
                  <td class="text-center">
                     <p class="font-15 my-2">
                        <i class="fa-solid fa-magnifying-glass-chart me-1"></i>
                        Sin información disponible por el momento.
                     </p>
                  </td>
               </tr>
            </tbody>
         </table>
      </div>

      <div id="table-data" class="row d-none">
         <table class="table table-striped table-bordered view-table" id="module-table">
         </table>
      </div>

      <!-- # [ M O D A L S ] # -->
      <?php require 'modal/modalProductTest.php'; ?>

      <?php require_once 'layout/footer.php'; ?>
      <script src="../public/js/modules/moduleRecord.js"></script>
      <script src="../public/js/modules/moduleProductTest.js"></script>

   </div>
</div>