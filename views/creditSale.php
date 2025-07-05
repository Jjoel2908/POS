<?php
require_once 'layout/TemplateView.php';

$config = [
   'module'        => 'VentaCredito',
   'title'         => 'Ventas a Crédito',
   'icon'          => 'fa-solid fa-hand-holding-dollar',
   'showAddButton' => false,
   'modals'        => [
      'modal/modalViewDetails.php',
      'modal/modalViewPayment.php',
   ],
   'moduleScript' => 'moduleCreditSale',
   'description' => 'Este módulo permite gestionar las ventas realizadas a crédito, ofreciendo un seguimiento detallado de los pagos pendientes por parte de los clientes, así como la consulta de información relacionada con cada venta.',
];

$view = new TemplateView($config);
$view->render();