<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 5,
   'title'        => 'Cajas',
   'icon'         => 'fa-solid fa-cash-register',
   'addAction'  => "openModal('Caja')",
   'addLabel'     => 'Caja',
   'modals'     => [
      'modal/modalCashbox.php',
      'modal/modalOpenCashbox.php'
   ],
   'moduleScript' => 'moduleCashbox',
];

$view = new TemplateView($config);
$view->render();
