<?php  
/* 
** File: barcode.thtml 
** Location: views/test_pdfb 
*/ 
    $this->Pdf->set('P', 'mm', array( 75.0, 33.0 )); 
    $this->Pdf->SetMargins(0.0, 0.0); 
    $this->Pdf->SetFont("Helvetica", "", 9.5); 

    $this->Pdf->AddPage(); 

    //$doc_id = '1234567'; // come from controller 
    $this->Pdf->BarCode($doc_id, "C39", 0, 4, 75.0, 33.0, 1, 1, 1, 1, "", "PNG"); 

    $this->Pdf->SetXY(2, 4); 
    $this->Pdf->Cell(0, 0, "Code: 39 - 17 march 2007", 0, 0, 'C'); 

    $this->Pdf->Rect(0.3, 0.3, 74.4, 32.4); 

    $this->Pdf->SetDisplayMode('real'); 
    $this->Pdf->Output(); 
    $this->Pdf->closeParsers(); 
?>
