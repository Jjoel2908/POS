<!-- # [ A D D   R E T U R N ] # -->
<div class="modal fade" id="modalAddReturn" tabindex="-1" role="dialog" aria-labelledby="modalAddReturnLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header justify-content-center">
            <h5 class="modal-title" id="modalTitle">Nueva Devolución</h5>
         </div>

         <form class="validation" id="formAddReturn" method="POST" action="" name="" novalidate="">

            <!-- # [ B O D Y ] # -->
            <div class="modal-body">
               <div class="row px-3">
                 
                  <div class="col-md-12 view-form">
                     <label class="mb-1" for="producto"><i class="fa-solid fa-basket-shopping me-1"></i> Venta</label>
                     <select name="id_venta" id="id_venta" class="form-control select"></select>
                  </div>   

                  <div class="col-md-12 view-form">
                     <label class="mb-1" for="producto"><i class="fa-brands fa-slack me-1"></i> Producto</label>
                     <select name="id_detail" id="id_detail" class="form-control select"></select>
                  </div>

                  <div class="col-md-12 view-form">
                     <label class="mb-1" for="cantidad">Cantidad</label>
                     <div class="position-relative input-icon">
                        <input type="hidden" name="precio" id="precio">
                        <input type="hidden" name="id_producto" id="id_producto">
                        <input class="form-control" type="number" name="cantidad" id="cantidad" min="1" placeholder="Cantidad de producto devuelto" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="bx bx-cube"></i></span>
                     </div>
                  </div>

                  <div class="col-md-12 view-form">
                     <label class="mb-1" for="motivo">Motivo</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="text" name="motivo" id="motivo" placeholder="Escribe el motivo de devolución" oninput="validateString(event)" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-comment"></i></span>
                     </div>
                  </div>
               </div>
            </div>

            <!-- # [ F O O T E R ] # -->
            <div class="modal-footer">
               <button id="btnSave" type="submit" class="btn btn-primary fs-14 rounded-0"><i class="fa-solid fa-money-bill me-1"></i> Guardar</button>
               <button id="btnCancel" type="button" class="btn btn-warning fs-14 rounded-0"  data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> Cancelar</button>
            </div>
         </form>
      </div>
   </div>
</div>