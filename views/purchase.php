<?php 
session_start();

if ( empty($_SESSION['user']) || !in_array(4, $_SESSION['permisos']) ) header('Location: index.php');
else {
   /** ### [ H E A D E R ] ### */
   require_once 'layout/header.php';
?>

<!-- # [ M O D U L E  |  P U R C H A S E S ] # -->
<div class="card">
   <div class="card-body p-4">

      <!-- # [ T I T L E ] # -->
      <div class="row">
         <div class="col-sm-12">
            <h5 class="view-title">
               <i class="fa-solid fa-bag-shopping me-1"></i>
               Compras
            </h5>
         </div>
      </div>

      <!-- # [ A C T I O N S ] # -->
      <div class="row">
         <div class="col-sm-12">
            <div class="d-flex justify-content-end">
               <button class="btn btn-success radius-30 px-4 fs-14" onclick="modulePurchase.modalAddPurchase()">
                  <i class="fa-solid fa-plus me-1"></i>
                  Compra
               </button>
            </div>
         </div>
      </div>

      <!-- # [ C O N T E N T ] # -->
      <div class="row">
         <table class="table table-striped view-table" id="table-purchases">
         </table>
      </div>
      
      <!-- # [ M O D A L S ] # -->
      <?php require 'modal/purchase/modalAddPurchase.php' ?>

   </div>
</div>

<?php 
   /** ### [ F O O T E R ] ### */
   require_once 'layout/footer.php'; 
?>

<!-- # [ S C R I P T ] # -->
<script src="../public/js/export/classPOS.js"></script>
<script src="../public/js/modules/modulePurchase.js"></script>

<?php } ?>