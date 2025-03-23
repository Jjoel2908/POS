<?php
require '../models/Cashbox.php';

class CashboxController
{
    private $table = "cajas";
    private $model;
    private $messages = [
        "save_success" => "Caja registrada correctamente.",
        "save_failed" => "Error al registrar caja.",
        "update_success" => "Caja actualizada correctamente.",
        "update_failed" => "Error al actualizar la caja.",
        "delete_success" => "Caja eliminada correctamente.",
        "delete_failed" => "Error al eliminar la caja.",
        "required" => "Debe completar la información obligatoria.",
        "box_open_error" => "Es necesario cerrar la caja para eliminar."
    ];

    public function __construct()
    {
        $this->model = new Cashbox();
    }

    public function save()
    {
        /** Sucursal */
        $idSucursal = $_POST['id_sucursal'] ?? $_SESSION['sucursal'];

        /** Información a registrar o actualizar */
        $data = ['nombre' => $_POST['nombre']];

        /** Valida campos requeridos */
        if (!$this->model::validateData(['nombre'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Valida que no exista un registro similar al entrante */
        if($this->model::existsByFieldAndSucursal($this->table, 'nombre', $_POST['nombre'], $idSucursal)) {
            echo json_encode(['success' => false, 'message' => "La caja {$_POST['nombre']} ya existe"]);
            return;
        }

        if (empty($_POST['id'])) {

            /** Agregamos la fecha de creación */
            $data['fecha'] = date('Y-m-d H:i:s');

            /** Agregamos sucursal */
            $data['id_sucursal'] = $idSucursal;

            /** Agregamos el usuario */
            $data['creado_por'] = $_SESSION['id'];

            $save = $this->model::insert($this->table, $data);

            echo json_encode(
                $save 
                    ? ['success' => true, 'message' => $this->messages['save_success']] 
                    : ['success' => false, 'message' => $this->messages['save_failed']]
                );

        } else {
            $save = $this->model::update($this->table, $_POST['id'], $data);
            echo json_encode(
                $save 
                    ? ['success' => true, 'message' => $this->messages['update_success']] 
                    : ['success' => false, 'message' => $this->messages['update_failed']]
                );
        }    
    }

    public function update()
    {
        $recoverRegister = $this->model->selectOne($_POST['id']);

        echo json_encode(
            count($recoverRegister) > 0     
                ? ['success' => true, 'message' => '', 'data' => $recoverRegister[0]] 
                : ['success' => false, 'message' => 'No se encontró el registro']
        );
    }

    public function delete()
    {
        $stateCashbox = $this->model->select($this->table, $_POST['id']);
        $isOpen = $stateCashbox['abierta'];

        if ($isOpen == 1) {
            echo json_encode(['success' => false, 'message' => $this->messages['box_open_error']]);
            return;
        }

        $delete = $this->model::delete($this->table, $_POST['id']);
        echo json_encode(
            $delete
                ? ['success' => true, 'message' => $this->messages['delete_success']]
                : ['success' => false, 'message' => $this->messages['delete_failed']]
        );
    }

    public function dataTable()
    {
        $response = $this->model::selectAllBySucursal($this->table, $_SESSION['sucursal']);
        $existPurchaseOpen = $this->model::exists($this->table, 'abierta', 1);
        $data = array();

        if (count($response) > 0) {
            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date  = date("d/m/Y", strtotime($day));

                $isOpen = $row['abierta'];
                $estado = $isOpen ? "<span class=\"badge bg-success font-14 px-3 fw-normal cursor-pointer\" onclick=\"moduleCashbox.modalOpenCashbox()\">Abierta</span>" : "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Cerrada</span>";

                $invisible = "";
                if ($existPurchaseOpen) $invisible = "invisible";

                $btn  = "<button type=\"button\" class=\"btn btn-inverse-success mx-1 $invisible\" onclick=\"moduleCashbox.openCashbox('{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-box m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"moduleCashbox.updateCashbox('{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"moduleCashbox.deleteCashbox('{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";

                $data[] = [
                    "CAJA" => $row['nombre'],
                    "FECHA DE CREACIÓN" => $date,
                    "ESTADO"    => $estado,
                    "ACCIONES"  => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}