<?php
require_once "global.php";

class Connection
{

    private static $conexion;

    public function __construct() {}

    public static function conectionMySQL()
    {
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

    public static function closeConection()
    {
        if (isset(self::$conexion)) {
            self::$conexion->close();
            self::$conexion = null;
        }
    }

    public static function loginMySQL(string $table, string $value): array|null
    {
        $conexion = self::conectionMySQL();

        /** Escapar correctamente nombre de tabla */
        $table = "`" . str_replace("`", "``", $table) . "`";
        $sql = "SELECT * FROM $table WHERE correo = ? AND estado = 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('s', $value);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        self::closeConection();
        return $row;
    }

    public static function insert(string $table, array $data): bool
    {
        /** Agregamos la fecha de creación */
        if ($table != "detalle_compra" && $table != "detalle_venta")
            $data['fecha'] = date('Y-m-d H:i:s');

        /** Agregamos el usuario */
        $data['creado_por'] = filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0;

        /** Escapar correctamente nombres de tabla y columna */
        $table = "`" . str_replace("`", "``", $table) . "`";

        $conexion = self::conectionMySQL();
        $colums = implode(', ', array_keys($data));
        $values = str_repeat('?, ', count($data) - 1) . '?';
        $types = str_repeat('s', count($data));
        $sql = "INSERT INTO $table ($colums) VALUES ($values)";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param($types, ...array_values($data));
        $success = $stmt->execute();
        $stmt->close();
        self::closeConection();
        return $success;
    }

    public static function insertAndGetId(string $table, array $data): ?int
    {
        /** Escapar correctamente nombres de tabla y columna */
        $table = "`" . str_replace("`", "``", $table) . "`";

        /** Agregamos la fecha de creación */
        $data['fecha'] = date('Y-m-d H:i:s');

        /** Agregamos el usuario */
        $data['creado_por'] = filter_var($_SESSION['id'], FILTER_VALIDATE_INT) ?: 0;

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

    public static function update(string $table, int $id, array $data): bool
    {
        $conexion = self::conectionMySQL();
        $setValues = '';
        foreach ($data as $key => $value) {
            $setValues .= "$key=?, ";
        }
        $setValues = rtrim($setValues, ', ');

        $types = str_repeat('s', count($data));
        $sql = "UPDATE $table SET $setValues WHERE id=?";
        $stmt = $conexion->prepare($sql);
        $bindValues = array_values($data);
        $bindValues[] = $id;
        $stmt->bind_param($types . 'i', ...$bindValues);
        $success = $stmt->execute();
        $stmt->close();
        self::closeConection();
        return $success;
    }

    public static function delete(string $table, int $id): bool
    {
        $conexion = self::conectionMySQL();
        if ($table === "detalle_compra" || $table === "detalle_venta") {
            $sql = "DELETE FROM $table WHERE id=?";
        } else {
            $sql = "UPDATE $table SET estado = 0 WHERE id=?";
        }
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $success = $stmt->execute();
        $stmt->close();
        self::closeConection();
        return $success;
    }

    public static function select(string $table, int $id): array|null
    {
        /** Escapar correctamente nombres de tabla y columna */
        $table = "`" . str_replace("`", "``", $table) . "`";
        $conexion = self::conectionMySQL();
        $sql = "SELECT * FROM $table WHERE id = ? AND estado = 1 LIMIT 1";
        $stmt = $conexion->prepare($sql);
        $stmt->bind_param('i', $id);
        $stmt->execute();
        $result = $stmt->get_result();
        $row = $result->fetch_assoc();
        $stmt->close();
        self::closeConection();
        return $row;
    }

    public static function selectAll(string $table): array
    {
        $conexion = self::conectionMySQL();

        /** Escapar correctamente el nombre de la tabla */
        $table = "`" . str_replace("`", "``", $table) . "`";

        $sql = "SELECT * FROM $table WHERE estado = 1";
        $result = $conexion->query($sql);

        if (!$result) {
            self::closeConection();
            return [];
        }

        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $result->free();

        self::closeConection();

        return $rows ?: [];
    }

    public static function selectAllBySucursal(string $table, int $idSucursal): array
    {
        /** Escapar correctamente el nombre de la tabla */
        $table = "`" . str_replace("`", "``", $table) . "`";

        $sql = "SELECT * FROM $table WHERE estado = 1 AND id_sucursal = ? ORDER BY id DESC";

        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }

        if (!$stmt->bind_param('i', $idSucursal)) {
            throw new RuntimeException("Error en bind_param(): " . $stmt->error);
        }

        if (!$stmt->execute()) {
            throw new RuntimeException("Error en execute(): " . $stmt->error);
        }

        $result = $stmt->get_result();
        $rows = $result->fetch_all(MYSQLI_ASSOC);

        $result->free();
        $stmt->close();
        self::closeConection();

        return $rows;
    }

    public static function queryMySQL(string $query): array|bool|null
    {
        $conexion = self::conectionMySQL();
        $resultado = $conexion->query($query);

        if ($resultado instanceof mysqli_result) {
            $filas = [];
            while ($fila = $resultado->fetch_assoc()) {
                $filas[] = $fila;
            }
            $resultado->free();
            return $filas;
        } else {
            return true;
        }
    }

    public static function exists(string $table, string $field, string $value): bool
    {
        /** Escapar correctamente nombres de tabla y columna */
        $table = "`" . str_replace("`", "``", $table) . "`";
        $field = "`" . str_replace("`", "``", $field) . "`";

        $sql = "SELECT 1 FROM $table WHERE $field = ? AND estado = 1 LIMIT 1";

        $conexion = self::conectionMySQL();
        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException("Error en prepare(): " . $conexion->error);
        }

        /** Determinar tipo de dato de $value */
        $type = is_int($value) ? 'i' : 's';
        $stmt->bind_param($type, $value);

        $stmt->execute();
        $result = $stmt->get_result();
        $exists = $result->fetch_assoc() !== null;

        $stmt->close();
        self::closeConection();

        return $exists;
    }

