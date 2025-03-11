<!-- # [ H E A D E R ] # -->
<?php 
   $formId = "formReturns"; 
   $module = "Devolucion";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
 
<div class="col-md-12 view-form">
                     <label class="mb-1" for="producto"><i class="fa-solid fa-basket-shopping me-1"></i> Venta</label>
                     <select name="id_venta" id="id_venta" class="form-control select"></select>
                  </div>   

                  <div class="col-md-12 view-form">
                     <label class="mb-1" for="producto"><i class="fa-brands fa-slack me-1"></i> Producto</label>
                     <select name="id_detail" id="id_detail" class="form-control select"></select>
                  </div>

                  <div class="col-md-12 view-form">
                     <label class="mb-1" for="cantidad">Cantidad</label>
                     <div class="position-relative input-icon">
                        <input type="hidden" name="precio" id="precio">
                        <input type="hidden" name="id_producto" id="id_producto">
                        <input class="form-control" type="number" name="cantidad" id="cantidad" min="1" placeholder="Cantidad de producto devuelto" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="bx bx-cube"></i></span>
                     </div>
                  </div>

                  <div class="col-md-12 view-form">
                     <label class="mb-1" for="motivo">Motivo</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="text" name="motivo" id="motivo" placeholder="Escribe el motivo de devolución" oninput="validateString(event)" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-comment"></i></span>
                     </div>
                  </div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>