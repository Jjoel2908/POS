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
        "save_success"      => "Venta registrada correctamente.",
        "save_failed"       => "Error al registrar la venta.",
        "update_success"    => "Venta actualizada correctamente.",
        "update_failed"     => "Error al actualizar la venta.",
        "delete_success"    => "Venta eliminada correctamente.",
        "delete_failed"     => "Error al eliminar la venta.",
        "required"          => "Debe completar la información obligatoria de la venta.",
        "required_customer" => "Debe seleccionar un cliente para la venta a crédito.",
        "empty"             => "Aún no hay información de venta disponible.",
        "empty_cashbox"     => "Es necesario abrir una caja para las ventas."
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
        /** Valida campos requeridos */
        if (!$this->model::validateData(['saleType'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Tipo de Venta */
        $saleType = $this->model::sanitizeInput('saleType', 'int');

        /** Valida campos requeridos */
        if ($_POST['customerId'] == 0 && $saleType == 2) {
            echo json_encode(['success' => false, 'message' => $this->messages['required_customer']]);
            return;
        }

        /** Validamos si hay productos en el carrito del usuario */
        $details = $this->model->isCartEmpty($this->idUser);
        if (empty($details)) {
            echo json_encode(['success' => false, 'message' => $this->messages['empty']]);
            return;
        }

        /** Calcular total de la venta */
        $totalFormatted = $this->model->calculateSaleTotal($details);

        /** Obtener ID de caja abierta */
        $Cashbox = new Cashbox();
        $cashbox = $Cashbox->hasOpen($this->idSucursal);

        if ($cashbox == 0) {
            echo json_encode(['success' => false, 'message' => $this->messages['empty_cashbox']]);
            return;
        }

        /** Cliente */
        $customerId = $this->model::sanitizeInput('customerId', 'int');

        /** Registrar venta en el modelo */
        $save = $this->model->insertSale($this->idUser, $this->idSucursal, $cashbox, $saleType, $customerId, $totalFormatted);

        $module = $saleType == 1 ? "Venta" : "VentaCredito"; 
        echo json_encode(
            $save
                ? ['success' => true, 'message' => $this->messages['save_success'], 'data' => $module]
                : ['success' => false, 'message' => $this->messages['save_failed'], 'data' => $module]
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
