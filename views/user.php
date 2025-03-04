<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 10,
   'title'        => 'Usuarios',
   'icon'         => 'fa-solid fa-users',
   'addAction'    => "openModal('Usuario')",
   'addLabel'     => 'Usuario',
   'modals'       => [
      'modal/user/modalAddUser.php',
      'modal/user/modalUpdatePassword.php',
      'modal/user/modalUserPermissions.php'
   ],
   'moduleScript' => 'moduleUser',
];

$view = new TemplateView($config);
$view->render();