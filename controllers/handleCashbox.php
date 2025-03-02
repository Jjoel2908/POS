<?php
session_start();
require '../models/Cashbox.php';

date_default_timezone_set('America/Mexico_City');

$Cashbox = new Cashbox();

$openData = [
   'id_caja'       => $_POST['id']             ?? NULL,
   'id_usuario'    => $_SESSION['id']          ?? NULL,
   'fecha_inicio'  => date('Y-m-d H:i:s'),
   'monto_inicial' => $_POST['monto_inicial']  ?? 0.00
];

$closeData = [
   'fecha_fin'  => date('Y-m-d H:i:s'),
];

switch ($_GET['op']) {
   /**  O P E N  C A S H B O X  */
   case 'openCashbox':
      if ( $Cashbox::validateData( ['monto_inicial'], $_POST ) ) {

         $openCashbox = $Cashbox->openCashbox($openData);
         if ($openCashbox) $isOpen = $Cashbox::update('cajas', $_POST['id'], ['abierta' => 1]);
         if ($isOpen) $setIdCashbox = $Cashbox::update('usuarios', $openData['id_usuario'], ['id_caja' => $openData['id_caja']]);

         if ($setIdCashbox) {
            echo json_encode(['success' => true, 'message' => "Caja {$_POST['nombre']} abierta"]);
         } else {
            echo json_encode(['success' => false, 'message' => 'Error al abrir caja']);
         }
      } else echo json_encode(['success' => false, 'message' => "Complete los campos requeridos"]);
      break;
   /**  S H O W  O P E N  C A S H B O X  */
   case 'dataTableCashbox':

      $response = $Cashbox->dataTableCashboxes();
      $data = array();

      if (count($response) > 0) {
         foreach ($response as $row) {

            $btn  = "<button type=\"button\" class=\"btn btn-inverse-secondary text-dark mx-1\" onclick=\"moduleCashbox.closeCashbox( '{$row['id']}','{$row['id_caja']}','{$row['nombre_caja']}', '{$row['monto_fin']}')\"><i class=\"fa-solid fa-folder-closed me-1\"></i> Cerrar</button>";

            list($day, $hour) = explode(" ", $row['fecha_inicio']);
            $date  = date("d/m/Y", strtotime($day));
            $time  = date("h:i A", strtotime($hour));

            $sales = ($row['total_ventas'] == 1) ? ' Venta' : ' Ventas';
            
            $data[] = [
               "caja"   => $row['nombre_caja'],
               "fecha"  => $date,
               "hora"   => $time,
               "monto"  => $row['monto_inicial'],
               "ventas" => $row['total_ventas'] . $sales,
               "btn"    => $btn
            ];
         }
      }
      echo json_encode($data);
      break;

   /**  O P E N  C A S H B O X  */
   case 'closeCashbox':
      if ( $Cashbox::validateData( ['id_caja'], $_POST ) ) {

         $closeCashbox = $Cashbox->closeCashbox($closeData, $_POST['id']);
         if ($closeCashbox) $isOpen = $Cashbox::update('cajas', $_POST['id_caja'], ['abierta' => 0]);
         if ($isOpen) $setIdCashbox = $Cashbox::update('usuarios', $_SESSION['id'], ['id_caja' => 0]);

         if ($setIdCashbox) {
            echo json_encode(['success' => true, 'message' => "Caja {$_POST['nombre']} cerrada"]);
         } else {
            echo json_encode(['success' => false, 'message' => 'Error al cerrar caja']);
         }
      } else echo json_encode(['success' => false, 'message' => "Completa los campos requeridos"]);
      break;

   case 'infoArqueo':
      $subtotal = $Cashbox->creditSales($_POST['id']);
      $total    = (float) $subtotal + (float) $_POST['monto'];
      echo number_format($total, 2);
      break;
}
