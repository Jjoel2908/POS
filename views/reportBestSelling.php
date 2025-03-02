<?php
session_start();

if ( empty($_SESSION['user']) || !in_array(13, $_SESSION['permisos']) ) header('Location: index.php');
else {
   /** ### [ H E A D E R ] ### */
   require_once 'layout/header.php';

?>
   <!-- # [   M O D U L E  |  B E S T   S E L L I N G   P R O D U C T S   ] # -->
   <div class="row justify-content-center">

      <div class="col-sm-12 col-lg-12 p-2">
         <div class="card radius-10 w-100">
            <div class="card-header">
               <div class="d-flex align-items-center">
                  <div>
                     <h5 class="my-1 py-1 fw-normal font-17"> <i class="fa-solid fa-arrow-up-wide-short me-2"></i> Productos Destacados</h5>
                  </div>
                  <div class="dropdown ms-auto">
                     <a class="dropdown-toggle dropdown-toggle-nocaret" href="#" data-bs-toggle="dropdown"><i class='bx bx-dots-horizontal-rounded font-22 text-option'></i></a>
                     <ul class="dropdown-menu">
                        <li>
                           <a class="dropdown-item" href="../../reports/productsBestSelling.php" target="_blank"><i class="fas fa-file-pdf text-danger me-1"></i> Generar PDF</a>
                        </li>
                     </ul>
                  </div>
               </div>
            </div>
            <div class="card-body">
               <div class="row justify-content-center">
                  <div class="col-sm-12 col-md-9">
                     <div class="chart-container-1">
                        <canvas id="products-best-selling" width="841" height="320"></canvas>
                     </div>
                  </div>
                  <div class="col-sm-12 col-md-9 mt-5">
                     <div class="table-responsive">
                        <table class="table align-middle">
                           <thead class="view-bg text-white text-center">
                              <tr>
                                 <th>Producto</th>
                                 <th>Categoría</th>
                                 <th>Código</th>
                                 <th>Ventas</th>
                                 <th>Total</th>
                              </tr>
                           </thead>
                           <tbody id="info-products">
                           </tbody>
                        </table>
                     </div>

                  </div>
               </div>
            </div>
         </div>
      </div>

   </div>

   <?php
   /** ### [ F O O T E R ] ### */
   require_once 'layout/footer.php';
   ?>

   <!-- # [ S C R I P T ] # -->
   <script src="../public/js/export/classPOS.js"></script>
   <script src="../public/js/modules/moduleReports.js"></script>
   <script>
      moduleBestSelling.productBestSelling();
   </script>

<?php } ?>