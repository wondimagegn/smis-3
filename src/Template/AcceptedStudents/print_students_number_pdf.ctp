<?php
use Cake\I18n\I18n;

$this->set('title', __('Print Student IDs'));

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetMargins(10, 10, 10);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('freeserif', '', 11);
$pdf->AddPage();

if (!empty($acceptedStudents)) {
    $pdf->writeHTML("<span class='fs-5'><strong class='text-muted'>" . __('College:') . "</strong><b>" . h($selectedCollegeName) . "</b></span>", true, 0, true, 0, '');
    $pdf->writeHTML("<span class='fs-5'><strong class='text-muted'>" . __('Campus:') . "</strong><b>" . h($selectedCampusName) . "</b></span>", true, 0, true, 0, '');
    $pdf->writeHTML("<span class='fs-5'><strong class='text-muted'>" . __('Program:') . "</strong><b>" . h($selectedProgramName) . "</b></span>", true, 0, true, 0, '');
    $pdf->writeHTML("<span class='fs-5'><strong class='text-muted'>" . __('Program Type:') . "</strong><b>" . h($selectedProgramTypeName) . "</b></span>", true, 0, true, 0, '');
    $pdf->writeHTML("<span class='fs-5'><strong class='text-muted'>" . __('Admission Year:') . "</strong><b>" . h($selectedAcademicYear) . "</b></span><br>", true, 0, true, 0, '');

    $tbl = '<table style="width: 800px;" cellspacing="0">';
    $tbl .= '<tr><th style="text-align: center; border: 1px solid #000000; width: 30px;font-size:40px;font-weight:bold;">' . __('#') . '</th><th style="border: 1px solid #000000; width: 200px;font-size:40px;font-weight:bold;">&nbsp;' . __('Full Name') . '</th><th style="text-align: center; border: 1px solid #000000; width: 50px;font-size:40px;font-weight:bold;">' . __('Sex') . '</th><th style="text-align: center; border: 1px solid #000000; width: 100px;font-size:40px;font-weight:bold;">' . __('Student ID') . '</th><th style="text-align: center; border: 1px solid #000000; width: 100px;font-size:40px;font-weight:bold;">' . __('Department') . '</th><th style="text-align: center; border: 1px solid #000000; width: 100px;font-size:40px;font-weight:bold;">' . __('Region') . '</th><th style="text-align: center; border: 1px solid #000000; width: 100px;font-size:40px;font-weight:bold;">' . __('National ID') . '</th></tr>';

    $count = 1;
    foreach ($acceptedStudents as $acceptedStudent) {
        $tbl .= '<tr><td style="text-align: center; border: 1px solid #000000;">' . $count++ . '</td><td style="border: 1px solid #000000;">&nbsp;' . h($acceptedStudent->full_name) . '</td><td style="text-align: center; border: 1px solid #000000;">' . h(strcasecmp(trim($acceptedStudent->sex), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent->sex), 'female') == 0 ? 'F' : '')) . '</td><td style="text-align: center; border: 1px solid #000000;">' . h($acceptedStudent->studentnumber) . '</td><td style="text-align: center; border: 1px solid #000000;">' . h($acceptedStudent->Department->name ?? 'Pre/Freshman') . '</td><td style="text-align: center; border: 1px solid #000000;">' . h($acceptedStudent->Region->name) . '</td><td style="text-align: center; border: 1px solid #000000;">' . h($acceptedStudent->Student->student_national_id ?? '') . '</td></tr>';
    }
    $tbl .= '</table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');
}

$pdf->lastPage();
$this->response = $this->response->withType('application/pdf');
$this->response = $this->response->withHeader('Content-Disposition', 'attachment;filename="Student_IDs_for_' . ($selectedDepartmentName ?? $selectedCollegeName) . '_' . str_replace('/', '-', $selectedAcademicYear) . '_' . date('Y-m-d') . '.pdf"');
ob_start();
$pdf->Output('php://output', 'D');
$output = ob_get_clean();
echo $output;
?>
