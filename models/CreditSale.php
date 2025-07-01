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
                v.estado_pago IN (2,3)
            ORDER BY 
                v.id DESC"
        );
    }

    public function updateSaleAsPaid(mysqli $conexion, int $saleId): bool
    {
        return $this->executeQueryWithTransaction(
            $conexion,
            "UPDATE 
                ventas 
            SET 
                total_pagado = total_venta, 
                estado_pago = 1 
            WHERE 
                id = $saleId"
        );
    }

    public function updatePartialPayment(mysqli $conexion, int $saleId, float $newAmount): bool
    {
        return $this->executeQueryWithTransaction(
            $conexion,
            "UPDATE 
                ventas
            SET 
                total_pagado = $newAmount,
                estado_pago = 2
            WHERE 
                id = $saleId"
        );
    }
        
    public function processPayment(int $saleId, array $saleData, float $amount): array
    {
        $conexion = self::conectionMySQL();
        try {
            $conexion->begin_transaction();
            $pendingAmount = round($saleData['total_venta'] - $saleData['total_pagado'], 2);

            /** Si el monto a pagar es mayor o igual al pendiente, completamos la venta como pagada */
            if ($amount >= $pendingAmount) {
                if (!$this->updateSaleAsPaid($conexion, $saleId))
                    throw new Exception("Error al completar la venta como pagada.");
            } else {
                /** Abonar parcialmente */
                $newAmount = round($saleData['total_pagado'] + $amount, 2);
                if (!$this->updatePartialPayment($conexion, $saleId, $newAmount))
                    throw new Exception("Error al actualizar la venta como parcial.");
            }

            /** Registramos el abono para el historial de pagos */
            $abonoData = [
                'id_venta_credito' => $saleId,
                'fecha'            => date('Y-m-d H:i:s'),
                'monto'            => $amount,
                'creado_por'       => $amount,
            ];

            $insertAbono = $this::insertWithTransaction($conexion, 'abonos_credito', $abonoData);

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
