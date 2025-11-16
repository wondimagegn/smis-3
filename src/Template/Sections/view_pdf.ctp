<?php
use TCPDF;

$this->disableAutoRender();

// Create new PDF document
$pdf = new TCPDF('L', 'mm', 'A4', true, 'UTF-8', false);

// Disable header and footer
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);

// Set margins (Left, Top, Right)
$pdf->SetMargins(10, 10, 10);

// Set landscape orientation
$pdf->setPageOrientation('L', true, 0);

// Add a page
$pdf->AddPage();

// Get section name
$section_name = '';
foreach ($studentsections as $studentsection) {
    $section_name = $studentsection['Section']['name'];
}

// Set font
$pdf->SetFont('freeserif', '', 11);

// HTML content for the header
$html = <<<EOF
<style>
    .centeralignment { text-align: center; }
</style>
<div class="centeralignment">
    <h1>
        {$collegename}<br/>
        Department: {$department_name}<br/>
        Section: {$section_name}
    </h1>
</div>
EOF;

$pdf->writeHTML($html, true, false, true, false, '');

// Generate table content
if (!empty($studentsections)) {
    $tbl = '<table style="width: 800px;" border="1" cellpadding="2" cellspacing="0">';
    $tbl .= '<tr>
                <th style="width: 30px; font-size: 14px; font-weight: bold;">No</th>
                <th style="width: 170px; font-size: 14px; font-weight: bold;">ID</th>
                <th style="width: 300px; font-size: 14px; font-weight: bold;">Name</th>
             </tr>';

    $count = 1;
    $no = [];
    $studentnumber = [];
    $full_name = [];
    foreach ($studentsections as $studentsection) {
        $students_per_section = count($studentsection['Student']);
        for ($i = 0; $i < $students_per_section; $i++) {
            $no[] = $count++;
            $studentnumber[] = $studentsection['Student'][$i]['studentnumber'];
            $full_name[] = $studentsection['Student'][$i]['full_name'];
        }
    }

    for ($i = 0; $i < count($no); $i++) {
        $tbl .= '<tr>
                    <td style="width: 30px;">' . h($no[$i]) . '</td>
                    <td style="width: 170px;">' . h($studentnumber[$i]) . '</td>
                    <td style="width: 300px;">' . h($full_name[$i]) . '</td>
                 </tr>';
    }

    $tbl .= '</table>';
    $pdf->writeHTML($tbl, true, false, true, false, '');
}

// Reset pointer to the last page
$pdf->lastPage();

// Output the PDF to the browser
$pdf->Output(h($section_name) . '.pdf', 'D');
?>
