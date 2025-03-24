<!-- # [ H E A D E R ] # -->
<?php
$formId = "formBrand";
$module = "Marca";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12 view-form my-3">
    <div class="position-relative input-icon">
        <input class="form-control" type="hidden" name="id" id="id">
        <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Ingrese el nombre de la marca" oninput="validateAndConvertToUppercase(event)" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-layer-group"></i></span>
    </div>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>