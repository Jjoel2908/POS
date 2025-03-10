<!-- # [ H E A D E R ] # -->
<?php 
   $formId = "formCashbox"; 
   $module = "Caja";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12 view-form my-3">
<label class="mb-1" for="caja">Nombre de Caja</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="hidden" name="id" id="id">
                        <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Ingrese el nombre de caja" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fas fa-shopping-bag"></i></span>
                     </div>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>