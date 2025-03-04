<?php
session_start();
require '../models/Purchase.php';

class PurchaseController {
    private $purchase;

    public function __construct() {
        $this->purchase = new Purchase();
    }

    public function infoProduct() {
        $infoProduct = $this->purchase::select('productos', $_POST['id']);
        echo json_encode(
            count($infoProduct) > 0 ?
                ['success' => true, 'message' => 'Producto Encontrado', 'data' => $infoProduct] :
                ['success' => false, 'message' => 'No se encontró el registro del producto']
        );
    }

    public function addPurchaseDetails() {
        if ($this->purchase::validateData(['id', 'cantidad'], $_POST)) {
            $product = $this->purchase->select('productos', $_POST['id']);
            $dataPurchase = [
                'id_producto' => $_POST['id'],
                'id_usuario'  => $_SESSION['id'],
                'precio'      => $product['precio_compra'],
                'cantidad'    => $_POST['cantidad'],
            ];
            $existProduct = $this->purchase->existProductDetail($_POST['id'], $_SESSION['id']);
            if (empty($existProduct)) {
                $result = $this->purchase->insertPurchaseDetail($dataPurchase);
            } else {
                $idPurchase = $existProduct[0]['id'];
                $quantity = $existProduct[0]['cantidad'] + $_POST['cantidad'];
                $result = $this->purchase->updatePurchaseDetail($idPurchase, ["cantidad" => $quantity]);
            }
            echo json_encode(['success' => $result, 'message' => $result ? "Operación realizada correctamente" : "Error en la operación"]);
        } else {
            echo json_encode(['success' => false, 'message' => "Datos de entrada no válidos"]);
        }
    }

    public function savePurchase() {
        if ($this->purchase::validateData(['id'], $_SESSION)) {
            $productDetails = $this->purchase->getProductDetails($_SESSION['id']);
            if (!empty($productDetails)) {
                $totalPurchase = array_reduce($productDetails, fn($sum, $detail) => $sum + ($detail['cantidad'] * $detail['precio']), 0);
                $savePurchase = $this->purchase->savePurchase(["id_usuario" => $_SESSION['id'], "total" => $totalPurchase]);
                if ($savePurchase > 0) {
                    $update = $this->purchase->updateIdPurchaseDetails($savePurchase, $_SESSION['id']);
                    foreach ($this->purchase->idPurchaseDetails($savePurchase) as $product) {
                        $this->purchase->addStock($product['id_producto'], $product['cantidad']);
                    }
                    echo json_encode(['success' => true, 'message' => 'La compra se realizó correctamente']);
                } else echo json_encode(['success' => false, 'message' => 'Error al generar la compra']);
            } else echo json_encode(['success' => false, 'message' => 'No se puede generar la compra']);
        } else echo json_encode(['success' => false, 'message' => 'Intente más tarde']);
    }

    public function deletePurchaseDetail() {
        $result = $this->purchase->deletePurchaseDetail($_POST['id']);
        echo json_encode(['success' => $result, 'message' => $result ? "Detalle eliminado correctamente" : "Error al eliminar detalle"]);
    }

    public function cancelPurchase() {
        $result = $this->purchase->cancelPurchase($_SESSION['id']);
        echo json_encode(['success' => $result, 'message' => $result ? "Compra eliminada correctamente" : "Error al cancelar la compra"]);
    }

    public function selectProducts() {
        $products = $this->purchase::selectAll('productos');
        foreach ($products as $product) {
            echo '<option value="' . $product['id'] . '">' . $product['codigo'] . ' | ' . $product['nombre'] . '</option>';
        }
    }
}

$controller = new PurchaseController();
$action = $_GET['op'] ?? '';
if (method_exists($controller, $action)) {
    $controller->$action();
} else {
    echo json_encode(['success' => false, 'message' => 'Operación no válida']);
}
