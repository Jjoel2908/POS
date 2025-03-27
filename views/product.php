<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 4,
   'module'        => 'Producto',
   'title'        => 'Productos',
   'icon'         => 'fa-solid fa-chevron-right',
   'addAction'    => "addProduct()",
   'modals'       => [
      'modal/modalProduct.php',
   ],
   'moduleScript' => 'moduleProduct',
];

$view = new TemplateView($config);
$view->render();