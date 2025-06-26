<?php
require_once '../config/Connection.php';

class Expense extends Connection
{
   public function __construct() {}

   public static $tiposDeGasto = [
      1 => 'Comida',
      2 => 'Transporte',
      3 => 'Servicios Básicos',
      4 => 'Papelería y oficina',
      5 => 'Mantenimiento / reparaciones',
      6 => 'Renta / alquiler',
      7 => 'Gastos bancarios',
      8 => 'Otros'
   ];

   public function selectOne($idRegister)
   {
      return $this::queryMySQL("SELECT * FROM gastos WHERE id = $idRegister AND estado = 1 LIMIT 1");
   }

   public function dataTable(): array
   {
      return $this->queryMySQL(
         "SELECT 
            g.*, 
            tg.nombre AS tipo_gasto 
         FROM 
            gastos g 
         INNER JOIN 
            tipos_gastos tg
         ON 
            g.id_tipo_gasto = tg.id 
         AND
            g.estado = 1"
      );
   }
}
