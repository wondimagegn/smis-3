<?php
//debug($student_copy);
App::import('Vendor','tcpdf/tcpdf');
// create new PDF document
    //$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true);  
	
    //show header or footer
    $pdf->SetPrintHeader(false); 
    $pdf->SetPrintFooter(false);
    //$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans' 
    // set default header data
   /*
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    //set margins
    
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //$pdf->SetMargins(15,15,15);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    */
    // set font
    $pdf->SetMargins(5, 3, 5);
    $pdf->SetFont("freeserif", "", 11);

    $pdf->setPageOrientation('L', true, 0);
    
  //  $pdf->AddPage("L");
    $pdf->AddPage("L"); 
   // $pdf->SetLineStyle('dash');
   // $pdf->SetLineWidth(0.1);
  //  $pdf->SetLineStyle('dash');
    
     $student_copy_content = '
     <table>
                   <tr>
		<td style="width:40%">
			<table class="fs13">
				<tr>
					<td style="width:30%">College:</td>
					<td style="width:70%; font-weight:bold">'.$college_detail['name'].'</td>
				</tr>
				<tr>
					<td>Program:</td>
					<td style="font-weight:bold">'.$program_detail['name'].'</td>
				</tr>
				<tr>
					<td>Program Type:</td>
					<td style="font-weight:bold">'.$program_type_detail['name'].'</td>
				</tr>
				<tr>
					<td>Department:</td>
					<td style="font-weight:bold">'.(!empty($department_detail['name']) && 
					$department_detail['name'] != "" ? $department_detail['name'] : 'Freshman Program').'</td>
				</tr>
				<tr>
					<td>Section:</td>
					<td style="font-weight:bold">'.$section_detail['name'].'</td>
				</tr>
				<tr>
					<td>Acdamic Year:</td>
					<td style="font-weight:bold">'.$academic_year.'</td>
				</tr>
				<tr>
					<td>Semester:</td>
					<td style="font-weight:bold">'.$semester.'</td>
				</tr>
			</table>
		</td>	
    ';
    $student_copy_content.='<td style="width:60%">';
    if(count($master_sheet['registered_courses']) > 0) {
         $student_copy_content.= '<div style="font-weight:bold; background-color:#cccccc; 
         padding:0px; font-size:36px">Registered Courses</div>';
         $student_copy_content.='<table class="courses_table">
				<tr>
					<th style="width:5%">No</th>
					<th style="width:55%">Course Title</th>
					<th style="width:20%">Course Code</th>
					<th style="width:20%">Cr. Hr.</th>
				</tr>';
				
		$registered_and_add_course_count = 0;
	    $registered_course_credit_sum = 0;
		foreach($master_sheet['registered_courses'] as $key => $registered_course) {
				$registered_and_add_course_count++;
				$registered_course_credit_sum += $registered_course['credit'];
				
				$student_copy_content.='<tr>
					<td>'.$registered_and_add_course_count.'</td>
					<td>'.$registered_course['course_title'].'</td>
					<td>'.$registered_course['course_code'].'</td>
					<td>'.$registered_course['credit'].'</td>
				</tr>';
				
		}
		
		$student_copy_content.='<tr style="font-weight:bold">
					<td colspan="3" style="text-align:right">Total</td>
					<td>'.$registered_course_credit_sum.'</td>
				</tr>
			</table>';
		
    }
    
     if(count($master_sheet['added_courses']) > 0) {
            $student_copy_content.='<div style="font-weight:bold; background-color:#cccccc; 
            padding:0px;font-size:36px;">Add Courses</div>
			<table class="courses_table">
				<tr>
					<th style="width:5%">No</th>
					<th style="width:55%">Course Title</th>
					<th style="width:20%">Course Code</th>
					<th style="width:20%">Cr. Hr.</th>
				</tr>';
		  	$added_course_credit_sum = 0;
			foreach($master_sheet['added_courses'] as $key => $added_course) {
				$registered_and_add_course_count++;
				$added_course_credit_sum += $added_course['credit'];
				
				$student_copy_content.='<tr>
					<td>'.$registered_and_add_course_count.'</td>
					<td>'.$added_course['course_title'].'</td>
					<td>'.$added_course['course_code'].'</td>
					<td>'.$added_course['credit'].'</td>
				</tr>';
			}
			
			$student_copy_content.='<tr style="font-weight:bold">
					<td colspan="3" style="text-align:right">Total</td>
					<td>'.$added_course_credit_sum.'</td>
				</tr>
			</table>';
     }
    $student_copy_content.='</td></tr></table>';
    
    $table_width = (count($master_sheet['registered_courses'])*10) + 
    (count($master_sheet['added_courses'])*10) + 86;
    
    $student_copy_content.='<table style="width:76%">
	<tr>';
	
	 $student_copy_content.='<th rowspan="2" style="vertical-align:bottom; width:2%;">No</th>
		<th rowspan="2" style="vertical-align:bottom; width:18%">Full Name</th>
		<th rowspan="2" style="vertical-align:bottom; width:8%">ID No</th>
		<th rowspan="2" style="vertical-align:bottom; width:3%">Sex</th>';
		$percent = 10;
		$last_percent = false;
		$total_percent = (count($master_sheet['registered_courses'])*10) + 
		(count($master_sheet['added_courses'])*10) + 86;
		if($total_percent > 100) {
			//$percent = (100 - 86) / (count($master_sheet['registered_courses']) + count($master_sheet['added_courses']));
		}
		else if($total_percent < 100) {
			$last_percent = 100 - $total_percent;
		}
		$registered_and_add_course_count = 0;
		foreach($master_sheet['registered_courses'] as $key => $registered_course) {
			$registered_and_add_course_count++;
			 
			 $student_copy_content.='<th colspan="2" style="width:'.$percent.'%; 
			 text-align:center;border-left:1px #000000 solid;border-right:1px #000000 solid;" class="bordering2">'.$registered_and_add_course_count.'</th>';
	      
	    }
	    
	    foreach($master_sheet['added_courses'] as $key => $added_course) {
			$registered_and_add_course_count++;
			
			$student_copy_content.='<th colspan="2" style="width:'.$percent.'%; text-align:center;">'.
			$registered_and_add_course_count.'</th>';
			
		}
		/*
		$student_copy_content.='<th colspan="3" style="text-align:center; width:15%" class="bordering2">Semester</th>
		<th colspan="3" style="text-align:center; width:15%" class="bordering2">Previous</th>
		<th colspan="3" style="text-align:center; width:15%" class="bordering2">Cumulative</th>
		<th rowspan="2" style="text-align:center; vertical-align:bottom; width:10%" class="bordering2">Status</th>';
		*/
		
		$student_copy_content .='<th colspan="3" style="text-align:center; width:15%"
		style="border-left:1px #000000 solid;border-right:1px #000000 solid;" class="bordering2">Semester</th>';
		
		$student_copy_content .='<th colspan="3" style="text-align:center; width:15%" 
		style="border-left:1px #000000 solid;border-right:1px #000000 solid;" class="bordering2">Previous</th>';
		
		$student_copy_content .='<th colspan="3" style="text-align:center; width:15%" 
		style="border-left:1px #000000 solid;border-right:1px #000000 solid;" class="bordering2">Cumulative</th>';
		
		 $student_copy_content .='<th rowspan="2" style="text-align:center; vertical-align:bottom; width:10%" 
		 style="border-left:1px #000000 solid;border-right:1px #000000 solid;" class="bordering2">Status</th>';
		
		
		if($last_percent) {
			
			$student_copy_content.='<th style="width:'.$last_percent.'%;">&nbsp;</th>';
			
		}
		$student_copy_content.='</tr>';
		
		$student_copy_content.='<tr>';
	
		foreach($master_sheet['registered_courses'] as $key => $registered_course) {
			
			$student_copy_content.='<th style="width:'.($percent/2).'%; 
			border-left:1px #000000 solid; border-right:1px #000000 solid">G</th>
			<th style="width:'.($percent/2).'%; border-left:1px #000000 solid; border-right:1px #000000 solid">
			GP</th>';
			
		}
		foreach($master_sheet['added_courses'] as $key => $added_course) {
		
			$student_copy_content.='<th style="width:'.($percent/2).'%; 
			border-left:1px #000000 solid; border-right:1px #000000 solid">G</th>
			<th style="width:'.($percent/2).'%; border-left:1px #000000 solid; border-right:1px #000000 solid">
			GP</th>';
			
		}
		
		$student_copy_content.='<th style="width:5%" class="bordering2">CH</th>
		<th style="width:5%" style="border-left:1px #000000 solid;border-right:1px #000000 solid;" class="bordering2">GP</th>
		<th style="width:5%" style="border-left:1px #000000 solid;border-right:1px #000000 solid;"  class="bordering2">SGPA</th>
		
		<th style="width:5%" style="border-left:1px #000000 solid;border-right:1px #000000 solid;"  class="bordering2">CH</th>
		<th style="width:5%" style="border-left:1px #000000 solid;border-right:1px #000000 solid;"  class="bordering2">GP</th>
		<th style="width:5%" style="border-left:1px #000000 solid;border-right:1px #000000 solid;"  class="bordering2">CGPA</th>
		
		<th style="width:5%" style="border-left:1px #000000 solid;border-right:1px #000000 solid;"  class="bordering2">CH</th>
		<th style="width:5%" style="border-left:1px #000000 solid;border-right:1px #000000 solid;" class="bordering2">GP</th>
		<th style="width:5%" style="border-left:1px #000000 solid;border-right:1px #000000 solid;" class="bordering2">CGPA</th>';
		
		if($last_percent) {
			$student_copy_content.='<th>&nbsp;</th>';
		}
		$student_copy_content.='</tr>';
		
  $student_count = 0;
  foreach($master_sheet['students_and_grades'] as $key => $student) {
	 $credit_hour_sum = 0;
	 $gp_sum = 0;
	 $student_count++;
	$student_copy_content.='<tr>';
	$student_copy_content.='<td >'.$student_count.'</td>
		<td>'.$student['full_name'].'</td>
		<td>'.$student['studentnumber'].'</td>
		<td>'.(strcasecmp($student['gender'], 'male') == 0 ? 'M' : 'F').'</td>';
	 foreach($master_sheet['registered_courses'] as $key => $registered_course) {
			 if($student['courses']['r-'.$registered_course['id']]['registered'] == 1) {
			 	if(isset($student['courses']['r-'.$registered_course['id']]['grade'])) {
			 		$student_copy_content.='<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">'.$student['courses']['r-'.$registered_course['id']]['grade'].'</td>';
			 		$student_copy_content.= '<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">';
			 		if(isset($student['courses']['r-'.$registered_course['id']]['point_value'])) {
			 			$student_copy_content.=number_format(($student['courses']['r-'.$registered_course['id']]['credit'] * $student['courses']['r-'.$registered_course['id']]['point_value']), 2, '.', '');
			 			$gp_sum += ($student['courses']['r-'.$registered_course['id']]['credit'] * $student['courses']['r-'.$registered_course['id']]['point_value']);
			 		}
			 		$student_copy_content.='</td>';
			 	}
			 	else {
			 		$student_copy_content.= '<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">'.($student['courses']['r-'.$registered_course['id']]['droped'] == 1 ? 'DP' : '**').'</td>';
			 		$student_copy_content.='<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">&nbsp;</td>';
			 	}
			 if($student['courses']['r-'.$registered_course['id']]['droped'] == 0)
			 	$credit_hour_sum += $student['courses']['r-'.$registered_course['id']]['credit'];
			 }
			 else {
			 	$student_copy_content.='<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">---</td>';
			 	$student_copy_content.= '<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">&nbsp;</td>';
			 	//the student didn't register and there is nothing to display
			 }
		}
		foreach($master_sheet['added_courses'] as $key => $added_course) {
			 if($student['courses']['a-'.$added_course['id']]['added'] == 1) {
			 	if(isset($student['courses']['a-'.$added_course['id']]['grade'])) {
			 		$student_copy_content.= '<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;" class="bordering">'.$student['courses']['a-'.$added_course['id']]['grade'].'</td>';
			 		$student_copy_content.= '<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">';
			 		if(isset($student['courses']['a-'.$added_course['id']]['point_value'])) {
			 			$student_copy_content.= number_format(($student['courses']['a-'.$added_course['id']]['credit'] * $student['courses']['a-'.$added_course['id']]['point_value']), 2, '.', '');
			 			$gp_sum += ($student['courses']['a-'.$added_course['id']]['credit'] * $student['courses']['a-'.$added_course['id']]['point_value']);
			 		}
			 		$student_copy_content.='</td>';
			 	}
			 	else {
			 		$student_copy_content.= '<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">**</td>';
			 		$student_copy_content.= '<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">&nbsp;</td>';
			 	}
			 $credit_hour_sum += $student['courses']['a-'.$added_course['id']]['credit'];
			 }
			 else {
			 	$student_copy_content.= '<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">---</td>';
			 	$student_copy_content.= '<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;"  class="bordering">&nbsp;</td>';
			 	//the student didn't register and there is nothing to display
			 }
		}
		
		$student_copy_content.='<td class="bordering">'.(!empty($student['StudentExamStatus']) ? 
		$student['StudentExamStatus']['credit_hour_sum'] : '---').'</td>
		<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;" class="bordering">'.(!empty($student['StudentExamStatus']) ? 
		$student['StudentExamStatus']['grade_point_sum'] : '---').'</td>
		<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;" class="bordering">'.(!empty($student['StudentExamStatus']) ? $student['StudentExamStatus']['sgpa'] : '---').
		'</td>

		<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;" class="bordering">'.
		(!empty($student['PreviousStudentExamStatus']) ? 
		$student['PreviousStudentExamStatus']['previous_credit_hour_sum'] : '---').'</td>
		<td  style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;" class="bordering">'.
		(!empty($student['PreviousStudentExamStatus']) ? 
		$student['PreviousStudentExamStatus']['previous_grade_point_sum'] : '---').'</td>
		<td style="border-left:1px #cccccc solid;border-right:1px #cccccc solid;" class="bordering">'.(!empty($student['PreviousStudentExamStatus']) ? 
		$student['PreviousStudentExamStatus']['cgpa'] : '---').'</td>';
		

	
	$student_copy_content.='</tr>';
	
  }
  	
		// print each student and its result 
		
		////////////////////////////
		
		
    $student_copy_content.='</table>';
		
		
    
    $pdf->writeHTML($student_copy_content);
   
   
    // reset pointer to the last page
    $pdf->lastPage();

    //output the PDF to the browser

    $pdf->Output('Mastersheet - '.$college_detail['name'].' '.$section_detail['name'].
    ' '.$academic_year.' '.$semester.'.pdf', 'I');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?>
