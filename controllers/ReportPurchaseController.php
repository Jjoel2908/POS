<?php
require '../models/ReportProcessor.php';
class ReportPurchaseController
{
    private $model;

    public function __construct()
    {
        $this->model = new ReportProcessor();
    }

    public function dataTable()
    {
        $response = $this->model->getAllPurchases();
        $data     = array();

        if (count($response['table']) > 0) {
            foreach ($response['table'] as $row) {

                list($day, $hour) = explode(" ", $row['fecha']);
                $date             = date("d/m/Y", strtotime($day));

                /** Botones */
                $btn = "<button type=\"button\" class=\"btn btn-warning text-white font-18 mx-1\" onclick=\"loadRegisteredDetails('DetalleCompra', '{$row['id']}', '{$date}')\"><i class=\"fa-solid fa-folder-open\"></i></button>";
                
                /** Formateamos el total */
                $total = "<span class=\"fw-bold\">$" . number_format($row['total'], 2) . "</span>";
                
                $data[] = [
                    "Folio"             => $row['id'],
                    "Fecha de Creación" => $date,
                    "Comprador"         => $row['comprador'],
                    "Total"             => $total,
                    "Acciones"          => $btn
                ];
            }

            echo json_encode(["success" => true, "total" => $response['total'], "table" => $data]);
        } else echo json_encode($data);
    }
}
