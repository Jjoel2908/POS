<?php
require_once 'layout/TemplateView.php';

$config = [
   'module'     => 'ReporteGastos',
   'title'      => 'Reporte de Gastos',
   'modals'     => [
      'modal/modalViewDetails.php',
   ],
];

$view = new TemplateView($config);
$view->renderReport();