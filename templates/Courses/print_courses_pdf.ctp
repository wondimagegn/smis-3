<?php
    App::import('Vendor', 'tcpdf/tcpdf');
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);

    //show header or footer
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(true);

    //Image processing
    /* if (isset($university['University'])) {

        if (strcasecmp($university['Attachment']['0']['group'], 'logo') == 0) {
            $logo_index = 0;
        } else {
            $logo_index = 1;
        }
    }

    $logo_path = $this->Media->file($university['Attachment'][$logo_index]['dirname'] .  DS . $university['Attachment'][$logo_index]['basename']); */

    //HEADER
    $pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_LOGO_HEADER_FOR_TCPDF, '5', '5', 25, 25, '', '', 'N', true, 300, 'C');
    //set margins

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //$this->Pdf->SetMargins(15,15,15);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    // set font
    $pdf->SetFont("freeserif", "", 11);

    // add a page
    $pdf->AddPage();

    if (!empty($course_associate_array)) {
        $pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">College: ' . $this_department_college_name . '</div>', true, 0, true, 0, '');
        $pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">Department: ' . $selected_department_name . '</div>', true, 0, true, 0, '');
        $pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">Program: ' . $program_name . '</div>', true, 0, true, 0, '');
        $pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">Program Type: ' . $program_type_name . '</div>', true, 0, true, 0, '');
        $pdf->writeHTML('<div style="width:800px;text-align:left;font-size:60px;font-weight:bold">Curriculum: ' . $selected_curriculum_name . '</div><br/>', true, 0, true, 0, '');

        foreach ($course_associate_array as $yearkey => $yearvalue) {
            foreach ($yearvalue as $semesterKey => $semestervalue) {

                $pdf->writeHTML('<div style="width:800px;text-align:left;font-size:50px;font-weight:bold">Year Level: ' . $yearvalue[$semesterKey][0]['YearLevel']['name'] . '</div>', true, 0, true, 0, '');
                $pdf->writeHTML('<div style="width:800px;text-align:left;font-size:50px;font-weight:bold">Semester: ' . $semesterKey . '</div><br/>', true, 0, true, 0, '');

                $tbl = '
                <table style="width: 800px;" cellspacing="0">
                    <tr>
                        <th style="border: 1px solid #000000; width: 30px;font-size:40px;font-weight:bold;">No</th>
                        <th style="border: 1px solid #000000; width: 170px;font-size:40px;font-weight:bold;">Course Title</th>
                        <th style="border: 1px solid #000000; width: 80px;font-size:40px;font-weight:bold;">Course Code</th>
                        <th style="border: 1px solid #000000; width: 50px;font-size:40px;font-weight:bold;">Credit</th>
                        <th style="border: 1px solid #000000; width: 50px;font-size:40px;font-weight:bold;">L T L</th>
                        <th style="border: 1px solid #000000; width: 70px;font-size:40px;font-weight:bold;">Course Category</th>
                        <th style="border: 1px solid #000000; width: 80px;font-size:40px;font-weight:bold;">Lecture Attendance Req.</th>
                        <th style="border: 1px solid #000000; width: 80px;font-size:40px;font-weight:bold;">Lab Attendance Req.</th>
                        <th style="border: 1px solid #000000; width: 60px;font-size:40px;font-weight:bold;">Grade Type</th>
                    </tr>';
                    $count = 1;
                    foreach ($semestervalue as $course) {
                        $tbl .= '
                        <tr>
                            <td style="border: 1px solid #000000; width: 30px;">' . $count++ . '</td>
                            <td style="border: 1px solid #000000; width: 170px;">' . $course['Course']['course_title'] . '</td>
                            <td style="border: 1px solid #000000; width: 80px;">' . $course['Course']['course_code'] . '</td>
                            <td style="border: 1px solid #000000; width: 50px;">' . $course['Course']['credit'] . '</td>
                            <td style="border: 1px solid #000000; width: 50px;">' . $course['Course']['course_detail_hours'] . '</td>
                            <td style="border: 1px solid #000000; width: 70px;">' . (isset($course['CourseCategory']['name']) ? $course['CourseCategory']['name'] : '') . '</td>
                            <td style="border: 1px solid #000000; width: 80px;">' . $course['Course']['lecture_attendance_requirement'] . '</td>
                            <td style="border: 1px solid #000000; width: 80px;">' . $course['Course']['lab_attendance_requirement'] . '</td>
                            <td style="border: 1px solid #000000; width: 60px;">' . (isset($course['GradeType']['type']) ? $course['GradeType']['type'] : '') . '</td>
                        </tr>';
                    }
                    $tbl .= '
                </table>';
                $pdf->writeHTML($tbl, true, false, false, false, '');
            }
        }
    }

    // reset pointer to the last page
    $pdf->lastPage();

    //output the PDF to the browser

    $pdf->Output('Course list of -' . $selected_curriculum_name . '.pdf', 'D');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
