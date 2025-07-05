<?php
require_once 'layout/TemplateView.php';

$config = [
   'module'     => 'Venta',
   'title'      => 'Ventas',
   'icon'       => 'fa-solid fa-cart-shopping',
   'modals'     => [
      'modal/modalSalePurchase.php',
      'modal/modalCustomer.php',
   ],
   'moduleScript' => 'moduleSales',
];

$view = new TemplateView($config);
$view->renderForm();