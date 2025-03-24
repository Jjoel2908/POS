<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 3,
   'module'        => 'Marca',
   'title'        => 'Marcas',
   'icon'         => 'fa-solid fa-layer-group',
   'addAction'  => "openModal('Marca')",
   'modals'     => [
      'modal/modalBrand.php'
   ],
];

$view = new TemplateView($config);
$view->render();