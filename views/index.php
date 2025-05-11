<?php require_once 'layout/headerLogin.php' ?>

<!-- [  # L O G I N #  ] -->
<div class="main-container">
    <div class="form-container">
        <div class="container-left"></div>
        <div class="container-right">
            <h1 class="title-form">Bienvenido</h1>
            <form class="validation" id="formLogin" method="POST" action="" name="" novalidate="">
                <div class="input-group">
                    <input type="text" name="correo" id="correo" required />
                    <label class="label-login" for="correo">Correo Electrónico</label>
                </div>
                <div class="input-group d-flex input-password" id="show_hide_password">
                    <input type="password" name="password" id="password" required />
                    <label class="label-login" for="password">Contraseña</label>
                    <a href="javascript:;" class="input-group-text border-0">
                        <i class="fa-solid fa-eye"></i>
                    </a>
                </div>
                <button class="btn-login">Iniciar Sesión</button>
            </form>
        </div>
    </div>
</div>

<?php require_once 'layout/footerLogin.php' ?>