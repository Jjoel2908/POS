<?php
session_start();
require '../models/Dashboard.php';
require '../models/MYPDF.php';

// Creación del objeto Dashboard y obtención de datos
$Dashboard = new Dashboard();
$query = $Dashboard->getProductStockMinimo();

// Creación del objeto TCPDF
$pdf = new MYPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true, 'UTF-8', false);

// Seteamos el margen del header
$pdf->SetMargins(PDF_MARGIN_LEFT, 20, PDF_MARGIN_RIGHT);

// Configuración del documento PDF
$pdf->SetTitle('Reporte de Productos con Stock Mínimo');
$pdf->SetFont('helvetica', '', 14);

$pdf->AddPage();

// Encabezado del documento PDF
$pdf->Cell(0, 16, 'Reporte de Productos con Stock Mínimo', 0, 1, 'C');
$pdf->Ln(2);

// Configuración de la tabla de datos
$pdf->SetFont('helvetica', 'B', 10);
$pdf->SetFillColor(41, 128, 185);  // Color de fondo para encabezados
$pdf->SetTextColor(255, 255, 255); // Color de texto para encabezados
$pdf->SetDrawColor(41, 128, 185);   // Color de borde para celdas

// Encabezados de las columnas
$pdf->Cell(30, 8, 'Código', 1, 0, 'C', true);
$pdf->Cell(130, 8, 'Nombre', 1, 0, 'C', true);
$pdf->Cell(20, 8, 'Stock', 1, 1, 'C', true);

// Configuración de fuente y color para datos de productos
$pdf->SetFont('helvetica', '', 10);
$pdf->SetTextColor(0, 0, 0); // Restaurar color de texto a negro

// Iteración sobre los datos de productos y creación de filas
foreach ($query as $product) {
   $pdf->Cell(30, 7, $product['codigo'], 1, 0, 'L');
   $pdf->Cell(130, 7, $product['nombre'], 1, 0, 'L');
   $pdf->Cell(20, 7, $product['stock'], 1, 1, 'C');
}

// Salida del documento PDF en modo inline
$pdf->Output("productos_stock_minimo.pdf", "I");
?>