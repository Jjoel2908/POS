<?php
require '../models/Category.php';
class CategoryController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "categorias";
    private $model;

    private $messages = [
        "save_success" => "Categoría registrada correctamente.",
        "save_failed" => "Error al registrar la categoría.",
        "update_success" => "Categoría actualizada correctamente.",
        "update_failed" => "Error al actualizar la categoría.",
        "delete_success" => "Categoría eliminada correctamente.",
        "delete_failed" => "Error al eliminar la categoría.",
        "required" => "Debe completar la información obligatoria."
    ];

    public function __construct()
    {
        $this->model = new Category();
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
            echo json_encode(['success' => false, 'message' => "La categoría " . $_POST['nombre'] .  " ya existe"]);
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
                : ['success' => false, 'message' => 'No se encontró el registro de la categoría']
        );
    }

    public function delete()
    {
        $validateProducts = $this->model::exists('productos', 'id_categoria', $_POST['id']);

        if (!$validateProducts) {
            $delete = $this->model::delete($this->table, $_POST['id']);
            echo json_encode(
                $delete 
                    ? ['success' => true, 'message' => $this->messages['delete_success']] 
                    : ['success' => false, 'message' => $this->messages['delete_failed']]
            );
        } else
            echo json_encode(['success' => false, 'message' => 'La categoría cuenta con productos']);
    }

    public function dataTable()
    {
        $response = $this->model::selectAllBySucursal($this->table, $_SESSION['sucursal']);
        $data = array();

        if (count($response) > 0) {

            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date  = date("d/m/Y", strtotime($day));

                $estado = $row['estado'] 
                    ? "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Activa</span>" 
                    : "";
                    
                $btn = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateRegister('Categoría', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Categoría', '{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";
                $data[] = [
                    "CATEGORÍA" => $row['nombre'],
                    "FECHA DE CREACIÓN" => $date,
                    "ESTADO"    => $estado,
                    "ACCIONES"  => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}
