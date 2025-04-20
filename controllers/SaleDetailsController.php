<?php
require '../models/SaleDetails.php';
require '../models/Product.php';

class SaleDetailsController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "detalle_venta";
    private $model;
    private $id;
    private $idUser;
    private $idSucursal;

    private $messages = [
        "save_success"   => "Detalle de venta registrado correctamente.",
        "save_failed"    => "Error al registrar el detalle de venta.",
        "update_success" => "Detalle de venta actualizado correctamente.",
        "update_failed"  => "Error al actualizar el detalle de venta.",
        "delete_success" => "Detalle de venta eliminado correctamente.",
        "delete_failed"  => "Error al eliminar el detalle de venta.",
        "required"       => "Debe completar la información obligatoria del detalle de venta.",
        "stock_failed"   => "Stock Insuficiente.",
    ];
    

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model      = new SaleDetails();
        $this->id         = $id         !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idUser     = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
    }

    public function save()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['id', 'cantidad'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        /** Verificamos si el producto ya existe en los detalles de venta */
        $existDetail        = $this->model->existSaleDetails($this->id, $this->idUser);

        /** Cantidad nueva ingresada por el usuario */
        $quantityInput = $this->model::sanitizeInput('cantidad', 'int');

        /** Obtenemos la información del  */
        $Product = new Product();
        $detail  = $Product->getSalePrice($this->id);

        /** Inicializamos nueva cantidad */
        $newQuantity = $quantityInput;

        /** Si ya existe un detalle de venta, sumamos cantidades */
        if (!empty($existDetail) && isset($existDetail[0]['cantidad'])) {
            $quantitySaleDetail = $existDetail[0]['cantidad'];
            $newQuantity += $quantitySaleDetail;
        }

        /** Validamos que la cantidad nueva sea menor o igual al stock del producto */
        if ($newQuantity > $detail['stock']) {
            echo json_encode(['success' => false, 'message' => $this->messages['stock_failed']]);
            return;
        }
    
        /** Información a registrar o actualizar */
        $data = [
            'id_producto'   => $this->id,
            'precio_compra' => number_format((float)$detail['precio_compra'], 2, '.', ''),
            'precio_venta'  => number_format((float)$detail['precio_venta'], 2, '.', ''),
            'cantidad'      => $quantityInput,
        ];

        /** Si no existe un detalle de venta idéntico, registramos uno nuevo */
        if (empty($existDetail)) {
            $save = $this->model::insert($this->table, $data);

            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['save_success'], 'data' => 'DetalleVenta']
                    : ['success' => false, 'message' => $this->messages['save_failed']]
            );
        } else {
            $idSaleDetail = $existDetail[0]['id'];
            /** Si el detalle de venta ya existe, actualizamos la cantidad */
            $save = $this->model->updateSaleDetail($idSaleDetail, $quantityInput);

            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['update_success'], 'data' => 'DetalleVenta']
                    : ['success' => false, 'message' => $this->messages['update_failed']]
            );
        }
    }

    public function delete()
    {
        $delete = $this->model::delete($this->table, $this->id);
        echo json_encode(
            $delete
                ? ['success' => true, 'message' => $this->messages['delete_success']]
                : ['success' => false, 'message' => $this->messages['delete_failed']]
        );
    }

    public function temporaryDataTable()
    {
        $response = $this->model->dataTable($this->idUser);
        $HTML     = "";
        $total    = 0;

        if (count($response) > 0) {
            foreach ($response as $row) {
                $product  = htmlspecialchars($row['producto']);
                $quantity = (int) $row['cantidad'];
                $price    = (float) $row['precio'];
                $btn = "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('DetalleVenta', '{$row['id']}', '{$product}')\"><i class=\"bx bx-trash m-0\"></i></button>";

                $subTotal  = $price * $quantity;
                $total    += $subTotal;

                $HTML .= "<tr>";
                $HTML .= "<td class='text-start'>{$product}</td>";
                $HTML .= "<td>{$quantity} uds.</td>";
                $HTML .= "<td class='text-end'>$" . number_format($price, 2) . "</td>";
                $HTML .= "<td class='text-end'>$" . number_format($subTotal, 2) . "</td>";
                $HTML .= "<td>{$btn}</td>";
                $HTML .= "</tr>";
            }
        } else {
            $HTML .= '<tr><td colspan="5">No hay detalles de venta disponibles.</td></tr>';
        }

        echo json_encode([
            'success' => true,
            'message' => '',
            'data' => [
                'data' => $HTML, 
                'total' => number_format($total, 2)
            ]
        ]);
    }

    public function dataTable()
    {
        $saleId = $_POST['registerId'] ? (filter_var($_POST['registerId'], FILTER_VALIDATE_INT) ?: 0) : null;

        $response = $this->model->dataTable($this->idUser, $saleId);
        $data     = array();

        if (count($response) > 0) {

            foreach ($response as $row) {

                /** Nombre de producto */
                $product  = htmlspecialchars($row['producto']);

                /** Cantidad del producto */
                $quantity = (int) $row['cantidad'];

                /** Precio de producto */
                $price    = (float) $row['precio'];

                /** Total de producto */
                $subTotal  = $price * $quantity;

                $data[] = [
                    "Producto" => $product,
                    "Cantidad" => $quantity . " uds.",
                    "Precio"   => "$" . number_format($price, 2),
                    "Subtotal" => "$" . number_format($subTotal, 2),
                ];
            }
        }
        echo json_encode($data);
    }
}