<?php
    App::import('Vendor','tcpdf/tcpdf');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    //debug($temporary_degrees);

    $pdf->SetProtection($permissions = array('modify', 'copy', 'extract', 'assemble'), $user_pass = USER_PASSWORD, $owner_pass = OWNER_PASSWORD, $mode = 0, $pubkeys = null);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
    $pdf->SetTitle('Mass Temporary Degree');
    $pdf->SetSubject('Mass Temporary Degree');
    $pdf->SetKeywords('Mass, Temporary, Degree, SMiS');


    $countryAmharic = Configure::read('ApplicationDeployedCountryAmharic'); 
	$cityAmharic = Configure::read('ApplicationDeployedCityAmharic');
	$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish'); 
	$cityEnglish = Configure::read('ApplicationDeployedCityEnglish');
	$pobox = Configure::read('POBOX');

    foreach ($temporary_degrees as $key => $temporary_degree) {

        if ($temporary_degree['student_detail']['Student']['graduated']) {
            $pdf->SetMargins(0, 10, 0);
            $pdf->setPageOrientation('L', true, 0);
            $pdf->AddPage("L");
            $pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
            
            
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_FULL_PAGE_TRANSPARENT_LOGO_FOR_TCPDF, 0, 15, 180, 180, '', '', '', false, 300, 'C', false, false, 0);
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_LOGO_HEADER_FOR_TCPDF, '5', '13', 35, 35, '', '', 'N', true, 300, 'C');

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 18, '', false);
            $pdf->MultiCell(107, 7, strtoupper($temporary_degree['student_detail']['University']['University']['name']), 0, 'L', false, 0, 30, 14);

            $pdf->SetFont($fontPath, 'U', 14, '', false);
            $pdf->MultiCell(157, 7, 'OFFICE OF THE REGISTRAR', 0, 'L', false, 0, 37, 21);
            
            
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerif.ttf');
            $pdf->SetFont($fontPath, '', 13, '', false);
            $pdf->MultiCell(60, 7, 'Tel: '.$temporary_degree['student_detail']['University']['University']['telephone'].'', 0, 'L', false, 0, 210, 15);
            $pdf->MultiCell(60, 7, 'Fax: '.$temporary_degree['student_detail']['University']['University']['fax'].'', 0, 'L', false, 0, 210, 20);
            $pdf->MultiCell(60, 7, 'P.O.Box:  '.$temporary_degree['student_detail']['University']['University']['p_o_box'].'', 0, 'L', false, 0, 210, 25);
            $pdf->MultiCell(60, 7, $cityEnglish . ', ' . $countryEnglish, 0, 'L', false, 0, 210, 30);

            // set visibility only for print or screen
            //$pdf->setVisibility('print');
            $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 3, 'color' => array(211, 211, 211)));
            $pdf->MultiCell(30, 7, "\n\n\n[Photo]\n\n\n\n", 1, 'C', false, 0,30, 33);
            // restore visibility
            //$pdf->setVisibility('all');

            
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 13, '', false);
            $pdf->MultiCell(100, 7, 'Date of Issue: ', 0, 'L', false, 0, 210, 45);
            $pdf->SetFont($fontPath, 'U', 13, '', false);
            $pdf->MultiCell(100, 7, date('d F, Y'), 0, 'L', false, 0, 238, 45);

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 13, '', false);
            $pdf->MultiCell(100, 7, 'Serial: ', 0, 'L', false, 0, 210, 52);
            $pdf->SetFont($fontPath, 'U', 13, '', false);
            $pdf->MultiCell(100, 7, ' '.$temporary_degree['student_detail']['Student']['code'] , 0, 'L', false, 0, 225, 52);
            

            $title_font_size = 16;
            $english_title = 'Temporary Certificate of Graduation';

            $content_font_size = 14;

            $toWhom = 'To whom it may concern';

            $pre_content = 'This is to certify that '. (strcasecmp(trim($temporary_degree['student_detail']['Student']['gender']), 'male') == 0 ? 'Mr.' : (strcasecmp(trim($temporary_degree['student_detail']['Student']['gender']), 'female') == 0 ? 'Ms./Mrs.' : 'Mr./Ms./Mrs.'));

            $content = 'has graduated from <u style="font-weight:bold;">'.$temporary_degree['student_detail']['College']['name']. '</u> with <u style="font-weight:bold;">' .$temporary_degree['student_detail']['Curriculum']['english_degree_nomenclature'] .'</u>  on <u style="font-weight:bold;">'. $this->Time->format("F j, Y", $temporary_degree['student_detail']['GraduateList']['graduate_date'], NULL, NULL) . '</u>';
            //if CGPA is Required.

            if ($temporary_degree['student_detail']['Student']['program_id'] == PROGRAM_POST_GRADUATE) {
                if (isset($temporary_degree['student_detail']['ThesisGrade']) && !empty($temporary_degree['student_detail']['ThesisGrade'])) {
                    if ($temporary_degree['student_detail']['ThesisGrade']['used_in_gpa'] || (isset($temporary_degree['student_detail']['ThesisGrade']['GraduationWork']) && strcasecmp(trim($temporary_degree['student_detail']['ThesisGrade']['GraduationWork']['type']), 'thesis') != 0)) {
                        // it is aproject as the grades are calculated 
                    } else {
                        $additionalContent = ' and a Thesis Result of <u><b>' . strtoupper($temporary_degree['student_detail']['ThesisGrade']['grade']) . '</b></u>.';
                    }
                }
            } else if ($temporary_degree['student_detail']['Student']['program_id'] == PROGRAM_PhD) {
                if (isset($temporary_degree['student_detail']['ThesisGrade']) && !empty($temporary_degree['student_detail']['ThesisGrade'])) {
                    if ($temporary_degree['student_detail']['ThesisGrade']['used_in_gpa']) {
                        // it is aproject as the grades are calculated 
                    } else {
                        $additionalContent = ' and scored a board of examiners’ result of <u><b>' . strtoupper($temporary_degree['student_detail']['ThesisGrade']['grade']) . '</b></u>.';
                    }
                }
            } else {
                $additionalContent = '.';
            }

            if (isset($have_agreement) && $have_agreement) {
                $agreementContent = ' upon the discharge of contract responsibility.';
            } else {
                $agreementContent = '.';
            }

            if ($temporary_degree['student_detail']['Student']['program_id'] == PROGRAM_PhD) {
                $content .= '.'. (strcasecmp(trim($temporary_degree['student_detail']['Student']['gender']), 'male') == 0 ? ' He' : (strcasecmp(trim($temporary_degree['student_detail']['Student']['gender']), 'female') == 0 ? ' She' : ' He/She')) . ' conducted a PhD dissertation entitled <b><i>"'. (isset($temporary_degree['student_detail']['ThesisGrade']['GraduationWork']) ? $temporary_degree['student_detail']['ThesisGrade']['GraduationWork']['title'] : ucfirst(strtolower('ERROR: DESSERTATION TITLE NOT FOUND!, RECORD THAT FIRST IN GRADUATION WORKS!!'))).'"</b></i>' . $additionalContent;
            } else {
                $content .= '.'. (strcasecmp(trim($temporary_degree['student_detail']['Student']['gender']), 'male') == 0 ? ' He' : (strcasecmp(trim($temporary_degree['student_detail']['Student']['gender']), 'female') == 0 ? ' She' : ' He/She')) . ' scored a Cumulative Grade Point Average (CGPA) of <u style="font-weight:bold;">'. $temporary_degree['student_detail']['StudentExamStatus']['cgpa'].'</u> out of 4' . $additionalContent;
            }

            //if the student has Exit Exam record it will append it.
            if (!empty($temporary_degree['student_detail']['ExitExam'])) {
                $content .= ' In addition,' . (strcasecmp(trim($temporary_degree['student_detail']['Student']['gender']), 'male') == 0 ? ' he' : (strcasecmp(trim($temporary_degree['student_detail']['Student']['gender']), 'female') == 0 ? ' she' : ' he/she')) . ' scored an avarage of <u style="font-weight:bold;">'. $temporary_degree['student_detail']['ExitExam']['result'].'%</u> in the National Exit Exam.';
            }
            
            $content .= ' This temporary certificate of graduation has been given pending the printing and issuance of actual diploma' . $agreementContent;


            $pdf->writeHTML('');
            $pdf->writeHTML('<br/>');

            //'<td style="line-height:4px; font-size:'.(($content_font_size*3)+8).'px">'. $toWhom.'</td>'

            $certificate_content = '
            <table style="width:100%;text-align:justify;">
                <tr>
                    <td style="width:10%"></td>
                    <td style="width:80%; text-align:center; text-decoration:underline; font-weight:bold; line-height:5px; font-size:'.($title_font_size*4).'px">'.$english_title.'</td>
                    <td style="width:10%"></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="line-height:5px; font-size:'.(($content_font_size*3)+8).'px">'.$pre_content.'</td>
                    <td></td>
                </tr>
                <tr>
                    <td></td>
                    <td style="font-weight:bold;text-decoration:underline; text-align:center; text-align:center;line-height:5px; font-size:'.(($content_font_size*3)+8).'px">'.strtoupper($temporary_degree['student_detail']['Student']['full_name']).'</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="3">&nbsp;</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="line-height:'. ($temporary_degree['student_detail']['Student']['program_id'] != PROGRAM_PhD ? '5px' : '4px').'; font-size:'.(($content_font_size *3)+5).'px">'.$content.'</td>
                    <td></td>
                </tr>
            </table>';

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerif.ttf');
            $pdf->SetFont($fontPath, '', 15, '', true);

            
            $pdf->writeHTML($certificate_content);

            $pdf->SetLineStyle(array('width' => 1, 'dash' => 0, 'color' => array(0, 0, 0)));
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
            //$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style.ttf');
            $pdf->SetFont($fontPath, '', 14, '', true);
            $pdf->MultiCell(125, '', 'University Registrar', 0, 'L', false, 0, 127, 192);
            $pdf->Line(95, 190, 200, 190);


            $pdf->write2DBarcode(BASE_URL_HTTPS.'pages/check_graduate/'.str_replace('/','-',$temporary_degree['student_detail']['Student']['studentnumber']), 'QRCODE,H', 30, 175, 20, 20, $style = array(), 'N');
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerif.ttf');
            $pdf->SetFont($fontPath, '', 8, '', true);
            $pdf->Text(30, 197, $temporary_degree['student_detail']['Student']['studentnumber']);
            
            // reset pointer to the last page
            $pdf->lastPage();
        }
    }

    //output the PDF to the browser

    if(count($temporary_degrees) == 1) {
        $pdf->Output('Temporary_Degree_'.str_replace('/','-',$temporary_degrees[0]['student_detail']['Student']['studentnumber']).'_'.$temporary_degrees[0]['student_detail']['Student']['first_name'].'_'.$temporary_degrees[0]['student_detail']['Student']['middle_name'].'_'.$temporary_degrees[0]['student_detail']['Student']['last_name'].'_'.date('Y-m-d').'.pdf', 'I');
    } else {
        $pdf->Output('Temporary_Degree_'.date('Y-m-d').'.pdf', 'I');
    }


    //$pdf->Output('Temporary_Degree_'.str_replace('/','-', $temporary_degree['student_detail']['Student']['studentnumber']).'_'.$temporary_degree['student_detail']['Student']['first_name'].'_'.$temporary_degree['student_detail']['Student']['middle_name'].'_'.$temporary_degree['student_detail']['Student']['last_name'].'_'.date('Y-m-d').'.pdf', 'I');
    
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
