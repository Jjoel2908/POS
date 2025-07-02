<?php
require '../models/ReportProcessor.php';
class ReportProcessorController
{
    private $model;
    private $id;
    private $idSucursal;

    public function __construct($id = null, $idSucursal = null)
    {
        $this->model = new ReportProcessor();
        $this->id = $id !== null ? (filter_var($id, FILTER_VALIDATE_INT) ?: 0) : null;
        $this->idSucursal = $idSucursal !== null ? (filter_var($idSucursal, FILTER_VALIDATE_INT) ?: 0) : null;
    }

    public function purchaseReport(){}
    public function saleReport(){}
    public function expenseReport(){}
    public function generalReport(){}
}