<?php
session_start();
require '../models/Product.php';

class ProductController {
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "productos";
    private $model;
    private $id;
    private $idSucursal;

    private $messages = [
        "save_success" => "Producto registrado correctamente.",
        "save_failed" => "Error al registrar el producto.",
        "update_success" => "Producto actualizado correctamente.",
        "update_failed" => "Error al actualizar el producto.",
        "delete_success" => "Producto eliminado correctamente.",
        "delete_failed" => "Error al eliminar el producto.",
        "required" => "Debe completar la información obligatoria."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new Product();
        $this->id = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    public function save() {
        /** Información a registrar o actualizar */
        $data = [
            'nombre'    => $this->model::sanitizeInput('nombre', 'text'),
            'codigo'    => $this->model::sanitizeInput('codigo', 'text'),
            'id_categoria'    => $this->model::sanitizeInput('id_categoria', 'int'),


            
            'precio_compra'  => isset($_POST['precio_compra']) ? max(0, (float) $_POST['precio_compra']) : 0.00,
            'precio_venta'   => isset($_POST['precio_venta']) ? max(0, (float) $_POST['precio_venta']) : 0.00,
        ];

        /** Valida campos requeridos */
        $validateData = ['nombre', 'stock_minimo', 'codigo', 'id_categoria', 'precio_compra', 'precio_venta'];
        if (!$this->model::validateData($validateData, $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        if (empty($_POST['id'])) {
            /** Valida que no exista un registro similar al entrante */
            if($this->model::exists($this->table, 'codigo', $_POST['codigo'])) {
                echo json_encode(['success' => false, 'message' => "El código " . $_POST['codigo'] .  " ya existe"]);
                return;
            }

            /** Agregamos la fecha de creación */
            $data['fecha'] = date('Y-m-d H:i:s');

            /** Agregamos sucursal */
            $data['id_sucursal'] = $idSucursal;

            /** Agregamos el usuario */
            $data['creado_por'] = $_SESSION['id'];

            $save = $this->model::insert($this->table, $data);

            echo json_encode(
                $save 
                    ? ['success' => true, 'message' => $this->messages['save_success']] 
                    : ['success' => false, 'message' => $this->messages['save_failed']]
                );

        } else {
            $save = $this->model::update($this->table, $_POST['id'], $data);
            echo json_encode(
                $save 
                    ? ['success' => true, 'message' => $this->messages['update_success']] 
                    : ['success' => false, 'message' => $this->messages['update_failed']]
                );
        }
    }

    public function update() {
        $recoverRegister = $this->model->select($this->table, $_POST['id']);

        echo json_encode(
            count($recoverRegister) > 0     
                ? ['success' => true, 'message' => '', 'data' => $recoverRegister] 
                : ['success' => false, 'message' => 'No se encontró el registro.']
        ); 
    }

    public function delete() {
        $dataProduct = $this->model->select($this->table, $_POST['id']);

        if ($dataProduct['stock'] > 0) {
            echo json_encode(['success' => false, 'message' => "Producto con {$dataProduct['stock']} unidades disponibles"]);
            return;
        }

        $delete = $this->model::delete($this->table, $_POST['id']);

        echo json_encode(
            $delete 
                ? ['success' => true, 'message' => $this->messages['delete_success']] 
                : ['success' => false, 'message' => $this->messages['delete_failed']]
        );
    }

    public function dataTable() 
    {
        $response = $this->Product->dataTable();
        $data = [];

        foreach ($response as $row) {
            $btn = "<button type='button' class='btn btn-inverse-primary mx-1' onclick='moduleProduct.updateProduct({$row['id']})'><i class='bx bx-edit-alt m-0'></i></button>";
            $btn .= "<button type='button' class='btn btn-inverse-danger mx-1' onclick='moduleProduct.deleteProduct({$row['id']}, `{$row['nombre']}`)'><i class='bx bx-trash m-0'></i></button>";

            $data[] = [
                "PRODUCTO" => $row['nombre'],
                "CÓDIGO" => $row['codigo'],
                "CATEGORÍA" => $row['nombre_categoria'],
                "COMPRA" => $row['precio_compra'],
                "VENTA" => $row['precio_venta'],
                "STOCK" => $row['stock'],
                "ACCIONES" => $btn
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