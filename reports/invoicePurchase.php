<?php
session_start();
require '../models/Invoice.php';
require '../models/MYPDF.php';

// Crear un objeto Invoice usando el ID obtenido de $_GET
$Invoice = new Invoice($_GET['id']);
$meses   = $Invoice->meses;
$total   = 0;

// Asignar el ID de compra desde $_GET['id'] a una variable separada
$idPurchase = $_GET['id'];

// Obtener los detalles de la compra usando el método getPurchaseDetails() del objeto Invoice
$result = $Invoice->getPurchaseDetails();

// Consultar la base de datos para obtener todos los detalles de la compra con el ID específico
$purchase = $Invoice::queryMySQL("SELECT * FROM compras WHERE id = $idPurchase");

// Separar la fecha de la compra en día y hora
list($day, $hour) = explode(" ", $purchase[0]['fecha']);

// Obtener el mes numérico de la fecha y convertirlo a su nombre correspondiente en español
$month      = date("n", strtotime($day));
$name_month = $meses[$month];

// Formatear la fecha para mostrarla en el formato "d de NombreMes de Y"
$date = date('d \d\e ', strtotime($day)) . $name_month . date(' \d\e Y', strtotime($day));

// Creación del objeto TCPDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Seteamos el margen del header
$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);

// Configuración del documento PDF
$pdf->SetTitle('Comprobante de Compra');
$pdf->SetFont('helvetica', '', 14);

$pdf->AddPage();
$pdf->Ln(6);

if (count($result) > 0) {
   // Configuración de la tabla de datos
   $pdf->SetFont('helvetica', 'B', 18);
   $pdf->SetFillColor(255, 255, 255);  // Color de fondo para encabezados
   $pdf->SetTextColor(0, 0, 0);  // Color de texto para encabezados
   $pdf->SetDrawColor(41, 128, 185);   // Color de borde para celdas

   // Encabezados de factura
   $pdf->Cell(100, 24, 'COMPROBANTE DE COMPRA', 0, 0, 'C', true);
   $pdf->SetFont('helvetica', 'B', 10);
   $pdf->SetFillColor(41, 128, 185);  // Color de fondo para encabezados
   $pdf->SetTextColor(255, 255, 255);  // Color de texto para encabezados
   $pdf->Cell(80, 7, 'Fecha', 1, 1, 'C', true);
   $pdf->SetFillColor(255, 255, 255);  // Color de fondo para encabezados
   $pdf->SetTextColor(0, 0, 0);        // Restaurar color de texto a negro
   $pdf->Cell(100, 0, '', 0, 0, 'C');
   $pdf->Cell(80, 8, $date, 1, 1, 'C', true);
   // Número Comprobante
   $pdf->SetTextColor(255, 255, 255);  // Color de texto para encabezados
   $pdf->Cell(100, 8, '', 0, 0, 'C', true);
   $pdf->SetFillColor(41, 128, 185);  // Color de fondo para encabezados
   $pdf->Cell(80, 7, 'No. Compra', 1, 1, 'C', true);
   $pdf->SetFillColor(255, 255, 255);  // Color de fondo para encabezados
   $pdf->SetTextColor(0, 0, 0);        // Restaurar color de texto a negro
   $pdf->Cell(100, 0, '', 0, 0, 'C');
   $pdf->Cell(80, 8, $idPurchase, 1, 1, 'C', true);
   $pdf->Ln(2);

   // Encabezados de la tabla
   $pdf->SetFillColor(41, 128, 185);  // Color de fondo para encabezados
   $pdf->SetTextColor(255, 255, 255); // Color de texto para encabezados
   $pdf->Cell(100, 8, 'Producto', 1, 0, 'C', true);
   $pdf->Cell(20, 8, 'Cantidad', 1, 0, 'C', true);
   $pdf->Cell(30, 8, 'Precio', 1, 0, 'C', true);
   $pdf->Cell(30, 8, 'Subtotal', 1, 1, 'C', true);

   // Configuración de fuente y color para datos de productos
   $pdf->SetFont('helvetica', '', 10);
   $pdf->SetTextColor(0, 0, 0); // Restaurar color de texto a negro

   // Iteración sobre los datos de productos y creación de filas
   foreach ($result as $product) {
      $producto  = htmlspecialchars($product['nombre_producto']);
      $cantidad  = (int) $product['cantidad'];
      $precio    = (float) $product['precio'];
      $subTotal  = $precio * $cantidad;
      $total    += $subTotal;

      $pdf->MultiCell(100, 7, $producto, 1, 'L', false, 0, '', '', true, 0, false, true, 7, 'M');
      $pdf->Cell(20, 7, $cantidad, 1, 0, 'C');
      $pdf->Cell(30, 7, number_format($precio, 2), 1, 0, 'R');
      $pdf->Cell(30, 7, number_format($subTotal, 2), 1, 1, 'R');
   }

   $pdf->Cell(150, 8, 'Total', 0, 0, 'R');
   $pdf->Cell(30, 8, "$" . number_format($purchase[0]['total'], 2), 1, 0, 'R');
}
// Salida del documento PDF en modo inline
$pdf->Output("factura_compra.pdf", "I");
?>