<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 8,
   'module'     => 'VentaCredito',
   'title'      => 'Ventas a Crédito',
   'icon'       => 'fa-solid fa-hand-holding-dollar',
   'addLabel'   => 'Venta a Crédito',
   'moduleScript' => 'moduleSales',
];

$view = new TemplateView($config);
$view->render();


El módulo de ventas a crédito permite consultar
aquellas que aún tienen pagos pendientes por parte del cliente. 
A través de este módulo, es posible acceder al detalle de cada 
venta, revisar la información de los pagos efectuados y complementar 
los registros con datos adicionales para un mejor seguimiento. 
Está diseñado para facilitar la gestión y el control de las cuentas 
por cobrar, brindando una visión clara del comportamiento de pago 
de cada cliente.