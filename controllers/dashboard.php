<?php 
session_start();
require '../models/Dashboard.php';

$Dashboard = new Dashboard();

switch ($_GET['op']) {

   /**  S T O C K   M I N I M U M  */
   case 'productStockMin':
      $dataProduct  = [];
      $dataQuantity = [];
      $products     = $Dashboard->getProductStockMinimo();

      if ( count($products) > 0 ) {

         foreach ($products as $row) {
            $dataProduct[] = [
               $row['nombre']
            ];

            $dataQuantity[] = [
               $row['stock']
            ];
         }
   
         echo json_encode(["success" => true, "message" => "", "product" => $dataProduct, "quantity" => $dataQuantity]);

      } else echo json_encode(["success" => false, "message" => "No hay productos con stock mínimo"]);

      break;

   /**  B E S T   S E L L I N G  */
   case 'productBestSelling':
      $dataProduct  = [];
      $dataQuantity = [];
      $products     = $Dashboard->getProductBestSelling();

      if (count($products) > 0) {

         foreach ($products as $row) {
            $dataProduct[] = [
               $row['nombre_producto']
            ];

            $dataQuantity[] = [
               $row['total_selling']
            ];
         }

         echo json_encode(["success" => true, "message" => "", "product" => $dataProduct, "quantity" => $dataQuantity]);
      } else echo json_encode(["success" => false, "message" => "No se encontraron productos mejor vendidos"]);

      break;
}

?>