<?php
session_start();
require '../models/ReturnProduct.php';

$Return = new ReturnProduct();

$data = [
   'id_producto' => $_POST['id_producto'] ?? NULL,
   'cantidad'    => $_POST['cantidad']    ?? NULL,
   'motivo'      => $_POST['motivo']      ?? NULL,
   'id_usuario'  => $_SESSION['id']
];

switch ($_GET['op']) {

      /**  S A V E  R E T U R N  */
   case 'saveReturn':

      /** Validar campos requeridos */
      if (!$Return::validateData(['id_venta', 'id_detail', 'id_producto', 'cantidad', 'motivo', 'precio'], $_POST)) {
         echo json_encode(['success' => false, 'message' => 'Complete los campos requeridos']);
         break;
      }

      $decreaseQuantity = $Return->decreaseDetailProduct($_POST['id_detail'], $_POST['cantidad']);
      if (!$decreaseQuantity) echo json_encode(['success' => false, 'message' => 'Error al modificar el detalle de venta']);
      else {

         $addStock = $Return->addStockProduct($_POST['id_producto'], $_POST['cantidad']);
         if (!$addStock) echo json_encode(['success' => false, 'message' => 'Error al agregar el producto al almacén']);
         else {

            /** Cliente */
            $sale               = $Return->select('ventas', $_POST['id_venta']);
            $id_cliente         = $sale['id_cliente'];
            $data['id_cliente'] = $id_cliente;

            /** Total Devolución */
            $total         = $_POST['cantidad'] * $_POST['precio'];
            $data['total'] = $total;

            $saveReturn = $Return->insertReturn($data);

            if ($saveReturn) {
               echo json_encode(['success' => true, 'message' => 'Devolución registrada correctamente']);
            } else {
               echo json_encode(['success' => false, 'message' => 'Error al registrar la devolución']);
            }
         }
      }
      break;

      /**  S E L E C T   S A L E S  */
   case 'selectSales':

      $sales = $Return::selectSales();

      if (!empty($sales)) {
         foreach ($sales as $sale) {

            $total = (int) $sale['total'];
            if ($total == 0) continue;

            list($day, $hour) = explode(" ", $sale['fecha']);
            $date  = date("d/m/Y", strtotime($day));
            $time  = date("h:i A", strtotime($hour));

            echo '<option value="' . $sale['id'] . '">' . $sale['nombre_cliente'] . ' - ' . $date . '</option>';
         }
      } else echo '<option value=""></option>';
      break;

   /**  S E L E C T   P R O D U C T S  */
   case 'selectProducts':

      $products = $Return::selectProducts($_POST['id']);
      if (!empty($products)) {
         foreach ($products as $product) {
            echo '<option value="' . $product['id'] . '">' . $product['codigo_producto'] . ' | ' . $product['nombre_producto'] . '</option>';
         }
      } else echo '<option value=""></option>';
      break;
   /**  S E L E C T   Q U A N T I T Y  */
   case 'selectQuantity':

      $detail = $Return::detailSale($_POST['id']);

      if (!empty($detail)) {
         echo json_encode(['quantity' =>  $detail[0]['cantidad'], 'price' => $detail[0]['precio'], 'id_producto' => $detail[0]['id_producto']]);
      }
      break;

   /**  S H O W  T A B L E  */
   case 'dataTable':

      $response = $Return->dataTable();
      $data = array();

      if (count($response) > 0) {
         foreach ($response as $row) {

            list($day, $hour) = explode(" ", $row['fecha']);
            $date  = date("d/m/Y", strtotime($day));
            $time  = date("h:i A", strtotime($hour));

            $data[] = [
               "fecha"    => $date,
               "usuario"  => $row['nombre_usuario'],
               "producto" => $row['nombre_producto'],
               "cantidad" => $row['cantidad'],
               "motivo"   => $row['motivo'],
               "total"    => "$" . $row['total']
            ];
         }
      }
      echo json_encode($data);
      break;
}
