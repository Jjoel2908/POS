<?php
session_start();
require '../models/ReportProcessor.php';

$dateRange = $_POST['date'] ?? '';
$Reports = new ReportProcessor($dateRange);

switch ($_GET['op']) {
  
   /* ===================================================================================  */
   /* -------------------------- R E P O R T   P U R C H A S E -------------------------- */
   /* ===================================================================================  */
   case 'getReportPurchase': 

      $response = $Reports->getReportPurchases();
      $data = array();

      if (count($response['table']) > 0) {

         $total     = $response['total'];
         $dateRange = $response['date'];

         foreach ($response['table'] as $row) {

            list($day, $hour) = explode(" ", $row['fecha']);
            $date             = date("d/m/Y", strtotime($day));

            $idPurchase  = $row['id'];
            $btn         = "<button type=\"button\" class=\"btn btn-warning text-white font-18 mx-1\" onclick=\"modulePurchase.viewDetails('{$row['id']}', '{$row['total']}')\"><i class=\"fa-solid fa-folder-open\"></i></button>";
            $btn        .= "<a href=\"../../reports/invoicePurchase.php?id=$idPurchase\" target=\"_blank\" class=\"btn btn-success font-18 mx-1\"><i class=\"fa-solid fa-file-invoice\"></i></a>";

            $proveedor = "";

            $data[] = [
               "id"      => $row['id'],
               "fecha"   => $date,
               "proveedor" => $proveedor,
               "usuario" => $row['nombre_usuario'],
               "total"   => "$" . $row['total'],
               "btn"     => $btn
            ];
         }

         echo json_encode(["success" => true, "total" => $total, "table" => $data, "date" => $dateRange]);
      } else echo json_encode(["success" => false, "message" => "No se encontraron resultados"]);

      break;
   
   case 'viewDetailsPurchase':  
      $response = $Reports->getPurchaseDetails($_GET['id']);
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

   /* ===================================================================================  */
   /* ------------------------------ R E P O R T   S A L E S ------------------------------ */
   /* ===================================================================================  */
   case 'getReportSales': 

      $response = $Reports->getReportSales();
      $data = array();
      
      if (count($response) > 0) {

         $total     = $response['total'];
         $earnings  = $response['earnings'];
         $dateRange = $response['date'];

         foreach ($response['table'] as $row) {

            $totalSale = (int) $row['total'];

            if ( $totalSale == 0 ) continue;

            list($day, $hour) = explode(" ", $row['fecha']);
            $date             = date("d/m/Y", strtotime($day));

            $idSale  = $row['id'];
            $btn     = "<button type=\"button\" class=\"btn btn-warning text-white font-18 mx-1\" onclick=\"moduleSales.viewDetails('{$row['id']}', '{$row['total']}')\"><i class=\"fa-solid fa-folder-open\"></i></button>";
            $btn    .= "<a href=\"../../reports/invoiceSale.php?id=$idSale\" target=\"_blank\" class=\"btn btn-success font-18 mx-1\"><i class=\"fa-solid fa-file-invoice\"></i></a>";

            $cliente = htmlspecialchars($row['nombre_cliente']);

            $data[] = [
               "id"      => $row['id'],
               "fecha"   => $date,
               "caja"    => $row['nombre_caja'],
               "cliente" => $cliente,
               "usuario" => $row['nombre_usuario'],
               "total"   => "$" . $row['total'],
               "btn"     => $btn
            ];
         }

         echo json_encode(["success" => true, "total" => $total, "earnings" => $earnings, "table" => $data, "date" => $dateRange]);
      } else echo json_encode(["success" => false, "message" => "No se encontraron resultados"]);

      break;
   
   case 'viewDetailsSale':  
      $response = $Reports->getSalesDetails($_GET['id']);
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

   /* ===================================================================================  */
   /* ------------------------------ B E S T   S E L L I N G ------------------------------ */
   /* ===================================================================================  */
   case 'productBestSelling':
      $dataProduct  = [];
      $dataQuantity = [];
      $HTML         = "";
      $total        = 0;
      $products     = $Reports->getReportProductBestSelling();

      if (count($products) > 0) {

         foreach ($products as $row) {

            $producto  = htmlspecialchars($row['nombre_producto']);
            $categoria = htmlspecialchars($row['nombre_categoria']);
            $precio    = (float) $row['precio'];

            $subTotal  = $precio * $row['total_selling'];
            $total    += $subTotal;

            $dataProduct[] = [
               $producto
            ];

            $dataQuantity[] = [
               $row['total_selling']
            ];

            $HTML .= "<tr>";
            $HTML .= "<td class='text-start'>{$producto}</td>";
            $HTML .= "<td class='text-start'>{$categoria}</td>";
            $HTML .= "<td>{$row['codigo_producto']}</td>";
            $HTML .= "<td class='text-end'>{$row['total_selling']} ventas</td>";
            $HTML .= "<td class='text-end'>$" . number_format($total, 2) . "</td>";
            $HTML .= "</tr>";
            
         }

         echo json_encode(["success" => true, "message" => "", "product" => $dataProduct, "quantity" => $dataQuantity, "data" => $HTML]);
      } else echo json_encode(["success" => false, "message" => "No se encontraron resultados"]);

      break;
}
