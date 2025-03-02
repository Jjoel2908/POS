<?php 
session_start();

if ( empty($_SESSION['user']) || !in_array(8, $_SESSION['permisos']) ) header('Location: index.php');
else {
   /** ### [ H E A D E R ] ### */
   require_once 'layout/header.php';
?>

<!-- # [ M O D U L E  |  R E T U R N S ] # -->
<div class="card">
   <div class="card-body p-4">

      <!-- # [ T I T L E ] # -->
      <div class="row">
         <div class="col-sm-12">
            <h5 class="view-title">
               <i class="fa-solid fa-person-chalkboard me-1"></i>
               Devoluciones
            </h5>
         </div>
      </div>

      <!-- # [ A C T I O N S ] # -->
      <div class="row">
         <div class="col-sm-12">
            <div class="d-flex justify-content-end">
               <button class="btn btn-success radius-30 px-4 fs-14" onclick="moduleReturns.modalAddReturn()">
                  <i class="fa-solid fa-plus me-1"></i>
                  Devolución
               </button>
            </div>
         </div>
      </div>

      <!-- # [ C O N T E N T ] # -->
      <div class="row">
         <table class="table table-striped view-table" id="table-returns">
         </table>
      </div>
      
      <!-- # [ M O D A L S ] # -->
      <?php require 'modal/return/modalAddReturns.php' ?>

   </div>
</div>

<?php 
   /** ### [ F O O T E R ] ### */
   require_once 'layout/footer.php'; 
?>

<!-- # [ S C R I P T ] # -->
<script src="../public/js/export/classPOS.js"></script>
<script src="../public/js/modules/moduleReturn.js"></script>

<?php } ?>