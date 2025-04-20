<?php
require '../models/Cashbox.php';

class CashboxController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "cajas";
    private $model;
    private $id;
    private $idSucursal;
    private $idUser;

    private $messages = [
        "save_success"   => "Caja registrada correctamente.",
        "save_failed"    => "Error al registrar caja.",
        "update_success" => "Caja actualizada correctamente.",
        "update_failed"  => "Error al actualizar la caja.",
        "delete_success" => "Caja eliminada correctamente.",
        "delete_failed"  => "Error al eliminar la caja.",
        "open_success"   => "Caja abierta correctamente.",
        "open_failed"    => "Error al abrir caja.",
        "close_success"  => "Caja cerrada correctamente.",
        "close_failed"   => "Error al cerrar la caja.",
        "required"       => "Debe completar la información obligatoria.",
        "box_open_error" => "Es necesario cerrar la caja para eliminar.",
        "empty_cashbox"  => "Es necesario abrir una caja para las ventas."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model      = new Cashbox();
        $this->id         = $id         !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idUser     = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
    }

    public function save()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['nombre'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Nombre */
        $name = $this->model::sanitizeInput('nombre', 'text');;

        /** Información a registrar o actualizar */
        $data = ['nombre' => $name];

        /** Valida que no exista un registro similar al entrante */
        if ($this->model::existsByFieldAndSucursal($this->table, 'nombre', $name, $this->idSucursal)) {
            echo json_encode(['success' => false, 'message' => "La caja " . $name .  " ya existe"]);
            return;
        }

        if (!$this->id) {
            /** Agregamos sucursal */
            $data['id_sucursal'] = $this->idSucursal;

            $save = $this->model::insert($this->table, $data);

            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['save_success']]
                    : ['success' => false, 'message' => $this->messages['save_failed']]
            );
        } else {
            $save = $this->model::update($this->table, $this->id, $data);
            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['update_success']]
                    : ['success' => false, 'message' => $this->messages['update_failed']]
            );
        }
    }

    public function update()
    {
        $recoverRegister = $this->model->selectOne($this->id);

        echo json_encode(
            count($recoverRegister) > 0
                ? ['success' => true, 'message' => '', 'data' => $recoverRegister[0]]
                : ['success' => false, 'message' => 'No se encontró el registro']
        );
    }

    public function delete()
    {
        $statusCashbox = $this->model->select($this->table, $this->id);
        $isOpen        = $statusCashbox['abierta'];

        if ($isOpen == 1) {
            echo json_encode(['success' => false, 'message' => $this->messages['box_open_error']]);
            return;
        }

        $delete = $this->model::delete($this->table, $this->id);
        echo json_encode(
            $delete
                ? ['success' => true, 'message' => $this->messages['delete_success']]
                : ['success' => false, 'message' => $this->messages['delete_failed']]
        );
    }

    public function dataTable()
    {
        $response = $this->model::selectAllBySucursal($this->table, $this->idSucursal);
        $data     = array();

        if (count($response) > 0) {
            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date             = date("d/m/Y", strtotime($day));

                $isOpen  = $row['abierta'] == 1;
                $estado  = $isOpen
                    ? "<span class=\"badge bg-success font-14 px-3 fw-normal cursor-pointer\" onclick=\"loadRegisteredDetails('ArqueoCaja', '{$row['id']}')\">Abierta</span>"
                    : "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Cerrada</span>";

                $btn  = "";
                $btn .= $row['abierta'] == 0 ? "<button type=\"button\" class=\"btn btn-inverse-success mx-1\" onclick=\"openCashbox('{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-box m-0\"></i></button>" : "";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateRegister('Caja', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Caja', '{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";

                $data[] = [
                    "Caja"          => $row['nombre'],
                    "Fecha de Alta" => $date,
                    "Estado"        => $estado,
                    "Acciones"      => $btn
                ];
            }
        }
        echo json_encode($data);
    }

    public function openCashbox()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['id', 'monto'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        $startDate = date('Y-m-d H:i:s');
        $initialAmount = $this->model::sanitizeInput('monto', 'float');

        $openCashbox = $this->model->open($this->id, $this->idUser, $startDate, $initialAmount);
        echo json_encode(
            $openCashbox
                ? ['success' => true, 'message' => $this->messages['open_success']]
                : ['success' => false, 'message' => $this->messages['open_failed']]
        );
    }

    public function closeCashbox()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['id', 'cashboxCountId'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        $cashboxCountId = $this->model::sanitizeInput('cashboxCountId', 'int');
        $endDate = date('Y-m-d H:i:s');

        $closeCashbox = $this->model->close($this->id, $cashboxCountId, $endDate);
        echo json_encode(
            $closeCashbox
                ? ['success' => true, 'message' => $this->messages['close_success']]
                : ['success' => false, 'message' => $this->messages['close_failed']]
        );
    }

    public function hasOpenCashbox() 
    {
        $hasOpen = $this->model->hasOpen($this->idSucursal);

        echo json_encode(
            $hasOpen > 0
                ? ['success' => true ]
                : ['success' => false, 'data' => false, 'message' => $this->messages['empty_cashbox']]
        );
    }
}