    /** Verifica si existe un registro en una tabla específica con un valor en un campo específico, en la columna 'id_sucursal', 
     * y con estado activo (estado = 1).
     *
     * @param string $table El nombre de la tabla donde buscar el registro.
     * @param string $field El nombre del campo en el cual se buscará el valor.
     * @param mixed $value El valor que se busca en el campo especificado.
     * @param int $idSucursal El valor de 'id_sucursal' que se busca.
     * @return bool Retorna true si el registro existe y tiene estado activo, false en caso contrario.
     */
    public static function existsByFieldAndSucursal(string $table, string $field, $value, int $idSucursal): bool
    {
        /** Escapar nombres de tabla y campo para evitar inyecciones SQL */
        $table = "`" . str_replace("`", "``", $table) . "`";
        $field = "`" . str_replace("`", "``", $field) . "`";
        $conexion = self::conectionMySQL();
        $sql = "SELECT 1 FROM $table WHERE $field = ? AND id_sucursal = ? AND estado = 1 LIMIT 1";
        $stmt = $conexion->prepare($sql);

        if (!$stmt) {
            throw new RuntimeException("Error en la preparación de la consulta: " . $conexion->error);
        }

        /** Determinar el tipo de dato de $value */
        $type = is_int($value) ? 'i' : 's';
        $stmt->bind_param($type . 'i', $value, $idSucursal);

        $stmt->execute();
        $stmt->store_result();
        $exists = $stmt->num_rows > 0;

        $stmt->close();
        self::closeConection();

        return $exists;
    }

