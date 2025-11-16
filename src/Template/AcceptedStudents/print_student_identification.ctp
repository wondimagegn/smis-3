<?php
use Cake\I18n\I18n;

$this->set('title', __('Print Student Identification'));

$pdf = new TCPDF('P', 'mm', [75.0, 33.0], true);
$pdf->SetMargins(0.0, 0.0);
$pdf->SetFont('helvetica', '', 9.5);
$pdf->AddPage();

$pdf->write1DBarcode($doc_id, 'C39', 0, 4, 75.0, 33.0, 0.4, [
    'position' => '',
    'align' => 'C',
    'stretch' => false,
    'fitwidth' => true,
    'border' => false,
    'vpadding' => 'auto',
    'hpadding' => 'auto',
    'fgcolor' => [0, 0, 0],
    'bgcolor' => false,
    'text' => true,
    'font' => 'helvetica',
    'fontsize' => 8,
    'stretchtext' => 4
], 'N');

$pdf->SetXY(2, 4);
$pdf->Cell(0, 0, __('Code: 39 - 17 March 2007'), 0, 0, 'C');
$pdf->Rect(0.3, 0.3, 74.4, 32.4);

$this->response = $this->response->withType('application/pdf');
$this->response = $this->response->withHeader('Content-Disposition', 'attachment;filename="Student_Identification.pdf"');
ob_start();
$pdf->Output('php://output', 'I');
$output = ob_get_clean();
echo $output;
?>
