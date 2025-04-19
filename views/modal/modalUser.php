<!-- # [ H E A D E R ] # -->
<?php
$formId = "formUser";
$module = "Usuario";
?>

<?php require 'modalHeader.php'; ?>

<!-- # [ B O D Y ] # -->
<div class="col-md-12 view-form view-form">
   <label class="mb-1" for="user">Usuario</label>
   <div class="position-relative input-icon">
      <input class="form-control" type="hidden" name="id" id="id">
      <input class="form-control" type="text" name="user" id="user" placeholder="Ingrese el usuario" oninput="validateAndConvertToLowercase(event)" minlength="4" maxlength="50" required>
      <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-chalkboard-user"></i></span>
   </div>
</div>

<div class="col-md-12 view-form">
   <label class="mb-1" for="nombre">Nombre</label>
   <div class="position-relative input-icon">
      <input class="form-control" type="text" name="nombre" id="nombre" placeholder="Ingrese el nombre del usuario" oninput="validateInputText(event)" minlength="8" maxlength="100" required>
      <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-user"></i></span>
   </div>
</div>

<div class="col-md-12 view-form">
   <label class="mb-1" for="correo">Correo</label>
   <div class="position-relative input-icon">
      <input class="form-control" type="email" name="correo" id="correo" placeholder="Correo electrónico" required>
      <span class="position-absolute top-50 translate-middle-y"><i class="fa-solid fa-envelope"></i></span>
   </div>
</div>

<div id="container-password" class="col-md-12 view-form">
   <label class="mb-1" for="password">Contraseña</label>
   <div class="input-group" id="show_hide_password">
      <input type="password" id="password" name="password" class="form-control" placeholder="Ingrese la contraseña" minlength="6" required>
      <a href="javascript:;" class="input-group-text">
         <i class="fa-solid fa-eye"></i>
      </a>
   </div>
   <p class="mb-0 pt-3 text-center font-13">Para la contraseña utiliza al menos 6 caracteres, con una mayúscula, una minúscula, un número y un símbolo (@ # $ % & * !).</p>
</div>

<!-- # [ F O O T E R ] # -->
<?php require 'modalFooter.php'; ?>