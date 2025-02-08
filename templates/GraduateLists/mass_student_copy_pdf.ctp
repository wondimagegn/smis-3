<?php
// ==================== NOT BEING USED, USING FILE FROM ELEMENTS ====================
//debug($student_copy);
//$student_copy=$student_copies[0];
App::import('Vendor', 'tcpdf/tcpdf');
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true);

//show header or footer
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
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
		//url(\''.$_SERVER['DOCUMENT_ROOT'].'/app/webroot/img/sc-diagonal-line.gif\')
		//$pdf->SetLineWidth(0.1);
		//$pdf->Cell(100, 20, 'Hello PDF', 1);
		$student_copy_content = '
    <table style="width:100%; font-size:' . $font_size . 'px">
    	<tr>
    		<td style="text-align:center; font-weight:bold">' . (!empty($student_copy['student_detail']['University']) ? strtoupper($student_copy['student_detail']['University']['University']['name']) : ' Arba Minch UNIVERSITY') . '</td>
    	</tr>
		
    	<tr>
    		<td background="/images/sc-diagonal-line.gif">
    			<table style="width:100%">
    				<tr>
    					<td style="text-align:left">OFFICE OF THE REGISTRAR</td>
    					<td style="text-align:center">STUDENT ACADEMIC RECORD</td>
    					<td style="text-align:right">Certificate: ' . (!empty($student_copy['student_detail']['Curriculum']) ? $student_copy['student_detail']['Curriculum']['certificate_name'] : '') . '</td>
    				</tr>
    				<tr>
    					<td style="text-align:left">P.O.Box: ' .
			(!empty($student_copy['student_detail']['University']) ?
				$student_copy['student_detail']['University']['University']['p_o_box'] : '' . $pobox . '')
			. ' ' . $cityEnglish . ', ' . $countryEnglish . '<br />Phone: ' . (!empty($student_copy['student_detail']['University']) ?
				$student_copy['student_detail']['University']['University']['telephone'] : '' . $pobox . '') . '</td>
    					<td valign="bottom" style="vertical-align:bottom; text-align:center"><br /><br />Medium of Instruction: English</td>
    					<td style="text-align:right">Degree Award Date: ' . (!empty($student_copy['student_detail']['GraduateList']) && $student_copy['student_detail']['GraduateList']['id'] != "" ? $this->Format->humanize_date_short($student_copy['student_detail']['GraduateList']['graduate_date']) : 'Not Graduated') . '<br />Department: ' . (isset($student_copy['student_detail']['Department']['name']) && !empty($student_copy['student_detail']['Department']['name']) ? $student_copy['student_detail']['Department']['name'] : 'Freshman Program') . '</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    	<tr>
    		<td style="height:5px; font-size:3px">&nbsp;</td>
    	</tr>
    	<tr>
    		<td><table style="width:100%; margin:0px; padding:0px">
    				<tr>
    					<td style="border:2px solid #000000"><table style="margin:0px; padding:0px">
    							<tr>
    								<td style="border-bottom:1px solid #000000"><table style="margin:0px; padding:1px">
				 							<tr>
				 								<td style="width:30%">Name: <strong>' . $student_copy['student_detail']['Student']['first_name'] . ' ' . $student_copy['student_detail']['Student']['middle_name'] . '</strong></td>
				 								<td style="width:25%">Father\'s Name: <strong>' . $student_copy['student_detail']['Student']['middle_name'] . ' ' . $student_copy['student_detail']['Student']['last_name'] . '</strong></td>
				 								<td style="width:25%">Date of Birth: <strong>' . (!empty($student_copy['student_detail']['Student']['birthdate']) && $student_copy['student_detail']['Student']['birthdate'] != '0000-00-00' ? $this->Format->humanize_date_short_extended($student_copy['student_detail']['Student']['birthdate']) . '(G.C.)' : '') . ' </strong></td>
				 								<td style="width:20%">Admission: <strong>' . $student_copy['student_detail']['ProgramType']['name'] . '</strong></td>
				 							</tr>
				 						</table></td>
    							</tr>
    							<tr>
    								<td style="border-bottom:1px solid #000000"><table style="padding:1px">
    										<tr>
    											<td style="width:19%">ID Number: <strong>' . $student_copy['student_detail']['Student']['studentnumber'] . '</strong></td>
    											<td style="width:8%">Sex: <strong>' . (strcasecmp($student_copy['student_detail']['Student']['gender'], 'male') == 0 ? 'M' : 'F') . '</strong></td>
    											<td style="width:45%">Entry From: <strong>' .
			(!empty($student_copy['student_detail']['HighSchoolEducationBackground'])
				? $student_copy['student_detail']['HighSchoolEducationBackground'][0]['name'] .
				' (' . $student_copy['student_detail']['HighSchoolEducationBackground'][0]['town'] . ', '
				. $student_copy['student_detail']['HighSchoolEducationBackground'][0]['Region']['name'] . ')' : '') . '</strong></td>
    											<td style="width:28%">Test Date: <strong>' .
			(isset($student_copy['student_detail']['EheeceResult'])
				&& !empty($student_copy['student_detail']['EheeceResult']) ?
				'EHEECE ' .
				$this->Format->humanize_date_short_extended(
					$student_copy['student_detail']['EheeceResult'][0]['exam_year']
				) .
				' (G.C.)' : (isset($student_copy['student_detail']['EslceResult'])
					&& !empty($student_copy['student_detail']['EslceResult']) ?
					'ESLCE ' .
					$this->Format->humanize_date_short_extended(
						$student_copy['student_detail']['EslceResult'][0]['exam_year']
					) .
					' (G.C.)' : '')) . '</strong></td>
    										</tr>
    									</table></td>
    							</tr>
    							<tr>
    								<td><table>
    										<tr>
												<td style="width:50%; height:435px"><table>
													<tr>
