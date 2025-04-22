<?php
require '../models/CreditSale.php';

class CreditSaleController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "ventas";
    private $model;
    private $id;
    private $idUser;
    private $idSucursal;

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model      = new CreditSale();
        $this->id         = $id         !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idUser     = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
    }

    // case 'processPayment':

    //     if (!$Sale::validateData(['id', 'total_pagado'], $_POST)) {
    //        echo json_encode(['success' => false, 'message' => 'Complete los campos requeridos']);
    //        break;
    //     }
  
    //     $idRepartidor  = intval($_POST['id']);
    //     $paymentAmount = number_format(floatval($_POST['total_pagado']), 2, '.', '');
  
    //     if ($paymentAmount <= 0) {
    //        echo json_encode(['success' => false, 'message' => 'El monto del pago debe ser mayor a cero']);
    //        break;
    //     }
  
    //     $response = $Sale->processPayment($idRepartidor, $paymentAmount);
  
    //     echo json_encode($response);
    //     break;

    public function dataTable()
    {
        $response = $this->model->dataTable();
        $data = array();

        if (count($response) > 0) {

            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date  = date("d/m/Y", strtotime($day));
                $time  = date("h:i A", strtotime($hour));

                $btn  = "";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-success mx-1 px-3\" onclick=\"addPayment(" . $row['id'] . ")\"><i class=\"fa-solid fa-dollar\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"loadRegisteredDetails('DetalleVenta', '{$row['id']}', '{$date}')\"><i class=\"fa-solid fa-folder-open\"></i></button>";
                
                $data[] = [
                    "Fecha"           => $date,
                    "Hora"            => $time,
                    "Cliente"         => $row['cliente'],
                    "Total Venta"     => "$" . number_format($row['total_venta'], 2),
                    "Total Pagado"    => "$" . number_format($row['total_pagado'], 2),
                    "Deuda Pendiente" => "$" . number_format($row['pendiente_pago'], 2),
                    "Acciones"        => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}
