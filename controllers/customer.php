<?php
session_start();
require '../models/Customer.php';

$Customer = new Customer();

$newEmail = !empty($_POST['correo']) ? filter_var($_POST['correo'], FILTER_VALIDATE_EMAIL) : "";

$data = [
   "nombre"   => $_POST['nombre']   ?? "",
   "correo"   => $newEmail,
   "telefono" => $_POST['telefono'] ?? NULL,
];

switch ($_GET['op']) {

   /**  S A V E  */
   case 'saveCustomer':
      if ($Customer::validateData(['nombre'], $_POST)) {
         /**  N E W  */
         if (empty($_POST['id'])) {

            $saveCustomer = $Customer->insertCustomer($data);

            if ($saveCustomer) {
               echo json_encode(['success' => true, 'message' => 'Cliente registrado correctamente']);
            } else {
               echo json_encode(['success' => false, 'message' => 'Error al registrar el cliente']);
            }

         } else {
            /**  U P D A T E  */
            $updateCustomer = $Customer->updateCustomer($_POST['id'], $data);

            if ($updateCustomer) {
               echo json_encode(['success' => true, 'message' => 'Cliente actualizado correctamente']);
            } else {
               echo json_encode(['success' => false, 'message' => 'Error al actualizar cliente']);
            }
         }
      } else echo json_encode(['success' => false, 'message' => 'Complete los campos requeridos']);
      break;

   /**  U P D A T E  U S E R  */
   case 'updateCustomer':
      $updateCustomer = $Customer->selectCustomer($_POST['id']);

      if ( count($updateCustomer) > 0 ) {
         echo json_encode(['success' => true, 'message' => 'Cliente Encontrado', 'data' => $updateCustomer]);
      } else {
         echo json_encode(['success' => false, 'message' => 'No se encontró el registro del cliente']);
      }
      break;

   /**  D E L E T E  U S E R  */
   case 'deleteCustomer':
      $deleteCustomer = $Customer->deleteCustomer($_POST['id']);

      if ($deleteCustomer) {
         echo json_encode(['success' => true, 'message' => 'Cliente eliminado correctamente']);
      } else {
         echo json_encode(['success' => false, 'message' => 'Error al eliminar el cliente']);
      }

      break;

   /**  S H O W  T A B L E  */
   case 'dataTable':

      $response = $Customer->dataTable();
      $data = array();

      if (count($response) > 0) {
         foreach ($response as $row) {

            $estado = $row['estado'] ? "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Activo</span>" : "";

            if ($row['id'] != 1) {
               $btn = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"moduleCustomer.updateCustomer('{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
               $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"moduleCustomer.deleteCustomer('{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";
            } else $btn = "";


            $data[] = [
               "id"       => $row['id'],
               "nombre"   => $row['nombre'],
               "telefono" => $row['telefono'],
               "correo"   => $row['correo'],
               "estado"   => $estado,
               "btn"      => $btn
            ];
         }
      }
      echo json_encode($data);
      break;
   default:
      echo json_encode(['success' => false, 'message' => 'Operación no válida']);
      break;
}
