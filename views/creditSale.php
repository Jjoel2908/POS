<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 7,
   'title'        => 'Ventas a crédito',
   'icon'         => 'fa-solid fa-hand-holding-dollar',
   'addAction'    => "openModal('Venta a Crédito')",
   'addLabel'     => 'Venta a crédito',
   'modals'       => [
      'modal/modalViewDetails.php',
   ],
   'moduleScript' => 'moduleSales',
   'extraScript'  => "$(() => { moduleSales.tableCreditSales(); });",
];

$view = new TemplateView($config);
$view->render();
