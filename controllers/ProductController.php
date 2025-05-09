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
        $validateData = ['nombre', 'codigo', 'id_marca', 'precio_compra', 'precio_venta'];
        if (!$this->model::validateData($validateData, $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        $code = $this->model::sanitizeInput('codigo', 'text');

        /** Información a registrar o actualizar */
        $data = [
            'nombre'        => $this->model::sanitizeInput('nombre', 'text'),
            'codigo'        => $code,
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

    /** Método para procesar los datos del módulo de productos usando DataTables con procesamiento en servidor.
     * Recibe parámetros de paginación, búsqueda y devuelve los resultados con estructura compatible con DataTables.
     */
    public function dataTable()
    {
        /** Parámetros recibidos desde el FrontEnd:
         *** @var int $draw - Identificador de solicitud de DataTables (sirve para saber qué respuesta corresponde a qué petición).
         *** @var int $start - Desde qué registro empezar a mostrar (paginación).
         *** @var int $length - Cuántos registros se deben mostrar (por página).
         *** @var string $search - Texto ingresado en el buscador de la tabla.
         */
        $draw   = $this->model::sanitizeInput('draw', 'int');
        $start  = $this->model::sanitizeInput('start', 'int');
        $length = $this->model::sanitizeInput('length', 'int');
        $search = preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]/', '', $_POST['search']['value'] ?? '');

        /** Información que será retornada en la vista:
         *** @var int $totalRecords - Total de registros sin filtros (para mostrar en el paginador).
         *** @var int $filteredRecords - Total de registros que coinciden con el término de búsqueda.
         *** @var array $productos - Registros obtenidos para esta página, con búsqueda aplicada si corresponde.
         */
        $totalRecords    = $this->model->countProducts();
        $filteredRecords = $this->model->countFilteredProducts($search);
        $productos       = $this->model->dataTable($start, $length, $search);

        $data = [];

        /** Recorremos cada producto y armamos la fila que se enviará al frontend */
        foreach ($productos as $row) {

            /** @var string $btn - Botones de acción (editar y eliminar). */
            $btn  = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateProduct('Producto', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
            $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Producto', '{$row['id']}', `{$row['nombre']}`)\"><i class=\"bx bx-trash m-0\"></i></button>";

            /** @var string $img - Nombre de la imagen (desde la base de datos). */
            $img = $row['imagen'] ?? 'default.png';

            /** @var string $imgUrl - Ruta completa a la imagen. */
            $imgUrl = "../media/products/$img";

            /** @var string $imgTag - Etiqueta HTML <img> para mostrar la imagen. */
            $imgTag = "<img src='$imgUrl' alt='img' width='50' height='50'>";

            /** Agregamos la fila al array de datos */
            $data[] = [
                "Producto"      => $row['nombre'],
                "Marca"         => $row['marca'],
                "Código"        => $row['codigo'],
                "Precio Compra" => "$" . number_format($row['precio_compra'], 2),
                "Precio Venta"  => "$" . number_format($row['precio_venta'], 2),
                "Cantidad"      => $row['stock'],
                "Imagen"        => $imgTag,
                "Acciones"      => $btn
            ];
        }

        /** Respondemos con un JSON en el formato que DataTables espera:
         *** - draw: número de solicitud
         *** - recordsTotal: total sin filtros
         *** - recordsFiltered: total después de filtros
         *** - data: los datos actuales para mostrar
         */
        echo json_encode([
            "draw"            => intval($draw),
            "recordsTotal"    => intval($totalRecords),
            "recordsFiltered" => intval($filteredRecords),
            "data"            => $data
        ]);
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

    public function droplistSales()
    {
        /** Producto a buscar */
        $text = $this->model::sanitizeInput('search', 'text');
        
        /** Buscamos coincidencias */
        $products = $this->model->searchProduct($text);

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
    }
}
