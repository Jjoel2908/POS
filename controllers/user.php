<?php
session_start();
require '../models/User.php';

$User = new User();

$newNameUser = !empty($_POST['user']) ? strtolower($_POST['user']) : "";
$newEmail    = !empty($_POST['correo']) ? filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL) : NULL;

$data = [
   "user"     => $newNameUser,
   "nombre"   => $_POST['nombre']   ?? "",
   "correo"   => $newEmail,
   "telefono" => $_POST['telefono'] ?? NULL,
];

switch ($_GET['op']) {

   /**  S A V E  U S E R  */
   case 'saveUser':
      if ($User::validateData(['user', 'nombre'], $_POST)) {
         /**  N E W   U S E R  */
         if (empty($_POST['id'])) {

            if ($User::validateData(['password'], $_POST)) {

               $data['password'] = (!empty($_POST['password'])) ? $User->hashPassword($_POST['password']) : "";

               $saveUser = $User->insertUser($data);

               if ($saveUser) {
                  echo json_encode(['success' => true, 'message' => 'Usuario registrado correctamente']);
               } else {
                  echo json_encode(['success' => false, 'message' => 'Error al registrar el usuario']);
               }

            } else echo json_encode(['success' => false, 'message' => 'La contraseña es requerida']);
        
         } else {
            /**  U P D A T E   U S E R  */
            $updateUser = $User->updateUser($_POST['id'], $data);

            if ($updateUser) {
               echo json_encode(['success' => true, 'message' => 'Usuario actualizado correctamente']);
            } else {
               echo json_encode(['success' => false, 'message' => 'Error al actualizar usuario']);
            }
         }
      } else echo json_encode(['success' => false, 'message' => 'Complete los campos requeridos']);
      break;

   /**  U P D A T E  U S E R  */
   case 'updateUser':
      $updateUser = $User->selectUser($_POST['id']);

      if ( count($updateUser) > 0 ) {
         echo json_encode(['success' => true, 'message' => 'Usuario Encontrado', 'data' => $updateUser]);
      } else {
         echo json_encode(['success' => false, 'message' => 'No se encontró el registro del usuario']);
      }
      break;

   /**  S H O W  P E R M I S S I O N S  */
   case 'showPermissions':
      $html        = '';
      $permissions = $User::selectAll("permisos");
      $usuario     = $User->selectUser($_POST['id']);
      $userPermissions = explode(",", $usuario['permisos']);

      foreach( $permissions as $permission ) {

         $checked = in_array($permission['id'], $userPermissions) ? 'checked' : '';
         
         $html .= '<li class="list-group-item d-flex bg-transparent align-items-center border-0">
                     <div class="form-check form-switch form-check-success">
                        <input  class="form-check-input me-2" type="checkbox" role="switch" name="permisos[]" id="'. $permission['id'] . '" value="'. $permission['id'] . '" '. $checked . '>
                        <label class="form-check-label" for="flexSwitchCheckSuccess">' . $permission['nombre'] . '</label>
                     </div>
                  </li>';
      } 
      echo $html;
      break;

   /**  U P D A T E  P A S S W O R D  */
   case 'updatePassword':
      if ($User::validateData(['new_password', 'new_password_confirm'], $_POST)) {
         $id_user         = $_POST['id']; 
         $newPassword     = $_POST['new_password'];
         $confirmPassword = $_POST['new_password_confirm'];

         if ($newPassword === $confirmPassword) {
            
            $password       = $User->hashPassword($newPassword);
            $updatePassword = $User->updateUser($id_user, ["password" => $password]);

            if ( $updatePassword ) {
               echo json_encode(['success' => true, 'message' => 'La contraseña se actualizó correctamente']);
            } else {
               echo json_encode(['success' => false, 'message' => 'No se encontró el registro del usuario']);
            }
         } else echo json_encode(['success' => false, 'message' => 'La confirmación es incorrecta']);
    
      } else echo json_encode(['success' => false, 'message' => 'Complete los campos requeridos']);
      break;

   /**  U P D A T E  P E R M I S S I O N S  */
   case 'updatePermissions':
      if ($User::validateData(['id'], $_POST)) {

         $id_user      = $_POST['id'];
         $permisos     = $_POST['permisos'];
         $userPermisos = (!empty($permisos) && is_array($permisos)) ? implode(',', $permisos) : $permisos;

         $updatePermissions = $User::queryMySQL("UPDATE usuarios SET permisos = '{$userPermisos}' WHERE id = $id_user");

         if ($updatePermissions) {
            echo json_encode(['success' => true, 'message' => 'Los permisos se actualizaron correctamente']);
         } else {
            echo json_encode(['success' => false, 'message' => 'Ocurrió un error al actualizar los permisos']);
         }

      } else echo json_encode(['success' => false, 'message' => 'No se encontró el usuario en la base de datos']);
      break;

   /**  D E L E T E  U S E R  */
   case 'deleteUser':
      $deleteUser = $User->deleteUser($_POST['id']);

      if ($deleteUser) {
         echo json_encode(['success' => true, 'message' => 'Usuario eliminado correctamente']);
      } else {
         echo json_encode(['success' => false, 'message' => 'Error al eliminar el usuario']);
      }

      break;

   /**  S H O W  T A B L E  */
   case 'dataTable':

      $response = $User->dataTable();
      $data = array();

      if (count($response) > 0) {
         foreach ($response as $row) {

            $estado = $row['estado'] ? "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Activo</span>" : "";

            $btn  = "<li><a class=\"dropdown-item\" href=\"javascript:moduleUser.updatePassword('{$row['id']}')\"><i class=\"bx bx-key me-2\"></i>Contraseña</a></li>";
            $btn .= "<li><a class=\"dropdown-item\" href=\"javascript:moduleUser.userPermissions('{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-list-ol me-2\"></i>Permisos</a></li>";
            $btn .= "<li><a class=\"dropdown-item\" href=\"javascript:moduleUser.updateUser('{$row['id']}')\"><i class=\"bx bx-edit-alt me-2\"></i>Editar</a></li>";
            $btn .= "<li><a class=\"dropdown-item\" href=\"javascript:moduleUser.deleteUser('{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash me-2\"></i>Eliminar</a></li>";
            
            if ($row['id'] != 1) {
               $options = "   <div class=\"dropdown\">
                                 <button class=\"btn btn-inverse-primary mx-1 dropdown-toggle\" type=\"button\" data-bs-toggle=\"dropdown\" aria-expanded=\"false\"><i class=\"fa-solid fa-layer-group\"></i></button>
                                    <ul class=\"dropdown-menu\" style=\"\">"
                                    . $btn .	
                                    "</ul>
                              </div>";
            } else $options = "";

            $data[] = [
               "id"       => $row['id'],
               "nombre"   => $row['nombre'],
               "user"     => $row['user'],
               "correo"   => $row['correo'],
               "telefono" => $row['telefono'],
               "estado"   => $estado,
               "btn"      => $options
            ];
         }
      }
      echo json_encode($data);
      break;
   default:
      echo json_encode(['success' => false, 'message' => 'Operación no válida']);
      break;
}
