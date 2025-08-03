<?php
require_once '../config/Connection.php';
require_once '../models/SaleDetails.php';
require_once '../models/Cashbox.php';
require_once '../models/CashboxCount.php';

class Sale extends Connection
{
    /** Tipos de venta */
    const CONTADO = 1; // Venta al contado
    const CREDITO = 2; // Venta a crédito

    /** Estados de pago */
    const PAGADO = 1;    // Venta pagada completamente
    const PARCIAL = 2;   // Venta pagada parcialmente
    const PENDIENTE = 3; // Venta pendiente de pago
    
    /** Tipos de venta disponibles en el sistema */
    public static $SALE_TYPE = [
        1 => 'Contado',  // Pago inmediato en el momento de la venta
        2 => 'Crédito'   // Pago aplazado, el cliente paga después
    ];

    /** Estados posibles del pago de una venta */
    public static $PAYMENT_STATUS = [
        1 => 'Pagado',    // La venta ya fue pagada completamente
        2 => 'Parcial',   // Solo se ha pagado una parte del total
        3 => 'Pendiente'  // No se ha recibido ningún pago aún
    ];

    /** Representa una venta realizada al contado (pagada en efectivo o en el momento) */
    public static $cashSale = 1;

    /** Representa una venta a crédito (el pago se realiza en una fecha posterior) */
    public static $creditSale = 2;

    public function __construct() {}

    /** Obtiene las últimas 5 ventas registradas
     *
     * @return array - Lista de ventas con detalles
     */
    public function dataTable(): array
    {
        return $this->queryMySQL(
            "SELECT 
                v.id,
                v.fecha, 
                v.total_pagado AS total, 
                u.nombre AS usuario 
            FROM 
                ventas v
            INNER JOIN 
                usuarios u 
            ON 
                v.creado_por = u.id 
            WHERE 
                v.estado = 1 
            AND
                v.tipo_venta = 1
            ORDER BY 
                v.id DESC 
            LIMIT 5"
        );
    }

    /** Verifica si el carrito de un usuario está vacío
     *
     * @param int $userId - ID del usuario a verificar
     * @return array - Detalles del carrito del usuario
     */
    public function isCartEmpty(int $userId): array
    {
        $SaleDetails = new SaleDetails();
        $details     = $SaleDetails->getSaleDetails($userId);
        return $details;
    }

    /** Calcula el total de una venta a partir de los detalles
     *
     * @param array $details - Detalles de la venta con cantidad y precio
     * @return float - Total calculado de la venta
     */
    public function calculateSaleTotal(array $details): float
    {
        $total = 0;
        foreach ($details as $detail) {
            $total += $detail['cantidad'] * $detail['precio'];
        }

        return number_format(floatval($total), 2, '.', '');
    }

    /** Actualiza los detalles de venta pendientes
     *
     * @param mysqli $conexion - Conexión a la base de datos
     * @param int $saleId - ID de la venta a actualizar
     * @param int $userId - ID del usuario que genera la venta
     * @return bool - Resultado de la operación
     */
    public function updateSaleDetails(mysqli $conexion, int $saleId, int $userId): bool
    {
        return $conexion->query(
            "UPDATE 
                detalle_venta
            SET 
                id_venta = $saleId, 
                estado = 1 
            WHERE 
                estado = 0 
            AND 
                id_venta IS NULL
            AND
                creado_por = $userId"
        );
    }

    /** Actualiza el stock de un producto al momento de registrar una venta
     *
     * @param mysqli $conexion - Conexión a la base de datos
     * @param int $idProduct - ID del producto a actualizar
     * @param int $quantity - Cantidad a restar del stock
     * @return bool - Resultado de la operación
     */
    public function updateProductStock(mysqli $conexion, int $idProduct, int $quantity): bool
    {
        return $this->executeQueryWithTransaction(
            $conexion,
            "UPDATE 
                productos
            SET 
                stock = stock - $quantity
            WHERE 
                id = $idProduct"
        );
    }

