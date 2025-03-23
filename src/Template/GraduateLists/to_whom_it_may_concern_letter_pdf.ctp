<?php
// ==================== NOT BEING USED, USING FILE FROM ELEMENTS ====================
//debug($graduation_certificate_template);
//debug($graduation_certificate);
App::import('Vendor','tcpdf/tcpdf');
// create new PDF document
    //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
	
    //show header or footer
    $pdf->SetPrintHeader(false); 
    $pdf->SetPrintFooter(false);

    //SetMargins(Left, Top, Right)
    //Font Family, Style, Size
    //$pdf->SetFont("pdfacourier", "", 11);
foreach($graduation_letter as $k=>$graduation_certificate) {
         $pdf->SetMargins(0, 10, 0);
   
    $pdf->setPageOrientation('L', true, 0);
    $pdf->AddPage("L");
    $pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
    
    //Image processing
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
    
    $pdf->Image($bg_path, 0, 7, 195, 195, $bg_mime, '', '', false, 300, 'C', false, false, 0);
    $pdf->Image($logo_path, '5', '5', 35, 35, $logo_mime, '', 'N', true, 300, 'C');
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/palatino_bold.ttf');
    $pdf->SetFont($fontPath, '', 17, '', false);
    $pdf->MultiCell(107, 7, strtoupper($graduation_certificate['student_detail']['University']['University']['name']), 0, 'L', false, 0, 30, 14);
    $pdf->SetFont($fontPath, 'U', 12, '', false);
    $pdf->MultiCell(157, 7, 'OFFICE OF THE UNIVERSITY REGISTRAR', 0, 'L', false, 0, 27, 21);
    
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
    $pdf->SetFont($fontPath, '', 20, '', true);
    $pdf->MultiCell(107, 7, strtoupper($graduation_certificate['student_detail']['University']['University']['amharic_name']), 0, 'L', false, 0, 185, 14);
    $pdf->SetFont($fontPath, 'U', 16, '', true);
    $pdf->MultiCell(157, 7, 'ሬጅስትራር ጽ/ቤት', 0, 'L', false, 0, 195, 21);
    $pdf->SetFont($fontPath, '', 15, '', true);
    $pdf->MultiCell(157, 7, 'አርባምንጭ፡ ኢትዮጵያ', 0, 'C', false, 0, 72, 40);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style_b.ttf');
    $pdf->SetFont($fontPath, '', 11, '', false);
    $pdf->MultiCell(157, 7, 'Arba Minch, Ethiopia', 0, 'C', false, 0, 72, 46);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/post_icon.png', '70', '29', 8, 8, 'PNG', '', '', true, 300, '');
    $pdf->MultiCell(15, 7, '21', 0, 'C', false, 0, 74, 31);
    $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/phone_icon.png', '200', '29', 8, 8, 'PNG', '', '', true, 300, '');
    $pdf->MultiCell(100, 7, '+251-046-8810772
+251-046-8810097-ext. 374/375
    ', 0, 'L', false, 0, 207, 31);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
    $pdf->SetFont($fontPath, '', 14, '', true);
    $pdf->MultiCell(100, 7, 'ቀን:', 0, 'L', false, 0, 215, 53);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
    $pdf->SetFont($fontPath, 'U', 15, '', true);
    $pdf->MultiCell(100, 7, $graduation_certificate['student_detail']['graduated_ethiopic_date']['e_month_name'].' '.$graduation_certificate['student_detail']['graduated_ethiopic_date']['e_day'].'/'.$graduation_certificate['student_detail']['graduated_ethiopic_date']['e_year'].' E.C', 0, 'L', false, 0, 224, 53);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style_b.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->MultiCell(100, 7, 'Date: ', 0, 'L', false, 0, 215, 60);
    $pdf->SetFont($fontPath, 'U', 12, '', false);
    $pdf->MultiCell(100, 7, date('d F, Y').' G.C', 0, 'L', false, 0, 228, 60);
    $pdf->Line(15, 68, 280, 68);
    $pdf->Line(150, 70, 150, 180);
    //Content Amharic
    $am_c = explode('STUDENT_NAME', $graduation_certificate_template['GraduationCertificate']['amharic_content']);
    $am_before_name = trim($am_c[0]);
    if(isset($am_c[1]))
    	$am_after_name = trim($am_c[1]);
    else
    	$am_after_name = null;
    $am_after_name = str_replace('STUDENT_DEPARTMENT', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size']+2)*3).'px">'.$graduation_certificate['student_detail']['Department']['amharic_name'].'</u>', $am_after_name);
    $am_after_name = str_replace('DEGREE_NOMENCLATURE', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size']+2)*3).'px">'.$graduation_certificate['student_detail']['Curriculum']['amharic_degree_nomenclature'].'</u>', $am_after_name);
    $am_after_name = str_replace('GRADUATION_DATE', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size']+2)*3).'px">'.$graduation_certificate['student_detail']['graduated_ethiopic_date']['e_g_month_name'].' '.$graduation_certificate['student_detail']['graduated_ethiopic_date']['e_g_day'].' ቀን '.$graduation_certificate['student_detail']['graduated_ethiopic_date']['e_g_year'].' ዓ.ም'.'</u>', $am_after_name);
    $am_after_name = str_replace('STUDENT_CGPA', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size']+2)*3).'px">'.$graduation_certificate['student_detail']['StudentExamStatus']['cgpa'].'</u>', $am_after_name);
    $am_after_name = str_replace('STUDENT_MCGPA', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size']+2)*3).'px">'.(!empty($graduation_certificate['student_detail']['StudentExamStatus']['mcgpa']) ? $graduation_certificate['student_detail']['StudentExamStatus']['mcgpa'] : '----').'</u>', $am_after_name);

    //Content English
    $en_c = explode('STUDENT_NAME', $graduation_certificate_template['GraduationCertificate']['english_content']);
    $en_before_name = trim($en_c[0]);
    if(isset($en_c[1]))
    	$en_after_name = trim($en_c[1]);
    else
    	$en_after_name = null;
    $en_after_name = str_replace('STUDENT_DEPARTMENT', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.$graduation_certificate['student_detail']['Department']['name'].'</u>', $en_after_name);
    $en_after_name = str_replace('DEGREE_NOMENCLATURE', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.$graduation_certificate['student_detail']['Curriculum']['english_degree_nomenclature'].'</u>', $en_after_name);
    $en_after_name = str_replace('GRADUATION_DATE', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.$graduation_certificate['student_detail']['graduated_date'].' G.C</u>', $en_after_name);
    $en_after_name = str_replace('STUDENT_CGPA', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.$graduation_certificate['student_detail']['StudentExamStatus']['cgpa'].'</u>', $en_after_name);
    $en_after_name = str_replace('STUDENT_MCGPA', '<u style="font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+3).'px">'.(!empty($graduation_certificate['student_detail']['StudentExamStatus']['mcgpa']) ? $graduation_certificate['student_detail']['StudentExamStatus']['mcgpa'] : '-----').'</u>', $en_after_name);

    $pdf->writeHTML('');
    $certificate_content = '
<table style="width:100%">
	<tr>
		<td style="width:6%"></td>
		<td style="text-align:center; width:42%; font-family:jiret; font-size:'.($graduation_certificate_template['GraduationCertificate']['am_title_font_size']*6).'px">'.$graduation_certificate_template['GraduationCertificate']['amharic_title'].'</td>
		<td style="width:5%"></td>
		<td style="text-align:center; width:42%; font-family:bookman_old_style; line-height:5px; font-size:'.($graduation_certificate_template['GraduationCertificate']['en_title_font_size']*3).'px">'.$graduation_certificate_template['GraduationCertificate']['english_title'].'</td>
		<td style="width:5%"></td>
	</tr>
	<tr>
		<td></td>
		<td style="font-family:jiret; font-size:'.($graduation_certificate_template['GraduationCertificate']['am_content_font_size']*3).'px">'.$am_before_name.'</td>
		<td></td>
		<td style="font-family:bookman_old_style; line-height:5px; font-size:'.($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3).'px">'.$en_before_name.'</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="5" style="height:5px; font-size:5px">&nbsp;</td>
	</tr>
	<tr>
		<td></td>
		<td style="text-decoration:underline; text-align:center; font-family:jiret; font-size:'.(($graduation_certificate_template['GraduationCertificate']['am_content_font_size'])*4).'px">'.$graduation_certificate['student_detail']['Student']['full_am_name'].'</td>
		<td></td>
		<td style="text-decoration:underline; text-align:center; text-align:center; font-family:bookman_old_style; line-height:5px; font-size:'.(($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3)+8).'px">'.$graduation_certificate['student_detail']['Student']['full_name'].'</td>
		<td></td>
	</tr>
	<tr>
		<td colspan="5" style="height:5px; font-size:5px">&nbsp;</td>
	</tr>
	<tr>
		<td></td>
		<td style="font-family:jiret; line-height:4px; font-size:'.($graduation_certificate_template['GraduationCertificate']['am_content_font_size']*3).'px">'.$am_after_name.'</td>
		<td></td>
		<td style="font-family:bookman_old_style; line-height:5px; font-size:'.($graduation_certificate_template['GraduationCertificate']['en_content_font_size']*3).'px">'.$en_after_name.'</td>
		<td></td>
	</tr>
</table>';
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
    $pdf->SetFont($fontPath, '', 15, '', true);
    $pdf->AddFont('bookman_old_style', '', 'bookman_old_style.php');
    //$pdf->AddFont('bookman_old_style_bold', '', 'bookman_old_style_b.php');
    $pdf->AddFont('jiret', '', 'jiret.php');
    $pdf->SetFont('bookman_old_style', '', 15, '', true);
	$pdf->writeHTML($certificate_content);
   
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
    $pdf->SetFont($fontPath, '', 15, '', true);
  	 $pdf->MultiCell(125, '', 'ሬጅስትራር', 0, 'L', false, 0, 123, 197);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style.ttf');
    $pdf->SetFont($fontPath, '', 13, '', true);
  	 $pdf->MultiCell(125, '', '/Registrar', 0, 'L', false, 0, 141, 197);
    $pdf->Line(95, 197, 200, 197);
// reset pointer to the last page
    $pdf->lastPage();
}
    //output the PDF to the browser

    $pdf->Output('graduation_certificate.'.date('Y').'.pdf', 'I');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?>
