<!-- # [ H E A D E R ] # -->
<?php
   $formId = "formPurchase";
   $module = "Compra";
   $modalClass = "modal-lg";
   $onClickEvent = "saveTransaction('Compra')"; 
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12">
   <div class="row">

      <div class="col-md-12 text-center mb-3">
         <select name="search" id="search" class="form-control select"></select>
      </div>

      <div class="col-md-7 view-form">
         <div class="form-floating">
            <input type="hidden" id="id" name="id">
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
            <input id="cantidad" class="form-control" type="number" name="cantidad" oninput="validateInt(event)" onkeypress="handleFormKeyPress(event, 'formPurchase', 'DetalleCompra')" disabled>
            <label for="cantidad"><i class="bx bx-cube me-1"></i> Cantidad</label>
         </div>
      </div>

      <div class="col-md-4 view-form">
         <div class="form-floating">
            <input id="precio_compra" class="form-control" type="text" name="precio_compra" readonly>
            <label for="precio_compra"><i class="fa-solid fa-sack-dollar me-1"></i> Precio</label>
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
                     <td class="text-end py-2 font-15" colspan="4"><strong>Total</strong></td>
                     <td class="text-end py-2 font-15" id="total-details">0.00</td>
                  </tr>
               </tfoot>
            </table>
         </div>
      </div>
   </div>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>