<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 2,
   'title'      => 'Categorías',
   'icon'       => 'bx bx-category',
   'addAction'  => "openModal('Categoría')",
   'addLabel'   => 'Categoría',
   'modals'     => [
      'modal/modalCategory.php',
   ],
   'moduleScript' => 'moduleCategory',
];

$view = new TemplateView($config);
$view->render();