<?php
require_once 'layout/TemplateView.php';

$config = [
   'module'     => 'Gasto',
   'title'      => 'Gastos del día',
   'icon'       => 'fa-solid fa-coins',
   'addAction'  => "openModal('Gasto')",
   'modals'     => [
      'modal/modalExpense.php',
   ],
   'moduleScript' => 'moduleExpense',
];

$view = new TemplateView($config);
$view->render();