<td><table style="padding:' . $course_justification . 'px; margin:0px; background-image:url(\'/img/sc-diagonal-line.gif\'); background-position:left top; background-attachment:scroll; background-repeat:no-repeat">
		<tr>
			<td style="width:52px; border-bottom:0px solid #000000">&nbsp;Dept.</td>
			<td style="width:41px; border-bottom:0px solid #000000">&nbsp;Co.No.</td>
			<td style="width:200px; border-bottom:0px solid #000000">&nbsp;Course Title</td>
			<td style="width:38px; border-bottom:0px solid #000000">&nbsp;' . (strcasecmp($student_copy['student_detail']['Curriculum']['type_credit'], 'Credit') == 0 ? 'Cr.Hrs.' : 'ECTS') . '</td>
			<td style="width:32px; border-bottom:0px solid #000000">&nbsp;Grade</td>
			<td style="width:42px; border-bottom:0px solid #000000">&nbsp;Gr.Pts</td>
		</tr>';
		for ($j = $i * $no_of_semester * 2; $j < (($i * $no_of_semester * 2) + $no_of_semester) && $j < count($student_copy['courses_taken']); $j++) {
			$student_copy_content .= '
			<tr>
				<td></td>
				<td></td>
				<td style="text-align:center"><strong>' . ($student_copy['courses_taken'][$j]['semester'] == 'I' ? 'First Semester' : ($student_copy['courses_taken'][$j]['semester'] == 'II' ? 'Second Semester' : 'Kiremet Semester')) . ' ' . $student_copy['courses_taken'][$j]['academic_year'] . ' Ac. Yr' . '</strong></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>';
			$semester_cr_hrs = 0;
			$semester_gr_pts = 0;
			$full_gr_pts = true;
			foreach ($student_copy['courses_taken'][$j]['courses_and_grades'] as $key => $courses_and_grade) {
				if (!isset($courses_and_grade['thesis']))
					$courses_and_grade['thesis'] = 0;
				$semester_cr_hrs += $courses_and_grade['credit'];
				if (isset($courses_and_grade['point_value']))
					$semester_gr_pts += ($courses_and_grade['used_in_gpa'] == 1 ? ($courses_and_grade['point_value'] * $courses_and_grade['credit']) : 0);
				else
					$full_gr_pts = false;
				$course_code = explode('-', $courses_and_grade['course_code']);
				$student_copy_content .= '
				<tr>
					<td style="text-align:center">' . $course_code[0] . '</td>
					<td style="text-align:center">' . $course_code[1] . '</td>
					<td>' . ($courses_and_grade['course_title']) . '</td>
					<td style="text-align:center">' . ($courses_and_grade['credit']) . '</td>
					<td  style="text-align:center; background-position:left top; background-attachment:scroll; background-repeat:no-repeat">' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] . (isset($courses_and_grade['repeated_new']) && $courses_and_grade['repeated_new'] == 1 ? '*' : ($courses_and_grade['thesis'] == 1 ? '**' : '')) : '') . '</td>
					<td style="text-align:center">' . (isset($courses_and_grade['point_value']) ? ($courses_and_grade['used_in_gpa'] == 1 ? ($courses_and_grade['point_value'] * $courses_and_grade['credit']) : '---') : (isset($courses_and_grade['grade']) && strcasecmp($courses_and_grade['grade'], 'Ex') == 0 ? '---' : '')) . '</td>
				</tr>';
			}
			$student_copy_content .= '<tr>
				<td></td>
				<td></td>
				<td style="text-align:right; font-weight:bold">TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . $semester_cr_hrs . '</td>
				<td style="border-top:1px solid #000000; border-bottom:2px solid #000000"></td>
				<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . ($full_gr_pts ? $semester_gr_pts : '') . '</td>
			</tr>';
			if (!empty($student_copy['courses_taken'][$j]['status'])) {
				$student_copy_content .= '<tr>
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
					<td style="font-weight:bold"><u style="text-align:center">' . (strcasecmp($student_copy['student_detail']['GraduationWork']['type'], 'thesis') == 0 ? 'Title of Thesis' : 'Title of Project') . '</u><br />' . ($student_copy['student_detail']['GraduationWork']['title']) . '<br /></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>';
			}
			if (($j + 1) == count($student_copy['courses_taken']) && $student_copy['student_detail']['GraduateList']['id'] != "") {
				$student_copy_content .= '<tr>
					<td></td>
					<td></td>
					<td style="font-weight:bold">Academic Status: Graduated ' . (!empty($student_copy['student_detail']['GraduationStatuse']) ? '<br /><span style="text-align:center">with ' . $student_copy['student_detail']['GraduationStatuse']['status'] . '</span>' : '') . '</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>';
			}
		}
		$student_copy_content .= '</table></td>
													</tr>
												</table></td>


												<td style="width:50%"><table style="width:100%; height:1000px">
													<tr>
