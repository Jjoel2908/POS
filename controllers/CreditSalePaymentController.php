<?php
require '../models/CreditSalePayment.php';

class CreditSalePaymentController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "abonos_credito";
    private $model;
    private $id;
    private $idUser;
    private $idSucursal;

    private $messages = [
        "amount_required" => "El monto del pago debe ser mayor a cero",
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model      = new CreditSalePayment();
        $this->id         = $id         !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idUser     = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
    }

    public function dataTable()
    {
        $registerId   = $this->model::sanitizeInput('registerId', 'int');
        $response = $this->model->dataTable($registerId);
        $data     = array();

        if (count($response) > 0) {

            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date  = date("d/m/Y", strtotime($day));
                $time  = date("h:i A", strtotime($hour));

                $data[] = [
                    "Fecha" => $date,
                    "Hora"  => $time,
                    "Monto" => "$" . number_format($row['monto'], 2),
                ];
            }
        } else {
            $data[] = [
                "Fecha" => "-",
                "Hora"  => "-",
                "Monto" => "-"
            ];
        }
        echo json_encode($data);
    }
}
