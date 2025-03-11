<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 5,
   'module'        => 'Marca',
   'title'        => 'Marcas',
   'icon'         => 'fa-solid fa-cash-register',
   'addAction'  => "openModal('Marca')",
   'modals'     => [
      'modal/modalBrand.php'
   ],
];

$view = new TemplateView($config);
$view->render();