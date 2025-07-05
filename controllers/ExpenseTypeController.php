<?php
require '../models/ExpenseType.php';
class ExpenseTypeController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "tipos_gasto";
    private $model;
    private $id;
    private $idSucursal;

    private $messages = [
        "save_success" => "Tipo de gasto registrado correctamente.",
        "save_failed" => "Error al registrar el tipo de gasto.",
        "update_success" => "Tipo de gasto actualizado correctamente.",
        "update_failed" => "Error al actualizar el tipo de gasto.",
        "delete_success" => "Tipo de gasto eliminado correctamente.",
        "delete_failed" => "Error al eliminar el tipo de gasto.",
        "required" => "Debe completar la información obligatoria."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new ExpenseType();
        $this->id = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    public function droplist() {
        $listRegister = "";
        $list = $this->model->selectAll($this->table);
        foreach ($list as $item) {
            $listRegister .= '<option value="' . $item['id'] . '">' . $item['nombre'] . '</option>';
        }

        echo json_encode(['success' => true, 'data' => $listRegister]);
    }
}