    /** Registra una nueva venta y actualiza stock de productos
     *
     * @param int $userId - ID del usuario que genera la venta.
     * @param int $branchId - ID de la sucursal donde se realiza la venta.
     * @param int $cashboxId - ID de la caja abierta.
     * @param int $saleType - Tipo de venta a realizar.
     * @param int $customerId - ID del cliente.
     * @param float $total - Total de la venta.
     */
    public function insertSale(int $userId, int $branchId, int $cashboxId, int $saleType, int|null $customerId, float $total): bool
    {
        $conexion = self::conectionMySQL();
        try {
            $conexion->begin_transaction();

            /** Información a registrar */
            $data = [
                'id_sucursal'  => $branchId,
                'id_caja'      => $cashboxId,
                'id_cliente'   => $customerId ?? 1,
                'total_venta'  => $total,
                'total_pagado' => $saleType == 1 ? $total : 0.00,
                'tipo_venta'   => $saleType,
                'estado_pago'  => $saleType == 1 ? 1 : 3,
                'fecha'        => date('Y-m-d H:i:s'),
                'creado_por'   => $userId
            ];

            /** Registramos la venta */
            $saleId = $this::insertAndGetIdWithTransaction($conexion, 'ventas', $data);

            if (!$saleId)
                throw new Exception('Error al registrar la venta');

            /** Obtenemos los productos pendientes en detalle_venta */
            $SaleDetails = new SaleDetails();
            $details     = $SaleDetails->getSaleDetails($userId);

            if (empty($details))
                throw new Exception('No hay productos para esta venta');

            /** Actualizamos los detalles de venta */
            $updateDetails = $this->updateSaleDetails($conexion, $saleId, $userId);

            if (!$updateDetails)
                throw new Exception('Error al actualizar los detalles de venta');

            /** Actualizar stock en productos */
            foreach ($details as $detail) {
                $updateStock = $this->updateProductStock($conexion, $detail['id_producto'], $detail['cantidad']);

                if (!$updateStock)
                    throw new Exception('Error al actualizar el stock del producto ID: ' . $detail['id_producto']);
            }

            /** Actualizar total de caja activa */
            $updateCashbox = CashboxCount::updateSaleTotal($conexion, $cashboxId, $total);
            if (!$updateCashbox)
                throw new Exception('Error al actualizar la caja');

            $conexion->commit();
            $conexion->close();
            return true;
        } catch (Exception $e) {
            $conexion->rollback();
            $conexion->close();
            return false;
        }
    }

    /** Actualiza el estado de una venta a inactiva
     *
     * @param mysqli $conexion - Conexión a la base de datos
     * @param int $idSale - ID de la venta a actualizar
     * @return bool - Resultado de la operación
     */
    public function updateSaleStatus(mysqli $conexion, int $idSale): bool
    {
        return $conexion->query(
            "UPDATE 
                ventas 
            SET 
                estado = 0 
            WHERE 
                id = $idSale"
        );
    }

    /** Actualiza el stock de un producto al eliminar una venta
     *
     * @param mysqli $conexion - Conexión a la base de datos
     * @param int $idProduct - ID del producto a actualizar
     * @param int $quantity - Cantidad a sumar al stock
     * @return bool - Resultado de la operación
     */
    public function updateProductStockOnDelete(mysqli $conexion, int $idProduct, int $quantity): bool
    {
        return $this->executeQueryWithTransaction(
            $conexion,
            "UPDATE 
                productos
            SET 
                stock = stock + $quantity
            WHERE 
                id = $idProduct"
        );
    }

    /** Elimina una venta y actualiza el stock de productos
     *
     * @param int $idSale - ID de la venta a eliminar
     * @param array $sale - Detalles de la venta a eliminar
     * @param array $details - Detalles de los productos vendidos
     * @return bool - Resultado de la operación
     */
    public function deleteSale(int $idSale, array $sale, array $details): bool
    {
        if (empty($idSale) || !filter_var($idSale, FILTER_VALIDATE_INT))
            return false;

        $conexion = self::conectionMySQL();
        $conexion->begin_transaction();

        try {
            /** Actualizamos el estado de la venta */
            $updatePurchase = $this->updateSaleStatus($conexion, $idSale);

            if (!$updatePurchase)
                throw new Exception('Error al actualizar el estado de la venta');

            $updateDetails = SaleDetails::updateSaleDetailStatus($conexion, $idSale);

            if (!$updateDetails)
                throw new Exception('Error al actualizar los detalles de venta');

            /** Actualizar stock en productos */
            foreach ($details as $detail) {
                $updateStock = $this->updateProductStockOnDelete($conexion, $detail['id_producto'], $detail['cantidad']);

                if (!$updateStock)
                    throw new Exception('Error al actualizar el stock del producto ID: ' . $detail['id_producto']);
            }

            /** Actualizar total de caja activa */
            $updateCashbox = CashboxCount::updateSaleTotalOnDelete($conexion, $sale['id_caja'], $sale['total_pagado'], $sale['fecha']);
            if (!$updateCashbox)
                throw new Exception('Error al actualizar la caja');

            $conexion->commit();
            return true;
        } catch (Exception $e) {
            echo "Error: " . $e->getMessage();
            $conexion->rollback();
            return false;
        } finally {
            $conexion->close();
        }
        }
}
