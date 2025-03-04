<?php
require '../models/Cashbox.php';

class CashboxController
{
    private $table = "cajas";
    private $model;
    private $messages = [
        "save_success" => "Caja registrada correctamente",
        "save_failed" => "Error al registrar caja",
        "update_success" => "Caja actualizada correctamente",
        "update_failed" => "No se pudo actualizar la caja",
        "delete_success" => "Caja eliminada correctamente",
        "delete_failed" => "Error al eliminar la caja",
        "box_open_error" => "Es necesario cerrar la caja para eliminar"
    ];

    public function __construct()
    {
        $this->model = new Cashbox();
    }

    public function save()
    {
        $data = [
            'caja' => $_POST['caja'] ?? NULL,
        ];

        if ($this->model::validateData(['caja'], $_POST)) {
            $validateForm = $this->model::exists($this->table, 'caja', $_POST['caja']);

            if (!$validateForm) {
                if (empty($_POST['id'])) {
                    $saveCashbox = $this->model->insertCashbox($data);
                    echo json_encode(
                        $saveCashbox
                            ? ['success' => true, 'message' => $this->messages['save_success']]
                            : ['success' => false, 'message' => $this->messages['save_failed']]
                    );
                } else {
                    $saveCashbox = $this->model->updateCashbox($data, $_POST['id']);
                    echo json_encode(
                        $saveCashbox
                            ? ['success' => true, 'message' => $this->messages['update_success']]
                            : ['success' => false, 'message' => $this->messages['update_failed']]
                    );
                }
            } else {
                echo json_encode(['success' => false, 'message' => "La caja {$_POST['caja']} ya existe"]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Complete los campos requeridos"]);
        }
    }

    public function update()
    {
        $updateCashbox = $this->model->selectCashbox($_POST['id']);

        echo json_encode(
            count($updateCashbox) > 0
                ? ['success' => true, 'message' => 'Caja encontrada', 'data' => $updateCashbox]
                : ['success' => false, 'message' => 'No se encontró el registro']
        );
    }

    public function delete()
    {
        $stateCashbox = $this->model->selectCashbox($_POST['id']);
        $isOpen = $stateCashbox['abierta'];

        if (!$isOpen) {
            $deleteCashbox = $this->model->deleteCashbox($_POST['id']);
            echo json_encode(
                $deleteCashbox
                    ? ['success' => true, 'message' => $this->messages['delete_success']]
                    : ['success' => false, 'message' => $this->messages['delete_failed']]
            );
        } else {
            echo json_encode(['success' => false, 'message' => $this->messages['box_open_error']]);
        }
    }

    public function dataTable()
    {
        $response = $this->model->dataTable();
        $existPurchaseOpen = $this->model::exists($this->table, 'abierta', 1);
        $data = array();

        if (count($response) > 0) {
            foreach ($response as $row) {
                $isOpen = $row['abierta'];
                $estatus = $isOpen ? "<span class=\"badge bg-success font-14 px-3 fw-normal cursor-pointer\" onclick=\"moduleCashbox.modalOpenCashbox()\">Abierta</span>" : "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Cerrada</span>";

                $invisible = "";
                if ($existPurchaseOpen) $invisible = "invisible";

                $btn  = "<button type=\"button\" class=\"btn btn-inverse-success mx-1 $invisible\" onclick=\"moduleCashbox.openCashbox('{$row['id']}', '{$row['caja']}')\"><i class=\"bx bx-box m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"moduleCashbox.updateCashbox('{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"moduleCashbox.deleteCashbox('{$row['id']}', '{$row['caja']}')\"><i class=\"bx bx-trash m-0\"></i></button>";

                $data[] = [
                    "id"       => $row['id'],
                    "caja"     => $row['caja'],
                    "estatus"  => $estatus,
                    "btn"      => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}