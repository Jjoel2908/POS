<?php
require '../models/Customer.php';

class CustomerController
{
    private $table = "clientes";  // Nombre de la tabla de clientes
    private $model;
    private $messages = [
        "save_success" => "Cliente registrado correctamente",
        "save_failed" => "Error al registrar el cliente",
        "update_success" => "Cliente actualizado correctamente",
        "update_failed" => "No se pudo actualizar el cliente",
        "delete_success" => "Cliente eliminado correctamente",
        "delete_failed" => "Error al eliminar el cliente"
    ];

    public function __construct()
    {
        $this->model = new Customer();
    }

    // Función para guardar o actualizar cliente
    public function save()
    {
        $data = [
            'nombre'   => $_POST['nombre'] ?? NULL,
            'correo'   => $_POST['correo'] ?? NULL,
            'telefono' => $_POST['telefono'] ?? NULL,
        ];

        if ($this->model::validateData(['nombre', 'correo'], $_POST)) {
            $validateForm = $this->model::exists($this->table, 'correo', $_POST['correo']);

            if (!$validateForm) {
                if (empty($_POST['id'])) {
                    $saveCustomer = $this->model->insertCustomer($data);
                    echo json_encode(
                        $saveCustomer
                            ? ['success' => true, 'message' => $this->messages['save_success']]
                            : ['success' => false, 'message' => $this->messages['save_failed']]
                    );
                } else {
                    $saveCustomer = $this->model->updateCustomer($data, $_POST['id']);
                    echo json_encode(
                        $saveCustomer
                            ? ['success' => true, 'message' => $this->messages['update_success']]
                            : ['success' => false, 'message' => $this->messages['update_failed']]
                    );
                }
            } else {
                echo json_encode(['success' => false, 'message' => "El correo {$_POST['correo']} ya está registrado"]);
            }
        } else {
            echo json_encode(['success' => false, 'message' => "Complete los campos requeridos"]);
        }
    }

    // Función para actualizar cliente
    public function update()
    {
        $updateCustomer = $this->model->selectCustomer($_POST['id']);

        echo json_encode(
            count($updateCustomer) > 0
                ? ['success' => true, 'message' => 'Cliente encontrado', 'data' => $updateCustomer]
                : ['success' => false, 'message' => 'No se encontró el registro']
        );
    }

    // Función para eliminar cliente
    public function delete()
    {
        $deleteCustomer = $this->model->deleteCustomer($_POST['id']);

        echo json_encode(
            $deleteCustomer
                ? ['success' => true, 'message' => $this->messages['delete_success']]
                : ['success' => false, 'message' => $this->messages['delete_failed']]
        );
    }

    // Función para obtener los datos de clientes en formato de tabla
    public function dataTable()
    {
        $response = $this->model->dataTable();
        $data = array();

        if (count($response) > 0) {
            foreach ($response as $row) {
                $btn  = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"moduleCustomer.updateCustomer('{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"moduleCustomer.deleteCustomer('{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";

                $data[] = [
                    "id"       => $row['id'],
                    "nombre"   => $row['nombre'],
                    "telefono" => $row['telefono'],
                    "correo"   => $row['correo'],
                    "btn"      => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}