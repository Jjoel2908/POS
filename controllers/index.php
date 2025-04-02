<?php
session_start();

/** Obtenemos el módulo y la operación a realizar */
$module    = $_POST['module'] ?? null;
$operation = $_POST['operation'] ?? null;
$id        = $_POST['id'] ?? null;
$idSucursal = $_POST['id_sucursal'] ?? $_SESSION['sucursal'];

/** Verificamos que se haya enviado el módulo */
if ($module === null)
    exit;

/** Mapeo de módulos con sus controladores */
$controllers = [
    'Categoría'     => 'CategoryController.php',
    'Marca'         => 'BrandController.php',
    'Producto'      => 'ProductController.php',
    'Cliente'       => 'CustomerController.php',
    'Compra'        => 'PurchaseController.php',
    'DetalleCompra' => 'PurchaseDetailsController.php',
    'Caja'          => 'CashboxController.php',
];

/** Verificamos si el módulo existe en la lista */
if (!isset($controllers[$module]))
    exit;

/** Cargamos el controlador correspondiente */
require_once $controllers[$module];
$controllerClass = str_replace('.php', '', $controllers[$module]);
$controller = new $controllerClass($id, $idSucursal);

/** Verificamos si el método existe antes de ejecutarlo */
if ($operation && method_exists($controller, $operation))
    $controller->$operation();
else
    echo json_encode(["success" => false, "message" => "Operación no válida"]);