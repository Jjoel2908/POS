<?php
require_once '../config/Connection.php';

class ReportProcessor extends Connection
{
    /** @var string $startDate Fecha de inicio del reporte en formato 'Y-m-d */
    private string $startDate = '';

    /** @var string $endDate Fecha de fin del reporte en formato 'Y-m-d' */
    private string $endDate   = '';

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

    /** Colores por tipo de venta */
    public static $SALE_TYPE_COLORS = [
        1 => 'bg-success',         // Contado
        2 => 'bg-info text-black'  // Crédito
    ];

    /** Colores por estado de pago */
    public static $PAYMENT_STATUS_COLORS = [
        1 => 'bg-success',             // Pagado
        2 => 'bg-warning text-dark',   // Parcial
        3 => 'bg-danger'               // Pendiente
    ];

    /** Constructor que inicializa las fechas de inicio y fin del reporte.
     *
     * Utiliza el método `sanitizeInput` para limpiar y validar los datos de entrada.
     * Si las fechas son válidas, se asignan a las propiedades `startDate` y `endDate`.
     */
    public function __construct()
    {
        $cleanedData = self::sanitizeInput('dateRange', 'daterange');
        if ($cleanedData) {
            $this->startDate = $cleanedData['start'];
            $this->endDate   = $cleanedData['end'];
        }
    }

    /** Genera un reporte de compras realizadas en un rango de fechas específico.
     *
     * Obtiene el total de las compras confirmadas (estado = 1) y una lista detallada 
     * con información de cada compra, incluyendo el usuario que la realizó.
     *
     * @return array Arreglo asociativo con:
     *               - 'total': Monto total de las compras formateado a 2 decimales.
     *               - 'table': Lista de compras con sus respectivos datos y nombre del usuario.
     */
    public function getAllPurchases(): array
    {
        $where = "c.fecha >= '{$this->startDate}' AND c.fecha <= '{$this->endDate}' AND c.estado = 1";

        /** Total de compras */
        $totalWidget = $this->queryMySQL("SELECT FORMAT(IFNULL(SUM(total), 0), 2) AS total_purchase FROM compras AS c WHERE $where");

        /** Detalles de compras */
        $purchases = $this->queryMySQL(
            "SELECT 
                c.id,
                c.total,
                c.fecha,
                c.estado,
                u.nombre AS comprador
            FROM 
                compras AS c
            INNER JOIN 
                usuarios AS u 
            ON 
                c.creado_por = u.id
            WHERE 
                $where
        "
        );

        return [
            "total" => $totalWidget[0]['total_purchase'] ?? "0.00",
            "table" => $purchases,
        ];
    }

    /** Genera un reporte de ventas realizadas en un rango de fechas específico.
     *
     * Obtiene el total de las ventas confirmadas (estado = 1) y una lista detallada 
     * con información de cada venta, incluyendo el usuario que la realizó.
     *
     * @return array Arreglo asociativo con:
     *               - 'total': Monto total de las ventas formateado a 2 decimales.
     *               - 'table': Lista de ventas con sus respectivos datos y nombre del usuario.
     */
    public function getAllSales(): array
    {
        $where = "v.fecha >= '{$this->startDate}' AND v.fecha <= '{$this->endDate}' AND v.estado = 1";

        /** Total de ventas */
        $totalWidget = $this->queryMySQL("SELECT FORMAT(IFNULL(SUM(total_venta), 0), 2) AS total_sales FROM ventas AS v WHERE $where");

        /** Detalles de ventas */
        $sales = $this->queryMySQL(
            "SELECT 
                v.id,
                v.total_venta AS total,
                v.tipo_venta,
                v.estado_pago AS estatus,
                v.fecha,
                CONCAT(c.nombre, ' ', c.apellidos) AS cliente
            FROM 
                ventas AS v
            INNER JOIN 
                clientes AS c
            ON 
                v.id_cliente = c.id
            WHERE 
                $where
        "
        );

        return [
            "total" => $totalWidget[0]['total_sales'] ?? "0.00",
            "table" => $sales,
        ];
    }

    /** Genera un reporte de gastos realizadas en un rango de fechas específico.
     *
     * Obtiene el total de los gastos confirmadas (estado = 1) y una lista detallada 
     * con información de cada gasto, incluyendo el usuario que la realizó.
     *
     * @return array Arreglo asociativo con:
     *               - 'total': Monto total de los gastos formateado a 2 decimales.
     *               - 'table': Lista de gastos con sus respectivos datos y nombre del usuario.
     */
    public function getAllExpenses(): array
    {
        $where = "g.fecha >= '{$this->startDate}' AND g.fecha <= '{$this->endDate}' AND g.estado = 1";

        /** Total de gastos */
        $totalWidget = $this->queryMySQL("SELECT FORMAT(IFNULL(SUM(monto), 0), 2) AS total_expenses FROM gastos AS g WHERE $where");

        /** Detalles de gastos */
        $expenses = $this->queryMySQL(
            "SELECT 
                g.id,
                g.monto,
                g.fecha,
                g.observaciones,
                tg.nombre AS concepto,
                u.nombre AS usuario
            FROM 
                gastos AS g
            INNER JOIN 
                tipos_gasto AS tg 
            ON 
                g.id_tipo_gasto = tg.id
            INNER JOIN 
                usuarios AS u
            ON 
                g.creado_por = u.id
            WHERE 
                $where
        "
        );

        return [
            "total" => $totalWidget[0]['total_expenses'] ?? "0.00",
            "table" => $expenses,
        ];
    }

