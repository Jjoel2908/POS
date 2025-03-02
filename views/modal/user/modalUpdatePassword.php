<!-- # [ U P D A T E   P A S S W O R D ] # -->
<div class="modal fade" id="modalUpdatePassword" tabindex="-1" role="dialog" aria-labelledby="modalUpdatePasswordLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Actualizar <span class="text-success"> Contraseña</span></h5>
         </div>

         <form class="validation" id="formUpdatePassword" method="POST" action="" name="" novalidate="">

            <!-- # [ B O D Y ] # -->
            <div class="modal-body">
               <div class="row px-2 gap-3">
                  <div class="col-md-12">
                     <label class="mb-1" for="password">Contraseña Nueva</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="hidden" name="id" id="id">
                        <input class="form-control" type="password" name="new_password" id="new_password" placeholder="Ingrese la contraseña nueva" minlength="4" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-lock"></i></span>
                     </div>
                  </div>
                  <div class="col-md-12">
                     <label class="mb-1" for="password">Confirmar Contraseña</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="password" name="new_password_confirm" id="new_password_confirm" placeholder="Confirme la contraseña nueva" minlength="4" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-lock"></i></span>
                     </div>
                  </div>
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
