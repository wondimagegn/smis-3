<?php
App::import('Vendor', 'tcpdf/tcpdf');
/*
// create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true); 

    //show header or footer
    $pdf->SetPrintHeader(true); 
    $pdf->SetPrintFooter(true);
   
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    //set margins
    
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //$pdf->SetMargins(15,15,15);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    
    // set font
    $pdf->SetFont("freeserif", "", 11);
    */
// add a page
App::import('Vendor', 'tcpdf/tcpdf');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true);
//show header or footer
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
$countryAmharic = Configure::read('ApplicationDeployedCountryAmharic');
$cityAmharic = Configure::read('ApplicationDeployedCityAmharic');
$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish');
$cityEnglish = Configure::read('ApplicationDeployedCityEnglish');
$pobox =  Configure::read('POBOX');
// set font
$pdf->SetMargins(3, 1, 3);
$pdf->SetFont("freeserif", "", 11);
$pdf->setPageOrientation('L', true, 0);
$header = '<table style="width:100%;">
            <tr>
                <td style="text-align:center; font-weight:bold">ARBA MINCH UNIVERSITY</td>
            </tr>
    <tr>
                <td style="text-align:center; font-weight:bold">OFFICE OF THE REGISTRAR</td>
            </tr>
    <tr>
                <td style="text-align:center; font-weight:bold;text-decoration:underline;">PLACEMENT CLASS REPORT FORM</td>
            </tr>
            </table>';
$pdf->AddPage();
$pdf->writeHTML($header);


if (!empty($autoplacedstudents)) {
    $summery = $autoplacedstudents['auto_summery'];
    $pdf->writeHTML('<div style="width:700px;text-align:left;font-size:70px">Auto Placement Summary</div><br/>', true, 0, true, 0, '');
    $tbl = '<table style="width: 638px;" cellspacing="0">';
    $tbl .= '<tr><th style="border: 1px solid #000000; width: 200px;font-weight:bold;">Department</th><th style="border: 1px solid #000000; width: 200px;width: 200px;font-weight:bold;">Competitive Assignment</th><th style="border: 1px solid #000000; width: 200px;width: 200px;font-weight:bold;"> Privilaged Quota Assignment</th></tr>';
    foreach ($summery as $sk => $sv) {
        $tbl .= '
            <tr>
                <td style="border: 1px solid #000000; width: 200px;">' . $sk . '</td>
                <td style="border: 1px solid #000000; width: 200px;">' . $sv['C'] . '</td>
                <td style="border: 1px solid #000000; width: 200px;">' . $sv['Q'] . '</td>
            </tr>
           ';
    }
    $tbl .= '</table>';
    $pdf->writeHTML($tbl, true, false, false, false, '');

    unset($autoplacedstudents['auto_summery']);
    foreach ($autoplacedstudents as $key => $data) {
        $count = 1;
        $pdf->writeHTML('<div style="width:710px;text-align:left;font-size:70px">' . $key . '</div><br/>', true, 0, true, 0, '');
        $department_placement = '<table margin="20px" cellspacing="0" cellpadding="2px">';
        $department_placement .= '<tr><th style="border: 1px solid #000000; width:50px;font-weight:bold;">No.</th><th style="border: 1px solid #000000; width: 200px;font-weight:bold;">Full Name</th><th style="border: 1px solid #000000; width: 40px;font-weight:bold;">Sex</th><th style="border: 1px solid #000000; width: 80px;font-weight:bold;">Student Number</th><th style="border: 1px solid #000000; width: 70px;font-weight:bold;"> Total Placement Weight</th><th style="border: 1px solid #000000; width: 80px;font-weight:bold;">Assigned</th>
                <th style="border: 1px solid #000000; width: 75px;font-weight:bold;">Preference</th><th style="border: 1px solid #000000; width: 80px;font-weight:bold;">Placement Based</th></tr>';
        foreach ($data as
            $acceptedStudent) {
            $preference_order = null;
            if (!empty($acceptedStudent['AcceptedStudent']['PlacementPreference'])) {
                foreach ($acceptedStudent['AcceptedStudent']['PlacementPreference'] as $key => $value) {
                    if ($value['placement_round_participant_id'] == $acceptedStudent['PlacementParticipatingStudent']['placement_round_participant_id']) {
                        $preference_order = $value['preference_order'];
                        break;
                    }
                }
            }

            $placement_based = ($acceptedStudent['PlacementParticipatingStudent']['placement_based'] == 'C' ? 'Competitive' : 'Quota');
            $sex = (strcasecmp($acceptedStudent['AcceptedStudent']['sex'], 'male') == 0 ? 'M' : 'F');
            $department_placement .= '
                            <tr>
                                 <td style="border: 1px solid #000000; width: 50px;">' . $count++ . '</td>
                                <td style="border: 1px solid #000000; width: 200px;">' . $acceptedStudent['AcceptedStudent']['full_name'] . '</td>
                                <td style="border: 1px solid #000000; width: 40px;">' . $sex . '</td>
                                <td style="border: 1px solid #000000; width: 80px;">' . $acceptedStudent['AcceptedStudent']['studentnumber'] . '</td>
                                <td style="border: 1px solid #000000; width: 70px;">' . $acceptedStudent['PlacementParticipatingStudent']['total_placement_weight'] . '</td>
                                <td style="border: 1px solid #000000; width:80px;">' . $acceptedStudent['PlacementRoundParticipant']['name'] . '</td>
                                <td style="border: 1px solid #000000; width: 75px;">' . $preference_order . '</td>
                                <td style="border: 1px solid #000000; width: 80px;">' . $placement_based . '</td>
                               
                            </tr>
                        ';
        }
        $department_placement .= '</table>';
        $pdf->writeHTML($department_placement, true, false, false, false, '');
    }
}

// reset pointer to the last page
$pdf->lastPage();

//output the PDF to the browser

//$pdf->Output('AutoPlaced-' . $selected_academic_year . '.pdf', 'D');
$pdf->Output('AutoPlaced-' . date('Y-m-d') . '.pdf', 'D');

    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
