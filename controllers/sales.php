<?php
session_start();
require '../models/Sales.php';

$Sales = new Sales();

switch ($_GET['op']) {
   /**  I N F O R M A T I O N   P R O D U C T  */
   case 'infoProduct':

      $infoProduct = $Sales::select('productos', $_POST['id']);

      if (count($infoProduct) > 0) {              
         echo json_encode(['success' => true, 'message' => 'Producto Encontrado', 'data' => $infoProduct]);
      } else {
         echo json_encode(['success' => false, 'message' => 'No se encontró el registro del producto']);
      }
      break;

   /**  A D D   S A L E S  */
   case 'addSaleDetails':
      if ($Sales::validateData(['id', 'cantidad'], $_POST)) {
  
          $product       = $Sales->select('productos', $_POST['id']);
          $pricePurchase = $product['precio_compra'];
          $priceProduct  = $product['precio_venta'];
          $stockProduct  = $product['stock'];

          if($stockProduct >= $_POST['cantidad']) {

            $dataSale = [
               'id_producto'   => $_POST['id'],
               'id_usuario'    => $_SESSION['id'],
               'precio_compra' => $pricePurchase,
               'precio'        => $priceProduct,
               'cantidad'      => $_POST['cantidad'],
            ];
      
            $existProduct = $Sales->existProductDetail($_POST['id'], $_SESSION['id']);
      
            if (empty($existProduct)) {

                  // Si el producto no existe en la venta, lo insertamos
                  $addSale = $Sales->insertSaleDetail($dataSale);
      
                  if ($addSale) {
                     echo json_encode(['success' => true, 'message' => "Producto añadido correctamente"]);
                  } else {
                     echo json_encode(['success' => false, 'message' => "Error al añadir el producto"]);
                  }
            } else {    

                  // Si el producto ya existe en la venta, actualizamos la cantidad
                  $idSale        = $existProduct[0]['id'];
                  $quantity      = $existProduct[0]['cantidad'] + $_POST['cantidad'];
                  $validateStock = $stockProduct - $existProduct[0]['cantidad'];

                  if ($validateStock >= $_POST['cantidad']) {
                       
                     $updateSale = $Sales->updateSaleDetail($idSale, ["cantidad" => $quantity]);
         
                     if ($updateSale) {
                        echo json_encode(['success' => true, 'message' => "Cantidad actualizada correctamente"]);
                     } else {
                        echo json_encode(['success' => false, 'message' => "Error al actualizar la cantidad del producto"]);
                     }
                  } else echo json_encode(['success' => false, 'message' => "Producto Agotado"]);
                
            }
          } else echo json_encode(['success' => false, 'message' => "La cantidad no debe ser mayor al stock"]);
  
      } else echo json_encode(['success' => false, 'message' => "Datos de entrada no válidos"]);
      break;

   /**  S A V E   S A L E S  */
   case 'saveSale':
      if ($Sales::validateData(['id'], $_SESSION)) {

         if ( !$Sales::validateData(['pago'], $_POST) ) {
            echo json_encode(['success'  => false, 'message' => 'Es necesario elegir el método de pago']);
            break;
         }

         if ( !$Sales::validateData(['anticipo', 'cliente'], $_POST) && $_POST['pago'] == 'credito' ) {
            echo json_encode(['success'  => false, 'message' => 'Es necesario elegir el cliente y agregar el monto de anticipo']);
            break;
         }

         $productDetails = $Sales->getProductDetails($_SESSION['id']);

         if (!empty($productDetails)) {

            $totalSale = 0;

            foreach($productDetails as $detail) {
               $quantity = $detail['cantidad'];
               $price    = $detail['precio'];

               $totalSale += $quantity * $price;
            }

            $user   = $Sales->select('usuarios', $_SESSION['id']);
            $idCaja = $user['id_caja'];

            if ($idCaja > 0) {

               $infoCashbox = $Sales->getOpenCashbox($idCaja);

               if (count($infoCashbox) > 0 && $_POST['pago'] != 'credito') {
                  $totalCashbox       = $infoCashbox[0]['monto_fin'];
                  $updateTotalCashbox = $totalCashbox + $totalSale;

                  $salesNumber       = $infoCashbox[0]['total_ventas'];
                  $updateSalesNumber = $salesNumber + 1;

                  $Sales->updateTotalCashbox($updateTotalCashbox, $updateSalesNumber, $idCaja);
               }

               if (count($infoCashbox) > 0) {

                  $customer = (!empty($_POST['cliente'])) ? $_POST['cliente'] : 1;

                  /** Si el pago es a crédito el estado será igual a '2', en caso contrario '1' */
                  $payment = ( $_POST['pago'] == 'credito' ) ? 2 : 1;

                  $dataSale = [
                     "id_usuario" => $_SESSION['id'],
                     "id_cliente" => $customer,
                     "id_caja"    => $idCaja,
                     "total"      => $totalSale,
                     "estado"     => $payment
                  ];
      
                  /** Registra la venta y obtiene el id correspondiente al registro */
                  $saveSale = $Sales->saveSale($dataSale);
      
                  if ($saveSale > 0) {
      
                     $id_venta   = $saveSale;
                     $id_usuario = $_SESSION['id'];
      
                     $updateSaleDetails = $Sales->updateIdSaleDetails($id_venta, $id_usuario, $payment);
                     
                     $detailProducts    = $Sales->idSaleDetails($id_venta);
      
                     if (!empty($detailProducts)) {
      
                        foreach($detailProducts as $decreaseProduct) {
                           $id_producto = $decreaseProduct['id_producto'];
                           $cantidad    = $decreaseProduct['cantidad'];
      
                           $dismStock = $Sales->decreaseStock($id_producto, $cantidad);
                        }

                        if ( $_POST['pago'] == 'credito' ) {
                           $dataCreditSale = [
                              "id_venta"   => $id_venta,
                              "id_arqueo"  => $infoCashbox[0]['id'],
                              "id_usuario" => $_SESSION['id'],
                              "id_cliente" => $customer,
                              "total"      => $totalSale,
                              "pagado"     => $_POST['anticipo'],
                              "estado"     => 0
                           ];

                           $creditSale = $Sales::insert("credito", $dataCreditSale);

                           if ($creditSale) {
                              echo json_encode(['success'  => true, 'message' => 'La venta se realizó correctamente']);
                              break;
                           } else {
                              echo json_encode(['success'  => false, 'message' => 'Error al actualizar detalles de venta']);
                              break;
                           }
                        }

                        if ($dismStock) {
                           echo json_encode(['success'  => true, 'message' => 'La venta se realizó correctamente']);
                        } else {
                           echo json_encode(['success'  => false, 'message' => 'Error al actualizar detalles de venta']);
                        }
                     }
      
                  } else echo json_encode(['success'  => false, 'message' => 'Error al generar la venta']);

               } else echo json_encode(['success'  => false, 'message' => 'No se encuentra caja de registro']);

            } else echo json_encode(['success'  => false, 'message' => 'No hay caja de registro abierta']);

         } else echo json_encode(['success'  => false, 'message' => 'No se puede generar la venta']);

      } else echo json_encode(['success'  => false, 'message' => 'Intente más tarde']);
      break;

   /**  S H O W   S A L E S   D E T A I L S  */
   case 'dataTableSaleDetails':

      $response = $Sales->dataTableSaleDetails($_SESSION['id']);
      $HTML     = "";
      $total    = 0;

      if (count($response) > 0) {
         foreach ($response as $row) {
            $producto = htmlspecialchars($row['nombre_producto']);
            $cantidad = (int) $row['cantidad'];
            $precio = (float) $row['precio'];

            $btn = "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"moduleSales.deleteSaleDetail('{$row['id']}', '{$producto}')\"><i class=\"bx bx-trash m-0\"></i></button>";

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
   /**  D E L E T E   S A L E S   D E T A I L S  */
   case 'deleteSaleDetail':

      $deleteSaleDetail = $Sales->deleteSaleDetail($_POST['id']);

      if ($deleteSaleDetail) {
         echo json_encode(['success'  => true, 'message'  => "Detalle de venta eliminado correctamente"]);
      } else {
         echo json_encode(['success'  => false, 'message' => 'Error al remover producto de la venta']);
      }
      break;
   
   /**  C A N C E L   S A L E S  */
   case 'cancelSale':

      $id_usuario = $_SESSION['id'];
      $cancelSale = $Sales->cancelSale($id_usuario);

      if ($cancelSale) {
         echo json_encode(['success'  => true, 'message'  => "Venta eliminada correctamente"]);
      } else {
         echo json_encode(['success'  => false, 'message' => 'Error al cancelar la venta']);
      }
      break;

   /**  S H O W  T A B L E  */
   case 'dataTable':
      
      $response = $Sales->dataTable();
      $data = array();

      if (count($response) > 0 ) {
         foreach ($response as $row) {

            $totalSale = (int) $row['total'];
            if ( $totalSale == 0 ) continue;

            $idSale = $row['id'];
            $btn = "<a href=\"../reports/invoiceSale.php?id=$idSale\" target=\"_blank\" class=\"btn btn-success font-18 mx-1\"><i class=\"fa-solid fa-file-invoice\"></i></a>";

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
   
   case 'creditDataTable':
      
      $response = $Sales->creditDataTable();
      $data = array();

      if (count($response) > 0 ) {
         foreach ($response as $row) {

            $pending = (float) $row['total'] - (float) $row['pagado'];

            if ($row['estado'] == 0) {
            $btn = " <button type=\"button\" class=\"btn btn-inverse-primary font-18 mx-1\" onclick=\"moduleCreditSales.viewDetails('{$row['id_venta']}', '{$row['total']}', '{$row['estado']}')\">
                        <i class=\"fa-solid fa-folder-open\"></i>
                     </button>";
               $btn .= " <button type=\"button\" class=\"btn btn-inverse-success font-18 mx-1\" onclick=\"moduleCreditSales.addPayment('{$row['id']}', '$pending', '{$row['id_venta']}')\">
                        <i class=\"fa-solid fa-money-bill-1\"></i>
                     </button>";
            } else $btn = "";

            list($day, $hour) = explode(" ", $row['fecha']);
            $date  = date("d/m/Y", strtotime($day));

            list($Ultday, $Ulthour) = explode(" ", $row['fecha_ult']);
            $ultPayment  = date("d/m/Y", strtotime($Ultday));

            $estatus = ($row['estado'] == 1) 
               ? "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Pagado</span>" 
               : "<span class=\"badge bg-secondary font-14 px-3 fw-normal\">Pendiente</span>";

            $saldoPendiente = ($pending == 0) ? "<span class='text-dark'>N/A</span>" : "$" . number_format($pending, 2);

            $data[] = [
               "fecha"       => $date,
               "cliente"     => $row['cliente'],
               "total"       => "$" . $row['total'],
               "pagado"      => $saldoPendiente,
               "ultimo_pago" => $ultPayment,
               "estatus"     => $estatus,
               "btn"         => $btn
            ];
         }
      }
      echo json_encode($data);
      break;

   /**  V I E W  D E T A I L S  P R O D U C T S  */
   case 'viewDetailsSale':  
      $response = $Sales->getSalesDetails($_GET['id']);
      $data = array();

      if (count($response) > 0 ) {
         foreach ($response as $row) {

            $product = htmlspecialchars($row['nombre_producto']);
            $quantity = (int) $row['cantidad'];
            $price   = (float) $row['precio'];
            $subTotal = $price * $quantity;

            $textQuantity = ($quantity == 1) ? " pza" : " pzas";

            $data[] = [
               "nombre"   => $product,
               "cantidad" => $quantity . $textQuantity,
               "precio"   => "$" . number_format($price, 2),
               "subtotal" => "$" . number_format($subTotal, 2),
            ];
         }
         echo json_encode($data);
      } else echo json_encode(["success" => false, "message" => "No se encontraron resultados"]);
      break;

   case 'addPayment':
      if ( !$Sales::validateData(['id', 'abono'], $_POST) ) {
         echo json_encode(['success'  => false, 'message' => 'No se encontró la referencia de la venta']);
         break;
      }

      if ( $_POST['abono'] > $_POST['pendiente'] + 0.1 ) {
         echo json_encode(['success'  => false, 'message' => 'No se puede abonar una cantidad superior al saldo pendiente por pagar']);
         break;
      }    

      $estado = 0;

      if ($_POST['abono'] == $_POST['pendiente'] ) $estado = 1;

      $addPayment = $Sales->addCreditPayment($_POST['id'], $_POST['abono'], $estado);

      if ($addPayment && $estado == 1) {
         $updateDetails = $Sales->updateStateSaleDetails($_POST['idSale']);

         if ($updateDetails) {
            $updateStateSale = $Sales->updateStateSale($_POST['idSale']);

            if ($updateStateSale) {
               
               $dataCredit = $Sales->select('credito', $_POST['id']);
               $addMonto = $dataCredit['total'];
               $idArqueo = $dataCredit['id_arqueo'];

               $updateStateCashbox = $Sales->updateStateCashbox($addMonto, $idArqueo);

               if ($updateStateCashbox) {
                  echo json_encode(['success'  => true, 'message' => 'El pago se realizó correctamente']);
                  break;
               } else {
                  echo json_encode(['success'  => false, 'message' => 'Error al actualizar el arqueo de la caja']);
                  break;
               }

            } else {
               echo json_encode(['success'  => false, 'message' => 'Error al actualizar la referencia de la venta']);
               break;
            }
         } else {
            echo json_encode(['success'  => false, 'message' => 'Error al actualizar los detalles de la venta']);
            break;
         }

      }

      if ($addPayment) {
         echo json_encode(['success'  => true, 'message' => 'El pago se realizó correctamente']);
      } else {
         echo json_encode(['success'  => false, 'message' => 'Error al actualizar el pago de la venta']);
      }
      break;

   /**  S E L E C T   P R O D U C T S  */
   case 'selectProducts':

      $text = $_GET['q'];

      $products = $Sales::queryMySQL("SELECT * FROM productos WHERE codigo LIKE '%$text%' OR nombre LIKE '%$text%'");
      
      $formatted_products = [];

      if (!empty($products)) {
         foreach ($products as $product) {

            if ($product['stock'] == 0) continue;

            $formatted_products[] = [
               'id'   => $product['id'],
               'text' => $product['codigo'] . ' | ' . $product['nombre']
            ];
         }
      }

      echo json_encode(['results' => $formatted_products]);
      exit;
      break;

   /**  S E L E C T   C U S T O M ER  */
   case 'selectCustomer':

      $customers = $Sales::selectAll('clientes');

      if (!empty($customers)) {
         foreach ($customers as $customer) {
            echo '<option value="' . $customer['id'] . '">' . $customer['nombre'] . '</option>';
         }
      } else echo '<option value=""></option>';
      break;
   
   /**  E X I S T   O P E N   C A S H B O X  */
   case 'existOpenCashbox':
      $user   = $Sales->select('usuarios', $_SESSION['id']);
      $idCaja = $user['id_caja'];

      if ($idCaja > 0) {
         echo 1;
      } else echo 0;
      break;
}
