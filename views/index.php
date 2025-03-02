<?php require_once 'layout/headerLogin.php' ?>

<!-- [  # L O G I N #  ] -->
<div class="row justify-content-center align-items-center vh-100 mx-4">

   <div class="container-login text-center p-4">
      <img class="img-fluid" src="../public/images/logo.jpg" alt="Logo">
      <h2 class="my-3">POS</h2>

      <form method="post" id="formLogin" action="">
         <input type="text" id="user" name="user" class="form-control my-4" placeholder="Usuario">
         <input type="password" id="password" name="password" class="form-control my-4" placeholder="Contraseña">
         <button type="submit" class="btn-login my-3">Ingresar</button>
      </form>
   </div>
   
</div>

<?php require_once 'layout/footerLogin.php' ?>