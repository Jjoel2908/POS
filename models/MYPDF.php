<?php
require_once '../public/TCPDF/examples/tcpdf_include.php';
require_once '../config/global.php';

class MYPDF extends TCPDF
{

    public function Header()
    {
        $this->Image(PDF_HEADER_IMAGE, 10, 2, 24, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
    }

    public function Footer()
    {
        $this->SetY(-15);
        $this->SetFont('helvetica', 'I', 10);
    }

    /** Método para renderizar una celda con etiqueta y valor
     * @param MYPDF $pdf Instancia del PDF
     * @param string $label Etiqueta de la celda
     * @param string $value Valor de la celda
     * @param bool $isMultiline Indica si el valor puede ser multilinea
     */
    public function renderKeyValue($pdf, string $label, string $value, bool $isMultiline = false): void
    {
        $pdf->SetFont('helvetica', 'B', 9);
        $pdf->Cell(18, 5, $label, 0, 0, 'L');
        $pdf->SetFont('helvetica', '', 9);
        if ($isMultiline) {
            $pdf->MultiCell(35, 5, $value, 0, 'L');
        } else {
            $pdf->Cell(20, 5, $value, 0, 1, 'L');
        }
    }

    /** Método para renderizar los encabezados de la tabla
     * @param MYPDF $pdf Instancia del PDF
     * @param array $headers Encabezados de la tabla con sus respectivos anchos
     */
    public function renderTableHeaders($pdf, array $headers): void
    {
        $pdf->SetFillColor(41, 128, 185);
        $pdf->SetTextColor(255);
        $pdf->SetFont('helvetica', 'B', 10);

        foreach ($headers as $text => $width) {
            $pdf->Cell($width, 8, $text, 1, 0, 'C', true);
        }
        $pdf->Ln();
    }

    /** Método para renderizar una celda centrada
     * @param MYPDF $pdf Instancia del PDF
     * @param string $text Texto a mostrar en la celda
     * @param int $width Ancho de la celda
     * @param int $height Alto de la celda
     */
    public function renderCenteredCell($pdf, string $text, int $width, int $height): void
    {
        $pdf->Cell($width, $height, $text, 0, 0, 'C', true);
    }
}
