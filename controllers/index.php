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

switch ($module) {
    case 'Categoría':
        require_once 'CategoryController.php';
        $controller = new CategoryController($id, $idSucursal);
        break;
    case 'Marca':
        require_once 'BrandController.php';
        $controller = new BrandController($id, $idSucursal);
        break;
    case 'Producto':
        require_once 'ProductController.php';
        $controller = new ProductController($id, $idSucursal);
        break;
    case 'Cliente':
        require_once 'CustomerController.php';
        $controller = new CustomerController($id, $idSucursal);
        break;
    case 'Caja':
        require_once 'CashboxController.php';
        $controller = new CashboxController($id, $idSucursal);
        break;
    default:
        exit;
}

/** Manejamos las operaciones para cada módulo */
if ($operation) {
    switch ($operation) {
        case 'save':
            $controller->save();
            break;
        case 'update':
            $controller->update();
            break;
        case 'delete':
            $controller->delete();
            break;
        case 'dataTable':
            $controller->dataTable();
            break;
        default:
            break;
    }
}