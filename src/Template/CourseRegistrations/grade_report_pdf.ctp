<?php
//debug($student_copies);
App::import('Vendor','tcpdf/tcpdf');
	// create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

	if ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) {
		// it is impossible to prevent pdf editing now a days but limit the ability to edit the document by techno-savy students, if downloading is allowed to student role in general setting. 
		// prevent students from printing the pdf file by software printers or online tools, 
		$pdf->SetProtection($permissions = array('modify', 'copy', 'extract', 'assemble', 'print'), $user_pass = USER_PASSWORD, $owner_pass = OWNER_PASSWORD, $mode = 0, $pubkeys = null);
	} else {
		$pdf->SetProtection($permissions = array('modify', 'copy', 'extract', 'assemble'), $user_pass = USER_PASSWORD, $owner_pass = OWNER_PASSWORD, $mode = 0, $pubkeys = null);
	}
	 
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

	//debug($studentnumber);

	if ((isset($student_copies) && count($student_copies) > 1) && empty($studentnumber)) {
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
		$pdf->SetTitle('Registration Slip for ' .(isset($student_copies[$first_student_id]['Department']) && !empty($student_copies[$first_student_id]['Department']['name']) ? $student_copies[$first_student_id]['Department']['name']: $student_copies[$first_student_id]['College']['name']).' '.(isset($student_copies[$first_student_id]['YearLevel']) && !empty($student_copies[$first_student_id]['YearLevel']['name']) ? $student_copies[$first_student_id]['YearLevel']['name'] : ($student_copies[$first_student_id]['Student']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program':'Pre/Freshman')).' for '.$student_copies[$first_student_id]['academic_year'] . ' academic year semester '.$student_copies[$first_student_id]['semester'] .'');
		$pdf->SetSubject('Registration Slip');
		$pdf->SetKeywords('Registration, Slip, '.$student_copies[$first_student_id]['Student']['full_name'].', '.$student_copies[$first_student_id]['academic_year'].','.$student_copies[$first_student_id]['Section']['name'].', '. (isset($student_copies[$first_student_id]['Department']) && !empty($student_copies[$first_student_id]['Department']['name']) ? $student_copies[$first_student_id]['Department']['name'] : $student_copies[$first_student_id]['College']['name']).', SMiS');
	} else if ((count($student_copies) == 1 ) || !empty($studentnumber)) {
		$pdf->SetCreator(PDF_CREATOR);
		$pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
		$pdf->SetTitle('Registration Slip for ' . $student_copies[$first_student_id]['Student']['full_name'] .' (' .$student_copies[$first_student_id]['Student']['studentnumber'].')' .' '.(isset($student_copies[$first_student_id]['YearLevel']) && !empty($student_copies[$first_student_id]['YearLevel']['name']) ? $student_copies[$first_student_id]['YearLevel']['name'] : ($student_copies[$first_student_id]['Student']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program':'Pre/Freshman')).' for '.$student_copies[$first_student_id]['academic_year'].' academic year semester '.$student_copies[$first_student_id]['semester'] .'');
		$pdf->SetSubject('Registration Slip');
		$pdf->SetKeywords('Registration, Slip, '.$student_copies[$first_student_id]['Student']['full_name'].', '.$student_copies[$first_student_id]['academic_year'].','.$student_copies[$first_student_id]['Section']['name'].', '. (isset($student_copies[$first_student_id]['Department']) && !empty($student_copies[$first_student_id]['Department']['name']) ? $student_copies[$first_student_id]['Department']['name']: $student_copies[$first_student_id]['College']['name']).',   SMiS');
	}


	if (!empty($student_copies)) {
	 
		foreach($student_copies as $key => $student_copy) {
			
			$pdf->AddPage("P");
			$pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
			$pdf->Ln(50);

			$type_credit = 'Credit';

			if (!empty($student_copy['Section']) && !is_numeric($student_copy['Section']['year_level_id']) || $student_copy['Section']['year_level_id'] == 0) {
				
				$preEngineeringColleges = Configure::read('preengineering_college_ids');

				if (isset($student_copy['Section']['College']['stream']) && $student_copy['Section']['College']['stream'] == STREAM_NATURAL && in_array($student_copy['Section']['College']['id'], $preEngineeringColleges)) {
					$stream[1] = 'Pre Engineering';
				} else if (isset($student_copy['Section']['College']['stream']) && $student_copy['Section']['College']['stream'] == STREAM_NATURAL) {
					$stream[1] = 'Natural';
				} else if (isset($student_copy['Section']['College']['stream']) && $student_copy['Section']['College']['stream'] == STREAM_SOCIAL) {
					$stream[1] = 'Social';
				}

				$type_credit = (count(explode('ECTS', $student_copy['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit');

			} else  if (isset($student_copy['Section']['Curriculum']) && !empty($student_copy['Section']['Curriculum']['name']) && !is_null($student_copy['Section']['department_id'])) {
				if (isset($student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature']) && !empty($student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature'])) {
					$stream = explode(' in ', $student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature']);
					if (count($stream) == 1) {
						$stream2 = explode(' of ', $student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature']);
						if (count($stream2) == 2 && count($stream) == 1) {
							$stream[1] = $stream2[1];
						} else if (count($stream2) > 2 && count($stream) == 1) {
							$stream[1] = $stream2[2];
						} else {
							$stream[1] = $student_copy['Section']['Curriculum']['specialization_english_degree_nomenclature'];
						}
					}
				} else {
					$stream = explode(' in ', $student_copy['Section']['Curriculum']['english_degree_nomenclature']);
					if (count($stream) == 1) {
						$stream = explode(' of ', $student_copy['Section']['Curriculum']['english_degree_nomenclature']);
					}
				}

				$type_credit = (count(explode('ECTS', $student_copy['Section']['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit');

			} else if (isset($student_copy['Curriculum']) && !empty($student_copy['Curriculum']['name'])) {
				
				if (isset($student_copy['Curriculum']['specialization_english_degree_nomenclature']) && !empty($student_copy['Curriculum']['specialization_english_degree_nomenclature'])) {
					$stream = explode(' in ', $student_copy['Curriculum']['specialization_english_degree_nomenclature']);
					if (count($stream) == 1) {
						$stream2 = explode(' of ', $student_copy['Curriculum']['specialization_english_degree_nomenclature']);
						if (count($stream2) == 2 && count($stream) == 1) {
							$stream[1] = $stream2[1];
						} else if (count($stream2) > 2 && count($stream) == 1) {
							$stream[1] = $stream2[2];
						} else {
							$stream[1] = $student_copy['Curriculum']['specialization_english_degree_nomenclature'];
						}
					}
				} else {
					$stream = explode(' in ', $student_copy['Curriculum']['english_degree_nomenclature']);
					if (count($stream) == 1) {
						$stream = explode(' of ', $student_copy['Curriculum']['english_degree_nomenclature']);
					}
				}

				$type_credit = (count(explode('ECTS', $student_copy['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit');

			} else {
				$stream[1] = '---';
			}

			if (isset($stream) && !empty($stream[1])) {
				$searchBracket = explode('(', $stream[1]);
				if (count($searchBracket) != 1) {
					$searchBracket = explode(')', $searchBracket[1]);
					$stream[1] = trim($searchBracket[0]);
				}
			}

			$pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_LOGO_HEADER_FOR_TCPDF, '5', '5', 25, 25, '', '', 'N', true, 300, 'C');
			
			$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
			$pdf->SetFont($fontPath, '', 13, '', false);
			$pdf->MultiCell(92, 7, ((isset($universityName) ? $universityName : $student_copy['University']['University']['name'])), 0, 'C', false, 0, 1, 10);
			
			$pdf->SetFont($fontPath, '', 12, '', false);
			if (!empty($student_copy['Section']['College']['id'])) {
				$pdf->MultiCell(92, 7, $student_copy['Section']['College']['name'], 0, 'C', false, 0, 1, 16);
			} else {
				$pdf->MultiCell(92, 7, '', 0, 'C', false, 0, 1, 16);
			}
			
			//$pdf->SetFont($fontPath, 'U', 13, '', false);
			$pdf->SetFont($fontPath, '', 12, '', false);
			if (!empty($student_copy['Section']['Department']['id'])) {
				$pdf->MultiCell(92, 7,  $student_copy['Section']['Department']['type']. ' of '. $student_copy['Section']['Department']['name'], 0, 'C', false, 0, 1, 22);
			} else {
				$pdf->MultiCell(92, 7, (($student_copy['Program']['id'] == PROGRAM_REMEDIAL || $student_copy['Student']['program_id'] == PROGRAM_REMEDIAL) ?  'Remedial Program' : 'Freshman Program'), 0, 'C', false, 0, 1, 22);
			}

			$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/Vendor/tcpdf/fonts/jiret.ttf');
			$pdf->SetFont($fontPath, '', 16, '', true);
			$pdf->MultiCell(85, 7, (isset($universityAmharicName) ? $universityAmharicName : $student_copy['University']['University']['amharic_name']), 0, 'C', false, 0, 120, 10);
			
			$pdf->SetFont($fontPath, '', 15, '', false);
			if (!empty($student_copy['Section']['College']['amharic_name']) && !empty($student_copy['Section']['College']['id'])) {
				$pdf->MultiCell(85, 7, $student_copy['Section']['College']['amharic_name'], 0, 'C', false, 0, 120, 16);
			} else {
				$pdf->MultiCell(85, 7, '', 0, 'C', false, 0, 120, 16);
			}

			//$pdf->SetFont($fontPath, 'U', 16, '', false);
			$pdf->SetFont($fontPath, '', 15, '', false);
			if (!empty($student_copy['Section']['Department']['id'])) {
				$pdf->MultiCell(85, 7,  'የ' . $student_copy['Section']['Department']['amharic_name'] . ' ' . $student_copy['Section']['Department']['type_amharic'], 0, 'C', false, 0, 120, 22);
			}  else {
				$pdf->MultiCell(85, 7, (($student_copy['Program']['id'] == PROGRAM_REMEDIAL || $student_copy['Student']['program_id'] == PROGRAM_REMEDIAL) ?  'የአቅም ማሻሻያ ፕሮግራም' : 'የመጀመሪያ አመት ተማሪዎች'), 0, 'C', false, 0, 120, 22);
			}

			//Department/College Address
			$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
			$pdf->SetFont($fontPath, '', 12, '', false);
			// $pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/post_icon.png', '40', '26', 7, 7, 'PNG', '', '', true, 300, '');
			$pdf->MultiCell(30, 7, 'P.O.Box: '. $pobox, 0, 'C', false, 0, 34, 35);

			if ((!empty($student_copy['Section']['Department']['id']) && !empty($student_copy['Section']['Department']['phone']) ) || (!empty($student_copy['Section']['College']['id']) && !empty($student_copy['Section']['College']['phone']))) {
				//$pdf->Image($_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/phone_icon.png', '140', '26', 7, 7, 'PNG', '', '', true, 300, '');
				if (!empty($student_copy['Section']['Department']['id'])) {
					$pdf->MultiCell(100, 7, 'Tel: '. $student_copy['Section']['Department']['phone'], 0, 'L', false, 0, 146, 35);
				} else if (!empty($student_copy['College']['id'])) {
					$pdf->MultiCell(100, 7, 'Tel: '. $student_copy['Section']['College']['phone'], 0, 'L', false, 0, 146, 35);
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

			$student_copy_html = '
			<table style="width:100%">
				<tr>
					<td style="width:2%">&nbsp;</td>
					<td style="width:45%"><span style="font-weight:bold">Name: </span> &nbsp;'.$student_copy['Student']['full_name'].'</td>
					<td style="width:51%"><span style="font-weight:bold">'.(isset($student_copy['Section']['College']['type']) ? $student_copy['Section']['College']['type'] : 'College').': </span> &nbsp;'.$student_copy['Section']['College']['name'].'</td>
					<td style="width:2%">&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><span style="font-weight:bold">Student ID: </span> &nbsp;'.$student_copy['Student']['studentnumber'].'</td>
					<td><span style="font-weight:bold">'.(isset($student_copy['Section']['Department']['type']) ? $student_copy['Section']['Department']['type'] : 'Department').': </span> &nbsp;'.(!empty($student_copy['Section']['Department']['name']) ? $student_copy['Section']['Department']['name'] : (($student_copy['Program']['id'] == PROGRAM_REMEDIAL || $student_copy['Student']['program_id'] == PROGRAM_REMEDIAL) ? 'Remedial Program' : 'Pre/Freshman')).'</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><span style="font-weight:bold">Program: </span> &nbsp;'.$student_copy['Program']['name'].'</td>
					<td><span style="font-weight:bold">Stream: </span> &nbsp;'.(isset($stream[1]) ? $stream[1] : '---').'</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><span style="font-weight:bold">Program Type: </span> &nbsp;'.$student_copy['ProgramType']['name'].'</td>
					<td>&nbsp;</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><span style="font-weight:bold">Section: </span> &nbsp;'.$student_copy['Section']['name'].'</td>
					<td><span style="font-weight:bold">Year Level: </span> &nbsp;'.(!empty($student_copy['Section']['YearLevel']['name']) ? $student_copy['Section']['YearLevel']['name'] : (($student_copy['Program']['id'] == PROGRAM_REMEDIAL || $student_copy['Student']['program_id'] == PROGRAM_REMEDIAL) ? 'Remedial' : 'Pre/1st')).'</td>
					<td>&nbsp;</td>
				</tr>
				<tr>
					<td>&nbsp;</td>
					<td><span style="font-weight:bold">Academic Year: </span> &nbsp;'.$student_copy['academic_year'].'</td>
					<td><span style="font-weight:bold">Semester: </span> &nbsp;'.$student_copy['semester'].'</td>
					<td>&nbsp;</td>
				</tr>
			</table>


			<br/>
			<table style="width:100%" cellpadding="1" class="table">
				<tr>
					<th class="center" rowspan="'.(count($student_copy['courses'])+2).'" style="width:2%"></th>
					<th class="center" style="font-weight: bold; border:1px solid #000000; vertical-align: middle; width:5%">&nbsp; N<u>o</u></th>
					<th class="vcenter" style="font-weight: bold; border:1px solid #000000; vertical-align: middle; width:16%">&nbsp; Course Code</th>
					<th class="vcenter" style="font-weight: bold; border:1px solid #000000; vertical-align: middle; width:44%">&nbsp; Course Title</th>
					<th class="center" style="font-weight: bold; border:1px solid #000000; vertical-align: middle; width:10%; text-align:center">'.$type_credit.'</th>
					<th class="center" style="font-weight: bold; border:1px solid #000000; vertical-align: middle; width:8%; text-align:center">Grade</th>
					<th class="center" style="font-weight: bold; border:1px solid #000000;  vertical-align: middle; width:13%; text-align:center">Grade Point</th>
					<th rowspan="'.(count($student_copy['courses'])+2).'" style="width:2%"></th>
				</tr>';

				$c_count = 0;
				$credit_hour_sum = 0;
				$grade_point_sum = 0;

				foreach($student_copy['courses'] as $key => $course_reg_add) {
					$c_count++;
					if(isset($course_reg_add['Grade']['grade'])) {
						if(isset($course_reg_add['Grade']['used_in_gpa']) && $course_reg_add['Grade']['used_in_gpa'] == 1) {
							$credit_hour_sum += $course_reg_add['Course']['credit'];
							$grade_point_sum += ($course_reg_add['Grade']['point_value']*$course_reg_add['Course']['credit']);
						} else if(strcasecmp($course_reg_add['Grade']['grade'], 'I') == 0) {
							$credit_hour_sum += $course_reg_add['Course']['credit'];
						}
					} else {
						$credit_hour_sum += $course_reg_add['Course']['credit'];
					}

					$student_copy_html .= 
					'<tr>
						<td class="center" style="border:1px solid #000000; vertical-align: middle;">&nbsp;&nbsp; '.$c_count.'</td>
						<td class="vcenter" style="border:1px solid #000000; vertical-align: middle;">&nbsp; '.$course_reg_add['Course']['course_code'].'</td>
						<td class="center" style="border:1px solid #000000; vertical-align: middle;">&nbsp; '.$course_reg_add['Course']['course_title'].'</td>
						<td class="center" style="border:1px solid #000000; vertical-align: middle; text-align:center">'.$course_reg_add['Course']['credit'].'</td>
						<td class="center" style="border:1px solid #000000; vertical-align: middle; text-align:center">'.(isset($course_reg_add['Grade']['grade']) ? $course_reg_add['Grade']['grade'] : '---').'</td>
						<td class="center" style="border:1px solid #000000; vertical-align: middle; text-align:center">'.(isset($course_reg_add['Grade']['grade']) && isset($course_reg_add['Grade']['used_in_gpa']) && $course_reg_add['Grade']['used_in_gpa'] == 1 ? ($course_reg_add['Grade']['point_value']*$course_reg_add['Course']['credit']) : '---').'</td>
					</tr>';
				}

				$student_copy_html .= 
				'<tr>
					<td colspan="3" style="border:1px solid #000000; text-align:right; font-weight:bold">TOTAL &nbsp;</td>
					<td style="border:1px solid #000000; text-align:center; font-weight:bold">'.($credit_hour_sum != 0 ? $credit_hour_sum : '---').'</td>
					<td style="border:1px solid #000000;">&nbsp;</td>
					<td style="border:1px solid #000000; text-align:center; font-weight:bold">'.($grade_point_sum != 0 ? $grade_point_sum : '---').'</td>
				</tr>
			</table>';

			$student_copy_html .= 
			'<br/><br/>
			<table>
				<tr>
					<td style="width:2%"></td>
					<td style="width:38%">
						<table cellpadding="1" cellspacing="0" class="table">
							<tr>
								<td colspan="2" style="font-weight:bold"><u>Previous Semester</u></td>
							</tr>
							<tr>
								<td style="width:40%">'.$type_credit.' Taken: </td>
								<td class="center" style="width:40%">'.(isset($student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum']) ? $student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'] : '---').'</td>
							</tr>
							<tr>
								<td>GP Earned: </td>
								<td class="center">'.(isset($student_copy['PreviousStudentExamStatus']['previous_grade_point_sum']) ? $student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'] : '---').'</td>
							</tr>
							<tr>
								<td>SGPA: </td>
								<td class="center">'.(isset($student_copy['PreviousStudentExamStatus']['sgpa']) ? $student_copy['PreviousStudentExamStatus']['sgpa'] : '---').'</td>
							</tr>
							<tr>
								<td>CGPA:</td>
								<td class="center">'.(isset($student_copy['PreviousStudentExamStatus']['cgpa']) ? $student_copy['PreviousStudentExamStatus']['cgpa'] : '---').'</td>
							</tr>
							<tr>
								<td>Status:</td>
								<td style="font-weight: bold" '.(isset($student_copy['PreviousStudentExamStatus']['academic_status_id']) ? ($student_copy['PreviousStudentExamStatus']['academic_status_id'] == 4 ? ' class="rejected center"' : ($student_copy['PreviousStudentExamStatus']['academic_status_id'] == 3 ? ' class="on-process center"' : ' class="accepted center"')) : '').'>'.(isset($student_copy['PreviousAcademicStatus']['name']) ? $student_copy['PreviousAcademicStatus']['name'] : '---').'</td>
							</tr>
						</table>
					</td>
					<td style="width:30%">
						<table cellpadding="1" cellspacing="0" class="table">
							<tr>
								<td colspan="2" style="font-weight:bold"><u>This Semester</u></td>
							</tr>
							<tr>
								<td style="width:50%;">'.$type_credit.' Taken: </td>
								<td style="width:40%" class="center">'.($credit_hour_sum != 0 ? $credit_hour_sum : '---').'</td>
							</tr>
							<tr>
								<td>GP Earned: </td>
								<td class="center">'.($grade_point_sum != 0 ? $grade_point_sum : '---').'</td>
							</tr>
							<tr>
								<td>SGPA: </td>
								<td class="center">'.(isset($student_copy['StudentExamStatus']['sgpa']) ? $student_copy['StudentExamStatus']['sgpa'] : '---').'</td>
							</tr>
							<tr>
								<td>CGPA:</td>
								<td class="center">'.(isset($student_copy['StudentExamStatus']['cgpa']) ? $student_copy['StudentExamStatus']['cgpa'] : '---').'</td>
							</tr>
							<tr>
								<td>Status:</td>
								<td style="font-weight: bold" '.(isset($student_copy['StudentExamStatus']['academic_status_id']) ? ($student_copy['StudentExamStatus']['academic_status_id'] == 4 ? ' class="rejected center"' : ($student_copy['StudentExamStatus']['academic_status_id'] == 3 ? ' class="on-process center"' : ' class="accepted center"')) : '').'>'.(isset($student_copy['AcademicStatus']['name']) ? $student_copy['AcademicStatus']['name'] : '---').'</td>
							</tr>
						</table>
					</td>
					<td style="width:30%">
						<table cellpadding="1" cellspacing="0" class="table">
							<tr>
								<td colspan="2" style="font-weight:bold"><u>Cumulative Academic Status</u></td>
							</tr>
							<tr>
								<td style="width:70%">Total '.$type_credit.' Taken: </td>
								<td style="width:20%; font-weight: bold" class="center">';
									if($credit_hour_sum != 0 && isset($student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'])) {
										$student_copy_html .= ($student_copy['PreviousStudentExamStatus']['previous_credit_hour_sum'] + $credit_hour_sum);
									} else if($credit_hour_sum != 0) {
										$student_copy_html .= $credit_hour_sum;
									} else {
										$student_copy_html .= '---';
									}
									$student_copy_html .= 
								'</td>
							</tr>
							<tr>
								<td>Total GP Earned: </td>
								<td class="center" style="font-weight: bold">';
									if($grade_point_sum != 0 && isset($student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'])) {
										$student_copy_html .= $student_copy['PreviousStudentExamStatus']['previous_grade_point_sum'] + $grade_point_sum;
									} else if($grade_point_sum != 0) {
										$student_copy_html .= $grade_point_sum;
									} else {
										$student_copy_html .= '---';
									}
									$student_copy_html .= 
								'</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>';

			$student_copy_html .= 
			'<br/><br/>
			<table>
				<tr>
					<td style="width:2%"></td>
					<td style="width:48%">
						<table cellpadding="1" cellspacing="2">
							<tr>
								<td><b>Generated By:</b></td>
							</tr>
							<tr>
								<td style="vertical-align:bottom; width:15%">Name:</td>
								<td style="width:50%; border-bottom:1px solid #000000">SMiS</td>
							</tr>
							<tr>
								<td>Date:</td>
								<td style="border-bottom:1px solid #000000">'.$this->Time->format("M j, Y h:i:s A", date('Y-m-d H:i:s'), NULL, NULL).'</td>
							</tr>
						</table>
					</td>
					<td style="width:8%"></td>
					<td style="width:40%">
						<table cellpadding="1" cellspacing="2">
							<tr>
								<td><b>Checked By:</b></td>
							</tr>
							<tr>
								<td style="vertical-align:bottom; width:25%">Name:</td>
								<td style="width:70%; border-bottom:1px solid #000000"></td>
							</tr>
							<tr>
								<td>Signature:</td>
								<td style="border-bottom:1px solid #000000"></td>
							</tr>
							<tr>
								<td>Date:</td>
								<td style="border-bottom:1px solid #000000"></td>
							</tr>
						</table>
					</td>
					<td style="width:2%"></td>
				</tr>
			</table>';
			
			$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerifBold.ttf');
			$pdf->SetFont($fontPath, '', 14, '', false);
			$pdf->MultiCell(157, 7, 'Student\'s Examination Grade Report', 0, 'C', false, 0, 27, 46);
			$pdf->Ln(15);

			$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'].'/app/webroot/fonts/FreeSerif.ttf');
			$pdf->SetFont($fontPath, '', 11, '', false);
			$pdf->writeHTML($student_copy_html);

			/* if ($this->Session->read('Auth.User')['role_id'] != ROLE_REGISTRAR) {
				$pdf->Image($_SERVER['DOCUMENT_ROOT'] . REGISTRAR_TRANSPARENT_STAMP_FOR_TCPDF, '215', '215', 40, 40, '', '', 'N', true, 300, 'C');
			} */


			//Footer
			$pdf->SetFont($fontPath, '', 12, '', false);
			
			//$pdf->MultiCell(100, 7, 'Web site: '.UNIVERSITY_WEBSITE.'  Email: '. REGISTRAR_EMAIL .'', 0, 'L', false, 0, 10, 270);
			$pdf->MultiCell(100, 7, 'Portal: '. PORTAL_URL_HTTPS .'', 0, 'L', false, 0, 8, 268);
			$pdf->SetFont('jiret', '', 12, '', true);
			$pdf->MultiCell(100, 7, 'መልስ ሲጽፉልን የኛን ቁጥር ይጥቀሱ::', 0, 'L', false, 0, 75, 268);

			// Amharic Motto
			$pdf->MultiCell(100, 7, $universityAmharicName . '፣ ' . UNIVERSITY_MOTTO_AM, 0, 'L', false, 0, 70, 281);

			$pdf->SetFont($fontPath, '', 11, '', false);
			$pdf->MultiCell(130, 7, 'When replying, Please, indicate our reference number.', 0, 'L', false, 0, 55, 274);
			$pdf->MultiCell(100, 7, 'Website: '. UNIVERSITY_WEBSITE .'', 0, 'L', false, 0, 145, 268);


			// English Motto
			$pdf->MultiCell(100, 7, $universityName . ', ' . UNIVERSITY_MOTTO_EN, 0, 'L', false, 0, 58, 286);


			////////////////////////////////////// WATER MARK //////////////////////////////////////

			$waterMarkFont = "Helvetica";
			$waterMarkFontSize = 40;
			$waterMarkFontStyle = "B";


			$chainWidth = $pdf->GetStringWidth(trim("FOR NON OFFICIAL USE ONLY"), $waterMarkFont, $waterMarkFontStyle, $waterMarkFontSize, false );
			$centerfactor = round(($chainWidth * sin(deg2rad(45))) / 2 , 0);

			// Get the page width/height
			$myPageWidth = $pdf->getPageWidth();
			$myPageHeight = $pdf->getPageHeight();

			// Find the middle of the page and adjust.
			$myX = ( $myPageWidth / 2 ) - $centerfactor;
			$myY = ( $myPageHeight / 2 ) + $centerfactor;

			// Set the transparency of the text to really light
			//$pdf->SetAlpha(0.09);
			$pdf->SetAlpha(0.08);

			// Rotate 45 degrees and write the watermarking text
			$pdf->StartTransform();
			$pdf->Rotate(45, $myX, $myY);
			$pdf->SetFont($waterMarkFont, $waterMarkFontStyle, $waterMarkFontSize);
			$pdf->Text($myX, $myY ,trim("FOR NON OFFICIAL USE ONLY"));
			$pdf->StopTransform();

			// Reset the transparency to default
			$pdf->SetAlpha(1);

			////////////////////////////////////// END WATER MARK //////////////////////////////////////

		}
	

		// reset pointer to the last page
		$pdf->lastPage();
		//output the PDF to the browser

		if ((count($student_copies) == 1) || !empty($studentnumber)) {
			$pdf->Output('Grade_Report_'.str_replace('/','-',$student_copies[$first_student_id]['Student']['studentnumber']).'_'.$student_copies[$first_student_id]['Student']['full_name'].'_'.(isset($student_copies[$first_student_id]['YearLevel']) && !empty($student_copies[$first_student_id]['YearLevel']['name']) ? $student_copies[$first_student_id]['YearLevel']['name'] : ($student_copies[$first_student_id]['Student']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program':'Pre/Freshman')).'_'.str_replace('/','-',$student_copies[$first_student_id]['academic_year']).'_'.$student_copies[$first_student_id]['semester'].'_'.date('Y-m-d').'.pdf', $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? 'I' : 'D');
		} else {
			$pdf->Output('Grade_Report_'.(isset($student_copies[$first_student_id]['Department']) && !empty($student_copies[$first_student_id]['Department']['name']) ? $student_copies[$first_student_id]['Department']['name']: $student_copies[$first_student_id]['College']['name']).'_'.$student_copies[$first_student_id]['Section']['name'].'_'.(isset($student_copies[$first_student_id]['YearLevel']) && !empty($student_copies[$first_student_id]['YearLevel']['name']) ? $student_copies[$first_student_id]['YearLevel']['name'] : ($student_copies[$first_student_id]['Student']['program_id'] == PROGRAM_REMEDIAL ?  'Remedial Program':'Pre/Freshman')).'_'.str_replace('/','-',$student_copies[$first_student_id]['academic_year']).'_'.$student_copies[$first_student_id]['semester'].'_'.date('Y-m-d').'.pdf', $this->Session->read('Auth.User')['role_id'] == ROLE_REGISTRAR ? 'I' : 'D');
		}
	}

    //$pdf->Output('student_copy.pdf', 'I');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
