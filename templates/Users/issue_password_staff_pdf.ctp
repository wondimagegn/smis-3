<?php
    App::import('Vendor','tcpdf/tcpdf');
    // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
    //show header or footer

    $pdf->SetProtection($permissions = array('modify', 'copy', 'extract', 'assemble'), $user_pass = USER_PASSWORD, $owner_pass = OWNER_PASSWORD, $mode = 0, $pubkeys = null);

    $pdf->SetPrintHeader(false); 
    $pdf->SetPrintFooter(false);
    //SetMargins(Left, Top, Right)
    $pdf->SetMargins(10, 10, 10);
    $pdf->setPageOrientation('P', true, 0);
    $pdf->AddPage("P");
    $pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
    $pdf->Ln(50);

    $countryAmharic = Configure::read('ApplicationDeployedCountryAmharic'); 
	$cityAmharic = Configure::read('ApplicationDeployedCityAmharic');
	
	$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish'); 
	$cityEnglish = Configure::read('ApplicationDeployedCityEnglish');
	$pobox =  Configure::read('POBOX');

    $pdf->SetCreator(PDF_CREATOR);
	$pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
	$pdf->SetTitle('Password Issue Letter Report for ' . $staff_details['Staff'][0]['Title']['title'] . ' ' . $staff_details['Staff'][0]['full_name'] . ' (' .  $staff_details['User']['username'] . ') ' .(isset($staff_details['Staff'][0]['Department']) && !empty($staff_details['Staff'][0]['Department']['name']) ? $staff_details['Staff'][0]['Department']['name']: (isset($staff_details['Staff'][0]['College']) && !empty($staff_details['Staff'][0]['College']['name']) ? $staff_details['Staff'][0]['College']['name'] : '')).'');
	$pdf->SetSubject('Password Issue Letter Report for ' . $staff_details['Staff'][0]['Title']['title'].' ' .$staff_details['Staff'][0]['full_name'].' ('. $staff_details['User']['username'] .')'.  '');
	$pdf->SetKeywords('Password, Issue, Letter, '.$staff_details['Staff'][0]['first_name'].', '.$staff_details['Staff'][0]['middle_name'].','.$staff_details['Staff'][0]['last_name'].', '.(isset($staff_details['Staff'][0]['Department']) && !empty($staff_details['Staff'][0]['Department']['name']) ? $staff_details['Staff'][0]['Department']['name'] : '').', '.(isset($staff_details['Staff'][0]['College']) && !empty($staff_details['Staff'][0]['College']['name']) ? $staff_details['Staff'][0]['College']['name'] : '').','.$staff_details['Staff'][0]['Title']['title'].','.$staff_details['User']['email'].', SMiS');

    //Image processing

    /* if (isset($university['University'])) {
        if (strcasecmp($university['Attachment']['0']['group'], 'logo') == 0) {
            $logo_index = 0;
        } else {
            $logo_index = 1;
        }
    }
    
    $logo_path = $this->Media->file($university['Attachment'][$logo_index]['dirname']. DS.$university['Attachment'][$logo_index]['basename']); */
    //HEADER

    $pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_LOGO_HEADER_FOR_TCPDF, '5', '5', 25, 25, '', '', 'N', true, 300, 'C');
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
    $pdf->SetFont($fontPath, '', 14, '', false);
    $pdf->MultiCell(92, 7, ($university['University']['name']), 0, 'C', false, 0, 1, 10);
    $pdf->SetFont($fontPath, '', 13, '', false);

    if(isset( $staff_details['Staff'][0]['College']['name'])) {
        $pdf->MultiCell(92, 7, $staff_details['Staff'][0]['College']['name'], 0, 'C', false, 0, 1, 17);
    } else {
        $pdf->MultiCell(92, 7, '', 0, 'C', false, 0, 1, 17);
    }

    //$pdf->SetFont($fontPath, 'U', 13, '', false);
    $pdf->SetFont($fontPath, '', 13, '', false);

    if(!empty($staff_details['Staff'][0]['Department']) && !empty($staff_details['Staff'][0]['Department']['id'])) {
		$pdf->MultiCell(92, 7, $staff_details['Staff'][0]['Department']['type']. ' of '. $staff_details['Staff'][0]['Department']['name'], 0, 'C', false, 0, 1, 22);
    } else {
		$pdf->MultiCell(92, 7, '', 0, 'C', false, 0, 1, 22);
    }

    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
    $pdf->SetFont($fontPath, '', 18, '', true);
    $pdf->MultiCell(85, 7, strtoupper($university['University']['amharic_name']), 0, 'C', false, 0, 120, 10);
    $pdf->SetFont($fontPath, '', 16, '', false);

    if(!empty($staff_details['Staff'][0]['College']['amharic_name']) && !empty( $staff_details['Staff'][0]['College']['amharic_name'])) {
		$pdf->MultiCell(85, 7, $staff_details['Staff'][0]['College']['amharic_name'], 0, 'C', false, 0, 120, 17);
    } else {
		$pdf->MultiCell(85, 7, '', 0, 'C', false, 0, 120, 17);
    }

    //$pdf->SetFont($fontPath, 'U', 16, '', false);
    $pdf->SetFont($fontPath, '', 16, '', false);

    if (!empty($staff_details['Staff'][0]['Department']) && !empty($staff_details['Staff'][0]['Department']['id'])) {
		$pdf->MultiCell(85, 7, '' . $staff_details['Staff'][0]['Department']['amharic_name'] . ' '. $staff_details['Staff'][0]['Department']['type_amharic'], 0, 'C', false, 0, 120, 22);
    }  else {
	    $pdf->MultiCell(85, 7, '', 0, 'C', false, 0, 120, 22);
    }

	//Department/College Address

    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    // $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/post_icon.png', '40', '26', 7, 7, 'PNG', '', '', true, 300, '');
    $pdf->MultiCell(30, 7, 'P.O.Box: '. $pobox, 0, 'C', false, 0, 34, 35);

    if ((!empty($staff_details['Staff'][0]['Department'])  && !empty($staff_details['Staff'][0]['Department']['id'])  && !empty($staff_details['Staff'][0]['Department']['phone']))  || (empty($staff_details['Staff'][0]['Department']) && !empty($staff_details['Staff'][0]['Department']['id']) && !empty($staff_details['Staff'][0]['College']['phone']))) {
        
        //$pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/phone_icon.png', '140', '26', 7, 7, 'PNG', '', '', true, 300, '');
    	
        if ((!empty($staff_details['Staff'][0]['Department']) && !empty($staff_details['Staff'][0]['Department']['id']))) {
    		$pdf->MultiCell(100, 7, 'Tel: '. $staff_details['Staff'][0]['Department']['phone'], 0, 'L', false, 0, 146, 35);
    	} else {
			$pdf->MultiCell(100, 7, 'Tel: '. $staff_details['Staff'][0]['College']['phone'], 0, 'L', false, 0, 146, 35);
		}
	}

    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->Line(2, 43, 207, 43);
    $pdf->SetFont('jiret', '', 14, '', true);
    $pdf->MultiCell(157, 7, $cityAmharic . '፡ ' . $countryAmharic, 0, 'C', false, 0, 27, 31);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->MultiCell(157, 7, $cityEnglish . ', ' . $countryEnglish, 0, 'C', false, 0, 27, 36);
   
    $welcomeFirstTime = null;

    if(!empty($staff_details['User']['last_login'])) { 
        $welcomeFirstTime= '<td colspan="2">This is a password reset letter, please login to SMiS <b>('.BASE_URL.')</b>, using the account details below: </td>';
    } else {
        $welcomeFirstTime= '<td colspan="2">Welcome to '.$university['University']['name'].'! It is exciting world of knowledge.
        All academic related transactions are handled by SMiS. Inorder to access the portal <b>('.BASE_URL.')</b>, use the account details below: </td>';
    } 

    $staff_copy_html = '
    <table style="width:100%" border="0" cellpadding="0" cellspacing="0" >
        <tr><td colspan="2">Dear ' . (isset($staff_details['Staff'][0]['Title']['title']) ? $staff_details['Staff'][0]['Title']['title'] . ' ' : '' ) . $staff_details['Staff'][0]['full_name']. ', </td></tr>
        <tr><td colspan="2">&nbsp;</td></tr>
        <tr>'.$welcomeFirstTime.'</tr>
        <tr><td colspan="2"> &nbsp;</td></tr>
        <tr>
            <td style="width:15%">&nbsp;</td>
            <td style="width:85%"><span style="padding-left: 5px;">Username: &nbsp;&nbsp;'.$staff_details['User']['username'].'</span></td>
        </tr>
        <tr>
            <td style="width:15%">&nbsp;</td>
            <td style="width:85%"><span style="padding-left: 5px;">Temporary Password: &nbsp;&nbsp;'.$password.'</span></td>
        </tr>
        <tr>
            <td>&nbsp;</td>
            <td><span style="padding-left: 5px;">Recovery Email: &nbsp;&nbsp;'.$staff_details['User']['email'].'</span></td>
        </tr>
        <tr>
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
                        <li> You are advised to keep your password secure and secret particularly if using a shared computer. </li>
                        <li> You can use the above registered email <b>('.$staff_details['User']['email'].')</b> on Forgot password link: <b>'.BASE_URL_HTTPS.'users/forget</b> to recover your username and password if forgotten. </li>
                    </ol>
                </p>
            </td>
        </tr>
        <tr><td colspan="2"> &nbsp;</td></tr>
    </table>
    <br />
    <br />';
	
	$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
	$pdf->SetFont($fontPath, '', 14, '', false);
    $pdf->MultiCell(157, 7, 'Password Issue Letter', 0, 'C', false, 0, 27, 46);
	$pdf->Ln(15);
	$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->writeHTML($staff_copy_html);
    // reset pointer to the last page
    $pdf->lastPage();
    //output the PDF to the browser
    $pdf->Output('Password_Issue_Letter_'.$staff_details['Staff'][0]['full_name'].'_('.$staff_details['User']['username'].')_'.date('Y-m-d').'.pdf', 'I');
    
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
