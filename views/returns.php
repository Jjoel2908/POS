<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 8,
   'module'        => 'Devolución',
   'title'        => 'Devoluciones',
   'icon'         => 'fa-solid fa-person-chalkboard',
   'addAction'    => "openModal('Devolución')",
   'modals'       => [
      'modal/modalReturns.php',
   ],
];

$view = new TemplateView($config);
$view->render();