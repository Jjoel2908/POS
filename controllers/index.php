<?php
session_start();

/** Zona horaria */
date_default_timezone_set('America/Mexico_City');

/** Obtenemos el módulo y la operación a realizar */
$module     = $_POST['module'] ?? null;
$operation  = $_POST['operation'] ?? null;
$id         = $_POST['id'] ?? null;
$idSucursal = $_POST['id_sucursal'] ?? $_SESSION['sucursal'];

/** Mapeo de módulos con sus controladores */
$controllers = [
    'Login'               => 'LoginController.php',
    'Cotizacion'          => 'QuoteController.php',
    'DetalleCotizacion'   => 'QuoteDetailsController.php',
    'Dashboard'           => 'DashboardController.php',
    'Categoría'           => 'CategoryController.php',
    'Marca'               => 'BrandController.php',
    'Producto'            => 'ProductController.php',
    'Compra'              => 'PurchaseController.php',
    'DetalleCompra'       => 'PurchaseDetailsController.php',
    'Caja'                => 'CashboxController.php',
    'ArqueoCaja'          => 'CashboxCountController.php',
    'Venta'               => 'SaleController.php',
    'DetalleVenta'        => 'SaleDetailsController.php',
    'VentaCredito'        => 'CreditSaleController.php',
    'AbonosVentaCredito'  => 'CreditSalePaymentController.php',
    'Gasto'               => 'ExpenseController.php',
    'TipoGasto'           => 'ExpenseTypeController.php',
    'Cliente'             => 'CustomerController.php',
    'Usuario'             => 'UserController.php',
    'ReporteCompra'       => 'ReportPurchaseController.php',
    'ReporteVenta'        => 'ReportSaleController.php',
    'ReporteGastos'       => 'ReportExpenseController.php',
    'ReporteGeneral'      => 'ReportProcessorController.php',
    'Test'                => 'TestController.php',
];

/** Verificamos si el módulo existe en la lista */
if ($module === null || $operation === null || !isset($controllers[$module]))
    exit;

/** Eventos realizados por el usuario */
// require_once 'ActionController.php';
// $Action = new ActionController();
// if (in_array($operation, ["login", "save", "update", "delete", "updatePassword", "updatePermissions"])) {
//     $Action->save();
// }

/** Cargamos el controlador correspondiente */
require_once $controllers[$module];
$controllerClass = str_replace('.php', '', $controllers[$module]);
$controller = new $controllerClass($id, $idSucursal);

/** Verificamos si el método existe antes de ejecutarlo */
if ($operation && method_exists($controller, $operation))
    $controller->$operation();
else
    echo json_encode(["success" => false, "message" => "Operación no válida"]);
