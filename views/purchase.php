<?php
require_once 'layout/TemplateView.php';

$config = [
   'permission'   => 4,
   'title'        => 'Compras',
   'icon'         => 'fa-solid fa-bag-shopping',
   'addAction'    => "openModal('Compra')",
   'addLabel'     => 'Compra',
   'modals'       => [
      'modal/modalPurchase.php',
   ],
   'moduleScript' => 'modulePurchase',
];

$view = new TemplateView($config);
$view->render();