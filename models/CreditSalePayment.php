<?php
require_once '../config/Connection.php';

class CreditSalePayment extends Connection
{
    public function __construct() {}

    public function dataTable(int $saleId): array
    {
        return $this->queryMySQL(
            "SELECT 
                fecha,
                monto
            FROM 
                abonos_credito
            WHERE 
                id_venta_credito = $saleId
            ORDER BY 
                fecha ASC"
        );
    }
}