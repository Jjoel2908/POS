<!-- # [ U S E R   P E R M I S S I O N S ] # -->
<div class="modal fade" id="modalUserPermissions" tabindex="-1" role="dialog" aria-labelledby="modalUserPermissionsLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Permisos <span class="mx-1"> | </span> <span id="nameUser" class="text-success"></span></h5>
         </div>

         <form class="validation" id="formUserPermissions" method="POST" action="" name="" novalidate="">

            <!-- # [ B O D Y ] # -->
            <div class="modal-body">
               <div class="row px-2 gap-3">
                  <input class="form-control" type="hidden" name="id" id="id">
                  <ul id="permissions" class="list-group list-group-flush border-0">
                  </ul>
               </div>
            </div>

            <!-- # [ F O O T E R ] # -->
            <div class="modal-footer">
               <button type="submit" class="btn btn-primary fs-14 rounded-0"><i class="fa-solid fa-pen-nib me-1"></i> Actualizar</button>
               <button type="button" class="btn btn-danger fs-14 rounded-0" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> Salir</button>
            </div>
         </form>
      </div>
   </div>
</div>