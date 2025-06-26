<!-- # [ H E A D E R ] # -->
<?php
$formId = "formProduct";
$module = "Producto";
$modalClass = "view-modal";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<input class="form-control" type="hidden" name="id" id="id">

<div class="col-md-12 col-lg-6 view-form">
    <label class="mb-1" for="id_tipo_gasto">Tipo de Gasto</label>
    <select name="id_tipo_gasto" id="id_tipo_gasto" class="form-control select" required>
    </select>
</div>

<div class="col-md-6 view-form">
    <label class="mb-1" for="monto">Monto</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="number" step="0.01" min="0" name="monto" id="monto" placeholder="Monto del Gasto" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-sack-dollar"></i></span>
    </div>
</div>

<div class="col-md-12 view-form">
    <label class="mb-1" for="descripcion">Descripción</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="text" name="descripcion" id="descripcion" maxlength="150" placeholder="Escribe una breve descripción" oninput="validateWithCommasAndDots(event)" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-chevron-right"></i></span>
    </div>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>