    /** Genera un resumen general de las compras, ventas y gastos en un rango de fechas específico.
     *
     * Calcula el total de compras, ventas al contado, ventas a crédito, gastos y la venta neta.
     * La venta neta se calcula como la suma de las ventas menos los gastos.
     *
     * @return array Arreglo asociativo con:
     *               - 'total_compras': Total de compras formateado a 2 decimales.
     *               - 'total_ventas_contado': Total de ventas al contado formateado a 2 decimales.
     *               - 'total_ventas_credito': Total de ventas a crédito formateado a 2 decimales.
     *               - 'total_pendiente_cobro': Total pendiente de cobro por ventas a crédito formateado a 2 decimales.
     *               - 'total_gastos': Total de gastos formateado a 2 decimales.
     *               - 'venta_neta': Venta neta calculada y formateada a 2 decimales.
     */
    public function getGeneralSummary(): array
    {
        $whereCompras = "c.fecha >= '{$this->startDate}' AND c.fecha <= '{$this->endDate}' AND c.estado = 1";
        $whereVentas  = "v.fecha >= '{$this->startDate}' AND v.fecha <= '{$this->endDate}' AND v.estado = 1";
        $whereGastos  = "g.fecha >= '{$this->startDate}' AND g.fecha <= '{$this->endDate}' AND g.estado = 1";

        /** Total de compras */
        $compras = $this->queryMySQL(
            "SELECT FORMAT(IFNULL(SUM(c.total), 0), 2) AS total_compras
            FROM compras AS c
            WHERE $whereCompras
        ");

        /** Ventas de contado */
        $ventasContado = $this->queryMySQL(
            "SELECT FORMAT(IFNULL(SUM(v.total_venta), 0), 2) AS total_ventas_contado
            FROM ventas AS v
            WHERE $whereVentas AND v.tipo_venta = 1
        ");

        /** Ventas a crédito y pendientes */
        $ventasCredito = $this->queryMySQL(
            "SELECT 
                FORMAT(IFNULL(SUM(v.total_venta), 0), 2) AS total_ventas_credito,
                FORMAT(IFNULL(SUM(v.total_venta - v.total_pagado), 0), 2) AS total_pendiente_cobro
            FROM ventas AS v
            WHERE $whereVentas AND v.tipo_venta = 2
        ");

        /** Total de gastos */
        $gastos = $this->queryMySQL(
            "SELECT FORMAT(IFNULL(SUM(g.monto), 0), 2) AS total_gastos
            FROM gastos AS g
            WHERE $whereGastos
        ");

        /** Gastos por tipo de gasto */
        $gastosPorTipo = $this->queryMySQL(
            "SELECT 
                tg.nombre AS tipo,
                SUM(g.monto) AS total
            FROM gastos AS g
            INNER JOIN tipos_gasto AS tg ON g.id_tipo_gasto = tg.id
            WHERE $whereGastos
            GROUP BY g.id_tipo_gasto, tg.nombre
            ORDER BY total DESC
        ");

        /** Productos más vendidos */
        $topProductos = $this->queryMySQL(
            "SELECT 
                p.nombre AS producto,
                SUM(dv.cantidad) AS total_vendido
            FROM detalle_venta dv
            INNER JOIN ventas v ON dv.id_venta = v.id
            INNER JOIN productos p ON dv.id_producto = p.id
            WHERE 
                v.fecha >= '{$this->startDate}' 
                AND v.fecha <= '{$this->endDate}' 
                AND v.estado = 1
            GROUP BY dv.id_producto, p.nombre
            ORDER BY total_vendido DESC
            LIMIT 5
        ");

        /** Conversión a flotantes para cálculo de venta neta */
        $ventaNeta = (
            floatval(str_replace(',', '', $ventasContado[0]['total_ventas_contado'] ?? 0)) +
            floatval(str_replace(',', '', $ventasCredito[0]['total_ventas_credito'] ?? 0))
        ) - floatval(str_replace(',', '', $gastos[0]['total_gastos'] ?? 0));

        return [
            "total_compras"         => $compras[0]['total_compras'] ?? "0.00",
            "total_ventas_contado"  => $ventasContado[0]['total_ventas_contado'] ?? "0.00",
            "total_ventas_credito"  => $ventasCredito[0]['total_ventas_credito'] ?? "0.00",
            "total_pendiente_cobro" => $ventasCredito[0]['total_pendiente_cobro'] ?? "0.00",
            "total_gastos"          => $gastos[0]['total_gastos'] ?? "0.00",
            "venta_neta"            => number_format($ventaNeta, 2),
            "gastos_por_tipo"       => $gastosPorTipo,
            "top_productos"         => $topProductos,
        ];
    }
}