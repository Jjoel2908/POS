<?php
require_once 'layout/TemplateView.php';

$config = [
   'module'     => 'ReporteVenta',
   'title'      => 'Reporte de Ventas',
   'modals'     => [
      'modal/modalViewDetails.php',
   ],
];

$view = new TemplateView($config);
$view->renderReport();