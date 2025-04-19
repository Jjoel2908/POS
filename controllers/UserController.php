<?php
require '../models/User.php';

class UserController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "usuarios";
    private $model;
    private $id;
    private $idUser;
    private $idSucursal;

    private $messages = [
        "save_success"            => "Usuario registrado correctamente.",
        "save_failed"             => "Error al registrar el usuario.",
        "update_success"          => "Usuario actualizado correctamente.",
        "update_failed"           => "Error al actualizar el usuario.",
        "update_password_success" => "Contraseña actualizada correctamente.",
        "update_password_failed"  => "Error al actualizar la contraseña.",
        "permissions_success"     => "Permisos actualizados correctamente.",
        "permissions_failed"      => "Error al actualizar los permisos.",
        "delete_success"          => "Usuario eliminado correctamente.",
        "delete_failed"           => "Error al eliminar el usuario.",
        "required"                => "Debe completar la información obligatoria.",
        "required_password"       => "La contraseña es requerida.",
        "password_failed"         => "La contraseña y su confirmación son incorrectas."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new User();
        $this->id    = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idUser     = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
    }

    public function save()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['user', 'nombre', 'correo'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Información a registrar o actualizar */
        $data = [
            'id_sucursal' => $this->idSucursal,
            'correo'      => $this->model::sanitizeInput('correo', 'email'),
            'nombre'      => $this->model::sanitizeInput('nombre', 'name'),
        ];

        if (!$this->id) {

            /** Usuario */
            $user = $this->model::sanitizeInput('user', 'text');

            /** Valida que no exista un registro similar al entrante */
            if ($this->model::existsByFieldAndSucursal($this->table, 'user', $user, $this->idSucursal)) {
                echo json_encode(['success' => false, 'message' => "El user " . $user .  " ya existe"]);
                return;
            }

            /** Validamos que la contraseña exista */
            if (!$this->model::validateData(['password'], $_POST)) {
                echo json_encode(['success' => false, 'message' => $this->messages['required_password']]);
                return;
            }

            /** Encriptamos la contraseña del usuario */
            $password         = $this->model::sanitizeInput('password', 'password');
            $data['password'] = $this->model->hashPassword($password);

            /** Asignamos el campo de usuario */
            $data['user']     = $user;

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
        $response = $this->model->selectAll($this->table);
        $data = array();

        if (count($response) > 0) {
            foreach ($response as $row) {

                if ($row['id'] == $this->idUser) continue;

                list($day, $hour) = explode(" ", $row['fecha']);
                $date = date("d/m/Y", strtotime($day));

                $btn  = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateUser('Usuario', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-secondary mx-1\" onclick=\"updatePermissions('{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-list-ol m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-success mx-1\" onclick=\"updatePassword('{$row['id']}')\"><i class=\"bx bx-key m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Usuario', '{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";

                $data[] = [
                    "Usuario"            => $row['user'],
                    "Nombre"             => $row['nombre'],
                    "Correo Electrónico" => $row['correo'],
                    "Fecha de Alta"      => $date,
                    "Acciones"           => $btn
                ];
            }
        }
        echo json_encode($data);
    }

    public function updatePassword()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['idPassword', 'new_password', 'new_password_confirm'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        $idUser          = $this->model::sanitizeInput('idPassword', 'int');
        $newPassword     = $this->model::sanitizeInput('new_password', 'password');
        $confirmPassword = $this->model::sanitizeInput('new_password_confirm', 'password');

        if ($newPassword != $confirmPassword) {
            echo json_encode(['success' => false, 'message' => $this->messages['password_failed']]);
            return;
        }

        $password       = $this->model->hashPassword($newPassword);
        $updatePassword = $this->model::update($this->table, $idUser, ["password" => $password]);
        echo json_encode(
            $updatePassword
                ? ['success' => true, 'message' => $this->messages['update_password_success']]
                : ['success' => false, 'message' => $this->messages['update_password_failed']]
        );
    }

    public function loadPermissionDetails()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['id'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        $html = '';

        /** Recuperamos los módulos con los que cuenta el sistema */
        $permissions     = $this->model->selectAll("permisos");

        /** Recuperamos la información del usuario */
        $recoverRegister = $this->model->select($this->table, $this->id);

        /** Permisos del usuario */
        $userPermissions = explode(",", $recoverRegister['permisos']);

        foreach($permissions as $permission) {

            $checked = in_array($permission['id'], $userPermissions) ? 'checked' : '';
            
            $html .= '<li class="list-group-item d-flex bg-transparent align-items-center border-0">
                        <div class="form-check form-switch form-check-success">
                            <input  class="form-check-input me-2" type="checkbox" role="switch" name="permisos[]" id="'. $permission['id'] . '" value="'. $permission['id'] . '" '. $checked . '>
                            <label class="form-check-label" for="flexSwitchCheckSuccess">' . $permission['nombre'] . '</label>
                        </div>
                    </li>';
        }

        echo json_encode(['success' => true, 'data' => $html]);
    }

    public function updatePermissions()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['idPermissions', 'permisos'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Identificador del usuario y permisos */
        $userId         = $this->model::sanitizeInput('idPermissions', 'int');
        $permisos       = $_POST['permisos'];
        $newPermissions = (!empty($permisos) && is_array($permisos)) ? implode(',', $permisos) : $permisos;
   
        /** Actualizamos los permisos del usuario */
        $updatePermissions = $this->model->updateUserPermissions($userId, $newPermissions);
        echo json_encode(
            $updatePermissions
                ? ['success' => true, 'message' => $this->messages['permissions_success']]
                : ['success' => false, 'message' => $this->messages['permissions_failed']]
        );
    }
}
