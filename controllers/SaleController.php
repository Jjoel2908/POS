<?php
require '../models/Sale.php';

class SaleController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "ventas";
    private $model;
    private $id;
    private $idUser;
    private $idSucursal;

    private $messages = [
        "save_success"   => "Venta registrada correctamente.",
        "save_failed"    => "Error al registrar la venta.",
        "update_success" => "Venta actualizada correctamente.",
        "update_failed"  => "Error al actualizar la venta.",
        "delete_success" => "Venta eliminada correctamente.",
        "delete_failed"  => "Error al eliminar la venta.",
        "required"       => "Debe completar la información obligatoria de la venta.",
        "empty"          => "Aún no hay información de venta disponible."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model      = new Sale();
        $this->id         = $id         !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idUser     = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
    }

    public function save()
    {
        /** Validamos si hay detalles de productos pendientes */
        $SaleDetails = new SaleDetails();
        $details     = $SaleDetails->getSaleDetails($this->idUser);

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

        $save = $this->model->insertSale($this->idUser, $this->idSucursal, $totalFormatter);
        echo json_encode(
            $save
                ? ['success' => true, 'message' => $this->messages['save_success']]
                : ['success' => false, 'message' => $this->messages['save_failed']]
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

                $btn = "<button type=\"button\" class=\"btn btn-warning text-white font-18 mx-1\" onclick=\"loadRegisteredDetails('DetalleVenta', '{$row['id']}', '{$date}')\"><i class=\"fa-solid fa-folder-open\"></i></button>";
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
