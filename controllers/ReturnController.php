<?php
session_start();
require '../models/ReturnProduct.php';

class ReturnController {
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "devoluciones";
    private $model;
    private $id;
    private $idSucursal;

    private $messages = [
        "save_success" => "Devolución registrada correctamente.",
        "save_failed" => "Error al registrar la devolución.",
        "update_success" => "Devolución actualizada correctamente.",
        "update_failed" => "Error al actualizar la devolución.",
        "delete_success" => "Devolución eliminada correctamente.",
        "delete_failed" => "Error al eliminar la devolución.",
        "required" => "Debe completar la información obligatoria."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new ReturnProduct();
        $this->id = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    private function save() 
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['id_venta', 'id_detail', 'id_producto', 'cantidad', 'motivo', 'precio'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Información a registrar o actualizar */
        $quantity = $this->model::sanitizeInput('cantidad', 'int');
        $price = $this->model::sanitizeInput('precio', 'float');

        $data = [
            'id_venta'        => $this->model::sanitizeInput('id_venta', 'int'),
            'id_cliente'      => $this->model::sanitizeInput('id_cliente', 'int'),
            'id_detail'       => $this->model::sanitizeInput('id_detail', 'int'),
            'id_producto'     => $this->model::sanitizeInput('id_producto', 'int'),
            'cantidad'        => $quantity,
            'id_venta'        => $this->model::sanitizeInput('id_venta', 'int'),
            'motivo'          => $this->model::sanitizeInput('motivo', 'text'),
            'total'           => $price * $quantity,
        ];

        if (!$this->return->decreaseDetailProduct($_POST['id_detail'], $_POST['cantidad'])) {
            echo json_encode(['success' => false, 'message' => 'Error al modificar el detalle de venta']);
            return;
        }

        if (!$this->return->addStockProduct($_POST['id_producto'], $_POST['cantidad'])) {
            echo json_encode(['success' => false, 'message' => 'Error al agregar el producto al almacén']);
            return;
        }

        $sale = $this->return->select('ventas', $_POST['id_venta']);
     

        if ($this->return->insertReturn($data)) {
            echo json_encode(['success' => true, 'message' => 'Devolución registrada correctamente']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Error al registrar la devolución']);
        }
    }

    private function selectSales() {
        $sales = $this->return::selectSales();
        if (!empty($sales)) {
            foreach ($sales as $sale) {
                if ((int) $sale['total'] == 0) continue;
                $date = date("d/m/Y", strtotime(explode(" ", $sale['fecha'])[0]));
                echo '<option value="' . $sale['id'] . '">' . $sale['nombre_cliente'] . ' - ' . $date . '</option>';
            }
        } else {
            echo '<option value=""></option>';
        }
    }

    private function selectProducts() {
        $products = $this->return::selectProducts($_POST['id']);
        if (!empty($products)) {
            foreach ($products as $product) {
                echo '<option value="' . $product['id'] . '">' . $product['codigo_producto'] . ' | ' . $product['nombre_producto'] . '</option>';
            }
        } else {
            echo '<option value=""></option>';
        }
    }

    private function selectQuantity() {
        $detail = $this->return->detailSale($_POST['id']);
        if (!empty($detail)) {
            echo json_encode([
                'quantity'   => $detail[0]['cantidad'],
                'price'      => $detail[0]['precio'],
                'id_producto' => $detail[0]['id_producto']
            ]);
        }
    }

    private function dataTable() {
        $response = $this->return->dataTable();
        $data = [];

        foreach ($response as $row) {
            list($day, $hour) = explode(" ", $row['fecha']);
            $data[] = [
                "fecha"    => date("d/m/Y", strtotime($day)),
                "usuario"  => $row['nombre_usuario'],
                "producto" => $row['nombre_producto'],
                "cantidad" => $row['cantidad'],
                "motivo"   => $row['motivo'],
                "total"    => "$" . $row['total']
            ];
        }
        echo json_encode($data);
    }
}