<?php
	App::import('Vendor','tcpdf/tcpdf');
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true);
	//show header or footer
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);

	$pdf->SetProtection($permissions = array('modify', 'extract', 'assemble'), $user_pass = '', $owner_pass = '1qazXSw23eDC@@', $mode = 0, $pubkeys = null);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SMiS, Arba Minch University');
    $pdf->SetTitle('Senate List PDF');
    $pdf->SetSubject('Senate List PDF');
    $pdf->SetKeywords('Senate, List, PDF, SMiS, AMU');

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
    	<tr><td style="text-align:center; font-weight:bold">ARBA MINCH UNIVERSITY</td></tr>
		<tr><td style="text-align:center; font-weight:bold">OFFICE OF THE REGISTRAR</td></tr>
		<tr><td style="text-align:center; font-weight:bold;text-decoration:underline;">GRADUATING CLASS REPORT FORM</td></tr>
    </table>';

   	$count = 1;
   	$graduationCriteria = '';
   	$excludeMajorColumnCGPA = '';
   	$excludeMajorColumnTotalCredit = '';
  	$totalStudents = 0;


	if (isset($students_for_senate_list_pdf) && !empty($students_for_senate_list_pdf)) {
		foreach($students_for_senate_list_pdf as $c_id => $students) {
			if (count($students) > 0) {
				// 0 - program, 1-program type, 2 - department, 3- curriculum name, 4 - minimum credit point, 5-amharic degree nomenclature, 6-specialization amharic degree nomenclature, 7-english degree nomenclature, 8-specialization english degree nomenclature
				$curriculumDetails = explode('~',$c_id);
				
				if (isset($curriculumDetails[9]) && $curriculumDetails[9] == 'ECTS Credit Point') {
						$typeCredit = 'ECTS';
				} else {
					$typeCredit = 'Credit';
				}

				// add a page
				// $pdf->AddPage("L");

				$graduationCriteria .= $header;
				$graduationCriteria .= '<table class="fs13 summery">
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

				if (!empty($curriculumDetails[8])) {
					$graduationCriteria.='<tr><td>Specialization:</td><td style="font-weight:bold">'.$curriculumDetails[8].'</td></tr>';
				}

				$graduationCriteria .= '<tr><td>Degree Designation (Amharic):</td><td class="bold">'.$curriculumDetails[5].'</td></tr>';

				if (!empty($curriculumDetails[6])) {
					$graduationCriteria .= '<tr><td>Specialization (Amharic):</td><td>'.$curriculumDetails[6].'</td></tr>';
				}

				$graduationCriteria .= '<tr><td>Required '.$typeCredit.' for Graduation:</td><td style="font-weight:bold">'.$curriculumDetails[4].'</td></tr></table>';
				$graduationCriteria .= '<table cellpadding="1" style="padding-left:2px;text-align:left;" >';

				if ((strcasecmp($excludeMajor,'0') == 0) || $excludeMajor == 0) {
					$excludeMajorColumnCGPA='<th style="width:7%;text-align:center; border:1px solid #000000; border-bottom:1px solid #000000;">MCGPA</th>';
					$excludeMajorColumnTotalCredit='<th style="width:5%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">Major '.$typeCredit.' Taken</th>';
				}

				// debug($students[0]['Student']['ThesisResult']['grade_type']);
				// debug(strpos($students[0]['Student']['ThesisResult']['grade_type'], 'Pass') !== false);
				
				if (strcmp($curriculumDetails[0],"Post graduate") == 0) {
					$remark='Thesis Result';
					if (isset($students[0]['Student']['ThesisResult']['grade_type']) && (strpos($students[0]['Student']['ThesisResult']['grade_type'], 'Pass') !== false || strpos($students[0]['Student']['ThesisResult']['grade_type'], 'Fail') !== false)) {
						$remark='Project Result';
					}
				} else if (strcmp($curriculumDetails[0],"PhD") == 0) {
					$remark='Dissertation Result';
				} else {
					$remark="Exit Exam";
				}

				$graduationCriteria .= '<tr>
					<th style="width:5%;border:1px solid #000000; border-bottom:1px solid #000000;text-align:center;">S.No</th>
					<th style="width:14%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">Student ID</th>
					<th style="width:10%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">National ID</th>
					<th style="width:25%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">Student Name</th>
					<th style="width:5%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">Sex</th>
					<th style="width:6%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">'.$typeCredit.' Taken</th>'.$excludeMajorColumnTotalCredit.'
					<th style="width:7%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">'.$typeCredit.' Exempted</th>
					<th style="width:7%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">'.$typeCredit.' Transfered</th>
					<th style="width:7%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">CGPA</th>'.$excludeMajorColumnCGPA.'
					<th style="width:12%;text-align:center;border:1px solid #000000; border-bottom:1px solid #000000;">'.$remark.'</th>
				</tr>';

				$s_count = 1;

				$excludeMajorColumnCGPAA = '';
				$excludeMajorColumnTotalCreditD = '';
				$pages = array();
				$pageContents = '';
				$pageElements = 0;
				$totalStudents = count($students);

				foreach ($students as $key => $student) {
					if (!empty($student['Student']['id'])) {
						$credit_hour_sum = 0;
						$major_credit_hour_sum = 0;
						$dropped_credit_sum = 0;

						foreach ($student['Student']['StudentExamStatus'] as $ses_key => $ses_value) {
							$major_credit_hour_sum+=$ses_value['m_credit_hour_sum'];
						}

						if (isset($senateList['Student']['CourseDrop']) && !empty($senateList['Student']['CourseDrop'])) {
							foreach ($senateList['Student']['CourseDrop'] as $drop_key => $drop_value) {
								if (isset($drop_value['CourseRegistration']['PublishedCourse']['Course']) && !empty($drop_value['CourseRegistration']['PublishedCourse']['Course'])) {
									if ($drop_value['CourseRegistration']['PublishedCourse']['Course']) {
										if ($drop_value['registrar_confirmation']==1 && $drop_value['department_approval']==1) {
											$dropped_credit_sum+=$drop_value['CourseRegistration']['PublishedCourse']['Course']['credit'];
										}
									}
								}
							}
						}

						if (isset($student['Student']['CourseAdd']) && !empty($student['Student']['CourseAdd'])) {
							foreach ($student['Student']['CourseAdd'] as $ses_key => $ses_value) {
								$credit_hour_sum += $ses_value['PublishedCourse']['Course']['credit'];
							}
						}

						foreach ($student['Student']['CourseRegistration'] as $ses_key => $ses_value) {
							$credit_hour_sum += $ses_value['PublishedCourse']['Course']['credit'];
						}


						if ((strcasecmp($excludeMajor,'0') == 0) || $excludeMajor == 0) {
							$excludeMajorColumnCGPAA='<td style="border:1px solid #000000;text-align:center; border-bottom:1px solid #000000;">'.$student['Student']['StudentExamStatus'][0]['mcgpa'].'</td>';
							$excludeMajorColumnTotalCreditD='<td style="border:1px solid #000000; border-bottom:1px solid #000000; text-align:center;">'.($major_credit_hour_sum-$student['Student']['PointDeducation']['m_deduct_credit_hour_sum']).'</td>';
						}

						if ($credit_hour_sum > $curriculumDetails[4]){
							$credit_hour_sum = $curriculumDetails[4];
						} else {
							$credit_hour_sum = ($credit_hour_sum-$dropped_credit_sum);
						}

						$gradeForDocument = null;

						if (!empty($student['Student']['ExitExamGrade'])) {

							$gradeForDocument = ((strcasecmp($student['Student']['ExitExamGrade']['grade'], 'P') == 0 || strcasecmp($student['Student']['ExitExamGrade']['grade'], 'Pass') == 0 ) ? 'Pass': ((strcasecmp($student['Student']['ExitExamGrade']['grade'], 'F') == 0 || strcasecmp($student['Student']['ExitExamGrade']['grade'], 'Fail') == 0 )? 'Fail': '---'));

							$exitExamresult = ClassRegistry::init('ExitExam')->find('first', array(
								'conditions' => array(
									'ExitExam.student_id' => $student['Student']['id'], 
									//'ExitExam.course_id' =>  $student['Student']['ExitExamGrade']['course_id']
								),
								'order' => array('ExitExam.exam_date' => 'DESC', 'ExitExam.id' => 'DESC'),
								'recursive' => -1
							));  

							if ($exitExamresult) {
								debug($exitExamresult['ExitExam']['result']);
								$gradeForDocument .= ' (' . $exitExamresult['ExitExam']['result'].'%)';
							}

							if (isset($exitExamresult['ExitExam']['result']) && is_numeric($exitExamresult['ExitExam']['result']) && ((int) $exitExamresult['ExitExam']['result'] < 50)) {
								$gradeForDocument = 'Fail (' . $exitExamresult['ExitExam']['result'].'%)';
							}

							if (isset($exitExamresult['ExitExam']['result']) && is_numeric($exitExamresult['ExitExam']['result']) && ((int) $exitExamresult['ExitExam']['result'] >= 50)) {
								$gradeForDocument = 'Pass (' . $exitExamresult['ExitExam']['result'].'%)';
							}
						} else {
							$gradeForDocument = '---';
						}

						$pageContents .= 
						'<tr>
							<td style="border:1px solid #000000; border-bottom:1px solid #000000; text-align:center;">'.$s_count++.'</td>
							<td style="border:1px solid #000000; border-bottom:1px solid #000000; text-align:center;">'.$student['Student']['studentnumber'].'</td>
							<td style="border:1px solid #000000; border-bottom:1px solid #000000; text-align:center;">'.(!empty($student['Student']['student_national_id']) ? $student['Student']['student_national_id'] : '--').'</td>
							<td style="border:1px solid #000000; border-bottom:1px solid #000000"> &nbsp;'.$student['Student']['full_name'].'</td>
							<td style="border:1px solid #000000; border-bottom:1px solid #000000; text-align:center;">'.(strcasecmp($student['Student']['gender'], 'male') == 0 ? 'M' : 'F').'</td>
							<td style="border:1px solid #000000; border-bottom:1px solid #000000; text-align:center;">'.($credit_hour_sum).'</td>'.$excludeMajorColumnTotalCreditD.'
							<td style="border:1px solid #000000; border-bottom:1px solid #000000; text-align:center;">'.$student['Student']['ExemptedCredit'].'</td>
							<td style="border:1px solid #000000; border-bottom:1px solid #000000; text-align:center;">'.$student['Student']['TransferedCredit'].'</td>
							<td style="border:1px solid #000000; border-bottom:1px solid #000000; text-align:center;">'.$student['Student']['StudentExamStatus'][0]['cgpa'].'</td>'.$excludeMajorColumnCGPAA.'';

							if($student['Student']['program_id'] != 1){
								$pageContents .='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:center;"> '.$student['Student']['ThesisResult']['grade'].'</td>
								</tr>';
							} else {
								$pageContents .='<td style="border:1px solid #000000; border-bottom:1px solid #000000;text-align:center;"> '. $gradeForDocument.'</td>
								</tr>';
							}

						$pageElements++;
						
						if ($pageElements % 20 == 0 || $pageElements == $totalStudents){
							$pages[] = $pageContents;
							$pageContents = '';
							//$s_count=1;
						}
					}
				}

				if (!empty($pages)) {
					foreach ($pages as $pk => $page) {
						$pdf->AddPage("L");
						$outputPage = '';
						$Pagefooter = '';
						$MainHeader = $graduationCriteria;
						$MainBody = $page;

						$Pagefooter .='
						<tr>
							<td style="width:100%">&nbsp;</td>
						</tr>';

						$Pagefooter.='
						<tr>
							<td style="width:25%">Generated By: SMiS</td>
							<td style="width:25%">Prepared By: College Registrar</td>
							<td style="width:25%">Checked By: University Registrar</td>
							<td style="width:25%">Approved By Senate</td>
						</tr>';

						$Pagefooter .='
						<tr>
							<td style="width:25%">Date Generated: <u>' . date('Y-m-d') . '</u></td>
							<td style="width:25%">Sign:__________________</td>
							<td style="width:25%">Sign:__________________</td>
							<td style="width:25%">&nbsp;</td>
						</tr>
						</table>';

						$outputPage .= $MainHeader;
						$outputPage .= $MainBody;
						$outputPage .= $Pagefooter;
						$MainHeader = '';
						$MainBody = '';
						$Pagefooter = '';

						$pdf->writeHTML($outputPage);
						// reset pointer to the last page
						$pdf->lastPage();
					}
				}

				$graduationCriteria = '';
			}
		}
	}
   	// reset pointer to the last page
   	//$pdf->lastPage();
    //output the PDF to the browser

    $pdf->Output('SenateListReport.'.date('Y-m-d').'.pdf', 'I');

    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
