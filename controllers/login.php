<?php

session_start();

require_once '../config/init.php';
require_once '../models/Login.php';

$response = (object) ['success' => false];
$Login    = new Login($_POST['user'], $_POST['password']);
$validate = $Login->validateUser();

if ($validate) {

   $_SESSION['id']        = $validate['id'];
   $_SESSION['user']      = $validate['user'];
   $_SESSION['user_name'] = $validate['nombre'];
   $_SESSION['permisos']  = explode(",", $validate['permisos']);

   $response->success = true;
   $response->url     = '/views/dashboard.php';

} else $response->message = "Credenciales Incorrectas";

echo json_encode($response);
?>