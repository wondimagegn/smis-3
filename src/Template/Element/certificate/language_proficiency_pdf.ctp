<?php
    App::import('Vendor','tcpdf/tcpdf');
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
    //show header or footer
    $pdf->SetPrintHeader(false); 
    $pdf->SetPrintFooter(false);
    //SetMargins(Left, Top, Right)
    $pdf->SetMargins(0, 10, 0);
    //Font Family, Style, Size
    //$pdf->SetFont("pdfacourier", "", 11);
    $pdf->SetProtection($permissions = array('modify', 'copy', 'extract', 'assemble'), $user_pass = USER_PASSWORD, $owner_pass = OWNER_PASSWORD, $mode = 0, $pubkeys = null);

    $universityName = Configure::read('CompanyName'); 
	$universityAmharicName = Configure::read('CompanyAmharicName');

    $registrarName = Configure::read('RegistrarName'); 
	$registrarAmharicName = Configure::read('RegistrarAmharicName');

    $countryAmharic = Configure::read('ApplicationDeployedCountryAmharic'); 
	$cityAmharic = Configure::read('ApplicationDeployedCityAmharic');
	$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish'); 
	$cityEnglish = Configure::read('ApplicationDeployedCityEnglish');
	$pobox = Configure::read('POBOX');

    $tel = Configure::read('Tel'); 
	$fax = Configure::read('Fax');

    if (count($graduation_letters) == 1) {
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
        $pdf->SetTitle('Language Proficiency Letter for ' .$graduation_letters[0]['Student']['full_name'] .' (' .$graduation_letters[0]['Student']['studentnumber'].')');
        $pdf->SetSubject($graduation_letters[0]['Student']['studentnumber']);
        $pdf->SetKeywords('Language, Proficiency, Letter, '.$graduation_letters[0]['Student']['studentnumber'].', SMiS');
    } else {
        $pdf->SetCreator(PDF_CREATOR);
        $pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
        $pdf->SetTitle('Mass Language Proficiency Letters');
        $pdf->SetSubject('Mass Language Proficiency Letters');
        $pdf->SetKeywords('Language, Proficiency, Letter, SMiS');
    }

    foreach($graduation_letters as $k => $graduation_letter) { 
        $pdf->setPageOrientation('P', true, 0);
        $pdf->AddPage("P");
        $pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
        
        
        $pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_LOGO_HEADER_FOR_TCPDF, '5', '7', 25, 25, '', '', 'N', true, 150, 'C');
        
        $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
        $pdf->SetFont($fontPath, '', 15, '', false);
        $pdf->MultiCell(107, 7, strtoupper($universityName), 0, 'L', false, 0, 13, 10);
        $pdf->SetFont($fontPath, '', 13, '', false);
        $pdf->MultiCell(137, 7, $registrarName, 0, 'L', false, 0, 14, 17);
        
        $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
        $pdf->SetFont($fontPath, '', 19, '', true);
        $pdf->MultiCell(107, 7, strtoupper($universityAmharicName), 0, 'L', false, 0, 132, 8);
        $pdf->SetFont($fontPath, '', 16, '', true);
        $pdf->MultiCell(125, 7, $registrarAmharicName, 0, 'L', false, 0, 125, 16);

        $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
        $pdf->SetFont($fontPath, '', 10, '', false);

        $pdf->MultiCell(115, 7, 'Tel: '.$tel .'      P.O.Box: '.$pobox.'', 0, '', false, 0, 18, 24);
        $pdf->MultiCell(127, 7, $cityEnglish. ',  '. $countryEnglish, 0, '', false, 0, 30, 29);
        
        $pdf->MultiCell(190, 7, 'Fax: '.$fax .'', 0, 'L', false, 0, 138, 24);

        $pdf->SetFont('jiret', '', 12, '', true);
        $pdf->MultiCell(110, 7, $cityAmharic . '፣ ' . $countryAmharic, 0, 'L', false, 0, 140, 29);
        $pdf->Line(2, 38, 207, 38);

        //$pdf->SetFont('jiret', '', 15, '', true);
        //$pdf->MultiCell(157, 7, $cityAmharic . ' ፡ ' . $countryAmharic, 0, 'C', false, 0, 27, 38);
        $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
        $pdf->SetFont($fontPath, '', 11, '', false);
        //$pdf->MultiCell(157, 7, $cityEnglish. ', '. $countryEnglish, 0, 'C', false, 0, 27, 43);
        //Reference Number
        $pdf->SetFont('jiret', '', 12, '', true);
        //$pdf->MultiCell(100, 7, 'ቁጥር:', 0, 'L', false, 0, 139, 44);
        
        $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
        $pdf->SetFont($fontPath, '', 11, '', false);
        //$pdf->MultiCell(100, 7, 'Ref.: ', 0, 'L', false, 0, 139, 48);
        //$pdf->Line(152, 50, 203, 50);
        //Date
        $pdf->SetFont('jiret', '', 12, '', true);
        //$pdf->MultiCell(100, 7, 'ቀን:', 0, 'L', false, 0, 139, 55);
        //$pdf->MultiCell(100, 7, 'ቀን:', 0, 'L', false, 0, 139, 50);
        
        $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
        $pdf->SetFont($fontPath, '', 12, '', false);
        //$pdf->MultiCell(100, 7, 'Date: ', 0, 'L', false, 0, 139, 60);
        $pdf->MultiCell(100, 7, 'Date: ', 0, 'L', false, 0, 139, 50);
        $pdf->MultiCell(100, 7, date('F d, Y').' G.C.', 0, 'L', false, 0, 154, 50);
        //$pdf->MultiCell(100, 7, date('F d, Y').' G.C.', 0, 'L', false, 0, 154, 55);
        $pdf->Line(152, 55, 203, 55);
        //$pdf->Line(2, 66, 207, 66);

        $pdf->SetFont($fontPath, '', 14, '', false);
        $pdf->MultiCell(100, 7, 'To whom it may concern', 0, 'L', false, 0, 15, 65);
        
        //Footer
        $pdf->Line(2, 264, 207, 264);

        if (isset($correct_degree_designation) && $correct_degree_designation && isset($degree_nomenclature_formatted) && !empty($degree_nomenclature_formatted)){
            $degree_designation = $degree_nomenclature_formatted;
        } else {
            $degree_designation = $graduation_letter['Student']['Curriculum']['english_degree_nomenclature'];
        }
        
        $title = 'Certificate of Medium of Instruction';

        //$letter_content = 'This is to certify that <b><u>'. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'Mr.' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'Ms./Mrs.' : 'Mr./Ms./Mrs.')). ' ' . $graduation_letter['Student']['full_name'] . '</u></b> studied <b><u>'. $degree_designation . '</u></b> using English as the only medium of instruction and graduated on <b><u>' .$this->Time->format("F j, Y", $graduation_letter['GraduateList']['graduate_date'], NULL, NULL) . '</u></b> from <b><u>' . $graduation_letter['Student']['College']['name'] . '</u></b> of Arba Minch University.';
        //$letter_content = 'This is to certify that <b><u>'. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'Mr.' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'Ms./Mrs.' : 'Mr./Ms./Mrs.')). ' ' . $graduation_letter['Student']['full_name'] . '</u></b> has completed '. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'his' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'her' : 'his/her')). ' <b><u>'. $degree_designation . '</u></b> study using English as the only medium of instruction and graduated on <b><u>' .$this->Time->format("F j, Y", $graduation_letter['GraduateList']['graduate_date'], NULL, NULL) . '</u></b>.';
        //$letter_content = 'This is to certify that <b><u>'. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'Mr.' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'Ms./Mrs.' : 'Mr./Ms./Mrs.')). ' ' . $graduation_letter['Student']['full_name'] . '</u></b> has completed '. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'his' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'her' : 'his/her')). ' <b><u>'. $degree_designation . '</u></b> study and graduated on <b><u>' .$this->Time->format("F j, Y", $graduation_letter['GraduateList']['graduate_date'], NULL, NULL) . '</u></b> using English as the only medium of instruction.';
        //$letter_content = 'This is to certify that <b><u>'. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'Mr.' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'Ms./Mrs.' : 'Mr./Ms./Mrs.')). ' ' . $graduation_letter['Student']['full_name'] . '</u></b> has completed '. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'his' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'her' : 'his/her')). ' <b><u>'. $degree_designation . '</u></b> study and graduated on <b><u>' .$this->Time->format("F j, Y", $graduation_letter['GraduateList']['graduate_date'], NULL, NULL) . '</u></b> using English as the only medium of instruction.';
        //$letter_content = 'This is to certify that <b><u>'. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'Mr.' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'Ms./Mrs.' : 'Mr./Ms./Mrs.')). ' ' . $graduation_letter['Student']['full_name'] . '</u></b> has completed '. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'his' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'her' : 'his/her')). ' <b><u>'. $degree_designation . '</u></b> study and graduated on <b><u>' .$this->Time->format("F j, Y", $graduation_letter['GraduateList']['graduate_date'], NULL, NULL) . '</u></b> from <u style="font-weight:bold;">'. $graduation_letter['Student']['College']['name'].'</u> with Cumulative Grade Point Average (CGPA) of <u style="font-weight:bold;">'. $graduation_letter['Student']['StudentExamStatus'][0]['cgpa'].'</u> using English as the only medium of instruction.';
        $letter_content = 'This is to certify that <b><u>'. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'Mr.' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'Ms./Mrs.' : 'Mr./Ms./Mrs.')). ' ' . $graduation_letter['Student']['full_name'] . '</u></b> has completed '. (strcasecmp(trim($graduation_letter['Student']['gender']), 'male') == 0 ? 'his' : (strcasecmp(trim($graduation_letter['Student']['gender']), 'female') == 0 ? 'her' : 'his/her')). '<b> <u>'. $degree_designation . '</u></b> study and graduated on <b><u>' .$this->Time->format("F j, Y", $graduation_letter['GraduateList']['graduate_date'], NULL, NULL) . '</u></b> from <u style="font-weight:bold;">'. $graduation_letter['Student']['College']['name'].'</u> using English as the only medium of instruction.';
        
        $graduation_letter_content = '
        <table style="width:100%;text-align:justify;">
            <tr>
                <td style="width:7%; height:20px"></td>
                <td style="width:86%; height:20px"></td>
                <td style="width:7%; height:20px"></td>
            </tr>
            <tr>
                <td></td>
                <td style="line-height:5px; text-align:center; font-size:'.(16*3).'px"><b><u>'.$title. '</b></u></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="height:20px"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="line-height:5px; font-size:'.(13*3).'px">'.$letter_content.'</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="height:40px"></td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="height:50px">With Regards!</td>
                <td></td>
            </tr>
             <tr>
                <td></td>
                <td style="height:20px">_______________________</td>
                <td></td>
            </tr>
            <tr>
                <td></td>
                <td style="height:20px;">University Registrar</td>
                <td></td>
            </tr>
        </table>';

        //nl2br($letter_content)
        $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerif.ttf');
        $pdf->SetFont($fontPath, '', 14, '', false);
        $pdf->writeHTML('');
        $pdf->writeHTML($graduation_letter_content);

        $pdf->SetFont($fontPath, '', 12, '', false);
        
        //$pdf->MultiCell(100, 7, 'Web site: '.UNIVERSITY_WEBSITE.'  Email: '. REGISTRAR_EMAIL .'', 0, 'L', false, 0, 10, 270);
        $pdf->MultiCell(100, 7, 'Email: '. REGISTRAR_EMAIL .'', 0, 'L', false, 0, 8, 268);
        $pdf->SetFont('jiret', '', 12, '', true);
        $pdf->MultiCell(100, 7, 'መልስ ሲጽፉልን የኛን ቁጥር ይጥቀሱ::', 0, 'L', false, 0, 75, 268);

        // Amharic Motto
        $pdf->MultiCell(100, 7, $universityAmharicName . '፣ ' . UNIVERSITY_MOTTO_AM, 0, 'L', false, 0, 70, 281);

        $pdf->SetFont($fontPath, '', 12, '', false);
        $pdf->MultiCell(130, 7, 'When replying, Please, indicate our reference number.', 0, 'L', false, 0, 55, 274);
        $pdf->MultiCell(100, 7, 'Website: '. UNIVERSITY_WEBSITE .'', 0, 'L', false, 0, 145, 268);


        // English Motto
        $pdf->MultiCell(100, 7, $universityName . ', ' . UNIVERSITY_MOTTO_EN, 0, 'L', false, 0, 58, 286);
        // reset pointer to the last page
        $pdf->lastPage();
    }
    //output the PDF to the browser

    if(count($graduation_letters) == 1) {
        $pdf->Output('Language_ Proficiency_Letter_'.str_replace('/','-',$graduation_letters[0]['Student']['studentnumber']).'_'.$graduation_letters[0]['Student']['first_name'].'_'.$graduation_letters[0]['Student']['middle_name'].'_'.$graduation_letters[0]['Student']['last_name'].'_'.date('Y-m-d').'.pdf', 'I');
    } else {
        $pdf->Output('Mass_Language_ Proficiency_Letters_'.date('Y-m-d').'.pdf', 'I');
    }
    
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?>
