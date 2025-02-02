<?php

App::import('Vendor','tcpdf/tcpdf');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true);  
//show header or footer
$pdf->SetPrintHeader(false); 
$pdf->SetPrintFooter(false);
$countryAmharic = Configure::read('ApplicationDeployedCountryAmharic'); 
$cityAmharic = Configure::read('ApplicationDeployedCityAmharic');
$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish'); 
$cityEnglish = Configure::read('ApplicationDeployedCityEnglish');
$pobox=  Configure::read('POBOX');	
    // set font
    $pdf->SetMargins(3, 1, 3);
    $pdf->SetFont("freeserif", "", 11);

    $pdf->setPageOrientation('L', true, 0);
   
    $header = '<table style="width:100%;">
    	<tr>
    		<td style="text-align:center; font-weight:bold">ARBA MINCH UNIVERSITY</td>
    	</tr>
<tr>
    		<td style="text-align:center; font-weight:bold">OFFICE OF THE REGISTRAR</td>
    	</tr>
<tr>
    		<td style="text-align:center; font-weight:bold;text-decoration:underline;">GRADUATING CLASS REPORT FORM</td>
    	</tr>
    	</table>';
  
   $count = 1;
   $graduationCriteria='';
   $excludeMajorColumn='';
   foreach($students_for_senate_list_pdf as $c_id => $students) 
   {
	

// 0 - program, 1-program type, 2 - department, 3- curriculum name, 4 - minimum credit point, 5-amharic degree nomenclature, 6-specialization amharic degree nomenclature, 7-english degree nomenclature, 8-specialization english degree nomenclature
	$curriculumDetails = explode('~',$c_id); 
	if(isset($curriculumDetails[9]) && $curriculumDetails[9]=='ECTS Credit Point') {
        	$typeCredit = 'ECTS';
	} else {
		$typeCredit = 'Credit';
	}

        // add a page
         $pdf->AddPage("L");
         $graduationCriteria .= $header;
	 $graduationCriteria .= '<br/><table class="fs13 summery">
		<tr>
			<td style="width:22%">Academic Year:</td>
			<td style="width:78%; font-weight:bold">'. $defaultacademicyear.'('.$ethiopicYear.' E.C)'.'</td>
		</tr>
		<tr>
			<td style="width:22%">Department:</td>
			<td style="width:78%; font-weight:bold">'.$curriculumDetails[2].'</td>
		</tr>

		<tr>
			<td>Program:</td>
			<td style="font-weight:bold">'.$curriculumDetails[0].'</td>
		</tr>

		<tr>
			<td>Program Type:</td>
			<td style="font-weight:bold">'.$curriculumDetails[1].'</td>
		</tr>

		<tr>
			<td>Curriculum:</td>
			<td style="font-weight:bold">'.$curriculumDetails[3].'</td>
		</tr>
		<tr>
			<td>Degree Designation:</td>
			<td style="font-weight:bold">'.$curriculumDetails[7].'</td>
		</tr>';

		if(!empty($curriculumDetails[8])) {
		
		$graduationCriteria.='<tr>
			<td>Specialization:</td>
			<td style="font-weight:bold">'.$curriculumDetails[8].'</td>
		</tr>';
		}

		$graduationCriteria .= '
		<tr>
			<td>Degree Designation (Amharic):</td>
			<td class="bold">'.$curriculumDetails[5].'</td>
		</tr>';
		
		if(!empty($curriculumDetails[6])) {
		
		$graduationCriteria .= '<tr>
			<td>Specialization (Amharic):</td>
			<td>'.$curriculumDetails[6].'</td>
		</tr>';
		
		}
		
		$graduationCriteria .= '
		<tr>
			<td>Required '.$typeCredit.' for Graduation:</td>
			<td style="font-weight:bold">'.$curriculumDetails[4].'</td>
		</tr></table>';
	$graduationCriteria .= '<br/><table cellpadding="1" style="padding-left:2px;text-align:left;" >';
	if((strcasecmp($excludeMajor,'0')==0)) {
   		$excludeMajorColumn.='<th style="width:7%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">MCGPA</th>';
	}
	
	$graduationCriteria .= '<tr>
			<th style="width:5%;border:1px solid #000000; border-bottom:2px solid #000000;text-align:center ">S.No</th>
			<th style="width:10%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">ID</th>			
			<th style="width:25%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">Student Name</th>
			
			<th style="width:5%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">Sex</th>
			<th style="width:6%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">'.$typeCredit.' Taken</th>
<th style="width:5%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">Major '.$typeCredit.' Taken</th>
			<th style="width:7%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">'.$typeCredit.' Exempted</th>
	<th style="width:7%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">'.$typeCredit.' Transfered</th>
			<th style="width:7%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">CGPA</th>
			'.$excludeMajorColumn.'

		<th style="width:16%;text-align:center;border:1px solid #000000; border-bottom:2px solid #000000; ">Remark</th>
		</tr>';
        $s_count = 1;
    $excludeMajorCGPA='';
	foreach($students as $key => $student) {
		$credit_hour_sum = 0;
		$major_credit_hour_sum=0;
		
	    $st_credit_hour_sum = 0;
		$not_used_gpa_sum=0;
		$dropped_credit_sum=0;
		
		foreach($student['Student']['StudentExamStatus'] as $ses_key => $ses_value) {
		//	$credit_hour_sum += $ses_value['credit_hour_sum'];
$major_credit_hour_sum+=$ses_value['m_credit_hour_sum'];
		}
		
		foreach($senateList['Student']['CourseDrop'] as $drop_key => $drop_value) {
			  if(isset($drop_value['CourseRegistration']['PublishedCourse']['Course']) && !empty($drop_value['CourseRegistration']['PublishedCourse']['Course'])){
			    if($drop_value['CourseRegistration']['PublishedCourse']['Course']){
			    	if($drop_value['registrar_confirmation']==1 && $drop_value['department_approval']==1){
			    		 $dropped_credit_sum+=$drop_value['CourseRegistration']['PublishedCourse']['Course']['credit'];
			  		
			    	}
			    	
			    }
			  
			  }
		}
		
		foreach($senateList['Student']['CourseAdd'] as $ses_key => $ses_value) {
			if($ses_value['PublishedCourse']['Course']['GradeType']['used_in_gpa']==false){
				$not_used_gpa_sum+= $ses_value['PublishedCourse']['Course']['credit'];
			}
			$credit_hour_sum += $ses_value['PublishedCourse']['Course']['credit'];
		}
		
		
		foreach($senateList['Student']['CourseRegistration'] as $ses_key => $ses_value) {
		  
			if($ses_value['PublishedCourse']['Course']['GradeType']['used_in_gpa']==false){
				$not_used_gpa_sum+= $ses_value['PublishedCourse']['Course']['credit'];
			}
			$credit_hour_sum += $ses_value['PublishedCourse']['Course']['credit'];
		}
		
		if(($credit_hour_sum-$dropped_credit_sum)>$senateList['Student']['Curriculum']['minimum_credit_points']){
					$credit_hour_sum= $senateList['Student']['Curriculum']['minimum_credit_points'];
		} else {
				   $credit_hour_sum=($credit_hour_sum-$dropped_credit_sum);
		}
		
		if((strcasecmp($excludeMajor,'0')==0)) {
		$excludeMajorCGPA='<td style="border:1px solid #000000;text-align:center; border-bottom:2px solid #000000; ">
		'.$student['Student']['StudentExamStatus'][0]['mcgpa'].
		'</td>';
		}

		$graduationCriteria .='<tr><td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center; ">'.
		$s_count++.'</td>
		<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center;">'.$student['Student']['studentnumber'].
		'</td><td style="border:1px solid #000000; border-bottom:2px solid #000000; ">'.$student['Student']['full_name'].'</td>
		<td style="border:1px solid #000000; border-bottom:2px solid #000000; text-align:center;">
		'.(strcasecmp($student['Student']['gender'], 'male') == 0 ? 'M' : 'F').'</td>
		<td style="border:1px solid #000000; border-bottom:2px solid #000000; text-align:center;">
		'.$credit_hour_sum.'</td>
		<td style="border:1px solid #000000; border-bottom:2px solid #000000; text-align:center;">'.$major_credit_hour_sum.'</td>
		<td style="border:1px solid #000000; border-bottom:2px solid #000000; text-align:center;">
		'.$student['Student']['ExemptedCredit'].'</td>

		<td style="border:1px solid #000000; border-bottom:2px solid #000000; text-align:center;">
		'.$student['Student']['TransferedCredit'].'</td>

		<td style="border:1px solid #000000; border-bottom:2px solid #000000; text-align:center;">
		'.$student['Student']['StudentExamStatus'][0]['cgpa'].'</td>'.$excludeMajorCGPA.'
		<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center; "> &nbsp;
		</td>
		</tr>';
      }
      //$graduationCriteria .='<tr><td></td></tr>';
        $graduationCriteria .='<tr><td style="width:100%">&nbsp;</td></tr>';

       $graduationCriteria .='<tr><td style="width:45%">Prepared By:</td>
<td style="width:40%">Checked By:</td><td style="width:15%">Approved By Senate</td></tr>';

       $graduationCriteria .='<tr><td style="width:45%">Sign:_________________</td>
<td style="width:40%">Sign:______________</td><td style="width:15%">&nbsp;</td></tr>';


      $graduationCriteria .= '</table>';
      $pdf->writeHTML($graduationCriteria);	
   }
   // reset pointer to the last page
   $pdf->lastPage();

    //output the PDF to the browser

    $pdf->Output('SenateListReport.'.date('Y').'.pdf', 'I');

    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?>
