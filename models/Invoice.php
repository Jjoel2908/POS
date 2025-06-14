<?php
require_once '../config/Connection.php';

class Invoice extends Connection {

   public array $meses = array(
      1 => "Enero", 
      2 => "Febrero", 
      3 => "Marzo", 
      4 => "Abril", 
      5 => "Mayo", 
      6 => "Junio", 
      7 => "Julio", 
      8 => "Agosto", 
      9 => "Septiembre", 
      10 => "Octubre", 
      11 => "Noviembre", 
      12 => "Diciembre"
   );
   
   public function __construct(private $id) {}

   /* ===================================================================================  */
   /* -------------------------------- P U R C H A S E S -------------------------------- */
   /* ===================================================================================  */

   public function getPurchaseDetails(): array {
      return $this->queryMySQL("SELECT dc.*, p.nombre AS nombre_producto FROM detalle_compra dc INNER JOIN productos p ON dc.id_producto = p.id WHERE id_compra = {$this->id}");
   }

   /* ===================================================================================  */
   /* ------------------------------------ S A L E S ------------------------------------ */
   /* ===================================================================================  */

   public function getSaleDetails(): array {
      return $this->queryMySQL("SELECT dv.*, p.nombre AS nombre_producto FROM detalle_venta dv INNER JOIN productos p ON dv.id_producto = p.id WHERE id_venta = {$this->id}");
   }
}