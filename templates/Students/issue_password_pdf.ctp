<?php
App::import('Vendor', 'tcpdf/tcpdf');
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

	$pdf->SetProtection($permissions = array('modify', /* 'copy', 'extract', */ 'assemble'), $user_pass = USER_PASSWORD, $owner_pass = OWNER_PASSWORD, $mode = 0, $pubkeys = null);
	 
    //show header or footer
    $pdf->SetPrintHeader(false); 
    $pdf->SetPrintFooter(false);
    //SetMargins(Left, Top, Right)
    $pdf->SetMargins(10, 10, 10);
    //$pdf->SetTopMargin(10);
    //Font Family, Style, Size
    //$pdf->SetFont("pdfacourier", "", 11);
    $pdf->setPageOrientation('P', true, 0);


	$universityName = Configure::read('CompanyName'); 
	$universityAmharicName = Configure::read('CompanyAmharicName');

    $registrarName = Configure::read('RegistrarName'); 
	$registrarAmharicName = Configure::read('RegistrarAmharicName');

    $countryAmharic = Configure::read('ApplicationDeployedCountryAmharic'); 
	$cityAmharic = Configure::read('ApplicationDeployedCityAmharic');
	$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish'); 
	$cityEnglish = Configure::read('ApplicationDeployedCityEnglish');
	$pobox = Configure::read('POBOX');

	//debug($student_passwords);

	if ((isset($student_passwords) && count($student_passwords) > 1)) {
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
		$pdf->SetTitle('Issue/Reset Password for ' .(isset($student_passwords[0]['Department']) && !empty($student_passwords[0]['Department']['name']) ? $student_passwords[0]['Department']['name']: $student_passwords[0]['College']['name']).' '.(isset($section_for_file_name) ? $section_for_file_name : '') .'');
		$pdf->SetSubject('Issue/Reset Password');
		$pdf->SetKeywords('Issue, Reset, Password, '.$student_passwords[0]['Student']['full_name'].', '. (isset($student_passwords[0]['Department']) && !empty($student_passwords[0]['Department']['name']) ? $student_passwords[0]['Department']['name'] : $student_passwords[0]['College']['name']).', SMiS');
	} else if ((count($student_passwords) == 1 )) {
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
		$pdf->SetTitle('Issue/Reset Password for ' . $student_passwords[0]['Student']['full_name'] .' (' .$student_passwords[0]['Student']['studentnumber'].')');
		$pdf->SetSubject('Issue/Reset Password');
		$pdf->SetKeywords('Issue, Reset, Password, '.$student_passwords[0]['Student']['full_name'].', '.$student_passwords[0]['Student']['studentnumber']. ', SMiS');
	}

    if (!empty($student_passwords)) {

        foreach ($student_passwords as $key => $student_copy) {
            
            $pdf->AddPage("P");
            $pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
            $pdf->Ln(50);

            $pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_LOGO_HEADER_FOR_TCPDF, '5', '5', 25, 25, '', '', 'N', true, 300, 'C');
                
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 13, '', false);
            $pdf->MultiCell(92, 7, ((isset($universityName) ? $universityName : $student_copy['University']['University']['name'])), 0, 'C', false, 0, 1, 10);
            
            $pdf->SetFont($fontPath, '', 12, '', false);
            if (!empty($student_copy['College']['id'])) {
                $pdf->MultiCell(92, 7, $student_copy['College']['name'], 0, 'C', false, 0, 1, 16);
            } else {
                $pdf->MultiCell(92, 7, '', 0, 'C', false, 0, 1, 16);
            }
            
            //$pdf->SetFont($fontPath, 'U', 13, '', false);
            $pdf->SetFont($fontPath, '', 12, '', false);
            if (!empty($student_copy['Department']['id'])) {
                $pdf->MultiCell(92, 7,  $student_copy['Department']['type']. ' of '. $student_copy['Department']['name'], 0, 'C', false, 0, 1, 22);
            } else {
                $pdf->MultiCell(92, 7, (($student_copy['Program']['id'] == PROGRAM_REMEDIAL || $student_copy['Student']['program_id'] == PROGRAM_REMEDIAL) ?  'Remedial Program' : 'Freshman Program'), 0, 'C', false, 0, 1, 22);
            }

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
            $pdf->SetFont($fontPath, '', 16, '', true);
            $pdf->MultiCell(85, 7, (isset($universityAmharicName) ? $universityAmharicName : $student_copy['University']['University']['amharic_name']), 0, 'C', false, 0, 120, 10);
            
            $pdf->SetFont($fontPath, '', 15, '', false);
            if (!empty($student_copy['College']['amharic_name']) && !empty($student_copy['College']['id'])) {
                $pdf->MultiCell(85, 7, $student_copy['College']['amharic_name'], 0, 'C', false, 0, 120, 16);
            } else {
                $pdf->MultiCell(85, 7, '', 0, 'C', false, 0, 120, 16);
            }

            //$pdf->SetFont($fontPath, 'U', 16, '', false);
            $pdf->SetFont($fontPath, '', 15, '', false);
            if (!empty($student_copy['Department']['id'])) {
                $pdf->MultiCell(85, 7,  'የ' . $student_copy['Department']['amharic_name'] . ' ' . $student_copy['Department']['type_amharic'], 0, 'C', false, 0, 120, 22);
            }  else {
                $pdf->MultiCell(85, 7, (($student_copy['Program']['id'] == PROGRAM_REMEDIAL || $student_copy['Student']['program_id'] == PROGRAM_REMEDIAL) ?  'የአቅም ማሻሻያ ፕሮግራም' : 'የመጀመሪያ አመት ተማሪዎች'), 0, 'C', false, 0, 120, 22);
            }

            //Department/College Address
            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
            $pdf->SetFont($fontPath, '', 12, '', false);
            // $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/post_icon.png', '40', '26', 7, 7, 'PNG', '', '', true, 300, '');
            $pdf->MultiCell(30, 7, 'P.O.Box: '. $pobox, 0, 'C', false, 0, 34, 35);

            if ((!empty($student_copy['Department']['id']) && !empty($student_copy['Department']['phone']) ) || (!empty($student_copy['College']['id']) && !empty($student_copy['College']['phone']))) {
                //$pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/phone_icon.png', '140', '26', 7, 7, 'PNG', '', '', true, 300, '');
                if (!empty($student_copy['Department']['id'])) {
                    $pdf->MultiCell(100, 7, 'Tel: '. $student_copy['Department']['phone'], 0, 'L', false, 0, 146, 35);
                } else if (!empty($student_copy['College']['id'])) {
                    $pdf->MultiCell(100, 7, 'Tel: '. $student_copy['College']['phone'], 0, 'L', false, 0, 146, 35);
                }
            }

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
            $pdf->SetFont($fontPath, '', 12, '', false);
            $pdf->Line(2, 43, 207, 43);

            $pdf->SetFont('jiret', '', 12, '', true);
            $pdf->MultiCell(157, 7, $cityAmharic . '፡ ' . $countryAmharic, 0, 'C', false, 0, 27, 31);

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
            $pdf->SetFont($fontPath, '', 10, '', false);
            $pdf->MultiCell(157, 7, $cityEnglish . ', ' . $countryEnglish, 0, 'C', false, 0, 27, 36);

            $pdf->Line(2, 264, 207, 264);
            
            
            $welcomeFirstTime = null;

            if (!empty($student_copy['User']['last_login'])) {
                $welcomeFirstTime = 'This is a password reset letter, please login to our student information system portal (' . BASE_URL . '), using the account below:';
            } else {
                $welcomeFirstTime = 'Welcome to ' . $student_copy['University']['University']['name'] . ' and its exciting world of knowledge. 
                We assure you that you will definitely benefit from your stay at ' .  $student_copy['University']['University']['name'] . '. 
                All academic related transaction is handled by our student management information system. Inorder to access the student portal (' . BASE_URL . '), use the account below:';
            }

            $student_copy_html = '
            <table style="width:100%" border="0" cellpadding="0" cellspacing="0" >
                <tr><td colspan="2">Dear '. $student_copy['Student']['full_name'] .', </td></tr>
                <tr><td colspan="2">&nbsp;</td></tr>
                <tr>'.$welcomeFirstTime.'</tr>
                <tr><td colspan="2"> &nbsp;</td></tr>
                <tr>
                    <td style="width:15%">&nbsp;</td>
                    <td style="width:85%"><br /><br /><span style="padding-left: 5px;">Username: &nbsp;&nbsp;'.$student_copy['Student']['studentnumber'].'</span></td>
                </tr>
                <tr>
                    <td style="width:15%">&nbsp;</td>
                    <td style="width:85%"><span style="padding-left: 5px;">Temporary Password: &nbsp;&nbsp;'. $student_copy['Student']['password_flat'] .'</span></td>
                </tr>' . (isset($reset_password_by_email) && $reset_password_by_email  ?
                '<tr>
                    <td>&nbsp;</td>
                    <td><span style="padding-left: 5px;">Recovery Email: &nbsp;&nbsp;'.$student_copy['Student']['email'].'</span></td>
                </tr>' : '') . 
                '<tr>
                    <td colspan="2"> &nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2"> &nbsp;</td>
                </tr>
                <tr>
                    <td colspan="2" style="width:100%"><u style="font-weight: bold;">Important Notes:</u>
                        <p>
                            <ol>
                                <li> For first time login, you will be forced to chanage the above temporary password to your own choosen password. </li>
                                <li> Make sure you provide strong password when your are presented with password change page and always remember your password. </li>
                                <li> You are advised to keep your password secure and secret particularly if using a shared computer. </li>'
                                . (isset($reset_password_by_email) && $reset_password_by_email ? '<li> You can use the above registered email <b>('.$student_copy['Student']['email'].')</b> on Forgot password link: <b>'.BASE_URL_HTTPS.'users/forget</b> to recover your username and password if forgotten. </li>' : '') .
                            '</ol>
                        </p>
                    </td>
                </tr>
                <tr><td colspan="2"> &nbsp;</td></tr>
            </table>
            <br />
            <br />';

            $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
			$pdf->SetFont($fontPath, '', 14, '', false);
			$pdf->MultiCell(157, 7, 'Student\'s Password Issue Letter', 0, 'C', false, 0, 27, 46);
			$pdf->Ln(15);
			
			$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
			$pdf->SetFont($fontPath, '', 12, '', false);
			$pdf->writeHTML($student_copy_html);

			//$pdf->Image($_SERVER['DOCUMENT_ROOT'] . REGISTRAR_TRANSPARENT_STAMP_FOR_TCPDF, '175', '175', 40, 40, '', '', 'N', true, 300, 'C');


			//Footer
			$pdf->SetFont($fontPath, '', 12, '', false);
			
			//$pdf->MultiCell(100, 7, 'Web site: '.UNIVERSITY_WEBSITE.'  Email: '. REGISTRAR_EMAIL .'', 0, 'L', false, 0, 10, 270);
			$pdf->MultiCell(100, 7, 'Portal: '. PORTAL_URL_HTTPS .'', 0, 'L', false, 0, 8, 268);
			// $pdf->SetFont('jiret', '', 12, '', true);
			// $pdf->MultiCell(100, 7, 'መልስ ሲጽፉልን የኛን ቁጥር ይጥቀሱ::', 0, 'L', false, 0, 75, 268);

			// Amharic Motto
			$pdf->MultiCell(100, 7, $universityAmharicName . '፣ ' . UNIVERSITY_MOTTO_AM, 0, 'L', false, 0, 70, 281);

			$pdf->SetFont($fontPath, '', 11, '', false);
			//$pdf->MultiCell(130, 7, 'When replying, Please, indicate our reference number.', 0, 'L', false, 0, 55, 274);
			$pdf->MultiCell(100, 7, 'Website: '. UNIVERSITY_WEBSITE .'', 0, 'L', false, 0, 145, 268);


			// English Motto
			$pdf->MultiCell(100, 7, $universityName . ', ' . UNIVERSITY_MOTTO_EN, 0, 'L', false, 0, 58, 286);
        }

        $pdf->lastPage();
        //output the PDF to the browser

        //$pdf->Output('password_issue.pdf', 'I');

        if ((count($student_passwords) == 1)) {
			$pdf->Output('Password_Issue_Reset_For_'.str_replace('/','-',$student_passwords[0]['Student']['studentnumber']).'_'.$student_passwords[0]['Student']['full_name'].'_'. (isset($section_for_file_name) ? $section_for_file_name : '') . '_'.date('Y-m-d').'.pdf', 'I');
		} else {
			$pdf->Output('Password_Issue_Reset_For_'.(isset($student_passwords[0]['Department']) && !empty($student_passwords[0]['Department']['name']) ? $student_passwords[0]['Department']['name']: $student_passwords[0]['College']['name']).'_'. (isset($section_for_file_name) ? $section_for_file_name : '') .'_'.date('Y-m-d').'.pdf', 'I');
		}
        /*
        I: send the file inline to the browser.
        D: send to the browser and force a file download with the name given by name.
        F: save to a local file with the name given by name.
        S: return the document as a string.
        */
    }

