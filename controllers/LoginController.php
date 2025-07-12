<?php
require '../models/Login.php';
class LoginController
{
   private $model;
   
   public function __construct()
   {
      $this->model = new Login();
   }

   public function login()
   {
      $email    = $this->model::sanitizeInput('correo', 'email');
      $password = $this->model::sanitizeInput('password', 'password');

      $validateUser = $this->model->validateUser($email, $password);

      if (!$validateUser || $validateUser == null) {
         echo json_encode(["success" => false, "data" => $validateUser]);
         return;
      }

      if (count($validateUser) > 0) {

            /** Regenerar el session_id después de un login exitoso */
            session_regenerate_id(true);

            $_SESSION['id']        = $validateUser['id'];
            $_SESSION['user']      = $validateUser['user'];
            $_SESSION['user_name'] = $validateUser['nombre'];
            $_SESSION['permisos']  = explode(",", $validateUser['permisos']);
            $_SESSION['sucursal']  = $validateUser['id_sucursal'];

            echo json_encode(["success" => true, "data" => "/views/dashboard.php"]);
      }
   }
}
