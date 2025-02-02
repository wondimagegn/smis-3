<?php
App::import('Vendor','tcpdf/tcpdf');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
$pdf->SetPrintHeader(false); 
$pdf->SetPrintFooter(false);
$pdf->SetMargins(10, 10, 10);

$pdf->setPageOrientation('P', true, 0);
$countryAmharic = Configure::read('ApplicationDeployedCountryAmharic'); 
$cityAmharic = Configure::read('ApplicationDeployedCityAmharic');

$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish'); 
$cityEnglish = Configure::read('ApplicationDeployedCityEnglish');
$pobox=  Configure::read('POBOX');


    $pdf->AddPage("P");
    $pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
    $pdf->Ln(50);
    //Image processing
    if(strcasecmp($university['University']['Attachment']['0']['group'], 'logo') == 0)    {
        $logo_index = 0;
    }
    else {
        $logo_index = 1;
    }
    $logo_path = $this->Media->file($university['University']['Attachment'][$logo_index]['dirname'].DS.$university['University']['Attachment'][$logo_index]['basename']);
     //HEADER
    $pdf->Image($logo_path, '5', '5', 25, 25, '', '', 'N', true, 300, 'C');
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/palatino_bold.ttf');
    $pdf->SetFont($fontPath, '', 16, '', false);
    $pdf->MultiCell(92, 7, ($university['University']['University']['name']), 0, 'C', false, 0, 1, 4);

    $pdf->MultiCell(92, 7, $colleges['College']['name'], 0, 'C', false, 0, 1, 12);
   

    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
    $pdf->SetFont($fontPath, '', 20, '', true);
   
    if(!empty($university['University']) && !empty($university['University']['University'])) {
       $pdf->MultiCell(85, 7, strtoupper($university['University']['University']['amharic_name']), 0, 'C', false, 0, 120, 4);
    }  else {
          $pdf->MultiCell(85, 7,'', 0, 'C', false, 0, 120, 4);
    }

    $pdf->SetFont($fontPath, '', 15, '', false);

    $pdf->MultiCell(85, 7, $colleges['College']['amharic_name'], 0, 'C', false, 0, 120, 12);
    
     //Department/College Address
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);

    $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/post_icon.png', '40', '42', 7, 7, 'PNG', '','', true, 300, '');
     
    $pdf->MultiCell(15, 7,$pobox, 0, 'C', false, 0, 45, 43);
    
    
    if((!empty($departments['Department']) && !empty($departments['Department']['id']) && 
    !empty($departments['Department']['phone'])) || 
        (empty($departments['Department']) && !empty($departments['Department']['id']) && 
        !empty($colleges['College']['phone']))
        ) {
        $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/phone_icon.png', '140', '42', 7, 7, 'PNG', '', '', true, 300, '');
        if((!empty($departments['Department']) && !empty($departments['Department']['id']))) {
            $pdf->MultiCell(100, 7, $departments['Department']['phone'], 0, 'L', false, 0, 146, 43);
        }
        else {
            $pdf->MultiCell(100, 7, $colleges['College']['phone'], 0, 'L', false, 0, 146, 43);
        }
     }
    
 $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style.ttf');
    $pdf->SetFont($fontPath, '', 12, '', false);
    $pdf->Line(2, 49, 207, 49);
    $pdf->SetFont('jiret', '', 15, '', true);
    
    $pdf->MultiCell(157, 7, $cityAmharic.'፡ '.$countryAmharic, 0, 'C', false, 0, 27, 31);
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style_b.ttf');
    $pdf->SetFont($fontPath, '', 11, '', false);
    $pdf->MultiCell(157, 7, ''.$cityEnglish.', '.$countryEnglish.'', 0, 'C', false, 0, 27, 36);
    $student_copy_html='';
   $student_copy_html.='<table>';
   $student_copy_html.='<tr><th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">S.N<u>o</u></th>';
   if(isset($display_field_student['Display']) && !empty($display_field_student['Display'])){     
        foreach ($display_field_student['Display'] as $dk => $dv) {
            if($dv==1){
                  $student_copy_html.= '<th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">'.ucwords(
				  str_replace('_',' ',$dk)).'</th>';
            }
          
        }
    } else {
         $student_copy_html.='<th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">Full name</th>
            
            <th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">Gender</th>
            <th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">Studentnumber</th>
            
            <th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">Admissionyear</th>

            <th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">Program</th>
            <th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">Program Type</th>
            <th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">College</th>
            <th style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">Department</th>';
    }
    $student_copy_html.='</tr>';
    $count=1;
    foreach($students as $key => $student) {
        $student_copy_html.='<tr>';
        $student_copy_html.='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:center">'.$count++.'</td>';
       if(isset($display_field_student['Display']) && !empty($display_field_student['Display'])){     
        foreach ($display_field_student['Display'] as $dk => $dv) {
            if($dv==1){
                
            if($dk=='program_type_id'){
                 $student_copy_html.='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['ProgramType']['name'].'</td>';
            } else if($dk=='program_id'){
                 $student_copy_html.='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['Program']['name'].'</td>';
            } else if($dk=='college_id'){
                 $student_copy_html.='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['College']['name'].'</td>';
            } else if($dk=='department_id') {
                $student_copy_html.='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['Department']['name'].'</td>';
            } else if ($dk=='curriculum_id') {
			      $student_copy_html.='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['Curriculum']['name'].'</td>';
  
			} else {
                $student_copy_html.='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['Student'][$dk].'</td>';
            }
        }
          
        }
    } else {
         $student_copy_html.='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['Student']['full_name'].'</td>
        
        <td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['Student']['gender'].'</td>
        <td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['Student']['studentnumber'].'</td>
        <td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$this->Format->short_date($student['Student']['admissionyear']).'</td>
        <td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['Program']['name'].'</td>
        <td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['ProgramType']['name'].'</td>
        <td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['College']['name'].'</td>
        
        <td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:left">'.$student['Department']['name'].'</td>';
    }
        $student_copy_html.='</tr>';

    }    

$student_copy_html.='</table>';
   
    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style_b.ttf');
    $pdf->SetFont($fontPath, '', 15, '', false);
   $pdf->MultiCell(157, 7, 'Student\'s List   ', 0, 'C', false, 0, 27, 52);
    $pdf->Ln(15);

    $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/bookman_old_style.ttf');
    $pdf->SetFont($fontPath, '', 11, '', false);
    $pdf->writeHTML($student_copy_html);
     // reset pointer to the last page
    
     $pdf->lastPage();
    //output the PDF to the browser

    $pdf->Output('student list.pdf', 'I');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?>