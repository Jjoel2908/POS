<!-- # [ H E A D E R ] # -->
<?php 
   $formId = "formUpdatePassword"; 
   $module = "Usuario";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
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

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>