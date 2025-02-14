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
     // add a page
     $pdf->AddPage("P");
     $graduationCriteria='';
     $header='';
    $header .= '<table style="width:100%;">
    	<tr>
    		<td style="text-align:center; font-weight:bold">Arba Minch  University</td>
    	</tr>
        <tr>
    		<td style="text-align:center; font-weight:bold">
OFFICE OF THE REGISTRAR </td>
    	</tr>

<tr>
    		<td style="text-align:center; font-weight:bold;text-decoration:underline;">Roster</td>
    	</tr>
    	</table>';



     $header .=' <table style="width:100%;">
    	<tr>
            <td style="width:55%;">
                  <table>
                      <tr><td>College/School/Center: '.$publish_course_detail_info['Department']['College']['name'].'</td></tr>
                      <tr><td>Department: '.$publish_course_detail_info['Department']['name'].'</td></tr>
                      <tr><td>Section: '.$publish_course_detail_info['Section']['name'].'</td></tr>
                      <tr><td>Year: '.$publish_course_detail_info['Section']['YearLevel']['name'].'</td></tr>
                      <tr><td>Academic Year: '.$publish_course_detail_info['PublishedCourse']['academic_year'].'</td></tr>
                      <tr><td>Semester: '.$publish_course_detail_info['PublishedCourse']['semester'].'</td></tr>
                  </table>
            </td>
           <td>
                  <table>
                      <tr><td style="word-wrap:break-word;">Course Title: '.$publish_course_detail_info['Course']['course_title'].'</td></tr>
                      <tr><td>Course Code: '.$publish_course_detail_info['Course']['course_code'].'</td></tr>

                       <tr><td>ECTS: '.$publish_course_detail_info['Course']['credit'].'</td></tr>

                 <tr><td>Instructor: '.$publish_course_detail_info['CourseInstructorAssignment'][0]['Staff']['full_name'].'</td></tr>
                     
                     
                  </table>
            </td>
    	</tr>
      </table>';
 
	
   $count = 1;
  
     $graduationCriteria .= $header;
	
	$graduationCriteria .= '<br/><table cellpadding="1" style="padding-left:2px;text-align:left;" >';

	$graduationCriteria .= '<tr>
			<th style="width:10%;border:1px solid #000000;text-align:center ">S.No</th>
			<th style="width:20%;text-align:center;border:1px solid #000000; ">ID</th>			
			<th style="width:30%;text-align:center;border:1px solid #000000;">Student Name</th>
			
           <th style="width:10%;text-align:center;border:1px solid #000000;">Sex</th>
           <th style="width:12%;text-align:center;border:1px solid #000000;">Total</th>
           <th style="width:10%;text-align:center;border:1px solid #000000;">Grade</th>
		</tr>';
         $s_count=1;
         foreach($students as $key => $student) 
         {
            	$sex = $student['Student']['gender']=='male' ? 'M':'F';
				$value ="";
                $total_100=0;
                $grade='';
				//Searching for the exam result from the databse returned value
				if(isset($student['ExamResult']) && !empty($student['ExamResult'])) {
					foreach($student['ExamResult'] as $key => $examResult) {
							 $value = $examResult['result'];
							$total_100 += $value;
					}
				}
               
					//GRADE
					//If the grade is from the database (regisration and add)
					$latest_grade_detail = $student['LatestGradeDetail'];
					
					if($display_grade && isset($student['GeneratedExamGrade'])){
						$grade=$student['GeneratedExamGrade']['grade'];
					} else if(!empty($latest_grade_detail['ExamGrade'])) {
						$grade=$latest_grade_detail['ExamGrade']['grade'];
					}
					else {
						$grade='**';
                    }

            $graduationCriteria .='<tr><td style="border:1px solid #000000;text-align:center; ">'.$s_count++.'</td>
			<td style="border:1px solid #000000; text-align:center;">'.$student['Student']['studentnumber'].
'</td><td style="border:1px solid #000000; ">'.$student['Student']['first_name'].' '.$student['Student']['middle_name'].' '.$student['Student']['last_name'].'</td><td style="border:1px solid #000000;text-align:center; ">'.$sex.'</td>
<td style="border:1px solid #000000;text-align:center; ">'.$total_100.'</td><td style="border:1px solid #000000;text-align:center; ">'.$grade.'</td>
 </tr>'; 
             if($s_count==25)
                  $pdf->SetAutoPageBreak(TRUE);
         }
         if(!empty($student_adds)) {

		  // echo "<tr><td colspan='6'>Students who add course from other section</td></tr>";
	     $graduationCriteria .="<tr><td style='width:200px;' colspan=\"6\">Students who add course from other section</td></tr>";


		 $s_count=1;

         foreach($student_adds as $key => $student) 
         {
            	$sex = $student['Student']['gender']=='male' ? 'M':'F';
				$value ="";
                $total_100=0;
                $grade='';
				//Searching for the exam result from the databse returned value
				if(isset($student['ExamResult']) && !empty($student['ExamResult'])) {
					foreach($student['ExamResult'] as $key => $examResult) {
							 $value = $examResult['result'];
							$total_100 += $value;
					}
				}
               
					//GRADE
					//If the grade is from the database (regisration and add)
					$latest_grade_detail = $student['LatestGradeDetail'];
					
					if($display_grade && isset($student['GeneratedExamGrade'])){
						$grade=$student['GeneratedExamGrade']['grade'];
					} else if(!empty($latest_grade_detail['ExamGrade'])) {
						$grade=$latest_grade_detail['ExamGrade']['grade'];
					}
					else {
						$grade='**';
                    }

            $graduationCriteria .='<tr><td style="border:1px solid #000000;text-align:center; ">'.$s_count++.'</td>
			<td style="border:1px solid #000000; text-align:center;">'.$student['Student']['studentnumber'].
'</td><td style="border:1px solid #000000; ">'.$student['Student']['first_name'].' '.$student['Student']['middle_name'].' '.$student['Student']['last_name'].'</td><td style="border:1px solid #000000;text-align:center; ">'.$sex.'</td>
<td style="border:1px solid #000000;text-align:center; ">'.$total_100.'</td><td style="border:1px solid #000000;text-align:center; ">'.$grade.'</td>
 </tr>'; 
             if($s_count==25)
                  $pdf->SetAutoPageBreak(TRUE);
         }
       }

		

   
        $graduationCriteria .='<tr><td style="width:100%">&nbsp;</td></tr>';

       $graduationCriteria .='<tr><td style="width:45%">Checked By:</td></tr>';

       $graduationCriteria .='<tr><td style="width:45%">Sign:_________________</td><td style="width:15%">&nbsp;</td></tr>';
            $graduationCriteria .='<tr><td style="width:45%">Date: '.date("F j, Y").'</td><td style="width:15%">&nbsp;</td></tr>';
      $graduationCriteria .= '</table>';
      $pdf->writeHTML($graduationCriteria);	
  
   // reset pointer to the last page
   $pdf->lastPage();

    //output the PDF to the browser

    $pdf->Output('AttendanceList.'.$filename.'.pdf', 'I');


    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?>
