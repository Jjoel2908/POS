<?php
require_once '../public/TCPDF/examples/tcpdf_include.php';
require_once '../config/global.php';

class MYPDF extends TCPDF {

   public function Header() {
       $this->Image(PDF_HEADER_IMAGE, 10, 2, 24, '', 'JPG', '', 'T', false, 300, '', false, false, 0, false, false, false);
   }

   public function Footer() {

       $this->SetY(-15);
       $this->SetFont('helvetica', 'I', 10);
       $this->Cell(0, 10, 'Página '.$this->getAliasNumPage().' de '.$this->getAliasNbPages(), 0, false, 'C', 0, '', 0, false, 'T', 'M');
   }
}

?>