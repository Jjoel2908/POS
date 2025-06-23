<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 6,
   'module'     => 'Compra',
   'title'      => 'Compras',
   'icon'       => 'fa-solid fa-cart-shopping',
   'modals'     => [
      'modal/modalSalePurchase.php',
      'modal/modalViewDetails.php',
   ],
   'moduleScript' => 'modulePurchase',
];

$view = new TemplateView($config);
$view->renderForm();