<?php
require_once 'layout/TemplateView.php';

$config = [
   'module'     => 'Cliente',
   'title'      => 'Clientes',
   'icon'       => 'fa-solid fa-users-line',
   'addAction'  => "openModal('Cliente')",
   'modals'     => [
      'modal/modalCustomer.php',
   ],
];

$view = new TemplateView($config);
$view->render();