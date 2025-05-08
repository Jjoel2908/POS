<!-- # [ H E A D E R ] # -->
<?php
$formId = "formProduct";
$module = "Test";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12 view-form">
    <label class="mb-1" for="nombre">Nombre</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="hidden" name="id" id="id">
        <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Ingrese el nombre del producto" oninput="validateString(event)" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-chevron-right"></i></span>
    </div>
</div>

<div class="col-md-12 view-form">
    <label class="mb-1" for="precio_venta">Precio Venta</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="number" step="0.01" min="0" name="precio_venta" id="precio_venta" placeholder="Precio Venta" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-sack-dollar"></i></span>
    </div>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>