<?php 
session_start();

if ( empty($_SESSION['user']) || !in_array(7, $_SESSION['permisos']) ) header('Location: index.php');
else {
   /** ### [ H E A D E R ] ### */
   require_once 'layout/header.php';
?>

<!-- # [ M O D U L E  |  S A L E S ] # -->
<div class="card">
   <div class="card-body p-4">

      <!-- # [ T I T L E ] # -->
      <div class="row">
         <div class="col-sm-12">
            <h5 class="view-title">
               <i class="fa-solid fa-hand-holding-dollar me-1"></i>
               Ventas a crédito
            </h5>
         </div>
      </div>

      <!-- # [ C O N T E N T ] # -->
      <div class="row">
         <table class="table table-striped view-table" id="table-credit-sales">
         </table>
      </div>

      <!-- # [ M O D A L S ] # -->
      <?php require 'modal/modalViewDetails.php' ?>

   </div>
</div>

<?php 
   /** ### [ F O O T E R ] ### */
   require_once 'layout/footer.php'; 
?>

<!-- # [ S C R I P T ] # -->
<script src="../public/js/export/classPOS.js"></script>
<script src="../public/js/modules/moduleSales.js"></script>

<script>
   $(() => { 
      moduleSales.tableCreditSales();
   });
</script>

<?php } ?>