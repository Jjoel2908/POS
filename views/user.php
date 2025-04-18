<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 10,
   'module'        => 'Usuario',
   'title'        => 'Usuarios',
   'icon'         => 'fa-solid fa-users',
   'addAction'    => "openModal('Usuario')",
   'modals'       => [
      'modal/modalUser.php',
      // 'modal/modalUpdatePassword.php',
      // 'modal/modalUserPermissions.php'
   ],
   'moduleScript' => 'moduleUser',
];

$view = new TemplateView($config);
$view->render();