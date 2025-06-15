<!-- # [ U P D A T E   P A S S W O R D ] # -->
<div class="modal fade" id="modalUpdatePassword" data-bs-backdrop="static" tabindex="-1" role="dialog">
   <div class="modal-dialog" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Actualizar Contraseña</h5>
         </div>

         <form class="validation" id="formUpdatePassword" method="POST" action="" name="" novalidate="">

            <!-- # [ B O D Y ] # -->
            <div class="modal-body">
               <div class="row px-2 gap-3">

                  <input class="form-control" type="hidden" name="idPassword" id="idPassword">

                  <div class="col-md-12 view-form">
                     <label class="mb-1" for="new_password">Contraseña</label>
                     <div class="input-group" id="show_hide_password_new">
                        <input type="password" id="new_password" name="new_password" class="form-control" placeholder="Ingrese la nueva contraseña" minlength="6" required>
                        <a href="javascript:;" class="input-group-text">
                           <i class="fa-solid fa-eye"></i>
                        </a>
                     </div>
                     <p class="mb-0 pt-3 text-center font-14">Para la contraseña utiliza al menos 6 caracteres, con una mayúscula, una minúscula, un número y un símbolo (@ # $ % & * !).</p>
                  </div>
               </div>
            </div>

            <!-- # [ F O O T E R ] # -->
            <div class="modal-footer">
               <div id="loadingSpinnerPassword" class="spinner-border text-success me-3" role="status" style="display: none;">
                  <span class="visually-hidden">Loading...</span>
               </div>
               <div class="btn-group" role="group">
                  <button type="submit" id="btnUpdate" class="btn btn-warning fs-14 border-r1 px-4 radius-30"><i class="fa-regular fa-floppy-disk me-1"></i> Guardar</button>
                  <button type="button" class="btn btn-danger fs-14 px-4 radius-30" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> Salir</button>
               </div>
            </div>
         </form>
      </div>
   </div>
</div>