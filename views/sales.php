<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 6,
   'title'        => 'Ventas',
   'icon'         => 'fa-solid fa-cart-shopping',
   'addAction'    => "openModal('Nueva Venta')",
   'addLabel'     => 'Nueva Venta',
   'modals'       => [
      'modal/modalAddSales.php',
      'modal/modalAddCreditSale.php'
   ],
   'moduleScript' => 'moduleSales',
];

$view = new TemplateView($config);
$view->render();