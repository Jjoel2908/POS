<!-- # [ H E A D E R ] # -->
<?php 
   $formId = "formCustomer"; 
   $module = "Cliente";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12 view-form my-3">
                     <label class="mb-1" for="nombre">Nombre</label>
                     <div class="position-relative input-icon">
                     <input class="form-control" type="hidden" name="id" id="id">
                        <input class="form-control" type="text" name="nombre" id="nombre" minlength="10" maxlength="100" placeholder="Ingrese el nombre" oninput="validateString(event)" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-user"></i></span>
                     </div>
                  </div>

                  <div class="col-md-12 view-form my-3">
                     <label class="mb-1" for="telefono">Teléfono</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="number" name="telefono" id="telefono" min="10" placeholder="Ingrese el teléfono" required>
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-phone"></i></span>
                     </div>
                  </div>

                  <div class="col-md-12 view-form my-3">
                     <label class="mb-1" for="correo">Correo</label>
                     <div class="position-relative input-icon">
                        <input class="form-control" type="email" name="correo" id="correo" placeholder="Ingrese el correo">
                        <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-envelope"></i></span>
                     </div>
                  </div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>