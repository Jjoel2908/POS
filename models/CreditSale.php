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

    public function updateSaleAsPaid(int $saleId): bool
    {
        return $this->queryWithTransaction(
            "UPDATE 
                ventas 
            SET 
                total_pagado = total_venta, 
                estado_pago = 1 
            WHERE 
                id = $saleId"
        );
    }

    public function updatePartialPayment(int $saleId, float $newAmount): bool
    {
        return $this->queryWithTransaction(
            "UPDATE 
                ventas
            SET 
                total_pagado = $newAmount 
            WHERE 
                id = $saleId"
        );
    }
        
    public function processPayment(int $saleId, float $amount): array
    {
        try {
            $conexion = self::conectionMySQL();
            $conexion->begin_transaction();

            $saleData = self::select("ventas", $saleId);
            $pendingAmount = round($venta['total_venta'] - $venta['total_pagado'], 2);

            /** Si el monto a pagar es mayor o igual al pendiente, completamos la venta como pagada */
            if ($amount >= $pendingAmount) {
                if (!$this->updateSaleAsPaid($saleId))
                    throw new Exception("Error al completar la venta como pagada.");
            } else {
                /** Abonar parcialmente */
                $newAmount = round($venta['total_pagado'] + $amount, 2);
                if (!$this->updatePartialPayment($saleId, $newAmount))
                    throw new Exception("Error al actualizar la venta como parcial.");
            }

            /** Registramos el abono para el historial de pagos */
            $abonoData = [
                'id_venta' => $saleId,
                'fecha'    => date('Y-m-d H:i:s'),
                'monto'    => $amount,
            ];

            $insertAbono = $this::insertWithTransaction($conexion, 'abonos_ventas', $abonoData);

            if (!$insertAbono)
                throw new Exception('Error al registrar el abono en la base de datos.');

            $this::commit();

            return [
                'success' => true,
                'message' => "Pago procesado correctamente",
            ];
        } catch (Exception $e) {
            $this::rollback();
            return [
                'success' => false,
                'message' => $e->getMessage(),
            ];
        }
    }
}
