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

   /** Lista de tablas asociadas a los módulos de facturación
    * @var array
    */
   public array $tables = [
      'Compra' => 'compras',
      'Venta'  => 'ventas'
   ];

   /** Constructor de la clase Invoice
    * @param string $module Módulo al que pertenece la factura (Compra, Venta, etc.)
    * @param int $id ID de la factura
    */
   public function __construct(private string $module, private int $id) {}

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

   /** Método para obtener la fecha de la factura en formato legible
    * @return string Fecha formateada
    */
   public function getDateInLetters(int $registerId): string
   {
      /** Consultar la base de datos para obtener la fecha de la compra */
      $table = $this->tables[$this->module] ?? null;
      $register = $this->queryMySQL("SELECT fecha FROM $table WHERE id = $registerId");
      if (empty($register))
         return 'Fecha no disponible';

      $date = $register[0]['fecha'];
      /** Separar la fecha en día, mes y año */
      $day        = date('d', strtotime($date));
      $month      = date('n', strtotime($date));
      $year       = date('Y', strtotime($date));
      $name_month = $this->meses[$month] ?? 'Mes Desconocido';
      return "$day de $name_month de $year";
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

   /** Método para generar el PDF del comprobante
    * @throws Exception Si no hay datos disponibles para generar el comprobante
    */
   public function generatePDF(): void
   {
      $module = $this->module;
      $format = ($module === "Compra") ? PDF_PAGE_FORMAT : [80, 200];
      $pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, $format, true, 'UTF-8', false);

      $pdf->SetMargins(PDF_MARGIN_LEFT, 8, PDF_MARGIN_RIGHT);
      $pdf->SetTitle("Comprobante de $module");
      $pdf->SetFont('helvetica', '', 14);

      $data = $this->getData();
      if (empty($data))
         die("No hay datos disponibles para generar el comprobante.");

      $pdf->AddPage();

      if ($module === 'Compra')
         $this->renderPurchasePDF($pdf, $data);
      else
         $this->renderSalePDF($pdf, $data);

      $pdf->Output("comprobante.pdf", "I");
   }

   /** Método para renderizar el PDF de compra
    * @param MYPDF $pdf Instancia del PDF
    * @param array $data Datos de la compra
    */
   private function renderPurchasePDF($pdf, $data): void
   {
      $date = $this->getDateInLetters($this->id);
      $total = 0;

      $pdf->Ln(6);

      $pdf->SetFont('helvetica', 'B', 18);
      $pdf->SetFillColor(255, 255, 255);  // Color de fondo para encabezados
      $pdf->SetTextColor(0, 0, 0);  // Color de texto para encabezados
      $pdf->SetDrawColor(41, 128, 185);   // Color de borde para celdas

      $pdf->renderCenteredCell($pdf, 'COMPROBANTE DE COMPRA', 100, 24);
      $pdf->SetFont('helvetica', 'B', 10);
      $pdf->SetFillColor(41, 128, 185);  // Color de fondo para encabezados
      $pdf->SetTextColor(255, 255, 255);  // Color de texto para encabezados
      $pdf->Cell(80, 7, 'Fecha', 1, 1, 'C', true);
      $pdf->SetFillColor(255, 255, 255);  // Color de fondo para encabezados
      $pdf->SetTextColor(0, 0, 0);        // Restaurar color de texto a negro
      $pdf->Cell(100, 0, '', 0, 0, 'C');
      $pdf->Cell(80, 8, $date, 1, 1, 'C', true);

      /** Número Comprobante */
      $pdf->SetTextColor(255, 255, 255);  // Color de texto para encabezados
      $pdf->Cell(100, 8, '', 0, 0, 'C', true);
      $pdf->SetFillColor(41, 128, 185);  // Color de fondo para encabezados
      $pdf->Cell(80, 7, 'No. Compra', 1, 1, 'C', true);
      $pdf->SetFillColor(255, 255, 255);  // Color de fondo para encabezados
      $pdf->SetTextColor(0, 0, 0);        // Restaurar color de texto a negro
      $pdf->Cell(100, 0, '', 0, 0, 'C');
      $pdf->Cell(80, 8, $this->id, 1, 1, 'C', true);
      $pdf->Ln(6);

      $pdf->renderTableHeaders($pdf, ['Producto' => 100, 'Cantidad' => 20, 'Precio' => 30, 'Subtotal' => 30]);

      /** Configuración de fuente y color para datos de productos */
      $pdf->SetFont('helvetica', '', 10);
      $pdf->SetTextColor(0, 0, 0); // Restaurar color de texto a negro

      /** Iteración sobre los datos de productos y creación de filas */
      foreach ($data as $product) {
         $producto  = htmlspecialchars($product['nombre_producto']);
         $cantidad  = (int) $product['cantidad'];
         $precio    = (float) $product['precio'];
         $subTotal  = $precio * $cantidad;
         $total    += $subTotal;

         $pdf->MultiCell(100, 7, $producto, 1, 'L', false, 0, '', '', true, 0, false, true, 7, 'M');
         $pdf->Cell(20, 7, $cantidad, 1, 0, 'C');
         $pdf->Cell(30, 7, "$" . number_format($precio, 2), 1, 0, 'R');
         $pdf->Cell(30, 7, "$" . number_format($subTotal, 2), 1, 1, 'R');
      }

      $pdf->Cell(150, 8, 'Total', 0, 0, 'R');
      $pdf->Cell(30, 8, "$" . number_format($total, 2), 1, 0, 'R');
   }

   /** Método para renderizar el PDF de venta
    * @param MYPDF $pdf Instancia del PDF
    * @param array $data Datos de la venta
    */
   private function renderSalePDF($pdf, $data): void
   {
      $total = 0;
      $pdf->SetMargins(2, 0, 0);
      $pdf->SetFont('helvetica', 'B', 12);

      $pdf->Cell(52, 10, PDF_HEADER_COMPANY, 0, 1, 'C');
      $pdf->Image(PDF_HEADER_IMAGE, 50, 16, 25, 25);

      $pdf->renderKeyValue($pdf, 'Teléfono:', PDF_HEADER_PHONE);
      $pdf->renderKeyValue($pdf, 'Dirección:', PDF_HEADER_LOCATION, true);
      $pdf->renderKeyValue($pdf, 'Folio:', $this->id);

      $pdf->Ln(8);
      $pdf->Cell(20, 5, 'Cant - Precio', 0, 0, 'L');
      $pdf->Cell(40, 5, 'Descripción', 0, 0, 'L');
      $pdf->Cell(15, 5, 'Subtotal', 0, 1, 'L');
      $pdf->Cell(75, 5, str_repeat('=', 40), 0, 1, 'L');

      $pdf->SetFont('helvetica', '', 8);
      foreach ($data as $row) {
         $cantidad  = (int) $row['cantidad'];
         $precio    = (float) $row['precio_venta'];
         $subTotal  = $precio * $cantidad;
         $total    += $subTotal;

         $pdf->Cell(20, 2, (int) $row['cantidad'] . ' - ' . $row['precio_venta'], 0, 0, 'L');
         $pdf->MultiCell(40, 5, $row['nombre_producto'], 0, 'L', false);
         $pdf->Cell(75, 5, "$" . number_format($row['precio_venta'] * $row['cantidad'], 2, '.', ','), 0, 1, 'R');
         $pdf->SetFont('helvetica', '', 9);
         $pdf->Cell(70, 5, str_repeat('=', 40), 0, 1, 'L');
      }

      $pdf->Ln(1);
      $pdf->SetFont('helvetica', 'B', 10);
      $pdf->Cell(75, 5, 'Total', 0, 1, 'R');
      $pdf->SetFont('helvetica', '', 10);
      $pdf->Cell(75, 5, "$" . number_format($total, 2, '.', ','), 0, 1, 'R');
      $pdf->Ln(4);
      $pdf->SetFont('helvetica', 'I', 8);
      $pdf->MultiCell(75, 5, '¡Gracias por su compra! Esperamos verlo de nuevo.', 0, 'C');
   }
}
