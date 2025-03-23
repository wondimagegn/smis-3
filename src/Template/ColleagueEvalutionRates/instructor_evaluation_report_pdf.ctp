<?php
    App::import('Vendor', 'tcpdf/tcpdf');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

    $pdf->SetProtection(array('modify', 'copy', 'extract', 'assemble'), $user_pass = USER_PASSWORD, $owner_pass = OWNER_PASSWORD, $mode = 3, $pubkeys = null);

    //show header or footer
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    //SetMargins(Left, Top, Right)
    $pdf->SetMargins(10, 10, 10);
    //$pdf->SetTopMargin(10);
    //Font Family, Style, Size
    //$pdf->SetFont("pdfacourier", "", 11);
    $pdf->SetFont("freeserif", "", 12);
    $pdf->setPageOrientation('P', true, 0);
    $evaluationPeriod = '';
    $selectedStaffCount = 0;
    $selectedInstructorName = '';
    debug($evaluationAggregateds);

    if (isset($evaluationAggregateds) && count($evaluationAggregateds) > 0) {

        if ((isset($evaluationAggregateds) && count($evaluationAggregateds) > 1)) {
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
            $pdf->SetTitle('Instructor Evaluation Report  for ' .(isset($evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Department']) && !empty($evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Department']['name']) ? $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Department']['name'] : $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['College']['name']).' for '. $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['academic_year'] . ' ' . $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['semester'] . '');
            $pdf->SetSubject('Instructor Evaluation Report');
            $pdf->SetKeywords('Instructor, Evaluation, Report, '.(isset($evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Department']) && !empty($evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Department']['name']) ? $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Department']['name'] : $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['College']['name']) .', '.$evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['academic_year'].','.$evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['semester'].', SMiS');
        } else if ((count($evaluationAggregateds) == 1 )) {
            $pdf->SetCreator(PDF_CREATOR);
            $pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
            $pdf->SetTitle('Instructor Evaluation Report  for '.$evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Staff']['full_name'].'  for '. $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['academic_year'] . ' ' . $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['semester'] . '');
            $pdf->SetSubject('Instructor Evaluation Report');
            $pdf->SetKeywords('Instructor, Evaluation, Report, '.$evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Staff']['full_name'].', '.(isset($evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Department']) && !empty($evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Department']['name']) ? $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['Department']['name'] : $evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['College']['name']) .', '.$evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['academic_year'].', '.$evaluationAggregateds[$first_staff_id]['EvaluatedStaffDetail']['semester'].', SMiS');
        }

        foreach ($evaluationAggregateds as $evaluationAggregated) {
            $pdf->AddPage("P");

            $evaluationPeriod = str_replace('/','-', $evaluationAggregated['EvaluatedStaffDetail']['academic_year']) . '-'.$evaluationAggregated['EvaluatedStaffDetail']['semester'];
            $selectedStaffCount ++;
            $selectedInstructorName = $evaluationAggregated['EvaluatedStaffDetail']['Title']['title'] . ' ' . $evaluationAggregated['EvaluatedStaffDetail']['Staff']['full_name'];

            $header = '
            <table style="width:100%;">
                <tr>
                    <td style="text-align:center; font-weight:bold">'. (strtoupper(Configure::read('CompanyName'))).'</td>
                </tr>
                <tr>
                    <td style="text-align:center; font-weight:bold">ACADEMIC STAFF SEMESTER EVALUTION REPORT</td>
                </tr>
            </table>';

            $body = '<br/><br/>
            
            <table cellspacing="1" cellpadding="2">
                <tr>
                    <td style="width:55%"><b>Name:</b> &nbsp;'. $selectedInstructorName . '</td>
                    <td style="width:5%">&nbsp;</td>
                    <td style="width:40%"><b>Academic Rank:</b> &nbsp;' . $evaluationAggregated['EvaluatedStaffDetail']['Position']['position'] . '</td>
                </tr>
                <tr>
                    <td style="width:55%"><b>' . (!empty($evaluationAggregated['EvaluatedStaffDetail']['Department']['type']) ? $evaluationAggregated['EvaluatedStaffDetail']['Department']['type'] : 'Department'). ':</b> &nbsp;' . $evaluationAggregated['EvaluatedStaffDetail']['Department']['name'] . '</td>
                    <td style="width:5%">&nbsp;</td>
                    <td style="width:40%"><b>Qualification:</b> &nbsp;' . $evaluationAggregated['EvaluatedStaffDetail']['Staff']['education'] . '</td>
                </tr>
                <tr>
                    <td style="width:55%"><b>' . (!empty($evaluationAggregated['EvaluatedStaffDetail']['College']['type']) ? $evaluationAggregated['EvaluatedStaffDetail']['College']['type'] : 'College'). ':</b> &nbsp;' . $evaluationAggregated['EvaluatedStaffDetail']['College']['name'] . '</td>
                    <td style="width:5%"></td>
                    <td style="width:40%"><b>Academic Year:</b> &nbsp;' . $evaluationAggregated['EvaluatedStaffDetail']['academic_year'] . '</td>
                </tr>
                <tr>
                    <td style="width:55%">&nbsp;</td>
                    <td style="width:5%"></td>
                    <td style="width:40%"><b>Semester:</b> &nbsp;' . $evaluationAggregated['EvaluatedStaffDetail']['semester'] . '</td>
                </tr>
                <tr>
                    <td colspan="3"><br/>Administrative post or additional assignment(if any): </td>
                </tr>
            </table>
            <br/>
            <br/>';

            $body .= '
            <table cellspacing="0" cellpadding="0">
                <tr>
                    <td style="width:18%;border:1px solid #000000; padding: 5px;">&nbsp;<b>Evalutor</b></td>
                    <td style="width:40%;border:1px solid #000000; padding: 5px;">&nbsp;<b>Course</b></td>
                    <td style="width:25%;border:1px solid #000000; text-align:center; padding: 5px;"><b>Section</b></td>
                    <td style="width:18%;border:1px solid #000000; text-align:center; padding: 5px;"><b>Evaluation(5pts)</b></td>
                </tr>';

                $studentcount = 0;
                $studentrateSum = 0;

                foreach ($evaluationAggregated['Student'] as $ckey => $cvalue) {
                    $exploded = explode('~', $ckey);

                    if (!is_null($cvalue['rateconverted5percent'])) {
                        $studentcount++;
                        $studentrateSum += $cvalue['rateconverted5percent'];
                        $body .= '
                        <tr>
                            <td style="border:1px solid #000000; padding:5px;">&nbsp;Student</td>
                            <td style="border:1px solid #000000; padding:5px; text-align: left;">&nbsp;' . $exploded[0] . '</td>
                            <td style="border:1px solid #000000; text-align:center; padding:5px;">' . $exploded[1] . '</td>
                            <td style="border:1px solid #000000; padding:5px; text-align:center;">'.number_format($cvalue['rateconverted5percent'], 2, '.', '') .'</td>
                        </tr>';
                    }
                }

                if (!empty($evaluationAggregated['Colleague']['rateconverted5percent'])) {
                    $body .= 
                    '<tr>
                        <td style="border:1px solid #000000; padding:5px;" colspan="3">&nbsp;Colleague</td>
                        <td style="border:1px solid #000000; text-align:center; padding:5px;">'.number_format($evaluationAggregated['Colleague']['rateconverted5percent'], 2, '.', '').'</td>
                    </tr>';
                } else {
                    $body .= 
                    '<tr>
                        <td style="border:1px solid #000000; padding:5px;" colspan="3">&nbsp;Colleague</td>
                        <td style="border:1px solid #000000; text-align:center; padding:5px;">---</td>
                    </tr>';
                }

                if ($evaluationAggregated['Head'][0]['rateconverted5percent'] > 0) {
                    $body .= 
                    '<tr>
                        <td style="border:1px solid #000000; padding:5px;" colspan="3">&nbsp;Department Head</td>
                        <td style="border:1px solid #000000; padding:5px; text-align:center;">'.number_format($evaluationAggregated['Head'][0]['rateconverted5percent'], 2, '.', '') .'</td>
                    </tr>';
                } else {
                    $body .= 
                    '<tr>
                        <td style="border:1px solid #000000; padding:5px;" colspan="3">&nbsp;Department Head</td>
                        <td style="border:1px solid #000000; padding:5px; text-align:center;">---</td>
                    </tr>';
                }

                $average = 0;

                if ($studentcount != 0) {
                    $average += (($evaluationAggregated['InstructorEvalutionSetting']['student_percent'] / 100) * ($studentrateSum / $studentcount));
                }

                if ($evaluationAggregated['Colleague']['rateconverted5percent'] != 0) {
                    $average += (($evaluationAggregated['InstructorEvalutionSetting']['colleague_percent'] / 100) * ($evaluationAggregated['Colleague']['rateconverted5percent']));
                }

                //debug($average);

                if ($evaluationAggregated['Head'][0]['rateconverted5percent'] != 0) {
                    $average += (($evaluationAggregated['InstructorEvalutionSetting']['head_percent'] / 100) * ($evaluationAggregated['Head'][0]['rateconverted5percent']));
                }

                $body .= 
                '<tr>
                    <td colspan="3" style="border:1px solid #000000; padding:5px;">&nbsp;<b>Average</b></td>
                    <td style="border:1px solid #000000; text-align:center; padding:5px;"><b>'. number_format($average, 2, '.', '').'</b></td>
                </tr>';

                $body .= 
            '</table>';

            $footer = '<br/>
            <table cellpadding="3">
                <tr>
                    <td style="width:35%;">Comments of the department head: </td>
                    <td style="width:65%;border-bottom:1px solid #000000;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:100%;border-bottom:1px solid #000000;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:100%;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:20%;text-align:center;">Name</td>
                    <td style="width:20%">&nbsp;</td>
                    <td style="width:20%;text-align:center;">Signature</td>
                    <td style="width:20%">&nbsp;</td>
                    <td style="width:20%;text-align:center;">Date</td>
                </tr>
                <tr>
                    <td style="width:20%;border-bottom:1px solid #000000;">&nbsp;</td>
                    <td style="width:20%;">&nbsp;</td>
                    <td style="width:20%;border-bottom:1px solid #000000;">&nbsp;</td>
                    <td style="width:20%">&nbsp;</td>
                    <td style="width:20%;border-bottom:1px solid #000000;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:100%;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:100%;">Approval By College/Institute/School Dean: </td>
                </tr>
                <tr>
                    <td style="width:20%;border-bottom:1px solid #000000;">&nbsp;</td>
                    <td style="width:20%;">&nbsp;</td>
                    <td style="width:20%;border-bottom:1px solid #000000;">&nbsp;</td>
                    <td style="width:20%">&nbsp;</td>
                    <td style="width:20%;border-bottom:1px solid #000000;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:100%;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:100%;">Received by HR archives and personnel section: </td>
                </tr>
                <tr>
                    <td style="width:20%;border-bottom:1px solid #000000;">&nbsp;</td>
                    <td style="width:20%;">&nbsp;</td>
                    <td style="width:20%;border-bottom:1px solid #000000;">&nbsp;</td>
                    <td style="width:20%">&nbsp;</td>
                    <td style="width:20%;border-bottom:1px solid #000000;">&nbsp;</td>
                </tr>
                <tr>
                    <td style="width:100%;">&nbsp;<br/></td>
                </tr>
            </table>';

            $footer .='<br/>
            <table>
                <tr>
                    <td style="width:100%;"><b><i>NB: This is to be filled in two copies: One copy for the department and the other copy for the HR archives and personnel section. </i></b> &nbsp;&nbsp;&nbsp;&nbsp;(<b><i>Average = ' . $this->Number->toPercentage($evaluationAggregated['InstructorEvalutionSetting']['student_percent'], 0) . ' student + ' . $this->Number->toPercentage($evaluationAggregated['InstructorEvalutionSetting']['head_percent'], 0) . ' department head + ' . $this->Number->toPercentage($evaluationAggregated['InstructorEvalutionSetting']['colleague_percent'], 0) . ' colleague.) </i></b></td>
                </tr>
            </table>';

            $content = $header . ' ' . $body . ' ' . $footer;

            $pdf->writeHTML($content);
        }
    }

    $pdf->lastPage();

    if ($selectedStaffCount == 1){
        $pdf->Output('Instructor Evaluation Report for '.$selectedInstructorName.' '. $evaluationPeriod . ' ' . date('Y-m-d') . '.pdf', 'D');
    } else {
        $pdf->Output('Instructor Evaluation Report for '.$selectedStaffCount.' staffs '. $evaluationPeriod . ' ' . date('Y-m-d') . '.pdf', 'D');
    }
    
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
