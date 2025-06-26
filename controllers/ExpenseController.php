<?php
require '../models/Expense.php';

class ExpenseController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "gastos";
    private $model;
    private $id;
    private $idSucursal;

    private $messages = [
        "save_success"   => "Gasto registrado correctamente.",
        "save_failed"    => "Error al registrar el gasto.",
        "update_success" => "Gasto actualizado correctamente.",
        "update_failed"  => "Error al actualizar el gasto.",
        "delete_success" => "Gasto eliminado correctamente.",
        "delete_failed"  => "Error al eliminar el gasto.",
        "required"       => "Debe completar la información obligatoria."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new Expense();
        $this->id    = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    public function save()
    {
        /** Valida campos requeridos */
        $validateData = ['monto', 'id_tipo_gasto'];
        if (!$this->model::validateData($validateData, $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Información a registrar o actualizar */
        $data = [
            'monto'         => $this->model::sanitizeInput('monto', 'float'),
            'id_tipo_gasto' => $this->model::sanitizeInput('id_tipo_gasto', 'int'),
            'descripcion'   => $this->model::sanitizeInput('descripcion', 'text'),
        ];

        if (!$this->id) {
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
        $recoverRegister = $this->model->select($this->table, $this->id);

        echo json_encode(
            count($recoverRegister) > 0     
                ? ['success' => true, 'message' => '', 'data' => $recoverRegister] 
                : ['success' => false, 'message' => 'No se encontró el registro']
        );
    }

    public function delete()
    {
        $delete = $this->model::delete($this->table, $this->id);
        
        echo json_encode(
                $delete 
                    ? ['success' => true, 'message' => $this->messages['delete_success']] 
                    : ['success' => false, 'message' => $this->messages['delete_failed']]
        );
    }

    public function dataTable()
    {
        $response = $this->model->dataTable();
        $data = array();

        if (count($response) > 0) {
            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date             = date("d/m/Y", strtotime($day));

                $btn  = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateRegister('Gasto', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Gasto', '{$row['id']}', '')\"><i class=\"bx bx-trash m-0\"></i></button>";

                $data[] = [ 
                    "Fecha de Creación" => $date,
                    "Hora"     => $time,
                    "Concepto" => $row['tipo_gasto'],
                    "Monto"    => "$" . number_format($row['monto'], 2),
                    "Descripción" => $row['descripcion'],
                    "Acciones" => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}