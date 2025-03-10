<!-- # [ H E A D E R ] # -->
<?php 
   $formId = "formCreditSale"; 
   $module = "Ventas";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="row px-3">
                  <div class="col-md-12 view-form d-none fecha">
                     <label class="mb-1" for="fecha_limite">Fecha de Vencimiento</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="date" name="fecha_limite" id="fecha_limite" min="<?php date("d-m-Y") ?>">
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-regular fa-calendar-days"></i>
                     </div>
                  </div>

                  <div class="col-md-12 view-form anticipo">
                     <label class="mb-1" for="anticipo">Monto de Anticipo</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="number" step="0.01" min="0" name="anticipo" id="anticipo" placeholder="Ingrese el monto de anticipo" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-sack-dollar"></i></span>
                     </div>
                  </div>
               </div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>


<div class="modal-footer">
               <button id="btnSave" type="submit" class="btn btn-primary fs-14 rounded-0"><i class="fa-solid fa-money-bill me-1"></i> Realizar Venta</button>
               <button id="btnCreditCancel" type="button" class="btn btn-danger fs-14 rounded-0" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> Salir</button>
            </div>