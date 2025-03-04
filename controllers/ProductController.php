<?php
session_start();
require '../models/Product.php';

class ProductController {
    private $Product;

    public function __construct() {
        $this->Product = new Product();
    }

    public function save() {
        if ($this->Product::validateData(['nombre', 'stock_minimo', 'codigo', 'id_categoria', 'precio_compra', 'precio_venta'], $_POST)) {
            if (empty($_POST['id'])) {
                if ($this->Product::exists('productos', 'codigo', $_POST['codigo'])) {
                    echo json_encode(['success' => false, 'message' => "El código {$_POST['codigo']} ya existe"]);
                    return;
                }
            }

            $imagen = $_POST["imagenactual"] ?? NULL;
            $updateImage = false;
            
            if (isset($_FILES['imagen']['tmp_name']) && is_uploaded_file($_FILES['imagen']['tmp_name'])) {
                $ext = pathinfo($_FILES["imagen"]["name"], PATHINFO_EXTENSION);
                $allowed_types = ["jpg", "jpeg", "png"];

                if (in_array($ext, $allowed_types) && exif_imagetype($_FILES["imagen"]["tmp_name"])) {
                    $imagen = round(microtime(true)) . '.' . $ext;
                    $ruta_destino = "../media/products/" . $imagen;
                    if (move_uploaded_file($_FILES["imagen"]["tmp_name"], $ruta_destino)) $updateImage = true;
                }
            }
            
            if ($updateImage && $_POST['imagenactual']) {
                $imageRoute = "../media/products/" . $_POST['imagenactual'];
                if (file_exists($imageRoute)) unlink($imageRoute);
            }
            
            $data = array_merge($_POST, ['imagen' => $imagen]);
            
            $result = empty($_POST['id']) 
                ? $this->Product->insertProduct($data)
                : $this->Product->updateProduct($data, $_POST['id']);
            
            echo json_encode(['success' => $result, 'message' => $result ? 'Producto guardado correctamente' : 'Error al guardar producto']);
        } else {
            echo json_encode(['success' => false, 'message' => "Complete los campos requeridos"]);
        }
    }

    public function update() {
        $updateProduct = $this->Product->selectProduct($_POST['id']);
        echo json_encode([ 'success' => count($updateProduct) > 0, 'message' => count($updateProduct) > 0 ? 'Producto Encontrado' : 'No se encontró el registro del producto', 'data' => $updateProduct ]);
    }

    public function delete() {
        $dataProduct = $this->Product->selectProduct($_POST['id']);
        if ($dataProduct['stock'] == 0) {
            $result = $this->Product->deleteProduct($_POST['id']);
            echo json_encode(['success' => $result, 'message' => $result ? 'Producto eliminado correctamente' : 'Error al eliminar producto']);
        } else {
            echo json_encode(['success' => false, 'message' => "Producto con {$dataProduct['stock']} unidades disponibles"]);
        }
    }

    public function dataTable() {
        $response = $this->Product->dataTable();
        $data = [];

        foreach ($response as $row) {
            $img = !empty($row['imagen']) ? "<img src='../media/products/{$row['imagen']}' height='48px' width='48px'>" : "<img src='../media/products/default.png' height='48px' width='48px'>";
            $btn = "<button type='button' class='btn btn-inverse-primary mx-1' onclick='moduleProduct.updateProduct({$row['id']})'><i class='bx bx-edit-alt m-0'></i></button>";
            $btn .= "<button type='button' class='btn btn-inverse-danger mx-1' onclick='moduleProduct.deleteProduct({$row['id']}, `{$row['nombre']}`)'><i class='bx bx-trash m-0'></i></button>";

            $data[] = [
                "nombre" => $row['nombre'],
                "codigo" => $row['codigo'],
                "id_categoria" => $row['nombre_categoria'],
                "precio_compra" => $row['precio_compra'],
                "precio_venta" => $row['precio_venta'],
                "stock" => $row['stock'],
                "imagen" => $img,
                "btn" => $btn
            ];
        }
        echo json_encode($data);
    }

    public function selectCategory() {
        $categories = $this->Product->selectAll('categorias');
        foreach ($categories as $category) {
            echo '<option value="' . $category['id'] . '">' . $category['categoria'] . '</option>';
        }
    }
}

$controller = new ProductController();

if (isset($_GET['op']) && method_exists($controller, $_GET['op'])) {
    $controller->{$_GET['op']}();
} else {
    echo json_encode(['success' => false, 'message' => 'Operación no válida']);
}