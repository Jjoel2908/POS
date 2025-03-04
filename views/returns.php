<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 8,
   'title'        => 'Devoluciones',
   'icon'         => 'fa-solid fa-person-chalkboard',
   'addAction'    => "openModal('Devolución')",
   'addLabel'     => 'Devolución',
   'modals'       => [
      'modal/modalReturn.php',
   ],
   'moduleScript' => 'moduleReturn',
];

$view = new TemplateView($config);
$view->render();