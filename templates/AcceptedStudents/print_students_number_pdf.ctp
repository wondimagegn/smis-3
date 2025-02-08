<?php
    App::import('Vendor', 'tcpdf/tcpdf');
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

    //show header or footer
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    //$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans' 
    // set default header data

    //$pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont(array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    //set margins

    //$pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    $pdf->SetMargins(10,10,10);

    //$pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    //$pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set font
    $pdf->SetFont("freeserif", "", 11);

    // add a page
    $pdf->AddPage();

    if (!empty($acceptedStudents)) {
        //$pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">College: ' . $selected_college_name . '</div>', true, 0, true, 0, '');
        //$pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">Campus: ' . $selected_campus_name . '</div>', true, 0, true, 0, '');

        

        /* if (!empty($selected_department_name)) {
            $pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">Department: ' . $selected_department_name . '</div>', true, 0, true, 0, '');
        } */

        //$pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">Program: ' . $selected_program_name . '</div>', true, 0, true, 0, '');
        //$pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">Program Type: ' . $selected_program_type_name . '</div>', true, 0, true, 0, '');
        //$pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">Academic Year: ' . $selected_acdemicyear . '</div>', true, 0, true, 0, '');

        $pdf->writeHTML("<span class='fs14'><strong class='text-gray'>College: </strong><b>" . $selected_college_name. '</b></span>', true, 0, true, 0, '');
		$pdf->writeHTML("<span class='fs14'><strong class='text-gray'>Campus: </strong><b>" . $selected_campus_name . '</b></span>', true, 0, true, 0, '');
		$pdf->writeHTML("<span class='fs14'><strong class='text-gray'>Program: </strong><b>" . $selected_program_name . '</b></span>', true, 0, true, 0, '');
		$pdf->writeHTML("<span class='fs14'><strong class='text-gray'>Program Type: </strong><b>" . $selected_program_type_name . '</b></span>', true, 0, true, 0, '');
		$pdf->writeHTML("<span class='fs14'><strong class='text-gray'>Admission Year: </strong><b>". $selected_acdemicyear. '</b></span><br>', true, 0, true, 0, '');

        $tbl = '
        <table style="width: 800px;" cellspacing="0">
            <tr>
                <th style="text-align: center; border: 1px solid #000000; width: 30px;font-size:40px;font-weight:bold;">#</th>
                <th style="border: 1px solid #000000; width: 200px;font-size:40px;font-weight:bold;">&nbsp; Full Name</th>
                <th style="text-align: center; border: 1px solid #000000; width: 50px;font-size:40px;font-weight:bold;">Sex</th>
                <th style="text-align: center; border: 1px solid #000000; width: 100px;font-size:40px;font-weight:bold;">Student ID</th>
                <th style="text-align: center; border: 1px solid #000000; width: 100px;font-size:40px;font-weight:bold;">Department</th>
                <th style="text-align: center; border: 1px solid #000000; width: 100px;font-size:40px;font-weight:bold;">Region</th>
                <th style="text-align: center; border: 1px solid #000000; width: 100px;font-size:40px;font-weight:bold;">National ID</th>
            </tr>';
            $count = 1;
            foreach ($acceptedStudents as $acceptedStudent) {
                $tbl .= '
                    <tr>
                        <td style="text-align: center; border: 1px solid #000000;">' . $count++ . '</td>
                        <td style="border: 1px solid #000000;">&nbsp;' . $acceptedStudent['AcceptedStudent']['full_name'] . '</td>
                        <td style="text-align: center; border: 1px solid #000000;">' . (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'female') == 0 ? 'F' : '')) . '</td>
                        <td style="text-align: center; border: 1px solid #000000;">' . $acceptedStudent['AcceptedStudent']['studentnumber'] . '</td>
                        <td style="text-align: center; border: 1px solid #000000;">' . (isset($acceptedStudent['Department']) && !is_null($acceptedStudent['Department']['name']) ? $acceptedStudent['Department']['name'] : 'Pre/Freshman'). '</td>
                        <td style="text-align: center; border: 1px solid #000000;">' . $acceptedStudent['Region']['name'] . '</td>
                        <td style="text-align: center; border: 1px solid #000000;">' . (isset($acceptedStudent['Student']) ? $acceptedStudent['Student']['student_national_id'] : '') . '</td>
                    </tr>';
            }
            $tbl .= '
        </table>';


        $pdf->writeHTML($tbl, true, false, false, false, '');
    }


    // reset pointer to the last page
    $pdf->lastPage();

    //output the PDF to the browser

    $pdf->Output('Student_IDs_for_' . (!empty($selected_department_name) ? $selected_department_name : $selected_college_name) . '_' . str_replace('/','-',$selected_acdemicyear) .'_'.date('Y-m-d'). '.pdf', 'D');
    /*
        I: send the file inline to the browser.
        D: send to the browser and force a file download with the name given by name.
        F: save to a local file with the name given by name.
        S: return the document as a string.
    */
