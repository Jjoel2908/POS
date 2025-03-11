<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 3,
   'module'        => 'Producto',
   'title'        => 'Productos',
   'icon'         => 'fa-solid fa-chevron-right',
   'addAction'    => "openModal('Producto')",
   'modals'       => [
      'modal/modalProduct.php',
   ],
];

$view = new TemplateView($config);
$view->render();