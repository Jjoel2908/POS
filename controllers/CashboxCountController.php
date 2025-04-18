<?php
require '../models/CashboxCount.php';

class CashboxCountController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "arqueo_caja";
    private $model;
    private $id;
    private $idUser;
    private $idSucursal;

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model      = new CashboxCount();
        $this->id         = $id         !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idUser     = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
    }

    public function dataTable()
    {
        $purchaseId = $_POST['registerId'] ? (filter_var($_POST['registerId'], FILTER_VALIDATE_INT) ?: 0) : null;

        $response = $this->model->dataTable($purchaseId);
        $data = array();

        if (count($response) > 0) {

            foreach ($response as $row) {

                $finalAcount = $row['monto_fin'] ? "$" . number_format($row['monto_fin'], 2) : "$0.00";

                $btn  = "<button type=\"button\" class=\"btn btn-inverse-success mx-1\" onclick=\"closeCashbox( '{$row['id']}','{$row['id_caja']}', '{$finalAcount}')\"><i class=\"fa-solid fa-folder-closed me-1\"></i> Cerrar Caja</button>";

                list($day, $hour) = explode(" ", $row['fecha_inicio']);
                $date  = date("d/m/Y", strtotime($day));
                $time  = date("h:i A", strtotime($hour));

                $sales = ($row['total_ventas'] == 1) ? ' Venta' : ' Ventas';

                $data[] = [
                    "Caja"          => $row['caja'],
                    "Fecha Inicio"  => $date,
                    "Hora Inicio"   => $time,
                    "Monto Inicial" => "$" . number_format($row['monto_inicial'], 2),
                    "Ventas"        => $row['total_ventas'] . $sales,
                    "Acciones"      => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}
