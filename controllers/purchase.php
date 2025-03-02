<?php
session_start();
require '../models/Purchase.php';

$Purchase = new Purchase();

switch ($_GET['op']) {
   /**  I N F O R M A T I O N   P R O D U C T  */
   case 'infoProduct':

      $infoProduct = $Purchase::select('productos', $_POST['id']);

      if (count($infoProduct) > 0) {              
         echo json_encode(['success' => true, 'message' => 'Producto Encontrado', 'data' => $infoProduct]);
      } else {
         echo json_encode(['success' => false, 'message' => 'No se encontró el registro del producto']);
      }
      break;

   /**  A D D   P U R C H A S E  */
   case 'addPurchaseDetails':
      if ($Purchase::validateData(['id', 'cantidad'], $_POST)) {
  
          $product = $Purchase->select('productos', $_POST['id']);
          $priceProduct = $product['precio_compra'];
  
          $dataPurchase = [
              'id_producto' => $_POST['id'],
              'id_usuario'  => $_SESSION['id'],
              'precio'      => $priceProduct,
              'cantidad'    => $_POST['cantidad'],
          ];
  
          $existProduct = $Purchase->existProductDetail($_POST['id'], $_SESSION['id']);
  
          if (empty($existProduct)) {
              // Si el producto no existe en la compra, lo insertamos
              $addPurchase = $Purchase->insertPurchaseDetail($dataPurchase);
  
              if ($addPurchase) {
                  echo json_encode(['success' => true, 'message' => "Producto añadido correctamente"]);
              } else {
                  echo json_encode(['success' => false, 'message' => "Error al añadir el producto"]);
              }
          } else {
              // Si el producto ya existe en la compra, actualizamos la cantidad
              $idPurchase = $existProduct[0]['id'];
              $quantity = $existProduct[0]['cantidad'] + $_POST['cantidad'];
  
              $updatePurchase = $Purchase->updatePurchaseDetail($idPurchase, ["cantidad" => $quantity]);
  
              if ($updatePurchase) {
                  echo json_encode(['success' => true, 'message' => "Cantidad actualizada correctamente"]);
              } else {
                  echo json_encode(['success' => false, 'message' => "Error al actualizar la cantidad del producto"]);
              }
          }
  
      } else {
          echo json_encode(['success' => false, 'message' => "Datos de entrada no válidos"]);
      }
      break;

   /**  S A V E   P U R C H A S E  */
   case 'savePurchase':
      if ($Purchase::validateData(['id'], $_SESSION)) {

         $productDetails = $Purchase->getProductDetails($_SESSION['id']);

         if (!empty($productDetails)) {

            $totalPurchase = 0;

            foreach($productDetails as $detail) {
               $quantity = $detail['cantidad'];
               $price = $detail['precio'];

               $totalPurchase += $quantity * $price;
            }
            
            $dataPurchase = [
               "id_usuario" => $_SESSION['id'],
               "total"      => $totalPurchase
            ];

            $savePurchase = $Purchase->savePurchase($dataPurchase);

            if ($savePurchase > 0) {

               $id_compra  = $savePurchase;
               $id_usuario = $_SESSION['id'];

               $updatePurchaseDetails = $Purchase->updateIdPurchaseDetails($id_compra, $id_usuario);

               $detailProducts = $Purchase->idPurchaseDetails($id_compra);

               if (!empty($detailProducts)) {

                  foreach($detailProducts as $addProduct) {
                     $id_producto = $addProduct['id_producto'];
                     $cantidad    = $addProduct['cantidad'];

                     $addStock = $Purchase->addStock($id_producto, $cantidad);
                  }

                  if ($addStock) {
                     echo json_encode(['success'  => true, 'message' => 'La compra se realizó correctamente']);
                  } else {
                     echo json_encode(['success'  => false, 'message' => 'Error al actualizar detalles de compra']);
                  }
               }

            } else echo json_encode(['success'  => false, 'message' => 'Error al generar la compra']);

         } else echo json_encode(['success'  => false, 'message' => 'No se puede generar la compra']);

      } else echo json_encode(['success'  => false, 'message' => 'Intente más tarde']);
      break;

   /**  S H O W   P U R C H A S E   D E T A I L S  */
   case 'dataTablePurchaseDetails':

      $response = $Purchase->dataTablePurchaseDetails($_SESSION['id']);
      $HTML     = "";
      $total    = 0;

      if (count($response) > 0) {
         foreach ($response as $row) {
            $producto = htmlspecialchars($row['nombre_producto']);
            $cantidad = (int) $row['cantidad'];
            $precio = (float) $row['precio'];
            
            $btn = "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"modulePurchase.deletePurchaseDetail('{$row['id']}', '{$producto}')\"><i class=\"bx bx-trash m-0\"></i></button>";

            $subTotal  = $precio * $cantidad;
            $total    += $subTotal;

            $HTML .= "<tr>";
            $HTML .= "<td class='text-start'>{$producto}</td>";
            $HTML .= "<td>{$cantidad}</td>";
            $HTML .= "<td class='text-end'>$" . number_format($precio, 2) . "</td>";
            $HTML .= "<td class='text-end'>$" . number_format($subTotal, 2) . "</td>";
            $HTML .= "<td>{$btn}</td>";
            $HTML .= "</tr>";
         }
      } else {
         $HTML .= "";
      }

      echo json_encode(['data' => $HTML, 'total' => number_format($total, 2)]);
      break;
   /**  D E L E T E   P U R C H A S E   D E T A I L S  */
   case 'deletePurchaseDetail':

      $deletePurchaseDetail = $Purchase->deletePurchaseDetail($_POST['id']);

      if ($deletePurchaseDetail) {
         echo json_encode(['success'  => true, 'message'  => "Detalle de compra eliminado correctamente"]);
      } else {
         echo json_encode(['success'  => false, 'message' => 'Error al remover producto de la compra']);
      }
      break;
   
   /**  C A N C E L   P U R C H A S E  */
   case 'cancelPurchase':

      $id_usuario     = $_SESSION['id'];
      $cancelPurchase = $Purchase->cancelPurchase($id_usuario);

      if ($cancelPurchase) {
         echo json_encode(['success'  => true, 'message'  => "Compra eliminada correctamente"]);
      } else {
         echo json_encode(['success'  => false, 'message' => 'Error al cancelar la compra']);
      }
      break;

   /**  S H O W  T A B L E  */
   case 'dataTable':
      
      $response = $Purchase->dataTable();
      $data = array();

      if (count($response) > 0 ) {
         foreach ($response as $row) {

            $idPurchase = $row['id'];
            $btn = "<a href=\"../reports/invoicePurchase.php?id=$idPurchase\" target=\"_blank\" class=\"btn btn-success font-18 mx-1\"><i class=\"fa-solid fa-file-invoice\"></i></a>";

            list($day, $hour) = explode(" ", $row['fecha']);
            $date  = date("d/m/Y", strtotime($day));
            $time  = date("h:i A", strtotime($hour));

            $data[] = [
               "id"      => $row['id'],
               "fecha"   => $date,
               "hora"    => $time,
               "usuario" => $row['nombre_usuario'],
               "btn"     => $btn
            ];
         }
      }
      echo json_encode($data);
      break;

   /**  S E L E C T   P R O D U C T S  */
   case 'selectProducts':

      $products = $Purchase::selectAll('productos');

      if ( !empty($products) ) {
         foreach ($products as $product) {
            echo '<option value="' . $product['id'] . '">' . $product['codigo'] . ' | ' . $product['nombre'] . '</option>';
         }
      } else echo '<option value=""></option>';
      break;
}
