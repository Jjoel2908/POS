<?php
require '../models/Purchase.php';

class PurchaseController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "compras";
    private $model;
    private $id;
    private $idUser;
    private $idSucursal;

    private $messages = [
        "save_success"   => "Compra registrada correctamente.",
        "save_failed"    => "Error al registrar la compra.",
        "update_success" => "Compra actualizada correctamente.",
        "update_failed"  => "Error al actualizar la compra.",
        "delete_success" => "Compra eliminada correctamente.",
        "delete_failed"  => "Error al eliminar la compra.",
        "required"       => "Debe completar la información obligatoria de la compra.",
        "empty"          => "Aún no hay información de compra disponible."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model      = new Purchase();
        $this->id         = $id         !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idUser     = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
    }

    public function save()
    {
        /** Validamos si hay detalles de productos pendientes */
        $PurchaseDetails = new PurchaseDetails();
        $details         = $PurchaseDetails->getPurchaseDetails($this->idUser);

        if (empty($details)) {
            echo json_encode(['success' => false, 'message' => $this->messages['empty']]);
            return;
        }

        /** Calcular total de la compra */
        $total = 0;
        foreach ($details as $detail) {
            $total += $detail['cantidad'] * $detail['precio'];
        }
        $totalFormatter  = number_format(floatval($total), 2, '.', '');

        $save = $this->model->insertPurchase($this->idUser, $this->idSucursal, $totalFormatter);
        echo json_encode(
            $save
                ? ['success' => true, 'message' => $this->messages['save_success']]
                : ['success' => false, 'message' => $this->messages['save_failed']]
        );
    }

    public function dataTable()
    {
        $idUser   = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
        $response = $this->model->dataTable($idUser);
        $HTML     = "";
        $total    = 0;

        if (count($response) > 0) {
            foreach ($response as $row) {
                $product  = htmlspecialchars($row['producto']);
                $quantity = (int) $row['cantidad'];
                $price    = (float) $row['precio'];
                $btn = "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Detalle de Compra', '{$row['id']}', '{$product}')\"><i class=\"bx bx-trash m-0\"></i></button>";

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
