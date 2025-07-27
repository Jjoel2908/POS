<?php
require '../models/ReportProcessor.php';
class ReportSaleController
{
    private $model;

    public function __construct()
    {
        $this->model = new ReportProcessor();
    }

    public function dataTable()
    {
        $response = $this->model->getAllSales();
        $data     = array();

        if (count($response['table']) > 0) {
            foreach ($response['table'] as $row) {

                list($day, $hour) = explode(" ", $row['fecha']);
                $date             = date("d/m/Y", strtotime($day));

                /** Botones */
                $btn = "<button type=\"button\" class=\"btn btn-inverse-warning font-18 mx-1\" onclick=\"loadRegisteredDetails('DetalleVenta', '{$row['id']}', '{$date}')\"><i class=\"fa-solid fa-folder-open\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger font-18 mx-1\" onclick=\"deleteRegister('Venta', '{$row['id']}')\"><i class=\"fa-solid fa-trash\"></i></button>";
                
                
                /** Formateamos el total */
                $total = "<span class=\"fw-bold\">$" . number_format($row['total'], 2) . "</span>";

                /** Tipo de Venta con badge */
                $saleType = $this->model::$SALE_TYPE[$row['tipo_venta']] ?? "N/A";
                $saleColor = $this->model::$SALE_TYPE_COLORS[$row['tipo_venta']] ?? 'bg-secondary';
                $saleTypeBadge = "<span class=\"badge {$saleColor} font-12 px-4 fw-normal\">{$saleType}</span>";

                /** Estatus de pago con badge */
                $status = $this->model::$PAYMENT_STATUS[$row['estatus']] ?? "N/A";
                $statusColor = $this->model::$PAYMENT_STATUS_COLORS[$row['estatus']] ?? 'bg-secondary';
                $statusBadge = "<span class=\"badge {$statusColor} font-12 px-4 fw-normal\">{$status}</span>";
                
                $data[] = [
                    "Folio"             => $row['id'],
                    "Fecha de Creación" => $date,
                    "Cliente"           => $row['cliente'],
                    "Tipo de Venta"     => $saleTypeBadge,
                    "Estatus"           => $statusBadge,
                    "Total"             => $total,
                    "Acciones"          => $btn
                ];
            }

            echo json_encode(["success" => true, "total" => $response['total'], "table" => $data]);
        } else echo json_encode($data);
    }
}
