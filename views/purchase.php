<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission' => 5,
   'module'     => 'Compra',
   'title'      => 'Compras',
   'icon'       => 'fa-solid fa-bag-shopping',
   'addAction'  => "addPurchase()",
   'modals'     => [
      'modal/modalPurchase.php',
   ],
   'moduleScript' => 'modulePurchase',
];

$view = new TemplateView($config);
$view->render();