<?php
session_start();

if ( empty($_SESSION['user']) || !in_array(12, $_SESSION['permisos']) ) header('Location: index.php');
else {
   /** ### [ H E A D E R ] ### */
   require_once 'layout/header.php';
?>

   <!-- # [ M O D U L E  |  C A T E G O R Y ] # -->
   <div id="container-report" class="card bg-transparent shadow-0 p-2">
      <div class="card-body">

         <!-- # [ T I T L E ] # -->
         <div class="row">
            <div class="col-sm-12">
               <h5 class="view-title">
                  <i class="fa-solid fa-chart-column me-1"></i>
                  Consultar Ventas
               </h5>
            </div>
         </div>

         <!-- # [ S E A R C H ] # -->
         <form class="validation" id="formReportSales" method="POST" action="" name="" novalidate="">
            <div class="row justify-content-center">
               <div class="col-sm-12 col-md-3 text-center pe-0">
                  <label class="mb-2" for="date">Selecciona un rango de fechas</label>
                  <div class="position-relative input-icon">
                     <input class="form-control" type="text" name="date" id="date" required>
                     <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-calendar-days font-20"></i>
                  </div>
               </div>
               <div class="col-sm-12 col-md-1 d-flex align-items-end ps-0">
                  <button type="submit" class="btn btn-success"><i class="fa-solid fa-magnifying-glass"></i></button>
               </div>
            </div>
         </form>

         <!-- # [ C O N T E N T ] # -->
         <div id="response" class="card radius-10 bg-transparent shadow-0 mt-4 d-none">
            <div class="card-header py-3 border-3">
               <div class="d-flex align-items-center justify-content-center">
                  <div>
                     <h6 class="mb-0 font-18">Resumen de Ventas <span class="mx-2">|</span> <span id="day"></span></h6>
                  </div>
               </div>
            </div>
            <div class="card-body">
               <!-- # [  W I D G E T S  ] # -->
               <div class="row row-cols-1 row-cols-md-2 row-cols-xl-2 row-cols-xxl-4 mt-2 justify-content-center">
                  <!-- # [ W I D G E T   T O T A L ] # -->
                  <div class="col">
                     <div class="card radius-10 view-bg mb-2">
                        <div class="card-body">
                           <div class="d-flex align-items-center">
                              <div class="me-auto">
                                 <p class="mb-0 text-white font-16">Total Ventas</p>
                                 <h4 class="my-1 text-white">$<span id="total">0.00</span></h4>
                              </div>
                              <div class="font-50 text-white">
                                 <i class="fa-solid fa-sack-dollar"></i>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>

                  <!-- # [ W I D G E T   W I N S ] # -->
                  <div class="col">
                     <div class="card radius-10 bg-gradient-ohhappiness mb-2">
                        <div class="card-body">
                           <div class="d-flex align-items-center">
                              <div class="me-auto">
                                 <p class="mb-0 text-white font-16">Ganancias</p>
                                 <h4 class="my-1 text-white">$<span id="earnings">0.00</span></h4>
                              </div>
                              <div class="font-50 text-white">
                                 <i class="fa-solid fa-cash-register"></i>
                              </div>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
               <!-- # [  T A B L E  ] # -->
               <div class="row mb-2">
                  <div class="col-sm-12 col-md-12">
                     <div class="table-responsive">
                        <table id="table-report-sales" class="table table-striped view-table"></table>
                     </div>
                  </div>
               </div>
            </div>
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
   <script src="../public/js/modules/moduleReports.js"></script>

<?php } ?>