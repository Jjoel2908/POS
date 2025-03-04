<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 9,
   'title'        => 'Clientes',
   'icon'         => 'fa-solid fa-users-line',
   'addAction'    => "openModal('Cliente')",
   'addLabel'     => 'Cliente',
   'modals'       => [
      'modal/modalCustomer.php',
   ],
   'moduleScript' => 'moduleCustomer',
];

$view = new TemplateView($config);
$view->render();
