<!-- # [ H E A D E R ] # -->
<?php 
   $formId = "formCategory"; 
   $module = "Categoría";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12 view-form my-3">
   <div class="position-relative input-icon">
      <input class="form-control" type="hidden" name="id" id="id">
      <input class="form-control" type="text" id="nombre" name="nombre" maxlength="100" placeholder="Ingrese el nombre de categoría" oninput="validateAndConvertToUppercase(event)" required>
      <span class="position-absolute top-50 translate-middle-y"><i class="bx bx-category"></i></span>
   </div>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>