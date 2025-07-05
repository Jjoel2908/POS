<?php
require_once 'layout/TemplateView.php';

$config = [
   'module'     => 'Categoría',
   'title'      => 'Categorías',
   'icon'       => 'bx bx-category',
   'addAction'  => "openModal('Categoría')",
   'modals'     => [
      'modal/modalCategory.php',
   ],
];

$view = new TemplateView($config);
$view->render();