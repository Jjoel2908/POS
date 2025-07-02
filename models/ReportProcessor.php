<?php
require_once '../config/Connection.php';

class ReportProcessor extends Connection
{
    public function __construct(private ?string $dateRange = '')
    {
        $this->setRange($dateRange);
    }

    private function setRange(string $dateRange): void {
        if (!empty($dateRange)) {
            $param = explode(' - ', $dateRange);
            $this->startDate = date('Y-m-d 00:00:00', strtotime(str_replace('/', '-', $param[0])));
            $this->endDate   = date('Y-m-d 23:59:59', strtotime(str_replace('/', '-', $param[1])));
        }
    }  

    public function getAllPurchases(){}
    public function getAllSales(){}
    public function getAllExpenses(){}
    public function getAllReport(){}

    public function getReportExpenses(?int $expenseType = null, ?int $idSucursal = null): array
    {
        $where = "g.fecha BETWEEN '{$this->startDate}' AND '{$this->endDate}' AND g.estado = 1";
        if ($idSucursal) {
            $where .= " AND g.id_sucursal = {$idSucursal}";
        }
        if ($expenseType) {
            $where .= " AND g.id_tipo_gasto = {$expenseType}";
        }

        $totalResult = $this->queryMySQL("
            SELECT SUM(g.monto) AS total_expenses
            FROM gastos g
            INNER JOIN tipos_gastos tg ON g.id_tipo_gasto = tg.id
            WHERE $where
        ");

        $table = $this->queryMySQL("
            SELECT 
                g.*,
                tg.nombre AS concepto,
                s.nombre AS sucursal
            FROM gastos g
            INNER JOIN tipos_gastos tg ON g.id_tipo_gasto = tg.id
            INNER JOIN sucursales s ON g.id_sucursal = s.id
            WHERE $where
            ORDER BY g.fecha ASC
        ");

        $total = $totalResult[0]['total_expenses'] ?? 0.0;
        $dateDiff = (new DateTime($this->startDate))->diff(new DateTime($this->endDate))->days + 1;
        $averagePerDay = $dateDiff > 0 ? $total / $dateDiff : 0.0;

        return [
            "total"          => number_format($total, 2),
            "averagePerDay"  => number_format($averagePerDay, 2),
            "table"          => $table
        ];
    }

    /** Obtiene los detalles de los gastos filtrados por rango de fechas, sucursal y tipo de gasto.
     * @param int|null $expenseType (Opcional) Identificador del tipo de gasto para filtrar. Si es null, incluye todos los tipos.
     * @param int|null $idSucursal (Opcional) Identificador de la sucursal para filtrar. Si es null, incluye todas las sucursales.
     * @return array Lista de gastos que cumplen con los filtros aplicados.
     */
    public function getReportExpenses(?int $expenseType = null, ?int $idSucursal = null): array
    {
        /** Construcción de condiciones dinámicas para sucursal y tipo de gasto */
        $conditionSucursal  = $idSucursal ? "AND g.id_sucursal = {$idSucursal}" : "";
        $conditionCategoria = $expenseType ? "AND g.id_tipo_gasto = {$expenseType}" : "";

        /** Total de gastos en el rango de fechas con filtros opcionales */
        $totalWidget = $this->queryMySQL(
            "SELECT 
                FORMAT(IFNULL(SUM(g.monto), 0.00), 2) AS total_expenses
            FROM 
                gastos g
            INNER JOIN 
                tipos_gastos tg ON g.id_tipo_gasto = tg.id
            WHERE 
                g.fecha >= '{$this->startDate}' AND 
                g.fecha <= '{$this->endDate}' AND 
                g.estado = 1 
                {$conditionSucursal} 
                {$conditionCategoria}"
        );

        /** Tabla detallada de gastos en el rango de fechas con filtros opcionales */
        $table = $this->queryMySQL(
            "SELECT 
                g.*,
                tg.nombre AS concepto,
                s.nombre AS sucursal
            FROM 
                gastos g
            INNER JOIN 
                tipos_gastos tg ON g.id_tipo_gasto = tg.id
            INNER JOIN 
                sucursales s ON g.id_sucursal = s.id
            WHERE
                g.fecha >= '{$this->startDate}' AND 
                g.fecha <= '{$this->endDate}' AND 
                g.estado = 1
                {$conditionSucursal} 
                {$conditionCategoria}
            ORDER BY g.fecha ASC"
        );

        /** Retornar el reporte */
        return [
            "total"    => $totalWidget[0]['total_expenses'],
            "table"    => $table
        ];
    }

    /** Genera un reporte de ventas en un rango de fechas, con opción de filtrar por sucursal, producto y tipo de venta.
     *
     * @param int|null $idSucursal  ID de la sucursal (opcional, si es null muestra todas).
     * @param int|null $idProducto  ID del producto (opcional, si es null muestra todos).
     * @param int|null $tipoVenta   Tipo de venta (opcional, si es null muestra todas; 
     *                              1 - Mostrador, 2 - Repartidor, etc.).
     * @return array Retorna el total de ventas y la tabla con el detalle de ventas agrupado.
     */
    public function getReportSales(?int $idSucursal = null, ?int $idProducto = null, ?int $tipoVenta = null): array
    {}

    /** Obtiene el detalle de ventas en un rango de fechas y con filtros opcionales.
     * Los filtros se definen mediante el parámetro queryType:
     *   - 1: Filtra por producto (se espera id_producto).
     *   - 2: Filtra por sucursal (se espera id_sucursal).
     *   - 3: Filtra por tipo de venta (se espera tipo_venta).
     *
     * @return array Lista de registros de ventas.
     */
    public function getSalesDetails(int $idSucursal, int $idProducto, int $tipoVenta): array
    {

    }
}