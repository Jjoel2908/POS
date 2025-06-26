<!-- Formulario -->
<div class="card radius-10 border-start border-0 border-4 border-primary">
    <div class="card-header">
        <div class="d-flex align-items-center justify-content-center py-1">
            <h5 class="mb-0"><?= $title ?></h5>
        </div>
    </div>
    <div class="card-body">
        <form class="validation" id="formAdd" method="POST" action="" name="" novalidate="" data-module="<?= $module ?>">
            <!-- Identificador -->
            <input type="hidden" id="id" name="id">

            <div class="row pt-1">

                <!-- Busqueda de producto y detalles -->
                <div class="col-md-8 text-center" id="container-search">
                    <div class="row g-0">

                        <!-- Buscador -->
                        <div class="col-md-12 mb-4">
                            <select name="search" id="search" class="form-control"></select>
                        </div>

                        <!-- Imagen del producto -->
                        <div class="col-md-5 pe-sm-0 pe-md-2 pt-1">
                            <div class="placeholder-icon" id="container-image-void">
                                <i class="fa-solid fa-box-open"></i>
                                <p>Sin imagen</p>
                            </div>
                            <div id="container-image"></div>
                        </div>

                        <!-- Información del producto -->
                        <div class="col-md-7 ps-sm-0 ps-md-2 pt-1">
                            <div class="row">
                                <div class="col-lg-12 view-form mt-0 mb-3">
                                    <div class="form-floating">
                                        <input id="nombre" class="form-control" type="text" name="nombre" readonly>
                                        <label for="nombre"><i class="fa-solid fa-chevron-right me-2"></i> Nombre</label>
                                    </div>
                                </div>

                                <div class="col-lg-6 view-form mb-3">
                                    <div class="form-floating">
                                        <input id="codigo" class="form-control" type="text" name="codigo" readonly>
                                        <label for="codigo"><i class="fa-solid fa-barcode me-2"></i>Código</label>
                                    </div>
                                </div>

                                <div class="col-lg-6 view-form mb-3">
                                    <div class="form-floating">
                                        <input id="color" class="form-control" type="text" name="color" readonly>
                                        <label for="color"><i class="fa-solid fa-palette me-2"></i>Color</label>
                                    </div>
                                </div>

                                <div class="col-lg-6 view-form mb-3">
                                    <div class="form-floating">
                                        <input id="presentacion" class="form-control" type="text" name="presentacion" readonly>
                                        <label for="presentacion"><i class="fa-solid fa-ruler me-2"></i>Talla / Presentación</label>
                                    </div>
                                </div>

                                <div class="col-lg-6 view-form mb-3">
                                    <div class="form-floating">
                                        <input id="cantidad" class="form-control" type="number" name="cantidad" oninput="validateInt(event)" onkeypress="handleFormKeyPress(event, 'formAdd', 'DetalleVenta')" disabled>
                                        <label for="cantidad"><i class="bx bx-cube me-1"></i> Cantidad</label>
                                    </div>
                                </div>

                                <div class="col-lg-6 view-form mb-0">
                                    <div class="form-floating">
                                        <input id="<?= $field ?>" class="form-control" type="text" name="<?= $field ?>" readonly>
                                        <label for="<?= $field ?>"><i class="fa-solid fa-sack-dollar me-1"></i> Precio</label>
                                    </div>
                                </div>

                                <div class="col-lg-6 view-form mb-0">
                                    <div class="form-floating">
                                        <input id="total" class="form-control" type="text" name="total" readonly>
                                        <label for="total"><i class="fa-solid fa-calculator me-1"></i> Sub Total</label>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <!-- Productos agregados al carrito -->
                        <div class="col-md-12 mt-4">
                            <div class="table-responsive">
                                <table class="table table-striped table-bordered view-table">
                                    <thead>
                                        <tr class="text-center">
                                            <th>Producto</th>
                                            <th>Cantidad</th>
                                            <th>Precio</th>
                                            <th>Sub Total</th>
                                            <th>Acciones</th>
                                        </tr>
                                    </thead>
                                    <tbody id="details"></tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Configuraciones de venta -->
                <div class="col-md-4">
                    <div class="card radius-10 border-top border-0 border-5 border-success">
                        <div class="card-header">
                            <div class="d-flex align-items-center justify-content-center py-2">
                                <p class="mb-0 font-22 fw-bold">Total <?= $module ?>: <span class="ps-2" id="total-details">0.00</span></p>
                            </div>
                        </div>
                        <div class="card-body">

                            <?php if ($module != "Compra") { ?>
                                <!-- Tipo de Venta -->
                                <div class="view-form">
                                    <label class="mb-1 font-15" for="tipo_venta"><i class="fa-solid fa-money-check me-1"></i> Tipo de Venta</label>
                                    <select name="tipo_venta" id="tipo_venta" class="form-control select" required>
                                        <option value="1">CONTADO</option>
                                        <option value="2">CRÉDITO</option>
                                    </select>
                                </div>

                                <!-- Cliente -->
                                <div class="view-form mt-3" id="customerField" style="display: none;">
                                    <label class="mb-1 font-15" for="id_cliente"><i class="fa-solid fa-user me-1"></i> Cliente</label>
                                    <select name="id_cliente" id="id_cliente" class="form-control select"></select>
                                </div>
                            <?php } ?>

                            <div class="d-grid gap-2">
                                <button class="btn btn-success btn-lg" type="button" onclick="saveTransaction()">Generar <?= $module ?></button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>