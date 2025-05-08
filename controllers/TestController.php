<?php
require '../models/Test.php';
class TestController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "test";
    private $model;
    private $id;
    private $idSucursal;

    private $messages = [
        "save_success" => "Producto registrado correctamente.",
        "save_failed" => "Error al registrar el producto.",
        "update_success" => "Producto actualizado correctamente.",
        "update_failed" => "Error al actualizar el producto.",
        "delete_success" => "Producto eliminado correctamente.",
        "delete_failed" => "Error al eliminar el producto.",
        "required" => "Debe completar la información obligatoria."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new Test();
        $this->id = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    public function save()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['nombre', 'precio_venta'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Nombre */
        $name = $this->model::sanitizeInput('nombre', 'text');

        /** Información a registrar o actualizar */
        $data = [
            'nombre'        => $name,
            'precio_venta'  => $this->model::sanitizeInput('precio_venta', 'float')
        ];

        /** Valida que no exista un registro similar al entrante */
        if ($this->model::existsByFieldAndSucursal($this->table, 'nombre', $name, $this->idSucursal)) {
            echo json_encode(['success' => false, 'message' => "El producto " . $name .  " ya existe"]);
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
                : ['success' => false, 'message' => 'No se encontró el registro.']
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
        $response = $this->model->selectAll($this->table);
        $data = array();

        if (count($response) > 0) {

            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date  = date("d/m/Y", strtotime($day));

                $estado = $row['estado']
                    ? "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Activo</span>"
                    : "";

                $btn = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateRegister('Test', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Test', '{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";
                $data[] = [
                    "Producto"               => $row['nombre'],
                    "Precio Venta"  => "$" . number_format($row['precio_venta'], 2),
                    "Fecha de Actualización" => $date,
                    "Estado"                 => $estado,
                    "Acciones"               => $btn
                ];
            }
        }
        echo json_encode($data);
    }

    public function droplist()
    {
        $listRegister = "";
        $list = $this->model->selectAll($this->table);
        foreach ($list as $item) {
            $listRegister .= '<option value="' . $item['id'] . '">' . $item['nombre'] . '</option>';
        }

        echo json_encode(['success' => true, 'data' => $listRegister]);
    }
}
