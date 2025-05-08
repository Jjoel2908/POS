<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 15,
   'module'     => 'Test',
   'addLabel'   => 'Producto',
   'title'      => 'Productos',
   'icon'       => 'fa-solid fa-chevron-right',
   'addAction'  => "openModal('Producto')",
   'modals'     => [
      'modal/modalProductTest.php',
   ]
];

$view = new TemplateView($config);
$view->render();