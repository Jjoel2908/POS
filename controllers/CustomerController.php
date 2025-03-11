<?php
require '../models/Customer.php';

class CustomerController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "clientes";
    private $model;

    private $messages = [
        "save_success" => "Cliente registrado correctamente.",
        "save_failed" => "Error al registrar el cliente.",
        "update_success" => "Cliente actualizado correctamente.",
        "update_failed" => "Error al actualizar el cliente.",
        "delete_success" => "Cliente eliminado correctamente.",
        "delete_failed" => "Error al eliminar el cliente.",
        "required" => "Debe completar la información obligatoria."
    ];

    public function __construct()
    {
        $this->model = new Customer();
    }

    public function save()
    {
        /** Valida el correo electrónico */
        $validateEmail = !empty($_POST['correo']) ? filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL) : NULL;

        /** Información a registrar o actualizar */
        $data = [
            'nombre'        => ucwords(strtolower(trim($_POST['nombre']))),
            'apellidos'     => ucwords(strtolower(trim($_POST['apellidos']))),
            'telefono'      => preg_replace('/\D/', '', $_POST['telefono']),
            'correo'        => $validateEmail,
        ];

        /** Valida campos requeridos */
        if (!$this->model::validateData(['nombre', 'apellidos', 'telefono'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Valida que no exista un registro similar al entrante */
        if($this->model::exists($this->table, 'telefono', $_POST['telefono'])) {
            echo json_encode(['success' => false, 'message' => "El número de teléfono ya existe"]);
            return;
        }

        if (empty($_POST['id'])) {
            /** Agregamos la fecha de creación */
            $data['fecha'] = date('Y-m-d H:i:s');

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
        $recoverRegister = $this->model->select($this->table, $_POST['id']);

        echo json_encode(
            count($recoverRegister) > 0     
                ? ['success' => true, 'message' => '', 'data' => $recoverRegister] 
                : ['success' => false, 'message' => 'No se encontró el registro']
        );
    }

    public function delete()
    {
        $delete = $this->model::delete($this->table, $_POST['id']);
        
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
                $customerName = $row['nombre'] . " " . $row['apellidos'];

                $btn  = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateRegister('Cliente', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Cliente', '{$row['id']}', '{$customerName}')\"><i class=\"bx bx-trash m-0\"></i></button>";

                $data[] = [
                    "NOMBRE"   => $customerName,
                    "TELÉFONO" => $row['telefono'],
                    "EMAIL"   => $row['correo'],
                    "FECHA DE ALTA" => $date,
                    "ACCIONES"      => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}