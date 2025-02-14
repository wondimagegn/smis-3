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
 foreach($graduation_letters as $k=>$graduation_letter) { 
   $pdf->setPageOrientation('P', true, 0);
    $pdf->AddPage("P");
    $pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
    
    //Image processing
    if(strcasecmp($graduation_letter['student_detail']['University']['Attachment']['0']['group'], 'logo') == 0)	{
    	$logo_index = 0;
    }
    else {
    	$logo_index = 1;
    }
	$logo_path = $this->Media->file($graduation_letter['student_detail']['University']['Attachment'][$logo_index]['dirname'].DS.$graduation_letter['student_detail']['University']['Attachment'][$logo_index]['basename']);
	 //HEADER
    $pdf->Image($logo_path, '5', '5', 25, 25, '', '', 'N', true, 300, 'C');
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/palatino_bold.ttf');
    $pdf->SetFont($fontPath, '', 15, '', false);
    $pdf->MultiCell(107, 7, strtoupper($graduation_letter['student_detail']['University']['University']['name']), 0, 'L', false, 0, 13, 12);
    $pdf->SetFont($fontPath, 'U', 11, '', false);
    $pdf->MultiCell(157, 7, 'OFFICE OF THE UNIVERSITY REGISTRAR', 0, 'L', false, 0, 10, 19);
    
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
    $pdf->SetFont($fontPath, '', 20, '', true);
    $pdf->MultiCell(107, 7, strtoupper($graduation_letter['student_detail']['University']['University']['amharic_name']), 0, 'L', false, 0, 135, 11);
    $pdf->SetFont($fontPath, 'U', 16, '', true);
    $pdf->MultiCell(157, 7, 'ዩኒቨርሲቲ ሬጅስትራር ጽ/ቤት', 0, 'L', false, 0, 134, 18);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/post_icon.png', 45, 25, 8, 8, 'PNG', '', '', true, 300, '');
    $pdf->MultiCell(15, 7, '21', 0, 'C', false, 0, 48, 27);
    $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/phone_icon.png', 125, 26, 8, 8, 'PNG', '', '', true, 300, '');
    $pdf->MultiCell(100, 7, '+251-046-8810772
+251-046-8810097-ext. 374/375
    ', 0, 'L', false, 0, 132, 27);
    $pdf->Line(2, 38, 207, 38);
    $pdf->SetFont('jiret', '', 15, '', true);
    $pdf->MultiCell(157, 7, 'አርባምንጭ፡ ኢትዮጵያ', 0, 'C', false, 0, 27, 38);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style_b.ttf');
    $pdf->SetFont($fontPath, '', 11, '', false);
    $pdf->MultiCell(157, 7, 'Arba Minch , Ethiopia', 0, 'C', false, 0, 27, 43);
	 //Reference Number
    $pdf->SetFont('jiret', '', 14, '', true);
    $pdf->MultiCell(100, 7, 'ቁጥር:', 0, 'L', false, 0, 139, 46);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style_b.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->MultiCell(100, 7, 'Ref. No: ', 0, 'L', false, 0, 139, 50);
    $pdf->Line(159, 54, 203, 54);
    //Date
    $pdf->SetFont('jiret', '', 14, '', true);
    $pdf->MultiCell(100, 7, 'ቀን:', 0, 'L', false, 0, 139, 56);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style_b.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->MultiCell(100, 7, 'Date: ', 0, 'L', false, 0, 139, 60);
    $pdf->MultiCell(100, 7, date('d F, Y').' G.C', 0, 'L', false, 0, 152, 59);
    $pdf->Line(152, 64, 203, 64);
    $pdf->Line(2, 66, 207, 66);
    
    //Footer
    $pdf->Line(2, 266, 207, 266);
    
    $letter_content = $graduation_letter_template['GraduationLetter']['content'];
    $letter_content = str_replace('STUDENT_NAME', '<u style="font-size:'.($graduation_letter_template['GraduationLetter']['content_font_size']+3).'">'.$graduation_letter['student_detail']['Student']['full_name'].'</u>', $letter_content);
    $letter_content = str_replace('STUDENT_DEPARTMENT', '<u style="font-size:'.($graduation_letter_template['GraduationLetter']['content_font_size']+3).'">'.$graduation_letter['student_detail']['Department']['name'].'</u>', $letter_content);
    $letter_content = str_replace('DEGREE_NOMENCLATURE', '<u style="font-size:'.($graduation_letter_template['GraduationLetter']['content_font_size']+3).'">'.$graduation_letter['student_detail']['Curriculum']['english_degree_nomenclature'].'</u>', $letter_content);
    $letter_content = str_replace('STUDENT_CERTIFICATE', '<u style="font-size:'.($graduation_letter_template['GraduationLetter']['content_font_size']+3).'">'.$graduation_letter['student_detail']['Curriculum']['certificate_name'].'</u>', $letter_content);
    $letter_content = str_replace('GRADUATION_DATE', '<u style="font-size:'.($graduation_letter_template['GraduationLetter']['content_font_size']+3).'">'.$graduation_letter['student_detail']['graduated_date'].' G.C</u>', $letter_content);
    $letter_content = str_replace('STUDENT_CGPA', '<u style="font-size:'.($graduation_letter_template['GraduationLetter']['content_font_size']+3).'">'.$graduation_letter['student_detail']['StudentExamStatus']['cgpa'].'</u>', $letter_content);
    $letter_content = str_replace('STUDENT_MCGPA', '<u style="font-size:'.($graduation_letter_template['GraduationLetter']['content_font_size']+3).'">'.(!empty($graduation_letter['student_detail']['StudentExamStatus']['mcgpa']) ? $graduation_letter['student_detail']['StudentExamStatus']['mcgpa'] : '-----').'</u>', $letter_content);
    
    //Femal students modification
    if(strcasecmp($graduation_letter['student_detail']['Student']['gender'], 'female') == 0) {
    	$letter_content = str_replace(' him ', ' her ', $letter_content);
    	$letter_content = str_replace(' Him ', ' Her ', $letter_content);
    	$letter_content = str_replace(' HIM ', ' HER ', $letter_content);
    	
    	$letter_content = str_replace('. him ', '. her ', $letter_content);
    	$letter_content = str_replace('. Him ', '. Her ', $letter_content);
    	$letter_content = str_replace('. HIM ', '. HER ', $letter_content);
    	
    	$letter_content = str_replace(' his ', ' hers ', $letter_content);
    	$letter_content = str_replace(' His ', ' Hers ', $letter_content);
    	$letter_content = str_replace(' HIS ', ' HERS ', $letter_content);
    	
    	$letter_content = str_replace('. his ', '. hers ', $letter_content);
    	$letter_content = str_replace('. His ', '. Hers ', $letter_content);
    	$letter_content = str_replace('. HIS ', '. HERS ', $letter_content);
    	
    	$letter_content = str_replace(' he ', ' she ', $letter_content);
    	$letter_content = str_replace(' He ', ' She ', $letter_content);
    	$letter_content = str_replace(' HE ', ' SHE ', $letter_content);
    	
    	$letter_content = str_replace('. he ', '. she ', $letter_content);
    	$letter_content = str_replace('. He ', '. She ', $letter_content);
    	$letter_content = str_replace('. HE ', '. SHE ', $letter_content);
    	
    	$letter_content = str_replace(' himself ', ' herself ', $letter_content);
    	$letter_content = str_replace(' Himself ', ' Herself ', $letter_content);
    	$letter_content = str_replace(' HIMSELF ', ' HERSELF ', $letter_content);
    	
    	$letter_content = str_replace(' himself.', ' herself.', $letter_content);
    	$letter_content = str_replace(' Himself.', ' Herself.', $letter_content);
    	$letter_content = str_replace(' HIMSELF.', ' HERSELF.', $letter_content);
    	
    	$letter_content = str_replace('. himself ', '. herself ', $letter_content);
    	$letter_content = str_replace('. Himself ', '. Herself ', $letter_content);
    	$letter_content = str_replace('. HIMSELF ', '. HERSELF ', $letter_content);
    }
    
    $graduation_letter_content = '
    <table>
    	<tr>
    		<td style="width:7%; height:40px"></td>
    		<td style="width:86%; height:40px"></td>
    		<td style="width:7%; height:40px"></td>
    	</tr>
    	<tr>
    		<td></td>
    		<td style="text-decoration:underline; text-align:center; font-size:'.($graduation_letter_template['GraduationLetter']['title_font_size']*3).'px">'.$graduation_letter_template['GraduationLetter']['title'].'</td>
    		<td></td>
    	</tr>
    	<tr>
    		<td></td>
    		<td style="height:30px"></td>
    		<td></td>
    	</tr>
    	<tr>
    		<td></td>
    		<td style="text-align:left; font-size:'.($graduation_letter_template['GraduationLetter']['content_font_size']*3).'px">'.nl2br($letter_content).'</td>
    		<td></td>
    	</tr>
    </table>
    ';
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->writeHTML('');
    $pdf->writeHTML($graduation_letter_content);
    
    $pdf->MultiCell(100, 7, 'our@amu.edu.et', 0, 'L', false, 0, 10, 270);
    $pdf->SetFont('jiret', '', 12, '', true);
    $pdf->MultiCell(100, 7, 'መልስ ሲጽፉልን የኛን ቁጥር ይጥቀሱ', 0, 'L', false, 0, 72, 270);
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->MultiCell(150, 7, 'When replying, please, indicate our reference No.', 0, 'L', false, 0, 72, 275);
    $pdf->MultiCell(100, 7, 'Fax: +251468810279/820', 0, 'L', false, 0, 145, 270);
     	 // reset pointer to the last page
    $pdf->lastPage();
}
    //output the PDF to the browser

    $pdf->Output('graduation_letter.'.date('Y').'.pdf', 'I');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?>
