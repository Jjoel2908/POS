<?php
require_once '../config/Connection.php';

class CreditSale extends Connection
{
    public function __construct() {}

    public function dataTable(): array
    {
        return $this->queryMySQL(
            "SELECT 
                v.id,
                v.fecha, 
                v.total_venta, 
                v.total_pagado,
                (v.total_venta - v.total_pagado) AS pendiente_pago,
                CONCAT(c.nombre, ' ', c.apellidos) AS cliente
            FROM 
                ventas v
            INNER JOIN 
                clientes c
            ON 
                v.id_cliente = c.id 
            WHERE 
                v.estado = 1 
            AND 
                v.tipo_venta = 2
            AND
                v.total_pagado < v.total_venta
            ORDER BY 
                v.id DESC 
            LIMIT 5"
        );
    }  
}
