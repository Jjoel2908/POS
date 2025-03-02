<?php
session_start();
require '../models/Cashbox.php';

$Cashbox = new Cashbox();

$data = [
   'caja' => $_POST['caja'] ?? NULL,
];

switch ($_GET['op']) {

   /**  S A V E  C A S H B O X  */
   case 'saveCashbox':

      if ( $Cashbox::validateData(['caja'], $_POST) ) {
         
         $validateForm = $Cashbox::exists('cajas', 'caja', $_POST['caja']);

         if (!$validateForm) {
            if (empty($_POST['id'])) {

               $saveCashbox = $Cashbox->insertCashbox($data);

               if ($saveCashbox) {              
                  echo json_encode(['success' => true, 'message' => 'Caja registrada correctamente']);
               } else {
                  echo json_encode(['success' => false, 'message' => 'Error al registrar caja']);
               }

            } else {

               $saveCashbox = $Cashbox->updateCashbox($data, $_POST['id']);
               
               if ($saveCashbox) {              
                  echo json_encode(['success' => true, 'message' => 'Caja actualizada correctamente']);
               } else {
                  echo json_encode(['success' => false, 'message' => 'No se pudo actualizar la caja']);
               }
            
            }
         } else echo json_encode(['success' => false, 'message' => "La caja {$_POST['caja']} ya existe"]);

      } else echo json_encode(['success' => false, 'message' => "Complete los campos requeridos"]);

      break;
   /**  U P D A T E  C A S H B O X  */
   case 'updateCashbox':
      $updateCashbox = $Cashbox->selectCashbox($_POST['id']);

      if (count($updateCashbox) > 0) {              
         echo json_encode(['success' => true, 'message' => 'Caja encontrado', 'data' => $updateCashbox]);
      } else {
         echo json_encode(['success' => false, 'message' => 'No se encontró el registro']);
      }
      break;
   /**  D E L E T E  C A S H B O X  */
   case 'deleteCashbox':

      $stateCashbox = $Cashbox->selectCashbox($_POST['id']);
      $isOpen = $stateCashbox['abierta'];

      if ( !$isOpen ) {

         $deleteCashbox = $Cashbox->deleteCashbox($_POST['id']);
         if ($deleteCashbox) {              
            echo json_encode(['success' => true, 'message' => 'Caja eliminada correctamente']);
         } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la caja']);
         }
      } else echo json_encode(['success' => false, 'message' => 'Es necesario cerrar la caja para eliminar']);
        
      break;
   /**  S H O W  T A B L E  */
   case 'dataTable':
      
      $response          = $Cashbox->dataTable();
      $existPurchaseOpen = $Cashbox::exists("cajas", "abierta", 1);
      $data              = array();

      if (count($response) > 0 ) {
         foreach ($response as $row) {

            $isOpen = $row['abierta'];
            $estatus = $isOpen ? "<span class=\"badge bg-success font-14 px-3 fw-normal cursor-pointer\" onclick=\"moduleCashbox.modalOpenCashbox()\">Abierta</span>" : "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Cerrada</span>";

            $invisible = "";
            if ($existPurchaseOpen) $invisible = "invisible";

            $btn  = "<button type=\"button\" class=\"btn btn-inverse-success mx-1 $invisible\" onclick=\"moduleCashbox.openCashbox('{$row['id']}', '{$row['caja']}')\"><i class=\"bx bx-box m-0\"></i></button>";
            $btn .= "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"moduleCashbox.updateCashbox('{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
            $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"moduleCashbox.deleteCashbox('{$row['id']}', '{$row['caja']}')\"><i class=\"bx bx-trash m-0\"></i></button>";
            
            $data[] = [
               "id"       => $row['id'],
               "caja"     => $row['caja'],
               "estatus"  => $estatus,
               "btn"      => $btn
            ];
         }
      }
      echo json_encode($data);
      break;
}
