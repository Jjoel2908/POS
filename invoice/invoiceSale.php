<?php
session_start();
require '../models/Invoice.php';
require_once '../public/TCPDF/examples/tcpdf_include.php';
require_once '../config/global.php';

// Crear un objeto Invoice usando el ID obtenido de $_GET
$Invoice = new Invoice($_GET['id']);

$meses  = $Invoice->meses;
$idSale = $_GET['id'];
$result = $Invoice->getSaleDetails();
$sale   = $Invoice::queryMySQL("SELECT * FROM ventas WHERE id = $idSale");

list ($day, $hour) = explode(" ", $sale[0]['fecha']);
$month             = date("n", strtotime($day));
$name_month        = $meses[$month];
$date              = date('d \d\e ', strtotime($day)) . $name_month . date(' \d\e Y', strtotime($day));

// Creación del objeto TCPDF
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, array(80, 200), true, 'UTF-8', false);

// Configuración del documento PDF
$pdf->SetTitle('Comprobante de Venta');
$pdf->SetFont('helvetica', '', 12);

$pdf->AddPage();

if (count($result) > 0) {
   $pdf->SetMargins(2, 0, 0);
   $pdf->SetFont('helvetica', 'B', 12);
   $pdf->Cell(65, 10, PDF_HEADER_COMPANY, 0, 1, 'C');
   $pdf->Image('../public/images/logo.jpg', 50, 18, 25, 25);
   $pdf->SetFont('helvetica', 'B', 9);
   $pdf->Cell(18, 5, 'Teléfono:', 0, 0, 'L');
   $pdf->SetFont('helvetica', '', 9);
   $pdf->Cell(20, 5, PDF_HEADER_PHONE, 0, 1, 'L');

   $pdf->SetFont('helvetica', 'B', 9);
   $pdf->Cell(18, 5, 'Dirección', 0, 0, 'L');
   $pdf->SetFont('helvetica', '', 9);
   $pdf->MultiCell(35, 5, PDF_HEADER_LOCATION, 0, 'L');
   $pdf->SetFont('helvetica', 'B', 9);
   $pdf->Cell(18, 5, 'Folio: ', 0, 0, 'L');
   $pdf->SetFont('helvetica', '', 9);
   $pdf->Cell(20, 5, $sale[0]['id'], 0, 1, 'L');        
   $pdf->Ln(8);


   //Encabezado de productos        
   $pdf->Cell(20, 5, 'Cant - Precio', 0, 0, 'L');
   $pdf->Cell(40, 5, 'Descripción', 0, 0, 'L');        
   $pdf->Cell(15, 5, 'Sub Total', 0, 1, 'L');
   $pdf->SetTextColor(0, 0, 0);
   $pdf->Cell(42, 5, '========================================', 0, 1, 'L');
   $pdf->SetFont('helvetica', '', 9);

   foreach ($result as $row) {
      $pdf->Cell(20, 5, $row['cantidad'] . ' - ' . $row['precio'], 0, 0, 'L');
      $pdf->MultiCell(40, 5, $row['nombre_producto'], 0, 'L', false);
      $pdf->Cell(75, 5, number_format($row['precio'] * $row['cantidad'], 2, '.', ','), 0, 1, 'R');
      $pdf->Cell(70, 5, '========================================', 0, 1, 'L');
   }

   $pdf->Ln();
   $pdf->SetFont('helvetica', 'B', 10);
   $pdf->Cell(75, 5, 'Total', 0, 1, 'R');
   $pdf->Cell(75, 5, "$" . number_format($sale[0]['total'], 2, '.', ','), 0, 1, 'R');

   $pdf->Ln(4);
   // Texto de agradecimiento
   $pdf->SetFont('helvetica', 'I', 8);
   $pdf->MultiCell(75, 5, 'Gracias por su compra.', 0, 'C');
}

// Salida del documento PDF en modo inline
$pdf->Output("comprobante_venta.pdf", "I");
?>