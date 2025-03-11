<!-- # [ H E A D E R ] # -->
<?php 
   $formId = "formProduct"; 
   $module = "Producto";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="row px-2">
                        <div class="col-md-12 view-form">
                            <label class="mb-1" for="nombre">Nombre</label>
                            <div class="position-relative input-icon">
                                <input class="form-control" type="hidden" name="id" id="id">
                                <input class="form-control" type="text" name="nombre" id="nombre" maxlength="100" placeholder="Ingrese el nombre de producto" oninput="validateString(event)" required>
                                <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-chevron-right"></i></span>
                            </div>
                        </div>

                        <div id="container-code" class="col-md-12 view-form">
                            <label class="mb-1" for="codigo">Código</label>
                            <div class="position-relative input-icon">
                                <input class="form-control" type="text" name="codigo" id="codigo" placeholder="Código del producto" oninput="validateString(event)" required>
                                <span class="position-absolute top-50 translate-middle-y"><i class="bx bx-barcode"></i></span>
                            </div>
                        </div>

                        <div class="col-md-12 view-form">
                            <label class="mb-1" for="id_categoria">Categoría</label>
                            <select name="id_categoria" id="id_categoria" class="form-control select" required>
                            </select>
                        </div>

                        <div class="col-md-12 view-form">
                            <label class="mb-1" for="stock_minimo">Stock Mínimo</label>
                            <div class="position-relative input-icon">
                                <input class="form-control" type="number" step="1" min="0" name="stock_minimo" id="stock_minimo" placeholder="Cantidad Mínima de producto" oninput="validateInt(event)" required>
                                <span class="position-absolute top-50 translate-middle-y"><i class="bx bx-cube"></i></span>
                            </div>
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

                        <div class="col-md-12 view-form">
                            <label class="mb-1" for="imagen">Imagen</label>
                            <input class="form-control" type="file" name="imagen" id="imagen" accept="image/*">
                            <input type="hidden" name="imagenactual" id="imagenactual">
                        </div>

                        <div id="showImage" class="col-md-8 view-form mx-auto"></div>
                    </div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>