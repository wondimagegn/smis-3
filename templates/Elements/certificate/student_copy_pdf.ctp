<?php
	//debug($student_copy);
	App::import('Vendor', 'tcpdf/tcpdf');
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true);
	//show header or footer
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);

	$pdf->SetProtection($permissions = array('modify', 'copy', 'extract', 'assemble'), $user_pass = USER_PASSWORD, $owner_pass = OWNER_PASSWORD, $mode = 0, $pubkeys = null);

    $pdf->SetCreator(PDF_CREATOR);
    $pdf->SetAuthor('SMiS, '.Configure::read('CompanyName').'');
    $pdf->SetTitle('Mass Student Copy');
    $pdf->SetSubject('Mass Student Copy');
    $pdf->SetKeywords('Mass, Student, Copy, SMiS');

	$countryAmharic = Configure::read('ApplicationDeployedCountryAmharic');
	$cityAmharic = Configure::read('ApplicationDeployedCityAmharic');
	$countryEnglish = Configure::read('ApplicationDeployedCountryEnglish');
	$cityEnglish = Configure::read('ApplicationDeployedCityEnglish');
	$pobox =  Configure::read('POBOX');

	// set font
	$pdf->SetMargins(5, 3, 5);
	$pdf->SetFont("freeserif", "", 11);

	foreach ($student_copies as $k => $student_copy) {
		$pdf->setPageOrientation('L', true, 0);
		$pdf->setPageOrientation('L', true, 0);
		//$no_of_semester = 1;
		for ($i = 0; ($i * $no_of_semester * 2) < count($student_copy['courses_taken']); $i++) {
			// add a page
			$pdf->AddPage("L");
			$pdf->SetLineStyle('dash');
			//The first part of the grade
			$x = $font_size + 4;
			$y = $font_size + 158;
			$pdf->SetLineWidth(0.1);
			$pdf->Line(24, $x, 24, $y);
			$pdf->Line(36, $x, 36, $y);
			$pdf->Line(109, $x, 109, $y);
			$pdf->Line(123, $x, 123, $y);
			$pdf->Line(134, $x, 134, $y);
			$pdf->SetLineWidth(0.6);
			$pdf->Line(149, $x, 149, $y);
			//The second part of the grade
			$pdf->SetLineWidth(0.1);
			$pdf->Line(167, $x, 167, $y);
			$pdf->Line(179, $x, 179, $y);
			$pdf->Line(251, $x, 251, $y);
			$pdf->Line(265, $x, 265, $y);
			$pdf->Line(277, $x, 277, $y);


			/* $stream = explode(' in ', $student_copy['student_detail']['Curriculum']['english_degree_nomenclature']);
			if (count($stream) == 1) {
				$stream = explode(' of ', $student_copy['student_detail']['Curriculum']['english_degree_nomenclature']);
			}
			*/
			if(isset($student_copy['student_detail']['Curriculum']['specialization_english_degree_nomenclature']) && !empty($student_copy['student_detail']['Curriculum']['specialization_english_degree_nomenclature'])){
				$stream = explode(' in ', $student_copy['student_detail']['Curriculum']['specialization_english_degree_nomenclature']);
				if (count($stream) == 1) {
					$stream2 = explode(' of ', $student_copy['student_detail']['Curriculum']['specialization_english_degree_nomenclature']);
					if (count($stream2) == 2 && count($stream) == 1) {
						$stream[1] =  $stream2[1];
					}else if (count($stream2) > 2 && count($stream) == 1) {
						$stream[1] =  $stream2[2];
					} else{
						$stream[1] = $student_copy['student_detail']['Curriculum']['specialization_english_degree_nomenclature'];
					}
				}
			} else {
				$stream = explode(' in ', $student_copy['student_detail']['Curriculum']['english_degree_nomenclature']);
				if (count($stream) == 1) {
					$stream = explode(' of ', $student_copy['student_detail']['Curriculum']['english_degree_nomenclature']);
				}
			}

			$searchBracket = explode('(', $stream[1]);

			if (count($searchBracket) != 1) {
				$searchBracket = explode(')', $searchBracket[1]);
				$stream[1] = trim($searchBracket[0]);
			}

			$default_exam_taken_date = ''; 

			if ($student_copy['student_detail']['Program']['id'] != PROGRAM_POST_GRADUATE && $student_copy['student_detail']['Program']['id'] != PROGRAM_PhD) {

				if ($student_copy['student_detail']['Student']['country_id'] == COUNTRY_ID_OF_ETHIOPIA && !empty($student_copy['student_detail']['Student']['AcceptedStudent']['high_school'])) {

					if (isset($student_copy['student_detail']['EheeceResult']) && !empty($student_copy['student_detail']['EheeceResult']) && $student_copy['student_detail']['EheeceResult'][0]['exam_year'] != '0000-00-00') {
						$exam_year = ((int) explode('-', $student_copy['student_detail']['EheeceResult'][0]['exam_year'])[0]);
						if ($exam_year > 1980) {
							$default_exam_taken_date = 'July 1, '. $exam_year;
						}
					} else if (isset($student_copy['student_detail']['EslceResult']) && !empty($student_copy['student_detail']['EslceResult']) && $student_copy['student_detail']['EslceResult'][0]['exam_year'] != '0000') {
						if ((int) $student_copy['student_detail']['EslceResult'][0]['exam_year'] > 1980) {
							$default_exam_taken_date = 'July 1, '. $student_copy['student_detail']['EslceResult'][0]['exam_year'];
						}
					} 
					
					if (empty($default_exam_taken_date)) {
						$exam_year = ((int) explode('/', $student_copy['student_detail']['Student']['AcceptedStudent']['academicyear'])[0]/*  - 1 */);
						$default_exam_taken_date = 'July 1, '. $exam_year;
					}
				} else if ($student_copy['student_detail']['Student']['country_id'] == COUNTRY_ID_OF_ETHIOPIA) {
					$exam_year = ((int) explode('/', $student_copy['student_detail']['Student']['AcceptedStudent']['academicyear'])[0]);
					$default_exam_taken_date = 'July 1, '. $exam_year;
				}

				$schoolHigherEntryForm = '
				<td style="width:45%">
					Entry From: <strong>' . (!empty($student_copy['student_detail']['HighSchoolEducationBackground']) ? (ucwords(strtolower(trim($student_copy['student_detail']['HighSchoolEducationBackground'][0]['name'])))) . ' (' . ($student_copy['student_detail']['Student']['region_id'] != REGION_ID_OF_ADDIS_ABABA && $student_copy['student_detail']['Student']['region_id'] != REGION_ID_OF_DIRE_DAWA && ((trim($student_copy['student_detail']['HighSchoolEducationBackground'][0]['town'])) !== (trim($student_copy['student_detail']['HighSchoolEducationBackground'][0]['Region']['name'])))  ? (ucwords(strtolower(trim($student_copy['student_detail']['HighSchoolEducationBackground'][0]['town'])))) . ', ' : '') . (ctype_upper(trim($student_copy['student_detail']['HighSchoolEducationBackground'][0]['Region']['name'])) ? trim($student_copy['student_detail']['HighSchoolEducationBackground'][0]['Region']['name']) : (ucwords(strtolower(trim($student_copy['student_detail']['HighSchoolEducationBackground'][0]['Region']['name']))))) . ', ' . $student_copy['student_detail']['HighSchoolEducationBackground'][0]['Region']['Country']['name']. ')' : (!empty($student_copy['student_detail']['Student']['AcceptedStudent']['high_school']) ? (ucwords(strtolower(trim($student_copy['student_detail']['Student']['AcceptedStudent']['high_school'])))) . ' (' . ($student_copy['student_detail']['Student']['AcceptedStudent']['Region']['Country']['name'] !== $student_copy['student_detail']['Student']['AcceptedStudent']['Region']['name'] ? $student_copy['student_detail']['Student']['AcceptedStudent']['Region']['name'] . ', ' : '') . $student_copy['student_detail']['Student']['AcceptedStudent']['Region']['Country']['name'] . ')'  : '')) . '</strong>
				</td>
				<td style="width:28%">
					Test Date: <strong>' . (isset($student_copy['student_detail']['EheeceResult']) && !empty($student_copy['student_detail']['EheeceResult']) && $student_copy['student_detail']['EheeceResult'][0]['exam_year'] != '0000-00-00' ? ($student_copy['student_detail']['Student']['country_id'] == COUNTRY_ID_OF_ETHIOPIA ? 'EHEECE ': '') . (((int) explode('-', $student_copy['student_detail']['EheeceResult'][0]['exam_year'][0]) > 1980 ) ? $this->Time->format("M j, Y", $student_copy['student_detail']['EheeceResult'][0]['exam_year'], NULL, NULL) : ($student_copy['student_detail']['Student']['country_id'] == COUNTRY_ID_OF_ETHIOPIA && !empty($default_exam_taken_date) ? $default_exam_taken_date : '')) . ' (G.C.)' : (isset($student_copy['student_detail']['EslceResult']) && !empty($student_copy['student_detail']['EslceResult'])  && $student_copy['student_detail']['EslceResult'][0]['exam_year'] != '0000' ? ($student_copy['student_detail']['Student']['country_id'] == COUNTRY_ID_OF_ETHIOPIA ? 'ESLCE ': '')  . 'July 1, '. $student_copy['student_detail']['EslceResult'][0]['exam_year']. ' (G.C.)' : (!empty($default_exam_taken_date) && !empty($student_copy['student_detail']['Student']['AcceptedStudent']['high_school']) ?  $default_exam_taken_date . ' (G.C.)' : ''))) . '</strong>
				</td>';

			} else {

				if ($student_copy['student_detail']['Program']['id'] == PROGRAM_PhD) {
					$EntryFrom = 'Second Degree Attended at: ';
					$TestDate = 'Second Degree Awareded on: ';
				} else {
					$EntryFrom = 'First Degree Attended at: ';
					$TestDate = 'First Degree Awareded on: ';
				}

				if (!empty($student_copy['student_detail']['HigherEducationBackground'])) {
					foreach ($student_copy['student_detail']['HigherEducationBackground'] as $hk => $hv) {
						/* if ($hv['first_degree_taken'] == 1) {
							$EntryFrom = $student_copy['student_detail']['HigherEducationBackground'][0]['name'] . ' (' . $student_copy['student_detail']['HigherEducationBackground'][0]['city'] . ')';
							//$TestDate = ' '. $this->Time->format("M j, Y", $student_copy['student_detail']['HigherEducationBackground'][0]['date_graduated'], NULL, NULL);
							$TestDate = (isset($student_copy['student_detail']['EheeceResult']) && !empty($student_copy['student_detail']['EheeceResult']) && $student_copy['student_detail']['EheeceResult'][0]['exam_year'] != '0000-00-00' ? 'EHEECE ' . $this->Time->format("M j, Y", $student_copy['student_detail']['EheeceResult'][0]['exam_year'], NULL, NULL) . ' (G.C.)' : (isset($student_copy['student_detail']['EslceResult']) && !empty($student_copy['student_detail']['EslceResult'])  && $student_copy['student_detail']['EslceResult'][0]['exam_year'] != '0000-00-00' ? 'ESLCE ' . $this->Time->format("M j, Y", $student_copy['student_detail']['EslceResult'][0]['exam_year'], NULL, NULL). ' (G.C.)' : ''));
							break;
						} else if ($hv['second_degree_taken'] == 1) {
							$EntryFrom = $student_copy['student_detail']['HigherEducationBackground'][0]['name'] . ' (' . $student_copy['student_detail']['HigherEducationBackground'][0]['city'] . ')';
							//$TestDate = ' '. $this->Time->format("M j, Y", $student_copy['student_detail']['HigherEducationBackground'][0]['date_graduated'], NULL, NULL);
							$TestDate = (isset($student_copy['student_detail']['EheeceResult']) && !empty($student_copy['student_detail']['EheeceResult']) && $student_copy['student_detail']['EheeceResult'][0]['exam_year'] != '0000-00-00' ? 'EHEECE ' . $this->Time->format("M j, Y", $student_copy['student_detail']['EheeceResult'][0]['exam_year'], NULL, NULL) . ' (G.C.)' : (isset($student_copy['student_detail']['EslceResult']) && !empty($student_copy['student_detail']['EslceResult'])  && $student_copy['student_detail']['EslceResult'][0]['exam_year'] != '0000-00-00' ? 'ESLCE ' . $this->Time->format("M j, Y", $student_copy['student_detail']['EslceResult'][0]['exam_year'], NULL, NULL). ' (G.C.)' : ''));
							break;
						} */
						
						if (isset($student_copy['student_detail']['HigherEducationBackground'][$hk]['name']) && !empty($student_copy['student_detail']['HigherEducationBackground'][$hk]['name'])) {
							$EntryFrom = (isset($hv['second_degree_taken']) && $hv['second_degree_taken'] == 1 || $student_copy['student_detail']['Program']['id'] == PROGRAM_PhD ? 'Second Degree Attended at: <strong>' : 'First Degree Attended at: <strong>') . (ucwords(strtolower(trim($student_copy['student_detail']['HigherEducationBackground'][$hk]['name'])))) . ' (' . (ucwords(strtolower(trim($student_copy['student_detail']['HigherEducationBackground'][$hk]['city'])))) . ')</strong>';
							$TestDate = (isset($student_copy['student_detail']['HigherEducationBackground'][$hk]['date_graduated']) && !empty($student_copy['student_detail']['HigherEducationBackground'][$hk]['date_graduated']) && $student_copy['student_detail']['HigherEducationBackground'][$hk]['date_graduated'] != '0000-00-00' ? (isset($hv['second_degree_taken']) && $hv['second_degree_taken'] == 1 || $student_copy['student_detail']['Program']['id'] == PROGRAM_PhD ? 'Second Degree Awareded on: <strong>' : 'First Degree Awareded on: <strong>'). $this->Time->format("M j, Y", $student_copy['student_detail']['HigherEducationBackground'][$hk]['date_graduated'], NULL, NULL) . ' (G.C.)</strong>' : '');
							break;
						} /* else {
							continue;
						} */
					}
				}


				$schoolHigherEntryForm = '
				<td style="width:45%">' . $EntryFrom . '</td>
				<td style="width:28%">' . $TestDate . '</td>';
			}

			$creditType = 'ECTS';

			if (strcasecmp($student_copy['student_detail']['Curriculum']['type_credit'], 'Credit') == 0) {
				$creditType = 'Credit';
			} else if (empty($student_copy['student_detail']['Student']['department_id'])) {
				$creditType = 'Credit';
			}

			$student_copy_content = '
			<table style="width:100%; font-size:' . $font_size . 'px">
				<tr>
					<td background="/images/sc-diagonal-line.gif">
						<table style="width:100%">
							<tr>
								<td style="text-align:left">OFFICE OF THE REGISTRAR</td>
								<td style="text-align:center;font-weight:bold">' . (!empty($student_copy['student_detail']['University']) ? strtoupper($student_copy['student_detail']['University']['University']['name']) : strtoupper(Configure::read('CompanyName'))) . '</td>
								<td style="text-align:right">Certificate: ' . (!empty($student_copy['student_detail']['Curriculum']) ? $student_copy['student_detail']['Curriculum']['certificate_name'] : '') . '</td>
							</tr>
							<tr>
								<td style="text-align:left">
									P.O.Box: ' . (!empty($student_copy['student_detail']['University']) ? $student_copy['student_detail']['University']['University']['p_o_box'] : '' . $pobox . '') . ' ' . $cityEnglish . ', ' . $countryEnglish . '<br/> 
									Phone: ' . $student_copy['student_detail']['University']['University']['telephone'] . '  &nbsp;&nbsp;Fax: ' . $student_copy['student_detail']['University']['University']['fax'] . ' <br/>
									Web site: ' . UNIVERSITY_WEBSITE .' &nbsp;Email: '.REGISTRAR_EMAIL.'
								</td>
								<td valign="bottom" style="vertical-align:bottom; text-align:center">
									STUDENT ACADEMIC RECORD <br/>
									Medium of Instruction: English
								</td>
								<td style="text-align:right">
									Degree Award Date: ' . (!empty($student_copy['student_detail']['GraduateList']) && $student_copy['student_detail']['GraduateList']['id'] != "" ? $this->Time->format("M j, Y", $student_copy['student_detail']['GraduateList']['graduate_date'], NULL, NULL) : 'Not Graduated') . '<br />
									Department: ' . (isset($student_copy['student_detail']['Department']['name']) && !empty($student_copy['student_detail']['Department']['name']) ? $student_copy['student_detail']['Department']['name'] : 'Freshman Program') . ' <br/> 
									Stream: ' . $stream[1] . '
								</td>
							</tr>
						</table>
					</td>
				</tr>
				<tr><td style="height:5px; font-size:3px">&nbsp;</td></tr>
				<tr>
					<td>
						<table style="width:100%; margin:0px; padding:0px">
							<tr>
								<td style="border:2px solid #000000">
									<table style="margin:0px; padding:0px">
										<tr>
											<td style="border-bottom:1px solid #000000">
												<table style="margin:0px; padding:1px">
													<tr>
														<td style="width:30%"> &nbsp;Name: <strong>' . $student_copy['student_detail']['Student']['first_name'] . ' ' . $student_copy['student_detail']['Student']['middle_name'] . '</strong></td>
														<td style="width:25%">Father\'s Name: <strong>' . $student_copy['student_detail']['Student']['middle_name'] . ' ' . $student_copy['student_detail']['Student']['last_name'] . '</strong></td>
														<td style="width:25%">Date of Birth: <strong>' . (!empty($student_copy['student_detail']['Student']['birthdate']) && $student_copy['student_detail']['Student']['birthdate'] != '0000-00-00' ? $this->Time->format("M j, Y", $student_copy['student_detail']['Student']['birthdate'], NULL, NULL) . '(G.C.)' : '') . ' </strong></td>
														<td style="width:20%">Admission: <strong>' . $student_copy['student_detail']['ProgramType']['name'] . '</strong></td>
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td style="border-bottom:1px solid #000000">
												<table style="padding:1px">
													<tr>
														<td style="width:19%"> &nbsp;ID Number: <strong>' . $student_copy['student_detail']['Student']['studentnumber'] . '</strong></td>
														<td style="width:8%">Sex: <strong>' . (strcasecmp($student_copy['student_detail']['Student']['gender'], 'male') == 0 ? 'M' : 'F') . '</strong></td>' . $schoolHigherEntryForm . '
													</tr>
												</table>
											</td>
										</tr>
										<tr>
											<td>
												<table>
													<tr>
														<td style="width:50%;height:435px;">
															<table>
																<tr>
																	<td>
																		<table style="padding:' . $course_justification . 'px; margin:0px; background-image:url(\'/img/sc-diagonal-line.gif\'); background-position:left top; background-attachment:scroll; background-repeat:no-repeat">
																			<tr>
																				<td style="width:52px; text-align:center; border-bottom:0px solid #000000">Dept</td>
																				<td style="width:41px; border-bottom:0px solid #000000">&nbsp; Code</td>
																				<td style="width:200px; border-bottom:0px solid #000000">Course Title</td>
																				<td style="width:38px; text-align:center; border-bottom:0px solid #000000">' . $creditType . '</td>
																				<td style="width:32px; border-bottom:0px solid #000000">&nbsp; Grade</td>
																				<td style="width:42px; text-align:center; border-bottom:0px solid #000000">GP</td>
																			</tr>';
																			//display exemption here
																			if (isset($student_copy['student_detail']['ExemptionList']) && !empty($student_copy['student_detail']['ExemptionList'])) {
																				$student_copy_content .= '
																				<tr>
																					<td></td>
																					<td></td>
																					<td style="text-align:center"><strong> Transfer from ' . $student_copy['student_detail']['ExemptionList'][0]['CourseExemption']['transfer_from'] . ' </strong></td>
																					<td></td>
																					<td></td>
																					<td></td>
																				</tr>';

																				$totalExemptedCredit = 0;
																				foreach ($student_copy['student_detail']['ExemptionList'] as $keyexemption => $exemptionValue) {
																					$totalExemptedCredit += $exemptionValue['Course']['credit'];
																					$course_code = explode('-', $exemptionValue['Course']['course_code']);
																					$student_copy_content .= '
																						<tr>
																							<td style="text-align:center">' . $course_code[0] . '</td>
																							<td style="margin-right:2px;"> &nbsp;' . $course_code[1] . '</td>
																							<td>' . ($exemptionValue['Course']['course_title']) . '</td>
																							<td style="text-align:center">' . ($exemptionValue['Course']['credit']) . '</td>';
																							$student_copy_content .= '<td style="text-align:center">' . ((isset($exemptionValue['CourseExemption']['grade']) && !empty($exemptionValue['CourseExemption']['grade'])) ? $exemptionValue['CourseExemption']['grade']: 'TR') . '</td><td style="text-align:center">' . ((!isset($exemptionValue['CourseExemption']['grade']) && empty($exemptionValue['CourseExemption']['grade'])) ? '---': 'TR') . '</td>
																						</tr>';
																				}
																				$student_copy_content .= '
																				<tr>
																					<td></td>
																					<td></td>
																					<td style="text-align:right; font-weight:bold">TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</td>
																					<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . $totalExemptedCredit . '</td>
																					<td style="border-top:1px solid #000000; border-bottom:2px solid #000000"></td>
																					<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold"> </td>
																				</tr>';
																			}


																			for ($j = $i * $no_of_semester * 2; $j < (($i * $no_of_semester * 2) + $no_of_semester) && $j < count($student_copy['courses_taken']); $j++) {

																				if ($student_copy['courses_taken'][$j]['readmitted']) {
																					$student_copy_content .= '
																					<tr>
																						<td></td>
																						<td></td>
																						<td><strong>Readmitted(' . $student_copy['courses_taken'][$j]['academic_year'] . ' Ac. Yr)</strong></td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}

																				$student_copy_content .= '
																				<tr>
																					<td></td>
																					<td></td>
																					<td style="text-align:center"><strong>' . ($student_copy['courses_taken'][$j]['semester'] == 'I' ? 'First Semester of' : ($student_copy['courses_taken'][$j]['semester'] == 'II' ? 'Second Semester of' : 'Kiremet Semester of')) . ' ' . $student_copy['courses_taken'][$j]['academic_year'] . ' Ac. Yr' . '</strong></td>
																					<td></td>
																					<td></td>
																					<td></td>
																				</tr>';

																				$semester_cr_hrs = 0;
																				$semester_gr_pts = 0;
																				$full_gr_pts = true;

																				foreach ($student_copy['courses_taken'][$j]['courses_and_grades'] as $key => $courses_and_grade) {
																					
																					if (!isset($courses_and_grade['thesis'])) {
																						$courses_and_grade['thesis'] = 0;
																					}

																					$semester_cr_hrs += $courses_and_grade['credit'];

																					if (isset($courses_and_grade['point_value'])) {
																						$semester_gr_pts += ($courses_and_grade['used_in_gpa'] == 1 ? ($courses_and_grade['point_value'] * $courses_and_grade['credit']) : 0);
																					} else {
																						$full_gr_pts = false;
																					}

																					$course_code = explode('-', $courses_and_grade['course_code']);

																					$student_copy_content .= '
																					<tr>
																						<td style="text-align:center">' . $course_code[0] . '</td>
																						<td style="margin-right:2px;"> &nbsp;' . $course_code[1] . '</td>
																						<td>' . ($courses_and_grade['course_title']) . '</td>
																						<td style="text-align:center">' . ($courses_and_grade['credit']) . '</td>';

																						if (isset($courses_and_grade['repeated_old']) && $courses_and_grade['repeated_old'] == true) {
																							if (isset($courses_and_grade['grade']) && strcasecmp($courses_and_grade['grade'], 'W') == 0) {
																								$student_copy_content .= '<td  style="text-align:center; background-position:left top; background-attachment:scroll; background-repeat:no-repeat">' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] : '') . '</td>';
																							} else {
																								$student_copy_content .= '<td  style="text-align:center; background-position:left top; background-attachment:scroll; background-repeat:no-repeat"><del>' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] : '') . '</del></td>';
																							}
																						} else {
																							$student_copy_content .= '<td  style="text-align:center; background-position:left top; background-attachment:scroll; background-repeat:no-repeat">' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] . (isset($courses_and_grade['repeated_new']) && $courses_and_grade['repeated_new'] == true ? '*' : ($courses_and_grade['thesis'] == 1 ? '**' : '')) : '---') . '</td>';
																						}

																						$student_copy_content .= '<td style="text-align:center">' . (isset($courses_and_grade['point_value']) ? ($courses_and_grade['used_in_gpa'] == 1 ? ($courses_and_grade['point_value'] * $courses_and_grade['credit']) : '---') : (isset($courses_and_grade['grade']) && strcasecmp($courses_and_grade['grade'], 'Ex') == 0 ? '---' : '---')) . '</td>
																					</tr>';
																				}

																				$student_copy_content .= '
																				<tr>
																					<td></td>
																					<td></td>
																					<td style="text-align:right; font-weight:bold">TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</td>
																					<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . $semester_cr_hrs . '</td>
																					<td style="border-top:1px solid #000000; border-bottom:2px solid #000000"></td>
																					<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . ($full_gr_pts ? $semester_gr_pts : '') . '</td>
																				</tr>';

																				if (!empty($student_copy['courses_taken'][$j]['status'])) {
																					$student_copy_content .= '
																					<tr>
																						<td></td>
																						<td></td>
																						<td style="font-weight:bold"> Semester Average: ' . (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['StudentExamStatus']['sgpa'] : '') . '' . ($j > 0 ? (!empty($student_copy['courses_taken'][$j]['status']) ? '<br />&nbsp;Cumulative GPA: ' . $student_copy['courses_taken'][$j]['status']['StudentExamStatus']['cgpa'] : '') : '') . '' . (!(($j + 1) == count($student_copy['courses_taken']) && $student_copy['student_detail']['GraduateList']['id'] != "") ? ('<br />&nbsp;Academic Status: ' . (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['AcademicStatus']['name'] : '')) : '') . '</td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}

																				if (($j + 1) == count($student_copy['courses_taken']) && !empty($student_copy['student_detail']['GraduationWork'])) {
																					$student_copy_content .= '
																					<tr>
																						<td></td>
																						<td></td>
																						<td style="font-weight:bold"><u style="text-align:center">' . (strcasecmp($student_copy['student_detail']['GraduationWork']['type'], 'thesis') == 0 ? 'Title of the ' . ($student_copy['student_detail']['Student']['program_id'] == PROGRAM_PhD ? 'Dissertation' : 'Thesis') : 'Title of the Project') . '</u><br />' . ($student_copy['student_detail']['GraduationWork']['title']) . '<br /></td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}

																				if (($j + 1) == count($student_copy['courses_taken']) && !empty($student_copy['student_detail']['ExitExam'])) {
																					$student_copy_content .= '
																					<tr><td collspan="6"></td></tr>
																					<tr>
																						<td></td>
																						<td></td>
																						<td style="font-weight:bold">
																							<br>
																							<u style="text-align:center">' . (isset($student_copy['student_detail']['ExitExam']['course']) ? $student_copy['student_detail']['ExitExam']['course'] : 'National Exit Exam') . '</u>
																							<br />   Result: '. ($student_copy['student_detail']['ExitExam']['result_formated']) . '<br />
																							Exam Date: ' . (isset($student_copy['student_detail']['ExitExam']['exam_date']) ? $this->Time->format("F Y", $student_copy['student_detail']['ExitExam']['exam_date'], NULL, NULL) : 'N/A') . '<br />
																						</td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}

																				if (($j + 1) == count($student_copy['courses_taken']) && $student_copy['student_detail']['GraduateList']['id'] != "") {
																					$student_copy_content .= '
																					<tr>
																						<td></td>
																						<td></td>
																						<td style="font-weight:bold">Academic Status: Graduated ' . (!empty($student_copy['student_detail']['GraduationStatuse']) ? '<br /><span style="text-align:center">with ' . $student_copy['student_detail']['GraduationStatuse']['status'] . '</span>' : '') . '</td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}
																			}
																			$student_copy_content .= '
																		</table>
																	</td>
																</tr>
															</table>
														</td>


														<td style="width:50%">
															<table style="width:100%; height:1000px">
																<tr>
																	<td>
																		<table style="padding:' . $course_justification . 'px; height:100%">
																			<tr>
																				<td style="width:52px; text-align:center; border-bottom:0px solid #000000">Dept</td>
																				<td style="width:41px; border-bottom:0px solid #000000">&nbsp; Code</td>
																				<td style="width:200px; border-bottom:0px solid #000000">Course Title</td>
																				<td style="width:38px; text-align:center; border-bottom:0px solid #000000">' . $creditType . '</td>
																				<td style="width:32px; border-bottom:0px solid #000000">&nbsp; Grade</td>
																				<td style="width:42px; text-align:center; border-bottom:0px solid #000000">GP</td>
																			</tr>';

																			for ($j = (($i * $no_of_semester * 2) + $no_of_semester); $j < (($i * $no_of_semester * 2) + $no_of_semester + $no_of_semester) && $j < count($student_copy['courses_taken']); $j++) {

																				if ($student_copy['courses_taken'][$j]['readmitted']) {
																					$student_copy_content .= '
																					<tr>
																						<td></td>
																						<td></td>
																						<td><strong>Readmitted(' . $student_copy['courses_taken'][$j]['academic_year'] . ' Ac. Yr)</strong></td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}

																				$student_copy_content .= '
																				<tr>
																					<td></td>
																					<td></td>
																					<td style="text-align:center"><strong>' . ($student_copy['courses_taken'][$j]['semester'] == 'I' ? 'First Semester of' : ($student_copy['courses_taken'][$j]['semester'] == 'II' ? 'Second Semester of' : 'Kiremet Semester of')) . ' ' . $student_copy['courses_taken'][$j]['academic_year'] . ' Ac. Yr' . '</strong></td>
																					<td></td>
																					<td></td>
																					<td></td>
																				</tr>';


																				$semester_cr_hrs = 0;
																				$semester_gr_pts = 0;
																				$full_gr_pts = true;

																				foreach ($student_copy['courses_taken'][$j]['courses_and_grades'] as $key => $courses_and_grade) {
																					if (!isset($courses_and_grade['thesis'])) {
																						$courses_and_grade['thesis'] = 0;
																					}
																					$semester_cr_hrs += $courses_and_grade['credit'];

																					if (isset($courses_and_grade['point_value'])) {
																						$semester_gr_pts += ($courses_and_grade['used_in_gpa'] == 1 ? ($courses_and_grade['point_value'] * $courses_and_grade['credit']) : 0);
																					} else {
																						$full_gr_pts = false;
																					}
																					
																					$course_code = explode('-', $courses_and_grade['course_code']);

																					$student_copy_content .= '
																					<tr>
																						<td style="text-align:center">' . $course_code[0] . '</td>
																						<td> &nbsp;' . $course_code[1] . '</td>
																						<td>' . ($courses_and_grade['course_title']) . '</td>
																						<td style="text-align:center">' . ($courses_and_grade['credit']) . '</td>';

																						if (isset($courses_and_grade['repeated_old']) && $courses_and_grade['repeated_old'] == true ) {
																							if (isset($courses_and_grade['grade']) && strcasecmp($courses_and_grade['grade'], 'W') == 0) {
																								$student_copy_content .= '<td  style="text-align:center; background-position:left top; background-attachment:scroll; background-repeat:no-repeat">' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] : '') . '</td>';
																							} else {
																								$student_copy_content .= '<td  style="text-align:center; background-position:left top; background-attachment:scroll; background-repeat:no-repeat"><del>' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] : '') . '</del></td>';
																							}
																						} else {
																							$student_copy_content .= '<td  style="text-align:center; background-position:left top; background-attachment:scroll; background-repeat:no-repeat">' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] . (isset($courses_and_grade['repeated_new']) && $courses_and_grade['repeated_new'] == 1 ? '*' : ($courses_and_grade['thesis'] == 1 ? '**' : '')) : '---') . '</td>';
																						}
																						$student_copy_content .= '<td style="text-align:center">' . (isset($courses_and_grade['point_value']) ? ($courses_and_grade['used_in_gpa'] == 1 ? ($courses_and_grade['point_value'] * $courses_and_grade['credit']) : '---') : (isset($courses_and_grade['grade']) && strcasecmp($courses_and_grade['grade'], 'Ex') == 0 ? '---' : '---')) . '</td>
																					</tr>';
																				}

																				$student_copy_content .= '
																				<tr>
																					<td></td>
																					<td></td>
																					<td style="text-align:right; font-weight:bold">TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</td>
																					<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . $semester_cr_hrs . '</td>
																					<td style="border-top:1px solid #000000; border-bottom:2px solid #000000"></td>
																					<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . ($full_gr_pts ? $semester_gr_pts : '') . '</td>
																				</tr>';

																				if (!empty($student_copy['courses_taken'][$j]['status'])) {
																					$student_copy_content .= '
																					<tr>
																						<td></td>
																						<td></td>
																						<td style="font-weight:bold"> Semester Average: ' . (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['StudentExamStatus']['sgpa'] : '') . '' . ($j > 0 ? (!empty($student_copy['courses_taken'][$j]['status']) ? '<br />&nbsp;Cumulative GPA: ' . $student_copy['courses_taken'][$j]['status']['StudentExamStatus']['cgpa'] : '') : '') . '' . (!(($j + 1) == count($student_copy['courses_taken']) && $student_copy['student_detail']['GraduateList']['id'] != "") ? ('<br />&nbsp;Academic Status: ' . (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['AcademicStatus']['name'] : '')) : '') . '</td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}

																				if (($j + 1) == count($student_copy['courses_taken']) && !empty($student_copy['student_detail']['GraduationWork'])) {
																					$student_copy_content .= '<tr>
																						<td></td>
																						<td></td>
																						<td style="font-weight:bold"><u style="text-align:center">' . (strcasecmp($student_copy['student_detail']['GraduationWork']['type'], 'thesis') == 0 ? 'Title of the ' . ($student_copy['student_detail']['Student']['program_id'] == PROGRAM_PhD ? 'Dissertation' : 'Thesis') : 'Title of the Project') . '</u><br />' . ($student_copy['student_detail']['GraduationWork']['title']) . '<br /></td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}

																				if (($j + 1) == count($student_copy['courses_taken']) && !empty($student_copy['student_detail']['ExitExam'])) {
																					$student_copy_content .= '
																					<tr><td collspan="6"></td></tr>
																					<tr>
																						<td></td>
																						<td></td>
																						<td style="font-weight:bold">
																							<br>
																							<u style="text-align:center">' . (isset($student_copy['student_detail']['ExitExam']['course']) ? $student_copy['student_detail']['ExitExam']['course'] : 'National Exit Exam') . '</u>
																							<br />   Result: '. ($student_copy['student_detail']['ExitExam']['result_formated']) . '<br />
																							Exam Date: ' . (isset($student_copy['student_detail']['ExitExam']['exam_date']) ? $this->Time->format("F Y", $student_copy['student_detail']['ExitExam']['exam_date'], NULL, NULL) : 'N/A') . '<br />
																						</td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}
																				
																				if (($j + 1) == count($student_copy['courses_taken']) && $student_copy['student_detail']['GraduateList']['id'] != "") {
																					$student_copy_content .= '
																					<tr>
																						<td></td>
																						<td></td>
																						<td style="font-weight:bold">Academic Status: Graduated ' . (!empty($student_copy['student_detail']['GraduationStatuse']) ? '<br /><span style="text-align:center">with ' . $student_copy['student_detail']['GraduationStatuse']['status'] . '</span>' : '') . '</td>
																						<td></td>
																						<td></td>
																						<td></td>
																					</tr>';
																				}
																			}
																			$student_copy_content .= '
																		</table>
																	</td>
																</tr>
															</table>
														</td>
													</tr>
												</table>
											</td>
										</tr>
									</table>
								</td>
							</tr>
						</table>
					</td>
				</tr>
			</table>';

			if (!empty($student_copy['student_detail']['TranscriptFooter'])) {

				$student_copy_content .= '
				<span style="font-size:' . (2 * $student_copy['student_detail']['TranscriptFooter']['font_size']) . 'px">';

					if ($student_copy['student_detail']['TranscriptFooter']['line1']) {
						$student_copy_content .= '&nbsp;' . $student_copy['student_detail']['TranscriptFooter']['line1'];
					}
					if ($student_copy['student_detail']['TranscriptFooter']['line2']) {
						$student_copy_content .= '<br />&nbsp;' . $student_copy['student_detail']['TranscriptFooter']['line2'];
					}
					if ($student_copy['student_detail']['TranscriptFooter']['line3']) {
						$student_copy_content .= '<br />&nbsp;' . $student_copy['student_detail']['TranscriptFooter']['line3'];
					}
					$student_copy_content .= '
				</span>';
			}

			if (isset($student_copy['student_detail']['Student']['student_national_id']) && !empty($student_copy['student_detail']['Student']['student_national_id'])) {
				$student_copy_content .= '<br />
				<table style="width:100%; padding-top:5px">
					<tr>
						<td style="width:30%">Date of Issue: <u>' . date('Y-m-d') . '</u></td>
						<td style="width:30%">Registrar: <u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</u></td>
						<td style="width:25%">Student National ID: <u>'. $student_copy['student_detail']['Student']['student_national_id'] .'</u></td>
						<td style="width:15%; text-align:right">Serial: <u>'.$student_copy['student_detail']['Student']['code'].'</u></td>
					</tr>
				</table>';
			} else {
				$student_copy_content .= '<br />
				<table style="width:100%; padding-top:5px">
					<tr>
						<td style="width:35%">Date of Issue: <u>' . date('Y-m-d') . '</u></td>
						<td style="width:45%">Registrar: <u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</u></td>
						<td style="width:20%;">Serial: <u>'.$student_copy['student_detail']['Student']['code'].'</u></td>
					</tr>
				</table>';
			}

			$pdf->writeHTML($student_copy_content);
		}
		// reset pointer to the last page
		$pdf->lastPage();
	}

	if(count($student_copies) == 1) {
        $pdf->Output('Student_Copy_'.str_replace('/','-',$student_copies[0]['student_detail']['Student']['studentnumber']).'_'.$student_copies[0]['student_detail']['Student']['first_name'].'_'.$student_copies[0]['student_detail']['Student']['middle_name'].'_'.$student_copies[0]['student_detail']['Student']['last_name'].'_'.date('Y-m-d').'.pdf', 'I');
    } else {
        $pdf->Output('Student_Copy_'.date('Y-m-d').'.pdf', 'I');
    }


    //output the PDF to the browser
	/*
    $pdf->Output('Student Copy - '.$student_copy['student_detail']['Student']['first_name'].' '.$student_copy['student_detail']['Student']['middle_name'].' '.$student_copy['student_detail']['Student']['last_name'].'.pdf', 'I');
	*/
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */