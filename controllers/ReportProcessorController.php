<?php
require '../models/ReportProcessor.php';
class ReportProcessorController
{
    private $model;

    public function __construct()
    {
        $this->model = new ReportProcessor();
    }

    public function dataTable()
    {
        $response = $this->model->getGeneralSummary();

        if (count($response) > 0)
            echo json_encode(array_merge($response, ["success" => true]));
        else
            echo json_encode([ "success" => false ]);
    }
}
