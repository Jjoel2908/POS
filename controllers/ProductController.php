<?php
require '../models/Product.php';

class ProductController
{
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

    public function save()
    {
        /** Valida campos requeridos */
        $validateData = ['nombre', 'codigo', 'id_categoria', 'id_marca', 'precio_compra', 'precio_venta'];
        if (!$this->model::validateData($validateData, $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        $code = $this->model::sanitizeInput('codigo', 'text');

        /** Información a registrar o actualizar */
        $data = [
            'nombre'        => $this->model::sanitizeInput('nombre', 'text'),
            'codigo'        => $code,
            'id_categoria'  => $this->model::sanitizeInput('id_categoria', 'int'),
            'id_marca'      => $this->model::sanitizeInput('id_marca', 'int'),
            'precio_compra' => $this->model::sanitizeInput('precio_compra', 'float'),
            'precio_venta'  => $this->model::sanitizeInput('precio_venta', 'float')
        ];

        if (!$this->id) {
            /** Valida que no exista un registro similar al entrante */
            if ($this->model::exists($this->table, 'codigo', $code)) {
                echo json_encode(['success' => false, 'message' => "El código " . $code .  " ya existe"]);
                return;
            }

            /** Agregamos sucursal */
            $data['id_sucursal'] = $this->idSucursal;

            $save = $this->model::insert($this->table, $data);

            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['save_success']]
                    : ['success' => false, 'message' => $this->messages['save_failed']]
            );
        } else {
            $save = $this->model::update($this->table, $this->id, $data);
            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['update_success']]
                    : ['success' => false, 'message' => $this->messages['update_failed']]
            );
        }
    }

    public function update()
    {
        $recoverRegister = $this->model->select($this->table, $this->id);

        echo json_encode(
            count($recoverRegister) > 0
                ? ['success' => true, 'message' => '', 'data' => $recoverRegister]
                : ['success' => false, 'message' => 'No se encontró el registro.']
        );
    }

    public function delete()
    {
        $dataProduct = $this->model->select($this->table, $this->id);

        if ($dataProduct['stock'] > 0) {
            echo json_encode(['success' => false, 'message' => "Producto con {$dataProduct['stock']} unidades disponibles"]);
            return;
        }

        $delete = $this->model::delete($this->table, $this->id);

        echo json_encode(
            $delete
                ? ['success' => true, 'message' => $this->messages['delete_success']]
                : ['success' => false, 'message' => $this->messages['delete_failed']]
        );
    }

    public function dataTable()
    {
        $response = $this->model->dataTable();
        $data = [];

        foreach ($response as $row) {

            $btn  = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateProduct('Producto', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
            $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Producto', '{$row['id']}', `{$row['nombre']}`)\"><i class=\"bx bx-trash m-0\"></i></button>";

            $data[] = [
                "Producto"      => $row['nombre'],
                "Código"        => $row['codigo'],
                "Categoría"     => $row['categoria'],
                "Marca"         => $row['marca'],
                "Precio Compra" => $row['precio_compra'],
                "Precio Venta"  => $row['precio_venta'],
                "Cantidad"      => $row['stock'],
                "Acciones"      => $btn
            ];
        }
        echo json_encode($data);
    }

    public function droplist()
    {
        $listRegister = "";
        $list = $this->model->selectAll($this->table);
        foreach ($list as $item) {
            $listRegister .= '<option value="' . $item['id'] . '">' . $item['nombre'] . '</option>';
        }

        echo json_encode(['success' => true, 'data' => $listRegister]);
    }
}
