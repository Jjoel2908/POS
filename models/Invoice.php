<?php
require_once '../config/Connection.php';

class Invoice extends Connection
{

   /** Lista de meses en español
    * @var array
    */
   public array $meses = [
      1  => "Enero",
      2  => "Febrero",
      3  => "Marzo",
      4  => "Abril",
      5  => "Mayo",
      6  => "Junio",
      7  => "Julio",
      8  => "Agosto",
      9  => "Septiembre",
      10 => "Octubre",
      11 => "Noviembre",
      12 => "Diciembre"
   ];

   /** Constructor de la clase Invoice
    * @param string $module Módulo al que pertenece la factura (Compra, Venta, etc.)
    * @param int $id ID de la factura
    */
   public function __construct(private string $module, private int $id)
   {}

   /** Método para obtener los datos de la factura según el módulo
    * @return array Datos de la factura
    * @throws Exception Si el módulo no es válido
    */
   public function getData(): array
   {
      /** Validar el módulo y obtener los datos correspondientes */
      switch ($this->module) {
         case 'Compra':
            return $this->getPurchaseDetails();
         case 'Venta':
            return $this->getSaleDetails();
         default:
            throw new Exception("Módulo no válido");
      }
   }

   /** Método para obtener los detalles de la compra
    * @return array Detalles de la compra
    */
   public function getPurchaseDetails(): array
   {
      return $this->queryMySQL("SELECT dc.*, p.nombre AS nombre_producto FROM detalle_compra dc INNER JOIN productos p ON dc.id_producto = p.id WHERE id_compra = {$this->id}");
   }

   /** Método para obtener los detalles de la venta
    * @return array Detalles de la venta
    */
   public function getSaleDetails(): array
   {
      return $this->queryMySQL("SELECT dv.*, p.nombre AS nombre_producto FROM detalle_venta dv INNER JOIN productos p ON dv.id_producto = p.id WHERE id_venta = {$this->id}");
   }
}
