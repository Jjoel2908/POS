<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 16,
   'module'     => 'Gasto',
   'title'      => 'Gastos del Día',
   'icon'       => 'fa-solid fa-coins',
   'addAction'  => "openModal('Gasto')",
   'modals'     => [
      'modal/modalExpense.php',
   ],
   'moduleScript' => 'moduleCashbox',
];

$view = new TemplateView($config);
$view->render();