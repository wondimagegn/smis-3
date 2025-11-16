<?php
use Cake\I18n\I18n;

$this->set('title', __('Auto Placement Result'));

$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
$pdf->SetPrintHeader(true);
$pdf->SetPrintFooter(true);
$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);
$pdf->setHeaderFont([PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN]);
$pdf->setFooterFont([PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA]);
$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
$pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
$pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
$pdf->SetFont('freeserif', '', 11);
$pdf->AddPage();

if (!empty($autoPlacedStudents)) {
    $summary = $autoPlacedStudents['auto_summary'];
    $pdf->writeHTML('<div style="width:700px;text-align:left;font-size:70px">' . __('Auto Placement Summary') . '</div><br/>', true, 0, true, 0, '');

    $tbl = '<table style="width: 638px;" cellspacing="0">';
    $tbl .= '<tr><th style="border: 1px solid #000000; width: 200px;font-weight:bold;">' . __('Department') . '</th><th style="border: 1px solid #000000; width: 200px;font-weight:bold;">' . __('Competitive Assignment') . '</th><th style="border: 1px solid #000000; width: 200px;font-weight:bold;">' . __('Privileged Quota Assignment') . '</th></tr>';
    foreach ($summary as $sk => $sv) {
        $tbl .= '<tr><td style="border: 1px solid #000000; width: 200px;">' . h($sk) . '</td><td style="border: 1px solid #000000; width: 200px;">' . h($sv['C']) . '</td><td style="border: 1px solid #000000; width: 200px;">' . h($sv['Q']) . '</td></tr>';
    }
    $tbl .= '</table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');

    unset($autoPlacedStudents['auto_summary']);

    foreach ($autoPlacedStudents as $key => $data) {
        $count = 1;
        $pdf->writeHTML('<div style="width:710px;text-align:left;font-size:70px">' . h($key) . '</div><br/>', true, 0, true, 0, '');

        $department_placement = '<table margin="20px" cellspacing="0" cellpadding="2px">';
        $department_placement .= '<tr><th style="border: 1px solid #000000; width:50px;font-weight:bold;">' . __('No.') . '</th><th style="border: 1px solid #000000; width:200px;font-weight:bold;">' . __('Full Name') . '</th><th style="border: 1px solid #000000; width:40px;font-weight:bold;">' . __('Sex') . '</th><th style="border: 1px solid #000000; width:80px;font-weight:bold;">' . __('Student Number') . '</th><th style="border: 1px solid #000000; width:70px;font-weight:bold;">' . __('EHEECE Total Result') . '</th><th style="border: 1px solid #000000; width:80px;font-weight:bold;">' . __('Department') . '</th><th style="border: 1px solid #000000; width:75px;font-weight:bold;">' . __('Preference') . '</th><th style="border: 1px solid #000000; width:80px;font-weight:bold;">' . __('Placement Based') . '</th></tr>';

        foreach ($data as $acceptedStudent) {
            $preference_order = '';
            if (!empty($acceptedStudent->Preferences)) {
                foreach ($acceptedStudent->Preferences as $preference) {
                    if ($preference->department_id == $acceptedStudent->Department->id) {
                        $preference_order = $preference->preferences_order;
                        break;
                    }
                }
            }
            $placement_based = $acceptedStudent->placement_based == 'C' ? __('Competitive') : __('Privileged Quota');
            $sex = strcasecmp($acceptedStudent->sex, 'male') == 0 ? 'M' : 'F';

            $department_placement .= '<tr><td style="border: 1px solid #000000; width:50px;">' . $count++ . '</td><td style="border: 1px solid #000000; width:200px;">' . h($acceptedStudent->full_name) . '</td><td style="border: 1px solid #000000; width:40px;">' . h($sex) . '</td><td style="border: 1px solid #000000; width:80px;">' . h($acceptedStudent->studentnumber) . '</td><td style="border: 1px solid #000000; width:70px;">' . h($acceptedStudent->EHEECE_total_results) . '</td><td style="border: 1px solid #000000; width:80px;">' . h($acceptedStudent->Department->name) . '</td><td style="border: 1px solid #000000; width:75px;">' . h($preference_order) . '</td><td style="border: 1px solid #000000; width:80px;">' . h($placement_based) . '</td></tr>';
        }
        $department_placement .= '</table>';
        $pdf->writeHTML($department_placement, true, false, false, false, '');
    }
}

$pdf->lastPage();
$this->response = $this->response->withType('application/pdf');
$this->response = $this->response->withHeader('Content-Disposition', 'attachment;filename="AutoPlaced-' . $selectedAcademicYear . '.pdf"');
ob_start();
$pdf->Output('php://output', 'I');
$output = ob_get_clean();
echo $output;
?>
