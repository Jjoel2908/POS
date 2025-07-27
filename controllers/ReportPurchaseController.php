<?php
require '../models/ReportProcessor.php';
require_once '../config/global.php';
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

                /** Identificador de la compra */
                $purchaseId = $row['id'];
                
                /** Módulo */
                $module = 'Compra';

                /** Referencia */
                $reference = PERMISSION_REPORT_PURCHASE;
            
                /** Token para la seguridad */
                $tokenData = "$purchaseId|$module|$reference";
                $token     = hash_hmac('sha256', $tokenData, SECRET_KEY);

                /** URL para cargar el PDF */
                $url = "../invoice/index.php?id=$purchaseId&reference=$reference&module=$module&token=$token";

                /** Botones */
                $btn  = "<button type=\"button\" class=\"btn btn-inverse-warning font-18 mx-1\" onclick=\"loadRegisteredDetails('DetalleCompra', '{$purchaseId}', '{$date}')\"><i class=\"fa-solid fa-eye\"></i></button>";
                $btn .= "<a href=\"$url\" target=\"_blank\" class=\"btn btn-inverse-primary font-18 mx-1\"><i class=\"fa-solid fa-file-invoice\"></i></a>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger font-18 mx-1\" onclick=\"deleteRegister('Compra', '{$purchaseId}')\"><i class=\"fa-solid fa-trash\"></i></button>";

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
