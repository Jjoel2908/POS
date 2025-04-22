<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 7,
   'module'     => 'VentaCredito',
   'title'      => 'Ventas a Crédito',
   'icon'       => 'fa-solid fa-hand-holding-dollar',
   'addAction'  => "addSale(2)",
   'addLabel'   => 'Venta a Crédito',
   'modals'     => [
      'modal/modalSale.php',
      'modal/modalViewDetails.php',
   ],
   'moduleScript' => 'moduleSales',
];

$view = new TemplateView($config);
$view->render();