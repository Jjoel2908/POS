<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 5,
   'module'        => 'Caja',
   'title'        => 'Cajas',
   'icon'         => 'fa-solid fa-cash-register',
   'addAction'  => "openModal('Caja')",
   'modals'     => [
      'modal/modalCashbox.php',
      'modal/modalViewDetails.php',
   ],
   'moduleScript' => 'moduleCashbox',
];

$view = new TemplateView($config);
$view->render();