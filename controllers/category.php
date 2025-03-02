<?php
session_start();
require '../models/Category.php';

$Category = new Category();

$data = [
   'categoria' => $_POST['categoria'] ?? NULL,
];

switch ($_GET['op']) {
   /**  S A V E  C A T E G O R Y  */
   case 'saveCategory':

      if ( $Category::validateData(['categoria'], $_POST) ) {

         $validateForm = $Category::exists('categorias', 'categoria', $_POST['categoria']);

         if (!$validateForm) {

            if (empty($_POST['id'])) {

               $saveCategory = $Category->insertCategory($data);
      
               if ($saveCategory) {              
                  echo json_encode(['success' => true, 'message' => 'Categoría registrada correctamente']);
               } else {
                  echo json_encode(['success' => false, 'message' => 'Error al registrar la categoría']);
               }
      
            } else {
      
               $saveCategory = $Category->updateCategory($data, $_POST['id']);
               
               if ($saveCategory) {              
                  echo json_encode(['success' => true, 'message' => 'Categoría actualizada correctamente']);
               } else {
                  echo json_encode(['success' => false, 'message' => 'Error al actualizar categoría']);
               }
            
            }
         } else echo json_encode(['success' => false, 'message' => "La categoría {$_POST['categoria']} ya existe"]);

      } else echo json_encode(['success' => false, 'message' => "Complete los campos requeridos"]);
      break;
   /**  U P D A T E  C A T E G O R Y  */
   case 'updateCategory':
      $updateCategory = $Category->selectCategory($_POST['id']);

      if (count($updateCategory) > 0) {              
         echo json_encode(['success' => true, 'message' => 'Categoría encontrado', 'data' => $updateCategory]);
      } else {
         echo json_encode(['success' => false, 'message' => 'No se encontró el registro de la categoría']);
      }
      break;
   /**  D E L E T E  C A T E G O R Y  */
   case 'deleteCategory':

      $validateProducts = $Category::exists('productos', 'id_categoria', $_POST['id']);

      if (!$validateProducts) {
         $deleteCategory = $Category->deleteCategory($_POST['id']);

         if ($deleteCategory) {
            echo json_encode(['success' => true, 'message' => 'Categoría eliminada correctamente']);
         } else {
            echo json_encode(['success' => false, 'message' => 'Error al eliminar la categoría']);
         }   
      } else echo json_encode(['success' => false, 'message' => 'La categoría cuenta con productos']);
      break;
   /**  S H O W  T A B L E  */
   case 'dataTable':
      
      $response = $Category->dataTable();
      $data = array();

      if (count($response) > 0 ) {
         foreach ($response as $row) {

            $estado = $row['estado'] ? "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Activa</span>" : "";

            $btn = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"moduleCategory.updateCategory('{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
            $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"moduleCategory.deleteCategory('{$row['id']}', '{$row['categoria']}')\"><i class=\"bx bx-trash m-0\"></i></button>";
            
            $data[] = [
               "id" => $row['id'],
               "categoria" => $row['categoria'],
               "estado" => $estado,
               "btn" => $btn
            ];
         }
      }
      echo json_encode($data);
      break;
}
