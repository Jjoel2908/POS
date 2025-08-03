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
        $data = [];

        if (count($response['table']) > 0) {
            foreach ($response['table'] as $row) {
                $data[] = $this->formatRow($row);
            }

            echo json_encode([
                "success" => true,
                "total"   => $response['total'],
                "table"   => $data
            ]);
        } else {
            echo json_encode($data);
        }
    }

    /** ------------------------
     *   Métodos Privados
     *  ------------------------ */

    private function formatRow(array $row): array
    {
        list($day,) = explode(" ", $row['fecha']);
        $date = date("d/m/Y", strtotime($day));

        return [
            "Folio"             => $row['id'],
            "Fecha de Creación" => $date,
            "Cliente"           => $row['cliente'],
            "Tipo de Venta"     => $this->getSaleTypeBadge($row['tipo_venta']),
            "Estatus"           => $this->getPaymentStatusBadge($row['estatus']),
            "Total"             => $this->formatTotal($row['total']),
            "Acciones"          => $this->getActionButtons($row, $date)
        ];
    }

    private function getSaleTypeBadge(int $tipoVenta): string
    {
        $type  = $this->model::$SALE_TYPE[$tipoVenta] ?? "N/A";
        $color = $this->model::$SALE_TYPE_COLORS[$tipoVenta] ?? 'bg-secondary';

        return "<span class=\"badge {$color} font-12 px-4 fw-normal\">{$type}</span>";
    }

    private function getPaymentStatusBadge(int $estatus): string
    {
        $status = $this->model::$PAYMENT_STATUS[$estatus] ?? "N/A";
        $color  = $this->model::$PAYMENT_STATUS_COLORS[$estatus] ?? 'bg-secondary';

        return "<span class=\"badge {$color} font-12 px-4 fw-normal\">{$status}</span>";
    }

    private function formatTotal($total): string
    {
        return "<span class=\"fw-bold\">$" . number_format($total, 2) . "</span>";
    }

    private function getActionButtons(array $row, string $date): string
    {
        $saleId    = $row['id'];
        $module    = 'Venta';
        $reference = PERMISSION_REPORT_SALE;

        $tokenData = "$saleId|$module|$reference";
        $token     = hash_hmac('sha256', $tokenData, SECRET_KEY);
        $url       = "../invoice/index.php?id=$saleId&reference=$reference&module=$module&token=$token";

        $btn  = "<button type=\"button\" class=\"btn btn-inverse-warning font-18 mx-1\" onclick=\"loadRegisteredDetails('DetalleVenta', '{$saleId}', '{$date}')\"><i class=\"fa-solid fa-eye\"></i></button>";
        $btn .= "<a href=\"$url\" target=\"_blank\" class=\"btn btn-inverse-primary font-18 mx-1\"><i class=\"fa-solid fa-file-invoice\"></i></a>";

        // Mostrar botón de eliminar si cumple las condiciones
        $fechaVenta = strtotime($row['fecha']);
        $ahora      = time();
        $unaSemana  = 7 * 24 * 60 * 60;

        if (
            (int)$row['tipo_venta'] === 1 &&
            (int)$row['estatus'] === 1 &&
            ($ahora - $fechaVenta) <= $unaSemana
        ) {
            $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger font-18 mx-1\" onclick=\"deleteRegister('Venta', '{$saleId}')\"><i class=\"fa-solid fa-trash\"></i></button>";
        }

        return $btn;
    }
}
