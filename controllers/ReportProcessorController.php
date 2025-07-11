<?php
require '../models/ReportProcessor.php';
class ReportProcessorController
{
    private $model;
    private $id;
    private $idSucursal;
    private $module;

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new ReportProcessor();
        $this->module = $module;
        $this->id = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    public function tableName() {
        $modules = [
            'ReporteCompra' => 'compras',
            'ReporteVenta' => 'ventas',
            'ReporteGastos' => 'gastos',
        ];

        $currenModule = $this->model::sanitizeInput('module', 'text');
        return $modules[$currenModule] ?? null;
    }

    public function dataTable()
    {
        $response = $this->model->getReport($this->module);
        $data = array();

        if (count($response) > 0) {

            foreach ($response as $row) {
                list($day, $hour) = explode(" ", $row['fecha']);
                $date  = date("d/m/Y", strtotime($day));

                $estado = $row['estado'] 
                    ? "<span class=\"badge bg-primary font-14 px-3 fw-normal\">Activo</span>" 
                    : "";
                    
                $btn = "<button type=\"button\" class=\"btn btn-inverse-primary mx-1\" onclick=\"updateRegister('Marca', '{$row['id']}')\"><i class=\"bx bx-edit-alt m-0\"></i></button>";
                $btn .= "<button type=\"button\" class=\"btn btn-inverse-danger mx-1\" onclick=\"deleteRegister('Marca', '{$row['id']}', '{$row['nombre']}')\"><i class=\"bx bx-trash m-0\"></i></button>";
                $data[] = [
                    "Marca"     => $row['nombre'],
                    "Fecha de Alta" => $date,
                    "Estado"        => $estado,
                    "Acciones"      => $btn
                ];
            }
        }
        echo json_encode($data);
    }
}