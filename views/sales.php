<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 7,
   'module'     => 'Venta',
   'title'      => 'Ventas',
   'icon'       => 'fa-solid fa-cart-shopping',
   'modals'     => [
      'modal/modalSale.php',
      'modal/modalViewDetails.php',
   ],
   'moduleScript' => 'moduleSales',
];

$view = new TemplateView($config);
$view->renderForm();