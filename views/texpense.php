<?php
require_once 'layout/TemplateView.php';

$config = [
    'permission'   => 17,
    'module'        => 'TipoGasto',
    'title'        => 'Tipos de Gastos',
    'icon'         => 'fa-solid fa-filter-circle-dollar',
    'addAction'  => "openModal('TipoGasto')",
    'modals'     => [
        'modal/modalTypeExpense.php',
    ],
    'moduleScript' => 'moduleCashbox',
];

$view = new TemplateView($config);
$view->render();