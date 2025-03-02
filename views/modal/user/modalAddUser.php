<!-- # [ A D D   U S E R ] # -->
<div class="modal fade" id="modalAddUser" tabindex="-1" role="dialog" aria-labelledby="modalAddUserLabel" aria-hidden="true">
   <div class="modal-dialog" role="document">
      <div class="modal-content">

         <!-- # [ H E A D E R ] # -->
         <div class="modal-header">
            <h5 class="modal-title" id="modalTitle">Nuevo Usuario</h5>
         </div>

         <form class="validation" id="formAddUser" method="POST" action="" name="" novalidate="">

            <!-- # [ B O D Y ] # -->
            <div class="modal-body">
               <div class="row px-2 gap-3">
                  <div class="col-md-12">
                     <label class="mb-1" for="user">Usuario</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="hidden" name="id" id="id">
                        <input class="form-control" type="text" name="user" id="user" placeholder="Ingrese el usuario" oninput="validateString(event)" minlength="4" maxlength="50" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-chalkboard-user"></i></span>
                     </div>
                  </div>

                  <div class="col-md-12">
                     <label class="mb-1" for="nombre">Nombre</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="text" name="nombre" id="nombre" placeholder="Ingrese el nombre" oninput="validateString(event)" minlength="8" maxlength="100" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-user"></i></span>
                     </div>
                  </div>

                  <div class="col-md-12">
                     <label class="mb-1" for="correo">Correo</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="email" name="correo" id="correo" placeholder="Ingrese el correo">
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-envelope"></i></span>
                     </div>
                  </div>

                  <div class="col-md-12">
                     <label class="mb-1" for="telefono">Teléfono</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="number" name="telefono" id="telefono" placeholder="Ingrese el teléfono"  min="10" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-phone"></i></span>
                     </div>
                  </div>

                  <div id="container-password" class="col-md-12">
                     <label class="mb-1" for="password">Contraseña</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="password" name="password" id="password" placeholder="Ingrese la contraseña" minlength="4" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-lock"></i></span>
                     </div>
                  </div>
               </div>
            </div>

            <!-- # [ F O O T E R ] # -->
            <div class="modal-footer">
               <button id="btnSave" type="submit" class="btn btn-primary fs-14 rounded-0"></button>
               <button type="button" class="btn btn-danger fs-14 rounded-0" data-bs-dismiss="modal"><i class="fa-solid fa-xmark me-1"></i> Salir</button>
            </div>
         </form>
      </div>
   </div>
</div>
