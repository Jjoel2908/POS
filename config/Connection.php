<?php
require_once "global.php";

class Connection {

    private static $conexion;

    public function __construct() {}

    public static function conectionMySQL() {
        if (!isset(self::$conexion)) {
            self::$conexion = new mysqli(DB_HOST, DB_USERNAME, DB_PASSWORD, DB_NAME);
            if (self::$conexion->connect_error) {
                die("Error al conectar con la base de datos: " . self::$conexion->connect_error);
            }
            // Establecer el juego de caracteres utf8
            self::$conexion->set_charset(DB_ENCODE);
        }
        return self::$conexion;
    }

    public static function closeConection() {
        if (isset(self::$conexion)) {
            self::$conexion->close();
            self::$conexion = null;
        }
    }

    public static function loginMySQL(string $table, string $field, string $value): array|null {
        // Lista blanca de tablas permitidas (modifica según tu base de datos)
        $allowedTables = ['usuarios', 'administradores']; 
        $allowedFields = ['email', 'username']; 
    
        if (!in_array($table, $allowedTables) || !in_array($field, $allowedFields)) {
            throw new InvalidArgumentException("Tabla o campo no permitido.");
        }
    
        // Escapar los nombres de tabla y campo
        $table = "`" . str_replace("`", "``", $table) . "`";
        $field = "`" . str_replace("`", "``", $field) . "`";
    
        // Especificar las columnas necesarias para login (evita exponer datos innecesarios)
        $sql = "SELECT id, nombre, email, password FROM $table WHERE $field = ? AND estado = 1 LIMIT 1";
    
        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);
    
        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }
    
        $stmt->bind_param('s', $value);
        $stmt->execute();
    
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        $stmt->close();
        self::closeConection();
    
        return $row ?: null;
    }    

    public static function insert(string $table, array $data): bool {
        // Lista blanca de tablas permitidas (modifica según tu base de datos)
        $allowedTables = ['clientes', 'usuarios', 'pedidos']; 
    
        if (!in_array($table, $allowedTables)) {
            throw new InvalidArgumentException("Tabla no permitida.");
        }
    
        // Escapar el nombre de la tabla
        $table = "`" . str_replace("`", "``", $table) . "`";
    
        $columns = implode(', ', array_map(fn($col) => "`" . str_replace("`", "``", $col) . "`", array_keys($data)));
        $values = str_repeat('?, ', count($data) - 1) . '?';
    
        // Generar consulta segura
        $sql = "INSERT INTO $table ($columns) VALUES ($values)";
        
        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);
    
        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }
    
        // Determinar tipos de datos dinámicamente
        $types = '';
        $params = [];
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= 'i'; // Entero
            } elseif (is_float($value)) {
                $types .= 'd'; // Decimal
            } else {
                $types .= 's'; // String
            }
            $params[] = $value;
        }
    
        $stmt->bind_param($types, ...$params);
    
        // Ejecutar y verificar éxito
        $success = $stmt->execute();
        if (!$success) {
            throw new RuntimeException("Error en execute(): " . $stmt->error);
        }
    
        $stmt->close();
        self::closeConection();
    
        return $success;
    }
    

    public static function insertAndGetId(string $table, array $data): ?int {
        $conexion = self::conectionMySQL();
        $colums = implode(', ', array_keys($data));
        $values = str_repeat('?, ', count($data) - 1) . '?';
        $types = str_repeat('s', count($data));
        $sql = "INSERT INTO $table ($colums) VALUES ($values)";
        $stmt = $conexion->prepare($sql);
        
        if ($stmt === false) return NULL;
        
        $stmt->bind_param($types, ...array_values($data));
        $success = $stmt->execute();
        
        if ($success) {
            $insert_id = $stmt->insert_id;
        } else $insert_id = NULL;
        
        $stmt->close();
        self::closeConection();
        
        return $insert_id;
    }

    public static function update(string $table, int $id, array $data): bool {
        // Lista blanca de tablas permitidas (modifica según tu base de datos)
        $allowedTables = ['clientes', 'usuarios', 'pedidos']; 
    
        if (!in_array($table, $allowedTables)) {
            throw new InvalidArgumentException("Tabla no permitida.");
        }
    
        // Escapar el nombre de la tabla
        $table = "`" . str_replace("`", "``", $table) . "`";
    
        // Escapar nombres de columnas y construir la parte SET de la consulta
        $setValues = [];
        foreach ($data as $key => $value) {
            $safeKey = "`" . str_replace("`", "``", $key) . "`";
            $setValues[] = "$safeKey=?";
        }
        $setClause = implode(', ', $setValues);
    
        // Generar consulta segura
        $sql = "UPDATE $table SET $setClause WHERE id=?";
        
        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);
    
        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }
    
        // Determinar tipos de datos dinámicamente
        $types = '';
        $params = [];
        foreach ($data as $value) {
            if (is_int($value)) {
                $types .= 'i'; // Entero
            } elseif (is_float($value)) {
                $types .= 'd'; // Decimal
            } else {
                $types .= 's'; // String
            }
            $params[] = $value;
        }
        $types .= 'i'; // Tipo de `id`, que siempre será un entero
        $params[] = $id;
    
        $stmt->bind_param($types, ...$params);
    
        // Ejecutar y verificar éxito
        $success = $stmt->execute();
        if (!$success) {
            throw new RuntimeException("Error en execute(): " . $stmt->error);
        }
    
        $stmt->close();
        self::closeConection();
    
        return $success;
    }    
    
    public static function delete(string $table, int $id): bool {
        // Lista blanca de tablas permitidas
        $hardDeleteTables = ['detalle_compra', 'detalle_venta']; 
        $softDeleteTables = ['usuarios', 'productos', 'categorias']; // Agrega más si es necesario
    
        if (!in_array($table, array_merge($hardDeleteTables, $softDeleteTables))) {
            throw new InvalidArgumentException("Tabla no permitida.");
        }
    
        // Escapar el nombre de la tabla
        $table = "`" . str_replace("`", "``", $table) . "`";
    
        // Definir consulta según el tipo de eliminación
        if (in_array($table, $hardDeleteTables)) {
            $sql = "DELETE FROM $table WHERE id = ?";
        } else {
            $sql = "UPDATE $table SET estado = 0 WHERE id = ?";
        }
    
        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);
    
        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }
    
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
    
        if (!$success) {
            throw new RuntimeException("Error en execute(): " . $stmt->error);
        }
    
        $stmt->close();
        self::closeConection();
    
        return $success;
    }    

    public static function select(string $table, int $id, array $columns = ['*']): array|null {
        // Lista blanca de tablas permitidas
        $allowedTables = ['usuarios', 'productos', 'categorias']; 
        $allowedColumns = ['id', 'nombre', 'email', 'precio', 'estado']; // Agrega según tu BD
    
        if (!in_array($table, $allowedTables)) {
            throw new InvalidArgumentException("Tabla no permitida.");
        }
    
        // Si `*` está en las columnas, seleccionamos todas las permitidas
        if (in_array('*', $columns)) {
            $columns = $allowedColumns;
        } else {
            // Filtrar columnas permitidas
            $columns = array_intersect($columns, $allowedColumns);
            if (empty($columns)) {
                throw new InvalidArgumentException("Ninguna columna válida seleccionada.");
            }
        }
    
        // Escapar los nombres de tabla y columnas
        $table = "`" . str_replace("`", "``", $table) . "`";
        $columns = array_map(fn($col) => "`" . str_replace("`", "``", $col) . "`", $columns);
        $cols = implode(', ', $columns);
    
        $sql = "SELECT $cols FROM $table WHERE id = ? AND estado = 1 LIMIT 1";
    
        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);
    
        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }
    
        $stmt->bind_param('i', $id);
        $stmt->execute();
        
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
    
        $stmt->close();
        self::closeConection();
    
        return $row ?: null;
    }    
    
    public static function selectAll(string $table, array $columns = ['*']): array {
        // Lista blanca de tablas permitidas
        $allowedTables = ['usuarios', 'productos', 'categorias']; 
        $allowedColumns = ['id', 'nombre', 'email', 'precio', 'estado']; // Agrega según tu BD
    
        if (!in_array($table, $allowedTables)) {
            throw new InvalidArgumentException("Tabla no permitida.");
        }
    
        // Si `*` está en las columnas, seleccionamos todas las permitidas
        if (in_array('*', $columns)) {
            $columns = $allowedColumns;
        } else {
            // Filtrar columnas permitidas
            $columns = array_intersect($columns, $allowedColumns);
            if (empty($columns)) {
                throw new InvalidArgumentException("Ninguna columna válida seleccionada.");
            }
        }
    
        // Escapar los nombres de tabla y columnas
        $table = "`" . str_replace("`", "``", $table) . "`";
        $columns = array_map(fn($col) => "`" . str_replace("`", "``", $col) . "`", $columns);
        $cols = implode(', ', $columns);
    
        $sql = "SELECT $cols FROM $table WHERE estado = 1";
    
        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);
    
        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
        
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    
        $stmt->close();
        self::closeConection();
    
        return $rows;
    }      
    
    public static function selectAllBySucursal(string $table, int $idSucursal): array {
        // Lista blanca de tablas permitidas
        $allowedTables = ['usuarios', 'productos', 'categorias', 'ventas']; 
    
        if (!in_array($table, $allowedTables)) {
            throw new InvalidArgumentException("Tabla no permitida.");
        }
    
        // Escapar correctamente el nombre de la tabla
        $table = "`" . str_replace("`", "``", $table) . "`";
    
        $sql = "SELECT * FROM $table WHERE estado = 1 AND id_sucursal = ? ORDER BY id DESC";
    
        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);
    
        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }
    
        $stmt->bind_param('i', $idSucursal);
        $stmt->execute();
        $result = $stmt->get_result();
    
        $rows = [];
        while ($row = $result->fetch_assoc()) {
            $rows[] = $row;
        }
    
        $stmt->close();
        self::closeConection();
    
        return $rows;
    }    

    public static function queryMySQL(string $query, array $params = []): array|bool {
        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($query);
    
        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }
    
        if (!empty($params)) {
            $types = '';
            $bindValues = [];
    
            foreach ($params as $param) {
                $types .= is_int($param) ? 'i' : 's';
                $bindValues[] = $param;
            }
    
            $stmt->bind_param($types, ...$bindValues);
        }
    
        $stmt->execute();
        $result = $stmt->get_result();
    
        if ($result instanceof mysqli_result) {
            $filas = [];
            while ($fila = $result->fetch_assoc()) {
                $filas[] = $fila;
            }
            $result->free();
            $stmt->close();
            self::closeConection();
            return $filas;
        } else {
            $stmt->close();
            self::closeConection();
            return true;
        }
    }    

    public static function exists(string $table, string $field, mixed $value): bool {
        // Lista blanca de tablas y columnas permitidas
        $allowedTables = ['usuarios', 'productos', 'categorias', 'ventas'];
        $allowedColumns = ['id', 'nombre', 'email', 'precio', 'estado']; // Agrega según tu BD
    
        if (!in_array($table, $allowedTables)) {
            throw new InvalidArgumentException("Tabla no permitida.");
        }
    
        if (!in_array($field, $allowedColumns)) {
            throw new InvalidArgumentException("Columna no permitida.");
        }
    
        // Escapar correctamente nombres de tabla y columna
        $table = "`" . str_replace("`", "``", $table) . "`";
        $field = "`" . str_replace("`", "``", $field) . "`";
    
        $sql = "SELECT 1 FROM $table WHERE $field = ? AND estado = 1 LIMIT 1";
    
        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);
    
        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }
    
        // Determinar tipo de dato de $value
        $type = is_int($value) ? 'i' : 's';
        $stmt->bind_param($type, $value);
        
        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->fetch_assoc() !== null;
    
        $stmt->close();
        self::closeConection();
    
        return $exists;
    }    

    /** Verifica si existe un registro en una tabla específica con un valor en un campo específico, 
     * en la columna 'id_sucursal' y con estado activo (estado = 1).
     *
     * @param string $table Nombre de la tabla donde buscar el registro.
     * @param string $field Nombre del campo en el cual se buscará el valor.
     * @param mixed $value Valor que se busca en el campo especificado.
     * @param int $idSucursal Valor de 'id_sucursal' que se busca.
     * @return bool Retorna true si el registro existe y tiene estado activo, false en caso contrario.
     */
    public static function existsByFieldAndSucursal(string $table, string $field, $value, int $idSucursal): bool
    {
        /** Listado de tablas y columnas permitidas para evitar inyección SQL */
        $allowedTables = ['categorias'];
        $allowedFields = ['nombre'];

        if (!in_array($table, $allowedTables) || !in_array($field, $allowedFields)) {
            throw new InvalidArgumentException("Tabla o campo no permitido.");
        }

        /** Escapar nombres de tabla y campo para evitar inyecciones SQL */
        $table = "`" . str_replace("`", "``", $table) . "`";
        $field = "`" . str_replace("`", "``", $field) . "`";

        $conexion = self::conectionMySQL();
        $sql = "SELECT 1 FROM $table WHERE $field = ? AND id_sucursal = ? AND estado = 1 LIMIT 1";
        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException("Error en la preparación de la consulta: " . $conexion->error);
        }

        // Determinar el tipo de dato de $value
        $type = is_int($value) ? 'i' : 's';
        $stmt->bind_param($type . 'i', $value, $idSucursal);

        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;

        $stmt->close();
        self::closeConection();

        return $exists;
    }

    public static function validateData(array $data_required, array $data): bool {
        foreach ($data_required as $required) {
            if (empty($data[$required])) {
                return false; // Si encuentra un campo vacío, retorna false indicando que los datos no son válidos.
            }
        }
        return true; // Si no se encontraron campos vacíos, retorna true, indicando que los datos son válidos.
    }
}

?>
