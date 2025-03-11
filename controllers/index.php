<?php
session_start();

/** Obtenemos el módulo y la operación a realizar */
$module = $_POST['module'] ?? null;
$operation = $_POST['operation'] ?? null;

/** Verificamos que se haya enviado el módulo */
if ($module === null)
    exit;

switch ($module) {
    case 'Categoría':
        require_once 'CategoryController.php';
        $controller = new CategoryController();
        break;
    case 'Cliente':
        require_once 'CustomerController.php';
        $controller = new CustomerController();
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