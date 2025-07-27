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
        $totalFormatted  = number_format(floatval($total), 2, '.', '');

        $save = $this->model->insertPurchase($this->idUser, $this->idSucursal, $totalFormatted);
        echo json_encode(
            $save
                ? ['success' => true, 'message' => $this->messages['save_success']]
                : ['success' => false, 'message' => $this->messages['save_failed']]
        );
    }

    public function delete()
    {
        /** Obtenemos los productos en detalle_compra */
        $details = PurchaseDetails::getPurchaseDetailsByPurchaseId($this->id);

        if (empty($details)) {
            echo json_encode(['success' => false, 'message' => $this->messages['empty']]);
            return;
        }
        
        $delete = $this->model->deletePurchase($this->id, $details);

        echo json_encode(
            $delete
                ? ['success' => true, 'message' => $this->messages['delete_success']]
                : ['success' => false, 'message' => $this->messages['delete_failed']]
        );
    }

    public function dataTable()
    {
        $response = $this->model->dataTable();
        $data = array();

        if (count($response) > 0) {

            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date  = date("d/m/Y", strtotime($day));
                $time  = date("h:i A", strtotime($hour));

                $btn = "<button type=\"button\" class=\"btn btn-warning text-white font-18 mx-1\" onclick=\"loadRegisteredDetails('DetalleCompra', '{$row['id']}', '{$date}')\"><i class=\"fa-solid fa-folder-open\"></i></button>";
                $data[] = [
                    "Fecha"       => $date,
                    "Hora"        => $time,
                    "Total"       => "$" . number_format($row['total'], 2),
                    "Creador Por" => $row['usuario'],
                    "Acciones"    => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}
