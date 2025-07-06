<?php
require '../models/Dashboard.php';
class DashboardController
{
    private $model;
    private $id;
    private $idSucursal;

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new Dashboard();
        $this->id = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    public function fetchTopSellingProducts()
    {
        /** Llamada al modelo para obtener los productos más vendidos */
        $products = $this->model::getTopSellingProducts();

        /** Si hay productos, procesamos la respuesta */
        if (!empty($products)) {
            /** Preparamos la estructura de la respuesta */
            $data = array_map(function ($row) {
                return [
                    'product'  => $row['nombre_producto'],
                    'quantity' => $row['total_selling']
                ];
            }, $products);

            /** Devolvemos los resultados en formato JSON */
            echo json_encode(["success" => true, "message" => "", "data" => $data]);
        } else
            echo json_encode(["success" => false]);
    }
}