<?php
require '../models/Category.php';
class CategoryController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "categorias";
    private $model;

    private $messages = [
        "save_success" => "Categoría registrada correctamente",
        "save_failed" => "Error al registrar la categoría",
        "update_success" => "Categoría actualizada correctamente",
        "update_failed" => "Error al actualizar la categoría",
        "delete_success" => "Categoría eliminada correctamente.",
        "delete_failed" => "Error al eliminar la categoría, intente más tarde.",
    ];

    public function __construct()
    {
        $this->model = new Category();
    }

    public function save()
    {
        $data = [
            'categoria' => $_POST['categoria'] ?? NULL,
        ];

        /** Valida campos requeridos */
        if (!$this->model::validateData(['categoria'], $_POST)) {
            echo json_encode(['success' => false, 'message' => 'Debe completar la información obligatoria']);
            return;
        }

        /** Valida el nombre */
        if ($this->model::exists($this->table, 'categoria', $_POST['categoria'])) {
            echo json_encode(['success' => false, 'message' => "La categoría " . $_POST['categoria'] .  " ya existe"]);
            return;
        }

        if (empty($_POST['id'])) {
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
        $recoverRegister = $this->model::select($this->table, $_POST['id']);

        echo json_encode(
            count($recoverRegister) > 0     
                ? ['success' => true, 'message' => '', 'data' => $recoverRegister] 
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
        $response = $this->model::selectAll($this->table);
        $data = array();

        if (count($response) > 0) {
            foreach ($response as $row) {
                $estado = $row['estado'] ? "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Activa</span>" : "";
                $btn = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"moduleCategory.updateCategory('{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"moduleCategory.delete('{$row['id']}', '{$row['categoria']}')\"><i class=\"bx bx-trash m-0\"></i></button>";
                $data[] = [
                    "id" => $row['id'],
                    "categoria" => $row['categoria'],
                    "estado" => $estado,
                    "btn" => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}
