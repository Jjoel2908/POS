<!-- # [ H E A D E R ] # -->
<?php
$formId = "formProduct";
$module = "Producto";
$modalClass = "view-modal";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12 view-form">
    <label class="mb-1" for="nombre">Nombre</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="hidden" name="id" id="id">
        <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Ingrese el nombre del producto" oninput="validateAndConvertToUppercase(event)" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-chevron-right"></i></span>
    </div>
</div>

<div class="col-md-12 col-lg-6 view-form">
    <label class="mb-1" for="id_marca">Marca</label>
    <select name="id_marca" id="id_marca" class="form-control select" required></select>
</div>

<div id="container-code" class="col-md-12 col-lg-6 view-form">
    <label class="mb-1" for="codigo">Código</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="text" name="codigo" id="codigo" placeholder="Código del producto" oninput="validateString(event)" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="bx bx-barcode"></i></span>
    </div>
</div>

<div class="col-md-12 col-lg-6 view-form">
    <label class="mb-1" for="modelo">Modelo</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="text" name="modelo" id="modelo" placeholder="Modelo del producto" oninput="validateString(event)">
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-tag"></i></span>
    </div>
</div>

<div class="col-md-12 col-lg-6 view-form">
  <label class="mb-1" for="id_presentacion">Talla / Presentación</label>
  <select name="id_presentacion" id="id_presentacion" class="form-control select"></select>
</div>

<div class="col-md-12 col-lg-6 view-form">
  <label class="mb-1" for="id_color">Color</label>
  <select name="id_color" id="id_color" class="form-control select"></select>
</div>

<div class="col-md-12 col-lg-6 view-form">
    <label class="mb-1" for="imagen">Imagen</label>
    <input class="form-control" type="hidden" name="current_image" id="current_image">
    <input class="form-control" type="file" name="imagen" id="imagen" accept="image/png, image/jpeg, image/jpg, image/webp">
</div>

<div class="col-md-6 view-form">
    <label class="mb-1" for="precio_compra">Precio Compra</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="number" step="0.01" min="0" name="precio_compra" id="precio_compra" placeholder="Precio Compra" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-sack-dollar"></i></span>
    </div>
</div>

<div class="col-md-6 view-form">
    <label class="mb-1" for="precio_venta">Precio Venta</label>
    <div class="position-relative input-icon">
        <input class="form-control" type="number" step="0.01" min="0" name="precio_venta" id="precio_venta" placeholder="Precio Venta" required>
        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-sack-dollar"></i></span>
    </div>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>