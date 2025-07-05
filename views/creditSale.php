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
];

// El módulo de ventas a crédito permite consultar
// aquellas ventas que aún tienen pagos pendientes por parte del cliente. 
// A través de este módulo, es posible acceder al detalle de cada 
// venta, revisar la información de los pagos efectuados y complementar 
// los registros con datos adicionales para un mejor seguimiento. 

$view = new TemplateView($config);
$view->render();
