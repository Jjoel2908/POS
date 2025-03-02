<!-- # [ A D D   P U R C H A S E ] # -->
<div class="modal fade" id="modalAddPurchase" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalAddPurchaseLabel" aria-hidden="true">
   <div class="modal-dialog modal-lg" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header justify-content-center">
            <h5 class="modal-title" id="modalTitle">Nueva Compra</h5>
            <button type="button" class="btn-close" onclick="modulePurchase.validateModalPurchase()"></button>
         </div>

         <form id="formAddPurchase" method="POST" action="" name="">

            <!-- # [ B O D Y ] # -->
            <div class="modal-body">
               <div class="row px-3">
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
                              <input id="cantidad" class="form-control" type="number" name="cantidad" oninput="validateInt(event)" onkeypress="modulePurchase.handleKeyPress(event)" disabled>
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
                              <input id="sub_total" class="form-control" type="text" name="sub_total" readonly>
                              <label for="sub_total">Sub Total</label>
                           </div>
                        </div>

                        <div class="col-md-12 mt-3">
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
                                       <td id="total-purchase">0.00</td>
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
            <div class="modal-footer">
               <button id="btnSave" type="button" class="btn btn-primary fs-14 rounded-0" onclick="modulePurchase.savePurchase()" disabled><i class="fa-solid fa-money-bill me-1"></i> Confirmar</button>
               <button id="btnCancel" type="button" class="btn btn-warning fs-14 rounded-0" onclick="modulePurchase.cancelPurchase()" disabled><i class="fa-solid fa-xmark me-1"></i> Cancelar</button>
            </div>
         </form>
      </div>
   </div>
</div>