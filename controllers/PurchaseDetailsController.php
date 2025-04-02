<?php
require '../models/PurchaseDetails.php';
require '../models/Product.php';

class PurchaseDetailsController
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "detalle_compra";
    private $model;
    private $id;
    private $idSucursal;

    private $messages = [
        "save_success"   => "Detalle de compra registrado correctamente.",
        "save_failed"    => "Error al registrar el detalle de compra.",
        "update_success" => "Detalle de compra actualizado correctamente.",
        "update_failed"  => "Error al actualizar el detalle de compra.",
        "delete_success" => "Detalle de compra eliminado correctamente.",
        "delete_failed"  => "Error al eliminar el detalle de compra.",
        "required"       => "Debe completar la información obligatoria del detalle de compra."
    ];

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new PurchaseDetails();
        $this->id = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    public function save()
    {
        /** Valida campos requeridos */
        if (!$this->model::validateData(['id', 'cantidad'], $_POST)) {
            echo json_encode(['success' => false, 'message' => $this->messages['required']]);
            return;
        }

        $Product = new Product();
        $detail  = $Product->getPurchasePrice($this->id);

        /** Información a registrar o actualizar */
        $data = [
            'id_producto' => $this->id,
            'precio'      => number_format((float)$detail['precio_compra'], 2, '.', ''),
            'cantidad'    => $this->model::sanitizeInput('cantidad', 'int'),
        ];

        $save = $this->model::insert($this->table, $data);

        echo json_encode(
            $save
                ? ['success' => true, 'message' => $this->messages['save_success']]
                : ['success' => false, 'message' => $this->messages['save_failed']]
        );
    }

    public function update()
    {
        // $recoverRegister = $this->model->selectOne($this->id);

        // echo json_encode(
        //     count($recoverRegister) > 0     
        //         ? ['success' => true, 'message' => '', 'data' => $recoverRegister[0]] 
        //         : ['success' => false, 'message' => 'No se encontró el registro.']
        // );
    }

    public function delete()
    {
        $validateProducts = $this->model::exists('productos', 'id_categoria', $this->id);

        if (!$validateProducts) {
            $delete = $this->model::delete($this->table, $this->id);
            echo json_encode(
                $delete
                    ? ['success' => true, 'message' => $this->messages['delete_success']]
                    : ['success' => false, 'message' => $this->messages['delete_failed']]
            );
        } else
            echo json_encode(['success' => false, 'message' => 'La categoría cuenta con productos']);
    }

    public function dataTable()
    {
        $response = $this->model->selectAll($this->table);
        $data = array();

        if (count($response) > 0) {

            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date  = date("d/m/Y", strtotime($day));

                $estado = $row['estado']
                    ? "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Activo</span>"
                    : "";

                $btn = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateRegister('Categoría', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Categoría', '{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";
                $data[] = [
                    "Categoría"     => $row['nombre'],
                    "Fecha de Alta" => $date,
                    "Estado"        => $estado,
                    "Acciones"      => $btn
                ];
            }
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
