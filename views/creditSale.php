<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 7,
   'module'     => 'Venta',
   'title'      => 'Ventas a Crédito',
   'icon'       => 'fa-solid fa-hand-holding-dollar',
   'addAction'  => "addSale(2)",
   'modals'     => [
      'modal/modalSale.php',
      'modal/modalViewDetails.php',
   ],
   'moduleScript' => 'moduleSales',
];

$view = new TemplateView($config);
$view->render();