    public static function sanitizeInput($key, $type = 'text')
    {
        if (!isset($_POST[$key])) return null;

        $value = trim($_POST[$key]);

        switch ($type) {
            case 'text':
                return preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]/', '', $value);
            case 'name':
                return ucwords(strtolower(preg_replace('/[^a-zA-ZáéíóúÁÉÍÓÚñÑ0-9 ]/', '', $value)));
            case 'phone':
                return preg_replace('/\D/', '', $value);
            case 'email':
                return filter_var($value, FILTER_VALIDATE_EMAIL) ?: null;
            case 'password':
                return preg_replace('/[^a-zA-Z0-9@#\$%\&\*\!\.\-\_]/', '', $value);
            case 'int':
                return filter_var($value, FILTER_VALIDATE_INT) ?: 0;
            case 'float':
                $value = preg_replace('/[^0-9\.]/', '', $value);

                if (substr_count($value, '.') > 1)
                    $value = substr($value, 0, strrpos($value, '.'));

                if ($value === '' || $value === '.')
                    return 0.00;

                return number_format((float)$value, 2, '.', '');
        }

        return $value;
    }

    public static function validateData(array $data_required, array $data): bool
    {
        foreach ($data_required as $required) {
            if (empty($data[$required])) {
                return false; // Si encuentra un campo vacío, retorna false indicando que los datos no son válidos.
            }
        }
        return true; // Si no se encontraron campos vacíos, retorna true, indicando que los datos son válidos.
    }

    public static function beginTransaction()
    {
        $conexion = self::conectionMySQL();
        $conexion->begin_transaction();
    }

    /** Ejecuta un INSERT dentro de una transacción.
     * Devuelve `true` si se ejecuta correctamente.
     */
    public static function insertWithTransaction(mysqli $conexion, string $table, array $data): bool
    {
        try {
            $columns = implode(', ', array_keys($data));
            $values  = str_repeat('?, ', count($data) - 1) . '?';
            $types   = str_repeat('s', count($data));
            $table   = "`" . str_replace("`", "``", $table) . "`";
            $sql     = "INSERT INTO $table ($columns) VALUES ($values)";
            $stmt    = $conexion->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Error al preparar la consulta: " . $conexion->error);
            }

            $stmt->bind_param($types, ...array_values($data));

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar el INSERT: " . $stmt->error);
            }

            $stmt->close();
            return true;
        } catch (Exception $e) {
            error_log("Error en insertWithTransaction: " . $e->getMessage());
            return false;
        }
    }

     /** Ejecuta un INSERT dentro de una transacción y devuelve el ID del último registro insertado.
     * Devuelve `NULL` si ocurre un error.
     */
    public static function insertAndGetIdWithTransaction(mysqli $conexion, string $table, array $data): ?int
    {
        try {
            $columns = implode(', ', array_keys($data));
            $values = str_repeat('?, ', count($data) - 1) . '?';
            $types = str_repeat('s', count($data));
            $table   = "`" . str_replace("`", "``", $table) . "`";
            $sql = "INSERT INTO $table ($columns) VALUES ($values)";
            $stmt = $conexion->prepare($sql);

            if ($stmt === false) {
                throw new Exception("Error al preparar la consulta: " . $conexion->error);
            }

            $stmt->bind_param($types, ...array_values($data));

            if (!$stmt->execute()) {
                throw new Exception("Error al ejecutar el INSERT: " . $stmt->error);
            }

            $insert_id = $stmt->insert_id;
            $stmt->close();

            return $insert_id;
        } catch (Exception $e) {
            error_log("Error en insertAndGetIdWithTransaction: " . $e->getMessage());
            return 0;
        }
    }

    public static function executeQueryWithTransaction(mysqli $conexion, string $query): bool
    {
        try {
            $resultado = $conexion->query($query);

            if ($resultado === false) {
                throw new Exception("Error al ejecutar la consulta: " . $conexion->error);
            }

            return true;
        } catch (Exception $e) {
            error_log("Error en executeQueryWithTransaction: " . $e->getMessage());
            return false;
        }
    }

    public static function commit()
    {
        $conexion = self::conectionMySQL();
        $conexion->commit();
        self::closeConection();
    }

    public static function rollback()
    {
        $conexion = self::conectionMySQL();
        $conexion->rollback();
        self::closeConection();
    }
}
