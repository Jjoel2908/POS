<!-- # [ H E A D E R ] # -->
<?php
$formId       = "formSale";
$module       = "Venta";
$modalClass   = "modal-lg";
$onClickEvent = "saveTransaction('Venta')";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12">
   <div class="row">

      <input type="hidden" id="id" name="id">
      <input type="hidden" id="tipo_venta" name="tipo_venta">

      <div class="col-md-12 text-center mb-3" id="container-search">
         <select name="search" id="search" class="form-control select"></select>
      </div>

      <div class="col-sm-12 col-lg-5 text-center mb-3 d-none" id="container-customer">
         <select name="cliente" id="cliente" class="form-control"></select>
      </div>

      <div class="col-md-7 view-form">
         <div class="form-floating">
            <input id="nombre" class="form-control" type="text" name="nombre" readonly>
            <label for="nombre"><i class="fa-solid fa-chevron-right me-1"></i> Nombre</label>
         </div>
      </div>

      <div class="col-md-5 view-form">
         <div class="form-floating">
            <input id="codigo" class="form-control" type="text" name="codigo" readonly>
            <label for="codigo"><i class="fa-solid fa-barcode me-1"></i>Código</label>
         </div>
      </div>

      <div class="col-md-4 view-form">
         <div class="form-floating">
            <input id="cantidad" class="form-control" type="number" name="cantidad" oninput="validateInt(event)" onkeypress="handleFormKeyPress(event, 'formSale', 'DetalleVenta')" disabled>
            <label for="cantidad"><i class="bx bx-cube me-1"></i> Cantidad</label>
         </div>
      </div>

      <div class="col-md-4 view-form">
         <div class="form-floating">
            <input id="precio_venta" class="form-control" type="text" name="precio_venta" readonly>
            <label for="precio_venta"><i class="fa-solid fa-sack-dollar me-1"></i> Precio</label>
         </div>
      </div>

      <div class="col-md-4 view-form">
         <div class="form-floating">
            <input id="total" class="form-control" type="text" name="total" readonly>
            <label for="total">Sub Total</label>
         </div>
      </div>

      <div class="col-md-12 mt-3">
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
               <tfoot>
                  <tr>
                     <td class="text-end" colspan="4"><strong>Total</strong></td>
                     <td class="text-end" id="total-details">0.00</td>
                  </tr>
               </tfoot>
            </table>
         </div>
      </div>
   </div>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>