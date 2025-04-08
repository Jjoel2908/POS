<?php require_once 'layout/headerLogin.php' ?>

<!-- [  # L O G I N #  ] -->
<div class="row justify-content-center align-items-center vh-100 mx-4">

    <div class="container-login text-center p-4 mb-2">

        <!-- [  #  T I T L E  #  ] -->
        <h2 class="fw-bold text-black my-4">Bienvenido</h2>
        <h3 class="title-login">Gastelum Jr</h3>

        <!-- [  #  F O R M  #  ] -->
        <form class="validation" id="formLogin" method="POST" action="" name="" novalidate="">
            <input type="email" id="correo" name="correo" class="form-control my-4 container-input border-0" placeholder="Correo Electrónico" required>
            <div class="input-group my-4" id="show_hide_password">
                <input type="password" id="password" name="password" class="form-control container-input border-0" id="inputChoosePassword" placeholder="Contraseña" required>
                <a href="javascript:;" class="input-group-text bg-white border-0">
                    <i class="fa-solid fa-eye"></i>
                </a>
            </div>
            <button type="submit" class="btn-login my-3">Ingresar</button>
        </form>
    </div>

</div>

<?php require_once 'layout/footerLogin.php' ?>