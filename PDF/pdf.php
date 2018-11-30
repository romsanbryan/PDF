<?php
require 'FPDF/fpdf.php';

class PDF extends FPDF
{
    // Cabecera de página
    function Header(){
        // Logo
        // Arial bold 15
        $this->SetFont('Arial','B',15);
        // Movernos a la derecha
        $this->Cell(100);
        // Título
        $this->Image('assets/images/logo.png', 25, 8, 30);
        // Salto de línea
        $this->Ln(20);
        $this->SetRightMargin(20);
    }

    // Pie de página
    /*function Footer(){
        // Posición: a 1,5 cm del final
        $this->SetY(-30);
        // Arial italic 8
        $this->SetFont('Arial','B',10);
        // Número de página
        $this->MultiCell(0,4,utf8_decode('REVISAR LOS PAQUETES A LA RECEPCIÓN DE LA MERCANCÍA ANTES DE SELLAR EL ALBARÁN AL TRANSPORTISTA, EN CASO DE OBSERVAR ALGUNA ANOMALÍA O DESPERFECTO, ANÓTELO EN EL ALBARÁN, SI NO SE HACE ASÍ, NO PODREMOS ATENDER RECLAMACIONES DE DESPERFECTOS'),0,'C');
        $this->SetFont('Arial','I',9);        
        $this->Cell(0,5,utf8_decode('Zarko Artículos de fumador'),0,0,'C');
//        $this->Cell(0,10,'Page '.$this->PageNo().'/{nb}',0,0,'C');
    }*/
}
?>