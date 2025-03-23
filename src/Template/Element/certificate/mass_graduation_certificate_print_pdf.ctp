<?php
    App::import('Vendor','tcpdf/tcpdf');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
    
    $pdf->SetPrintHeader(false);
    $pdf->SetPrintFooter(false);
    $pdf->SetProtection($permissions = array('modify', 'copy', 'extract', 'assemble'), $user_pass = USER_PASSWORD, $owner_pass = OWNER_PASSWORD, $mode = 0, $pubkeys = null);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
    $pdf->SetTitle('Mass Graduation Certificate');
    $pdf->SetSubject('Mass Graduation Certificate');
    $pdf->SetKeywords('Mass, Graduation, Certificate, SMiS');

    $countryAmharic = Configure::read('ApplicationDeployedCountryAmharic'); 
	$cityAmharic = Configure::read('ApplicationDeployedCityAmharic');
	$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish'); 
	$cityEnglish = Configure::read('ApplicationDeployedCityEnglish');
	$pobox = Configure::read('POBOX');

    foreach ($graduation_certificates as $k => $graduation_certificate) {
        if ($graduation_certificate['student_detail']['Student']['graduated']) {
            //SetMargins(Left, Top, Right)
            $pdf->SetMargins(0, 10, 0);
            $pdf->setPageOrientation('L', true, 0);
            $pdf->AddPage("L");
            $pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
    
            //Image processing
            /* 
            if(strcasecmp($graduation_certificate['student_detail']['University']['Attachment']['0']['group'], 'logo') == 0)	{
                $logo_index = 0;
                $bg_index = 1;
            }
            else {
                $logo_index = 1;
                $bg_index = 0;
            }
    
            $logo_path = $this->Media->file($graduation_certificate['student_detail']['University']['Attachment'][$logo_index]['dirname'].DS.$graduation_certificate['student_detail']['University']['Attachment'][$logo_index]['basename']);
            $logo_mime = $this->Media->mimeType($graduation_certificate['student_detail']['University']['Attachment'][$logo_index]['dirname'].DS.$graduation_certificate['student_detail']['University']['Attachment'][$logo_index]['basename']);
            $logo_mime = explode('/', $logo_mime);
            $logo_mime = strtoupper($logo_mime[1]);
            $bg_path = $this->Media->file($graduation_certificate['student_detail']['University']['Attachment'][$bg_index]['dirname'].DS.$graduation_certificate['student_detail']['University']['Attachment'][$bg_index]['basename']);
            $bg_mime = $this->Media->mimeType($graduation_certificate['student_detail']['University']['Attachment'][$bg_index]['dirname'].DS.$graduation_certificate['student_detail']['University']['Attachment'][$bg_index]['basename']);
    
    
            $bg_mime = explode('/', $bg_mime);
            $bg_mime = strtoupper($bg_mime[1]); 
            */
    
            //$pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/border-background-images-grad-certificate.gif', 3, 3, 580, 400, '', '', '', false, 600, 'C', false, false, 0);
    
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_FULL_PAGE_TRANSPARENT_LOGO_FOR_TCPDF, 0, 15, 180, 180, '', '', '', false, 300, 'C', false, false, 0);
            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_LOGO_HEADER_FOR_TCPDF, 5, 13, 35, 35, '', '', 'N', true, 300, 'C');
            
            /* $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 17, '', false);
            
            $pdf->MultiCell(107, 7, strtoupper($graduation_certificate['student_detail']['University']['University']['name']), 0, 'L', false, 0, 30, 14);
            $pdf->SetFont($fontPath, 'U', 14, '', false);
            $pdf->MultiCell(157, 7, 'OFFICE OF THE REGISTRAR', 0, 'L', false, 0, 35, 21);
    
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
            $pdf->SetFont($fontPath, '', 22, '', true);
            $pdf->MultiCell(107, 7, strtoupper($graduation_certificate['student_detail']['University']['University']['amharic_name']), 0, 'L', false, 0, 190, 14);
            $pdf->SetFont($fontPath, 'U', 18, '', true);
            $pdf->MultiCell(157, 7, 'ሬጅስትራር ጽ/ቤት', 0, 'L', false, 0, 200, 21); */

            // Correct Aamharic and English University Name Posisions

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
            $pdf->SetFont($fontPath, '', 24, '', true);
            $pdf->MultiCell(107, 7, strtoupper($graduation_certificate['student_detail']['University']['University']['amharic_name']), 0, 'L', false, 0, 40, 13);
            $pdf->SetFont($fontPath, 'U', 20, '', true);
            $pdf->MultiCell(157, 7, 'ሬጅስትራር ጽ/ቤት', 0, 'L', false, 0, 50, 22);

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 17, '', false);
            
            $pdf->MultiCell(107, 7, strtoupper($graduation_certificate['student_detail']['University']['University']['name']), 0, 'L', false, 0, 190, 14);
            $pdf->SetFont($fontPath, 'U', 14, '', false);
            $pdf->MultiCell(157, 7, 'OFFICE OF THE REGISTRAR', 0, 'L', false, 0, 195, 22);

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');

            // END Correct Aamharic and English University Name Posisions
            
            
            $pdf->SetFont($fontPath, '', 16, '', true);
            $pdf->MultiCell(157, 7, $cityAmharic . '፡ ' . $countryAmharic, 0, 'C', false, 0, 72, 50);
            
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 12, '', false);
            $pdf->MultiCell(157, 7, $cityEnglish . ', ' . $countryEnglish, 0, 'C', false, 0, 72, 57);
            
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
            $pdf->SetFont($fontPath, '', 12, '', false);
            
            // set visibility only for print or screen
            //$pdf->setVisibility('print');
            $pdf->SetLineStyle(array('width' => 0.1, 'cap' => 'butt', 'join' => 'miter', 'dash' => 3, 'color' => array(211, 211, 211)));
            $pdf->MultiCell(30, 7, "\n\n\n[Photo]\n\n\n\n", 1, 'C', false, 0,30, 33);
            // restore visibility
            //$pdf->setVisibility('all');
            
            /* 
            $pdf->MultiCell(100, 7, 'Tel: '.$graduation_certificate['student_detail']['University']['University']['telephone'], 0, 'L', false, 0, 200, 30);
            $pdf->MultiCell(100, 7, 'Fax: '.$graduation_certificate['student_detail']['University']['University']['fax'].'', 0, 'L', false, 0, 200, 35);
            $pdf->MultiCell(100, 7, 'P.O.Box: '. $graduation_certificate['student_detail']['University']['University']['p_o_box'] , 0, 'L', false, 0, 200, 40); 
            */
            
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
            $pdf->SetFont($fontPath, '', 14, '', true);
            $pdf->MultiCell(100, 7, 'ቀን:', 0, 'L', false, 0, 215, 40);
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
            $pdf->SetFont($fontPath, 'U', 15, '', true);
            $pdf->MultiCell(100, 7, $graduation_certificate['student_detail']['e_month_name'].' '.$graduation_certificate['student_detail']['e_day'].'/'.$graduation_certificate['student_detail']['e_year']. ' ዓ/ም', 0, 'L', false, 0, 224, 40);
    
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 12, '', false);
            $pdf->MultiCell(100, 7, 'Date: ', 0, 'L', false, 0, 215, 47);
            $pdf->SetFont($fontPath, 'U', 12, '', false);
            $pdf->MultiCell(100, 7, date('d F, Y').' G.C', 0, 'L', false, 0, 228, 47);
    
            $pdf->MultiCell(100, 7, 'Serial: ', 0, 'L', false, 0, 215, 52);
            $pdf->SetFont($fontPath, 'U', 12, '', false);
            $pdf->MultiCell(100, 7, ' '.$graduation_certificate['student_detail']['Student']['code'] , 0, 'L', false, 0, 228, 52);
    
            $pdf->MultiCell(100, 7, ' '.'' , 0, 'L', false, 0, 228, 62); 
    
    
            /* 
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
            $pdf->SetFont($fontPath, '', 14, '', true);
            $pdf->MultiCell(100, 7, 'ቀን:', 0, 'L', false, 0, 215, 50);
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
            $pdf->SetFont($fontPath, 'U', 15, '', true);
            $pdf->MultiCell(100, 7, $e_month_name.' '.$e_day.'/'.$e_year.' E.C', 0, 'L', false, 0, 224, 50);
    
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 12, '', false);
            $pdf->MultiCell(100, 7, 'Date: ', 0, 'L', false, 0, 215, 57);
            $pdf->SetFont($fontPath, 'U', 12, '', false);
            $pdf->MultiCell(100, 7, date('d F, Y').' G.C', 0, 'L', false, 0, 228, 57);
    
            $pdf->MultiCell(100, 7, 'Serial: ', 0, 'L', false, 0, 215, 62);
            $pdf->SetFont($fontPath, 'U', 12, '', false);
            $pdf->MultiCell(100, 7, ' '.$graduation_certificate['student_detail']['Student']['code'] , 0, 'L', false, 0, 228, 62); 
            */
    
            //$pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/centered-line-certificate.gif', 13, 65, 270, 5, '', '', '', false, 300, '', false, false, 0);
            
            //$pdf->Line(147, 85, 150, 180);
            $pdf->SetLineStyle(array('width' => 0.5, 'cap' => 'butt', 'join' => 'miter', 'dash' => 0, 'color' => array(0, 0, 0)));
            $pdf->Line(149, 89, 149, 177);
    
            //Content Amharic
            $am_c = explode('STUDENT_NAME', $graduation_certificate_template['GraduationCertificate']['amharic_content']);
            $am_before_name = trim($am_c[0]);
            
            if(isset($am_c[1])) {
                $am_after_name = trim($am_c[1]);
            } else {
                $am_after_name = null;
            }
    
            
            $am_after_name = str_replace('DEGREE_NOMENCLATURE', '<u style="font-family: jiret; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size']+2)*3).'px">'.$graduation_certificate['student_detail']['Curriculum']['amharic_degree_nomenclature'].'</u>', $am_after_name);
            $am_after_name = str_replace('SPECIALIZATION_DEGREE_NOMENCLATURE', '<u style="font-family: jiret; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size']+2)*3).'px">'.$graduation_certificate['student_detail']['Curriculum']['specialization_amharic_degree_nomenclature'].'</u>', $am_after_name);
            $am_after_name = str_replace('GRADUATION_DATE', '<u style="font-family: jiret; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size']+2)*3).'px">'.$graduation_certificate['student_detail']['e_g_month_name'].' <span style="font-family: freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size'])*3).'px">'.$graduation_certificate['student_detail']['e_g_day'].'</span> ቀን <span style="font-family: freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size'])*3).'px">'.$graduation_certificate['student_detail']['e_g_year'].'</span> ዓ/ም'.'</u>', $am_after_name);
            $am_after_name = str_replace('STUDENT_CGPA', '<u style="font-family:freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size'])*3).'px;">'.$graduation_certificate['student_detail']['StudentExamStatus']['cgpa'].'</u>', $am_after_name);
            $am_after_name = str_replace('STUDENT_MCGPA', '<u style="font-family:freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size'])*3).'px">'.(!empty($graduation_certificate['student_detail']['StudentExamStatus']['mcgpa']) ? $graduation_certificate['student_detail']['StudentExamStatus']['mcgpa'] : '***').'</u>', $am_after_name);
            $am_after_name = str_replace('EXIT_EXAM_RESULT', '<u style="font-family:freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size'])*3).'px">'.(!empty($graduation_certificate['student_detail']['ExitExam']['result']) ? $graduation_certificate['student_detail']['ExitExam']['result'] .'%' : '***').'</u>', $am_after_name);
    
            //Content English
            $en_c = explode('STUDENT_NAME', $graduation_certificate_template['GraduationCertificate']['english_content']);
            $en_before_name = trim($en_c[0]);
    
            if(isset($en_c[1])) {
                $en_after_name = trim($en_c[1]);
            } else {
                $en_after_name = null;
            }
    
            
            $en_after_name = str_replace('DEGREE_NOMENCLATURE', '<u style="font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.$graduation_certificate['student_detail']['Curriculum']['english_degree_nomenclature'].'</u>', $en_after_name);
            $en_after_name = str_replace('SPECIALIZATION_DEGREE_NOMENCLATURE', '<u style="font-family:freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.$graduation_certificate['student_detail']['Curriculum']['specialization_english_degree_nomenclature'].'</u>', $en_after_name);
            $en_after_name = str_replace('GRADUATION_DATE', '<u style="font-family:freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.$graduation_certificate['student_detail']['graduated_date'].' G.C</u>', $en_after_name);
            $en_after_name = str_replace('STUDENT_CGPA', '<u style="font-family:freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.$graduation_certificate['student_detail']['StudentExamStatus']['cgpa'].'</u>', $en_after_name);
            $en_after_name = str_replace('STUDENT_MCGPA', '<u style="font-family:freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.(!empty($graduation_certificate['student_detail']['StudentExamStatus']['mcgpa']) ? $graduation_certificate['student_detail']['StudentExamStatus']['mcgpa'] : '***').'</u>', $en_after_name);
            $en_after_name = str_replace('EXIT_EXAM_RESULT', '<u style="font-family:freeserif; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.(!empty($graduation_certificate['student_detail']['ExitExam']['result']) ? $graduation_certificate['student_detail']['ExitExam']['result'] .'%' : '***').'</u>', $en_after_name);
    
            $pdf->writeHTML('');
            $pdf->writeHTML('<br/>');
    
            $certificate_content = '
            <table style="width:100%; text-align:justify;">
                <tr>
                    <td style="width:6%"></td>
                    <td style="text-align:center; text-decoration:underline;width:42%; font-family:jiret; font-size:'.($graduation_certificate_template['GraduationCertificate']['am_title_font_size']*5).'px">'.$graduation_certificate_template['GraduationCertificate']['amharic_title'].'</td>
                    <td style="width:5%"></td>
                    <td style="text-align:center;font-weight:bold; font-family:freeserif; text-decoration:underline; width:42%; font-size:'.($graduation_certificate_template['GraduationCertificate']['en_title_font_size']*4).'px">'.$graduation_certificate_template['GraduationCertificate']['english_title'].'</td>
                    <td style="width:5%"></td>
                </tr>
                <tr>
                    <td></td>
                    <td style="font-family:jiret; line-height:4px; font-size:'.($graduation_certificate_template['GraduationCertificate']['am_content_font_size']*3+4).'px">'.$am_before_name.'</td>
                    <td></td>
                    <td style="font-family:freeserif; font-weight:bold; line-height:5px; font-size:'.($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3).'px">'.$en_before_name.'</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" style="height:5px; font-size:5px">&nbsp;</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="text-decoration:underline; text-align:center; font-family:jiret; font-weight:bold; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size'])*4).'px">'.$graduation_certificate['student_detail']['Student']['full_am_name'].'</td>
                    <td></td>
                    <td style="font-family:freeserif; text-decoration:underline;font-weight:bold; text-align:center; font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3+8)).'px">'.$graduation_certificate['student_detail']['Student']['full_name'].'</td>
                    <td></td>
                </tr>
                <tr>
                    <td colspan="5" style="height:5px; font-size:5px">&nbsp;</td>
                </tr>
                <tr>
                    <td></td>
                    <td style="font-family:jiret; font-weight:bold;line-height:4px; font-size:'.($graduation_certificate_template['GraduationCertificate']['am_content_font_size']*3+2).'px">'.$am_after_name.'</td>
                    <td></td>
                    <td style="font-family:freeserif; line-height:5px; font-size:'.($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3+3).'px">'.$en_after_name.'</td>
                    <td></td>
                </tr>
            </table>';
    
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
            $pdf->SetFont($fontPath, '', 15, '', true);
            //$pdf->AddFont('bookman_old_style', '', 'bookman_old_style.php');
            $pdf->AddFont('freeserif', '', 'freeserif.php');
            $pdf->AddFont('jiret', '', 'jiret.php');
            //$pdf->SetFont('bookman_old_style', '', 15, '', true);
            $pdf->SetFont('freeserif', '', 15, '', true);
            $pdf->writeHTML($certificate_content);
    
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
            $pdf->SetFont($fontPath, '', 15, '', true);
            $pdf->MultiCell(125, '', 'ሬጅስትራር', 0, 'L', false, 0, 125, 195);
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
            $pdf->SetFont($fontPath, '', 15, '', true);
            $pdf->MultiCell(125, '', '/ Registrar', 0, 'L', false, 0, 145, 195);
            $pdf->Line(95, 195, 200, 195);
    
            /*
            return $this->MultiCell($w, $h, $html, $border, $align, $fill, $ln, $x, $y, $reseth, 0, true, $autopadding, 0, 'T', false);
            */
    
            /* $style = array(
                'border' => 2,
                'vpadding' => 'auto',
                'hpadding' => 'auto',
                'fgcolor' => array(0,0,0),
                'bgcolor' => false, //array(255,255,255)
                'module_width' => 1, // width of a single module in points
                'module_height' => 1 // height of a single module in points
            ); */

            $style = array();
            
            $pdf->write2DBarcode(BASE_URL_HTTPS.'pages/check_graduate/'.str_replace('/','-',$graduation_certificate['student_detail']['Student']['studentnumber']), 'QRCODE,H', 260, 175, 20, 20, $style, 'N');
            $pdf->SetFont($fontPath, '', 8, '', true);
            $pdf->Text(258, 197, $graduation_certificate['student_detail']['Student']['studentnumber']);
    
            // set alpha to semi-transparency
            $pdf->SetAlpha(0.3);
    
            // draw gray square
            $pdf->SetFillColor(211, 211, 211);
            $pdf->SetDrawColor(211, 211, 211);
            $pdf->Rect(18, 182, 60, 20, 'DF');
            
            // restore full opacity
            $pdf->SetAlpha(1);
    
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
            $pdf->SetFont($fontPath, '', 12, '', false);
            $pdf->MultiCell(100, 7, 'Tel: '.$graduation_certificate['student_detail']['University']['University']['telephone'], 0, 'L', false, 0, 20, 185);
            $pdf->MultiCell(100, 7, 'Fax: '.$graduation_certificate['student_detail']['University']['University']['fax'].'', 0, 'L', false, 0, 20, 190);
            $pdf->MultiCell(100, 7, 'P.O.Box: '. $graduation_certificate['student_detail']['University']['University']['p_o_box'] , 0, 'L', false, 0, 20, 195);
    
            // reset pointer to the last page
            $pdf->lastPage();
        }
    }

    //output the PDF to the browser
    if(count($graduation_certificates) == 1) {
        $pdf->Output('Graduation_Certificate_'.str_replace('/','-',$graduation_certificates[0]['student_detail']['Student']['studentnumber']).'_'.$graduation_certificates[0]['student_detail']['Student']['first_name'].'_'.$graduation_certificates[0]['student_detail']['Student']['middle_name'].'_'.$graduation_certificates[0]['student_detail']['Student']['last_name'].'_'.date('Y-m-d').'.pdf', 'I');
    } else {
        $pdf->Output('Graduation_Certificate_'.date('Y-m-d').'.pdf', 'I');
    }
    
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
