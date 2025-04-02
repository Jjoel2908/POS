<!-- # [ A D D   P R O D U C T ] # -->
<div class="modal fade" id="modalAddSale" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalAddSaleLabel">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header justify-content-center">
            <h5 class="modal-title" id="modalTitle">Nueva Venta</h5>
            <button type="button" class="btn-close" onclick="moduleSales.validateModalSale()"></button>
         </div>

         <form id="formAddSale" method="POST" action="" name="">

            <!-- # [ B O D Y ] # -->
            <div class="modal-body">
               <div class="row px-3">
                  <div class="col-md-12">
                     <div class="row">

                        <div class="col-sm-12 col-lg-7 text-center mb-3">
                           <select name="search" id="search" class="form-control search-product"></select>
                        </div>

                        <div class="col-sm-12 col-lg-5 text-center mb-3">
                           <select name="cliente" id="cliente" class="form-control select"></select>
                        </div>

                        <div class="col-sm-12 col-md-5 view-form">
                           <div class="form-floating">
                              <input type="hidden" id="id" name="id">
                              <input id="nombre" class="form-control" type="text" name="nombre" readonly>
                              <label for="nombre"><i class="fa-solid fa-chevron-right me-1"></i> Nombre</label>
                           </div>
                        </div>

                        <div class="col-sm-12 col-md-4 view-form">
                           <div class="form-floating">
                              <input id="codigo" class="form-control" type="text" name="codigo" readonly>
                              <label for="codigo"><i class="fa-solid fa-barcode me-1"></i>Código</label>
                           </div>
                        </div>

                        <div class="col-sm-12 col-md-3 view-form">
                           <div class="form-floating">
                              <input type="hidden" id="id" name="id">
                              <input id="stock" class="form-control" type="text" name="stock" readonly>
                              <label for="stock"><i class="fa-solid fa-chevron-right me-1"></i> Stock</label>
                           </div>
                        </div>

                        <div class="col-sm-12 col-md-4 view-form">
                           <div class="form-floating">
                              <input id="cantidad" class="form-control" type="number" name="cantidad" oninput="validateInt(event)" onkeypress="moduleSales.handleKeyPress(event)" disabled>
                              <label for="cantidad"><i class="bx bx-cube me-1"></i> Cantidad</label>
                           </div>
                        </div>

                        <div class="col-sm-12 col-md-4 view-form">
                           <div class="form-floating">
                              <input id="precio_venta" class="form-control" type="text" name="precio_venta" readonly>
                              <label for="precio_venta"><i class="fa-solid fa-sack-dollar me-1"></i> Precio</label>
                           </div>
                        </div>

                        <div class="col-sm-12 col-md-4 view-form">
                           <div class="form-floating">
                              <input id="sub_total" class="form-control" type="text" name="sub_total" readonly>
                              <label for="sub_total">Sub Total</label>
                           </div>
                        </div>

                        <div class="col-sm-12 col-md-12 mt-3">
                           <div class="table-responsive">
                              <table class="table table-striped view-table">
                                 <thead>
                                    <tr class="text-center">
                                       <th>Nombre</th>
                                       <th>Cantidad</th>
                                       <th>Precio</th>
                                       <th>Sub Total</th>
                                       <th>Acción</th>
                                    </tr>
                                 </thead>
                                 <tbody id="table-product-details"></tbody>
                                 <tfoot>
                                    <tr>
                                       <td class="text-end" colspan="4"><strong>Total</strong></td>
                                       <td>$<span id="total-sales">0.00</span></td>
                                    </tr>
                                 </tfoot>
                              </table>
                           </div>
                        </div>
                     </div>
                  </div>
               </div>
            </div>

            <!-- # [ F O O T E R ] # -->
            <div class="modal-footer justify-content-between">
               <div>
                  <button id="btnCredit" type="button" class="btn btn-primary fs-14 rounded-0" onclick="moduleSales.modalAddCreditSale()" disabled><i class="fa-solid fa-hand-holding-dollar me-1"></i>Venta a Crédito</button>
               </div>
               <div>
                  <button id="btnSave" type="button" class="btn btn-primary fs-14 rounded-0" onclick="moduleSales.saveSale('contado')" disabled><i class="fa-solid fa-money-bill me-1"></i> Confirmar</button>
                  <button id="btnCancel" type="button" class="btn btn-warning fs-14 rounded-0" onclick="moduleSales.cancelSale()" disabled><i class="fa-solid fa-xmark me-1"></i> Cancelar</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>




<!-- # [ H E A D E R ] # -->
<?php
   $formId = "formSale";
   $module = "Venta";
   $modalClass = "modal-lg";
   $onClickEvent = "saveTransaction('Venta')"; 
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