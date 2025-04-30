<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 11,
   'module'     => 'ReporteVenta',
   'title'      => 'Reporte de Ventas',
   'modals'     => [
      'modal/modalViewDetails.php',
   ],
   'moduleScript' => 'moduleReportSales',
];

$view = new TemplateView($config);
$view->renderReport();