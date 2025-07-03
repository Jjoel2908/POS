<?php
require '../models/Quote.php';

class QuoteController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "cotizaciones";
    private $model;
    private $id;
    private $idUser;
    private $idSucursal;

    private $messages = [
        "save_success"      => "Cotización registrada correctamente.",
        "save_failed"       => "Error al registrar la cotización.",
        "update_success"    => "Cotización actualizada correctamente.",
        "update_failed"     => "Error al actualizar la cotización.",
        "delete_success"    => "Cotización eliminada correctamente.",
        "delete_failed"     => "Error al eliminar la cotización.",
        "required"          => "Debe completar la información obligatoria de la cotización.",
        "required_customer" => "Debe seleccionar un cliente para la cotización.",
        "empty"             => "Aún no hay información de cotización disponible.",
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model      = new Qoute();
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
        if ($saleType == $this->model::$creditSale && empty($_POST['customerId'])) {
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
        $customerId = !empty($_POST['customerId']) ? $this->model::sanitizeInput('customerId', 'int') : null;

        /** Registrar venta en el modelo */
        $save = $this->model->insertSale($this->idUser, $this->idSucursal, $cashbox, $saleType, $customerId, $totalFormatted);
        echo json_encode(
            $save
                ? ['success' => true, 'message' => $this->messages['save_success']]
                : ['success' => false, 'message' => $this->messages['save_failed']]
        );
    }

    public function dataTable()
    {
        $response = $this->model->dataTable();
        $now = date('Y-m-d');
        $data = array();

        if (count($response) > 0) {
            foreach ($response as $row) {

                $status = $row['estado_cotizacion'] == 1 && $row['fecha_vencimiento'] < $now
                    ? 5
                    : $row['estado_cotizacion']
                    
                $data[] = [
                    "Cliente" => $row['cliente'],
                    "Fecha Creación"       => date("d/m/Y", strtotime($row['fecha_creacion'])),
                    "Fecha Vencimiento"       => date("d/m/Y", strtotime($row['fecha_vencimiento'])),
                    "Estado" => $status
                    "Total"        => "$" . number_format($row['total'], 2),
                ];
            }
        }
        echo json_encode($data);
    }
}
