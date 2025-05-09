<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 9,
   'module'        => 'Devolución',
   'title'        => 'Devoluciones',
   'icon'         => 'fa-solid fa-person-chalkboard',
   'addAction'    => "addReturn()",
   'modals'       => [
      'modal/modalReturns.php'
   ],
];

$view = new TemplateView($config);
$view->render();