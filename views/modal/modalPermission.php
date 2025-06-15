<!-- # [ U S E R   P E R M I S S I O N S ] # -->
<div class="modal fade" id="modalUserPermissions" data-bs-backdrop="static" tabindex="-1" role="dialog" aria-labelledby="modalUserPermissionsLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Actualizar Permisos</h5>
         </div>

         <form class="validation" id="formUserPermissions" method="POST" action="" name="" novalidate="">

            <!-- # [ B O D Y ] # -->
            <div class="modal-body pt-2">
               <div class="row px-2 gap-3">
                  <input class="form-control" type="hidden" name="idPermissions" id="idPermissions">
                     <ul id="permissions" class="list-group list-group-flush border-0">
                  </ul>
               </div>
            </div>

            <!-- # [ F O O T E R ] # -->
            <div class="modal-footer">
               <div id="loadingSpinnerPermissions" class="spinner-border text-success me-3" role="status" style="display: none;">
                  <span class="visually-hidden">Loading...</span>
               </div>
               <div class="btn-group" role="group">
                  <button type="submit" id="btnPermissions" class="btn btn-warning fs-14 border-r1 px-4 radius-30"><i class="fa-regular fa-floppy-disk me-1"></i> Guardar</button>
                  <button type="button" class="btn btn-danger fs-14 px-4 radius-30" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> Salir</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>