<td><table style="padding:' . $course_justification . 'px; height:100%">
		<tr>
			<td style="width:13%; border-bottom:0px solid #000000">&nbsp;&nbsp;Dept.</td>
			<td style="width:10%; border-bottom:0px solid #000000">&nbsp;Co.No.</td>
			<td style="width:50%; border-bottom:0px solid #000000">&nbsp;Course Title</td>
			<td style="width:10%; border-bottom:0px solid #000000">&nbsp;' . (strcasecmp($student_copy['student_detail']['Curriculum']['type_credit'], 'Credit') == 0 ? 'Cr.Hrs.' : 'ECTS') . '</td>
			<td style="width:8%; border-bottom:0px solid #000000">&nbsp;Grade</td>
			<td style="width:10%; border-bottom:0px solid #000000">&nbsp;Gr.Pts</td>
		</tr>';
		for ($j = (($i * $no_of_semester * 2) + $no_of_semester); $j < (($i * $no_of_semester * 2) + $no_of_semester + $no_of_semester) && $j < count($student_copy['courses_taken']); $j++) {
			$student_copy_content .= '
			<tr>
				<td></td>
				<td></td>
				<td style="text-align:center"><strong>' . ($student_copy['courses_taken'][$j]['semester'] == 'I' ? 'First Semester' : ($student_copy['courses_taken'][$j]['semester'] == 'II' ? 'Second Semester' : 'Kiremet Semester')) . ' ' . $student_copy['courses_taken'][$j]['academic_year'] . ' Ac. Yr' . '</strong></td>
				<td></td>
				<td></td>
				<td></td>
			</tr>';
			$semester_cr_hrs = 0;
			$semester_gr_pts = 0;
			$full_gr_pts = true;
			foreach ($student_copy['courses_taken'][$j]['courses_and_grades'] as $key => $courses_and_grade) {
				if (!isset($courses_and_grade['thesis']))
					$courses_and_grade['thesis'] = 0;
				$semester_cr_hrs += $courses_and_grade['credit'];
				if (isset($courses_and_grade['point_value']))
					$semester_gr_pts += ($courses_and_grade['used_in_gpa'] == 1 ? ($courses_and_grade['point_value'] * $courses_and_grade['credit']) : 0);
				else
					$full_gr_pts = false;
				$course_code = explode('-', $courses_and_grade['course_code']);
				$student_copy_content .= '
				<tr>
					<td style="text-align:center">' . $course_code[0] . '</td>
					<td style="text-align:center">' . $course_code[1] . '</td>
					<td>' . ($courses_and_grade['course_title']) . '</td>
					<td style="text-align:center">' . ($courses_and_grade['credit']) . '</td>
					<td  style="text-align:center; background-position:left top; background-attachment:scroll; background-repeat:no-repeat">' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] . (isset($courses_and_grade['repeated_new']) && $courses_and_grade['repeated_new'] == 1 ? '*' : ($courses_and_grade['thesis'] == 1 ? '**' : '')) : '') . '</td>
					<td style="text-align:center">' . (isset($courses_and_grade['point_value']) ? ($courses_and_grade['used_in_gpa'] == 1 ? ($courses_and_grade['point_value'] * $courses_and_grade['credit']) : '---') : (isset($courses_and_grade['grade']) && strcasecmp($courses_and_grade['grade'], 'Ex') == 0 ? '---' : '')) . '</td>
				</tr>';
			}
			$student_copy_content .= '<tr>
				<td></td>
				<td></td>
				<td style="text-align:right; font-weight:bold">TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . $semester_cr_hrs . '</td>
				<td style="border-top:1px solid #000000; border-bottom:2px solid #000000"></td>
				<td style="border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . ($full_gr_pts ? $semester_gr_pts : '') . '</td>
			</tr>';
			if (!empty($student_copy['courses_taken'][$j]['status'])) {
				$student_copy_content .= '<tr>
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
					<td style="font-weight:bold"><u style="text-align:center">' . (strcasecmp($student_copy['student_detail']['GraduationWork']['type'], 'thesis') == 0 ? 'Title of Thesis' : 'Title of Project') . '</u><br />' . ($student_copy['student_detail']['GraduationWork']['title']) . '<br /></td>
					<td></td>
					<td></td>
					<td></td>
				</tr>';
			}
			if (($j + 1) == count($student_copy['courses_taken']) && $student_copy['student_detail']['GraduateList']['id'] != "") {
				$student_copy_content .= '<tr>
					<td></td>
					<td></td>
					<td style="font-weight:bold">Academic Status: Graduated ' . (!empty($student_copy['student_detail']['GraduationStatuse']) ? '<br /><span style="text-align:center">with ' . $student_copy['student_detail']['GraduationStatuse']['status'] . '</span>' : '') . '</td>
					<td></td>
					<td></td>
					<td></td>
				</tr>';
			}
		}
		$student_copy_content .= '</table></td>
													</tr>
												</table></td>


    										</tr>
    									</table></td>
    							</tr>
    						</table>
    					</td>
    				</tr>
    			</table>
    		</td>
    	</tr>
    </table>';
		if (!empty($student_copy['student_detail']['TranscriptFooter'])) {
			$student_copy_content .= '<span style="font-size:' . (2 * $student_copy['student_detail']['TranscriptFooter']['font_size']) . 'px">';
			if ($student_copy['student_detail']['TranscriptFooter']['line1'])
				$student_copy_content .= '&nbsp;' . $student_copy['student_detail']['TranscriptFooter']['line1'];
			if ($student_copy['student_detail']['TranscriptFooter']['line2'])
				$student_copy_content .= '<br />&nbsp;' . $student_copy['student_detail']['TranscriptFooter']['line2'];
			if ($student_copy['student_detail']['TranscriptFooter']['line3'])
				$student_copy_content .= '<br />&nbsp;' . $student_copy['student_detail']['TranscriptFooter']['line3'];
			$student_copy_content .= '</span>';
		}
		$student_copy_content .= '
	<br /><table style="width:100%; padding-top:10px">
    	<tr>
    		<td style="width:50%">Date of Issue: <u>' . date('Y-m-d') . '</u></td>
    		<td style="width:50%">Registrar: <u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</u></td>
    	</tr>
    </table>
    ';
		$pdf->writeHTML($student_copy_content);
	}
	// reset pointer to the last page
	$pdf->lastPage();
}
//output the PDF to the browser

$pdf->Output('Student Copy.pdf', 'I');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */