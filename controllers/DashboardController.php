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

    public function getProductBestSelling()
    {
        $dataProduct  = [];
        $dataQuantity = [];
        $products     = $Dashboard->getProductBestSelling();

        if (count($products) > 0) {

            foreach ($products as $row) {
                $dataProduct[] = [
                $row['nombre_producto']
                ];

                $dataQuantity[] = [
                $row['total_selling']
                ];
            }

            echo json_encode(["success" => true, "message" => "", "product" => $dataProduct, "quantity" => $dataQuantity]);
        } else echo json_encode(["success" => false, "message" => "No se encontraron productos mejor vendidos"]);
    }
}
