<?php
require '../models/ReportProcessor.php';
class ReportExpenseController
{
    private $model;

    public function __construct()
    {
        $this->model = new ReportProcessor();
    }

    public function dataTable()
    {
        $response = $this->model->getAllExpenses();
        $data     = array();

        if (count($response['table']) > 0) {
            foreach ($response['table'] as $row) {

                list($day, $hour) = explode(" ", $row['fecha']);
                $date = date("d/m/Y", strtotime($day));
                $time = date("h:i A", strtotime($hour));
                
                /** Formateamos el total */
                $monto = "<span class=\"fw-bold\">$" . number_format($row['monto'], 2) . "</span>";

                $data[] = [
                    "Folio"             => $row['id'],
                    "Concepto"          => $row['concepto'],
                    "Fecha de Creación" => $date,
                    "Hora"              => $time,
                    "Observaciones"     => $row['observaciones'],
                    "Monto"             => $monto,
                    "Creado Por"        => $row['usuario']
                ];
            }

            echo json_encode(["success" => true, "total" => $response['total'], "table" => $data]);
        } else echo json_encode($data);
    }
}