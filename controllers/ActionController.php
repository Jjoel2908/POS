<?php
require_once '../config/Connection.php';

class ActionController extends Connection
{
    /** @var string Nombre de la tabla en la base de datos */
    private $table = "acciones";

    public function __construct() {}

    public function save()
    {
        $sucursalId = filter_var($_SESSION['sucursal'] ?? $_POST['id_sucursal'], FILTER_VALIDATE_INT);
        $sucursalId = ($sucursalId !== false) ? $sucursalId : 0;

        /** Información a registrar o actualizar */
        $data = [
            'id_sucursal' => $sucursalId,
            'modulo'      => self::sanitizeInput('module', 'text'),
            'accion'      => self::sanitizeInput('operation', 'text'),
            'data'        => json_encode($_POST)
        ];

        self::insert($this->table, $data);
    }
}
