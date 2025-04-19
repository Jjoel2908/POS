<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 10,
   'module'     => 'Usuario',
   'title'      => 'Usuarios',
   'icon'       => 'fa-solid fa-users',
   'addAction'  => "addUser()",
   'modals'     => [
      'modal/modalUser.php',
      'modal/modalPassword.php',
      'modal/modalPermission.php'
   ],
   'moduleScript' => 'moduleUser',
];

$view = new TemplateView($config);
$view->render();