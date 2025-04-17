<?php
require '../models/PurchaseDetails.php';
require '../models/Product.php';

class PurchaseDetailsController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "detalle_compra";
    private $model;
    private $id;
    private $idUser;
    private $idSucursal;

    private $messages = [
        "save_success"   => "Detalle de compra registrado correctamente.",
        "save_failed"    => "Error al registrar el detalle de compra.",
        "update_success" => "Detalle de compra actualizado correctamente.",
        "update_failed"  => "Error al actualizar el detalle de compra.",
        "delete_success" => "Detalle de compra eliminado correctamente.",
        "delete_failed"  => "Error al eliminar el detalle de compra.",
        "required"       => "Debe completar la información obligatoria del detalle de compra."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model      = new PurchaseDetails();
        $this->id         = $id         !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idUser     = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
    }

    public function save()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['id', 'cantidad'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        $Product = new Product();
        $detail  = $Product->getPurchasePrice($this->id);
        $quantity = $this->model::sanitizeInput('cantidad', 'int');

        /** Información a registrar o actualizar */
        $data = [
            'id_producto' => $this->id,
            'precio'      => number_format((float)$detail['precio_compra'], 2, '.', ''),
            'cantidad'    => $quantity,
        ];

        /** Identificador de usuario */
        $existDetail = $this->model->existPurchaseDetails($this->id, $this->idUser);

        /** Si no existe un detalle de compra idéntico, registramos uno nuevo */
        if (empty($existDetail)) {
            $save = $this->model::insert($this->table, $data);

            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['save_success'], 'data' => 'DetalleCompra']
                    : ['success' => false, 'message' => $this->messages['save_failed']]
            );
        } else {
            /** Si el detalle de compra ya existe, actualizamos la cantidad */
            $idPurchaseDetail = $existDetail[0]['id'];
            $save = $this->model->updatePurchaseDetail($idPurchaseDetail, $quantity);

            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['update_success'], 'data' => 'DetalleCompra']
                    : ['success' => false, 'message' => $this->messages['update_failed']]
            );
        }
    }

    public function delete()
    {
        $delete = $this->model::delete($this->table, $this->id);
        echo json_encode(
            $delete
                ? ['success' => true, 'message' => $this->messages['delete_success']]
                : ['success' => false, 'message' => $this->messages['delete_failed']]
        );
    }

    public function dataTable()
    {
        $purchaseId = $_POST['purchaseId'] ? (filter_var($_POST['purchaseId'], FILTER_VALIDATE_INT) ?: 0) : null;

        $response = $this->model->dataTable($this->idUser, $purchaseId);
        $HTML     = "";
        $total    = 0;

        if (count($response) > 0) {
            foreach ($response as $row) {
                $product  = htmlspecialchars($row['producto']);
                $quantity = (int) $row['cantidad'];
                $price    = (float) $row['precio'];
                $btn = "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('DetalleCompra', '{$row['id']}', '{$product}')\"><i class=\"bx bx-trash m-0\"></i></button>";

                $subTotal  = $price * $quantity;
                $total    += $subTotal;

                $HTML .= "<tr>";
                $HTML .= "<td class='text-start'>{$product}</td>";
                $HTML .= "<td>{$quantity} uds.</td>";
                $HTML .= "<td class='text-end'>$" . number_format($price, 2) . "</td>";
                $HTML .= "<td class='text-end'>$" . number_format($subTotal, 2) . "</td>";
                $HTML .= "<td>{$btn}</td>";
                $HTML .= "</tr>";
            }
        } else {
            $HTML .= '<tr><td colspan="5">No hay detalles de compra disponibles.</td></tr>';
        }

        echo json_encode([
            'success' => true,
            'message' => '',
            'data' => [
                'data' => $HTML, 
                'total' => number_format($total, 2)
            ]
        ]);
    }
}