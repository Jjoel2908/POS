<?php
session_start();
require '../models/Product.php';

$Product = new Product();

$data = [
   'codigo'         => $_POST['codigo']        ?? NULL,
   'nombre'         => $_POST['nombre']        ?? NULL,
   'precio_compra'  => $_POST['precio_compra'] ?? 0.00,
   'precio_venta'   => $_POST['precio_venta']  ?? 0.00,
   'stock_minimo'   => $_POST['stock_minimo']  ?? 0,
   'id_categoria'   => $_POST['id_categoria']  ?? 1,
   'imagen'         => $_POST['imagen']        ?? NULL,
];

switch ($_GET['op']) {
   /**  S A V E  P R O D U C T  */
   case 'saveProduct':

      if ($Product::validateData(['nombre', 'stock_minimo', 'codigo', 'id_categoria', 'precio_compra', 'precio_venta'], $_POST)) {

         if ( empty($_POST['id']) ) {
            $validateForm = $Product::exists('productos', 'codigo', $_POST['codigo']);

            if ($validateForm) {
               echo json_encode(['success' => false, 'message' => "El código {$_POST['codigo']} ya existe"]);
               break;
            }
         }

         /** I M A G E */
         $imagen = $_POST["imagenactual"] ?? NULL;
         $updateImage = false;

         if (isset($_FILES['imagen']['tmp_name']) && is_uploaded_file($_FILES['imagen']['tmp_name'])) {
            $ext = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
            $allowed_types = ["jpg", "jpeg", "png"];

            if (in_array($ext, $allowed_types) && exif_imagetype($_FILES["imagen"]["tmp_name"])) {
               $imagen = round(microtime(true)) . '.' . $ext;
               $ruta_destino = "../media/products/" . $imagen;
               if ( move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_destino) ) $updateImage = true;
            }
         }

         if ($updateImage && $_POST['imagenactual']) {
            $imageRoute = "../media/products/" . $_POST['imagenactual'];
            if (file_exists($imageRoute)) {
                unlink($imageRoute);
            }
         }

         $data['imagen'] = $imagen;

         if ( empty($_POST['id']) ) {
            
            $saveProduct = $Product->insertProduct($data);
            if ($saveProduct) {
               echo json_encode(['success' => true, 'message' => 'Producto registrado correctamente']);
            } else {
               echo json_encode(['success' => false, 'message' => 'Error al registrar producto']);
            }

         } else {

            $saveProduct = $Product->updateProduct($data, $_POST['id']);
            if ($saveProduct) {
               echo json_encode(['success' => true, 'message' => 'Producto actualizado correctamente']);
            } else {
               echo json_encode(['success' => false, 'message' => 'Error al actualizar producto']);
            }

         }

      } else echo json_encode(['success' => false, 'message' => "Complete los campos requeridos"]);
      break;

   /**  U P D A T E  P R O D U C T  */
   case 'updateProduct':
      $updateProduct = $Product->selectProduct($_POST['id']);

      if (count($updateProduct) > 0) {              
         echo json_encode(['success' => true, 'message' => 'Producto Encontrado', 'data' => $updateProduct]);
      } else {
         echo json_encode(['success' => false, 'message' => 'No se encontró el registro del producto']);
      }
      break;
   
   /**  D E L E T E  P R O D U C T  */
   case 'deleteProduct':

      $dataProduct = $Product->selectProduct($_POST['id']);
      $stock = $dataProduct['stock'];

      if ( $stock == 0 ) {

         $deleteProduct = $Product->deleteProduct($_POST['id']);
         if ($deleteProduct) {              
            echo json_encode(['success'  => true, 'message'  => 'Producto eliminado correctamente']);
         } else {
            echo json_encode(['success'  => false, 'message' => 'Error al eliminar producto']);
         }
      } else echo json_encode(['success' => false, 'message' => "Producto con $stock unidades disponibles"]);
      break;

   /**  S H O W  T A B L E  */
   case 'dataTable':
      
      $response = $Product->dataTable();
      $data = array();

      if (count($response) > 0 ) {
         foreach ($response as $row) {

            $img = (!empty($row['imagen'])) 
                                             ? "<img src='../media/products/" . $row['imagen'] . "' height='48px' width='48px'>"
                                             : "<img src='../media/products/default.png' height='48px' width='48px'>";

            $btn  = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"moduleProduct.updateProduct('{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
            $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"moduleProduct.deleteProduct('{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";
            
            $data[] = [
               "nombre"        => $row['nombre'],
               "codigo"        => $row['codigo'],
               "id_categoria"  => $row['nombre_categoria'],
               "precio_compra" => $row['precio_compra'],
               "precio_venta"  => $row['precio_venta'],
               "stock"         => $row['stock'],
               "imagen"        => $img,
               "btn"           => $btn
            ];
         }
      }
      echo json_encode($data);
      break;

   /**  S E L E C T   C A T E G O R Y  */
   case 'selectCategory':
      
      $categories = $Product->selectAll('categorias');

      if (!empty($categories)) {
         foreach ($categories as $category) {
            echo '<option value="' . $category['id'] . '">' . $category['categoria'] . '</option>';
         }
      } else {
         echo '<option value=""></option>';
      }

      break;
}
