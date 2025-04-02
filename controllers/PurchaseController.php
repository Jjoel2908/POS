<?php
require '../models/Purchase.php';
require '../models/PurchaseDetails.php';

class PurchaseController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "compras";
    private $model;
    private $id;
    private $idSucursal;

    private $messages = [
        "save_success"   => "Compra registrada correctamente.",
        "save_failed"    => "Error al registrar la compra.",
        "update_success" => "Compra actualizada correctamente.",
        "update_failed"  => "Error al actualizar la compra.",
        "delete_success" => "Compra eliminada correctamente.",
        "delete_failed"  => "Error al eliminar la compra.",
        "required"       => "Debe completar la información obligatoria de la compra."
        "empty"          => "No hay detalles de compra que registrar."
    ];
    
    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new Purchase();
        $this->id = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    public function save()
    {
        $total = 0;
        $idUser   = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);

        /** Validamos si hay detalles de productos pendientes */
        $PurchaseDetails = new PurchaseDetails();
        $details  = $PurchaseDetails->getPurchaseDetails($idUser);

        if (empty($details)) {
            echo json_encode(['success' => false, 'message' => $this->messages['empty']]);
            return;
        }
   
        /** MODIFICAR AQUI PARA CREAR UNA TRANSACCION MEJOR */
               foreach($productDetails as $detail) {
                  $quantity = $detail['cantidad'];
                  $price = $detail['precio'];
   
                  $totalPurchase += $quantity * $price;
               }
               
               $dataPurchase = [
                  "id_usuario" => $_SESSION['id'],
                  "total"      => $totalPurchase
               ];
   
               $savePurchase = $Purchase->savePurchase($dataPurchase);
   
               if ($savePurchase > 0) {
   
                  $id_compra  = $savePurchase;
                  $id_usuario = $_SESSION['id'];
   
                  $updatePurchaseDetails = $Purchase->updateIdPurchaseDetails($id_compra, $id_usuario);
   
                  $detailProducts = $Purchase->idPurchaseDetails($id_compra);
   
                  if (!empty($detailProducts)) {
   
                     foreach($detailProducts as $addProduct) {
                        $id_producto = $addProduct['id_producto'];
                        $cantidad    = $addProduct['cantidad'];
   
                        $addStock = $Purchase->addStock($id_producto, $cantidad);
                     }
   
                     if ($addStock) {
                        echo json_encode(['success'  => true, 'message' => 'La compra se realizó correctamente']);
                     } else {
                        echo json_encode(['success'  => false, 'message' => 'Error al actualizar detalles de compra']);
                     }
                  }
   
               } else echo json_encode(['success'  => false, 'message' => 'Error al generar la compra']);
   
         

   

        /** Información a registrar o actualizar */
        $data = [
            'id_producto' => $this->id,
            'precio'      => number_format((float)$detail['precio_compra'], 2, '.', ''),
            'cantidad'    => $quantity,
        ];

        /** Identificador de usuario */
        $idUser = filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0;
        $existDetail = $this->model->existPurchaseDetails($this->id, $idUser);

        /** Si no existe un detalle de compra idéntico, registramos uno nuevo */
        if (empty($existDetail)) {
            $save = $this->model::insert($this->table, $data);

            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['save_success'], 'data' => 'DetalleCompra']
                    : ['success' => false, 'message' => $this->messages['save_failed']]
            );
        } else {
            /** Si el detalle de compra ya existe, actualizamos la cantidad */
            $idPurchaseDetail = $existDetail[0]['id'];
            $save = $this->model->updatePurchaseDetail($idPurchaseDetail, $quantity);

            echo json_encode(
                $save
                    ? ['success' => true, 'message' => $this->messages['update_success'], 'data' => 'DetalleCompra']
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

    public function dataTable()
    {
        $idUser   = (filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0);
        $response = $this->model->dataTable($idUser);
        $HTML     = "";
        $total    = 0;

        if (count($response) > 0) {
            foreach ($response as $row) {
                $product  = htmlspecialchars($row['producto']);
                $quantity = (int) $row['cantidad'];
                $price    = (float) $row['precio'];
                $btn = "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Detalle de Compra', '{$row['id']}', '{$product}')\"><i class=\"bx bx-trash m-0\"></i></button>";

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
            $HTML .= '<tr><td colspan="5">No hay detalles de compra disponibles.</td></tr>';
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
}