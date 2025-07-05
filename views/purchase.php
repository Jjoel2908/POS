<?php
require_once 'layout/TemplateView.php';

$config = [
   'module'     => 'Compra',
   'title'      => 'Compras',
   'icon'       => 'fa-solid fa-bag-shopping',
   'modals'     => [
      'modal/modalSalePurchase.php',
   ],
   'moduleScript' => 'modulePurchase',
];

$view = new TemplateView($config);
$view->renderForm();