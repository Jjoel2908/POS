<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 3,
   'title'        => 'Productos',
   'icon'         => 'fa-solid fa-chevron-right',
   'addAction'    => "openModal('Producto')",
   'addLabel'     => 'Producto',
   'modals'       => [
      'modal/modalProduct.php',
   ],
   'moduleScript' => 'moduleProduct',
];

$view = new TemplateView($config);
$view->render();