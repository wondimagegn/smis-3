<?php
//debug($student_copy);
App::import('Vendor', 'tcpdf/tcpdf');
// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true);

//show header or footer
$this->Pdf->SetPrintHeader(false);
$this->Pdf->SetPrintFooter(false);
//$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'
// set default header data
/*
    $this->Pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $this->Pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $this->Pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    //set margins

    $this->Pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //$this->Pdf->SetMargins(15,15,15);
    $this->Pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $this->Pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //set auto page breaks
    $this->Pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $this->Pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    */
// set font
$this->Pdf->SetMargins(5, 3, 5);
$this->Pdf->SetFont("freeserif", "", 11);

$this->Pdf->setPageOrientation('L', true, 0);

for ($i = 0; ($i * $no_of_semester * 2) < count($student_copy['courses_taken']); $i++) {
	// add a page
	$this->Pdf->AddPage("L");
	//$this->Pdf->SetLineWidth(0.1);
	//$this->Pdf->Cell(100, 20, 'Hello PDF', 1);

	$creditType = 'ECTS';
	if (strcasecmp($student_copy['student_detail']['Curriculum']['type_credit'], 'Credit') == 0) {
		$creditType = 'Cr.Hrs.';
	} else if (empty($student_copy['student_detail']['Student']['department_id'])) {
		$creditType = 'Cr.Hrs.';
	}
	$student_copy_content = '
    <table style="width:100%; font-size:' . $font_size . 'px">
    	<tr>
    		<td style="text-align:center; font-weight:bold">' . (!empty($student_copy['student_detail']['University']) ? strtoupper($student_copy['student_detail']['University']['name']) : 'ARBA MINCH UNIVERSITY') . '</td>
    	</tr>
    	<tr>
    		<td>
    			<table style="width:100%">
    				<tr>
    					<td style="text-align:left">OFFICE OF THE REGISTRAR</td>
    					<td style="text-align:center">STUDENT ACADAMIC RECORD</td>
    					<td style="text-align:right">Certificate: ' . (!empty($student_copy['student_detail']['Curriculum']) ? $student_copy['student_detail']['Curriculum']['certificate_name'] : '') . '</td>
    				</tr>
    				<tr>
    					<td style="text-align:left">P.O.Box: 21 Arba Minch, Ethiopia</td>
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
				 								<td style="width:25%">Date of Birth: <strong>' . $this->Format->humanize_date_short_extended($student_copy['student_detail']['Student']['birthdate']) . ' (G.C.)</strong></td>
				 								<td style="width:20%">Admission: <strong>' . $student_copy['student_detail']['ProgramType']['name'] . '</strong></td>
				 							</tr>
				 						</table></td>
    							</tr>
    							<tr>
    								<td style="border-bottom:1px solid #000000"><table style="padding:1px">
    										<tr>
    											<td style="width:19%">ID Number: <strong>' . $student_copy['student_detail']['Student']['studentnumber'] . '</strong></td>
    											<td style="width:8%">Sex: <strong>' . (strcasecmp($student_copy['student_detail']['Student']['gender'], 'male') == 0 ? 'M' : 'F') . '</strong></td>
    											<td style="width:45%">Entry From: <strong>' . (!empty($student_copy['student_detail']['HighSchoolEducationBackground']) ? $student_copy['student_detail']['HighSchoolEducationBackground'][0]['name'] . ' (' . $student_copy['student_detail']['HighSchoolEducationBackground'][0]['town'] . ', ' . $student_copy['student_detail']['HighSchoolEducationBackground'][0]['Region']['name'] . ')' : '') . '</strong></td>
    											<td style="width:28%">Test Date: <strong>' . (isset($student_copy['student_detail']['EheeceResult']) && !empty($student_copy['student_detail']['EheeceResult']) ? 'EHEECE ' . $this->Format->humanize_date_short_extended($student_copy['student_detail']['EheeceResult'][0]['exam_year']) . ' (G.C.)' : (isset($student_copy['student_detail']['EslceResult']) && !empty($student_copy['student_detail']['EslceResult']) ? 'ESLCE ' . $this->Format->humanize_date_short_extended($student_copy['student_detail']['EslceResult'][0]['exam_year']) . ' (G.C.)' : '')) . '</strong></td>
    										</tr>
    									</table></td>
    							</tr>
    							<tr>
    								<td><table>
    										<tr>
												<td style="width:50%"><table>
													<tr>
<td><table style="padding:' . $course_justification . 'px; margin:0px">
		<tr>
			<td style="width:13%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;Dept.</td>
			<td style="width:9%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;Co.No.</td>
			<td style="width:50%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;Course Title</td>
			<td style="width:10%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;' . $creditType . ' </td>
			<td style="width:8%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;Grade</td>
			<td style="width:10%; border-bottom:0px solid #000000; border-right:2px solid #000000">&nbsp;Gr.Pts</td>
		</tr>';
	for ($j = $i * $no_of_semester * 2; $j < (($i * $no_of_semester * 2) + $no_of_semester) && $j < count($student_copy['courses_taken']); $j++) {
		$student_copy_content .= '
			<tr>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000; text-align:center"><strong>' . ($student_copy['courses_taken'][$j]['semester'] == 'I' ? 'First Semester' : ($student_copy['courses_taken'][$j]['semester'] == 'II' ? 'Second Semester' : 'Kiremet Semester')) . ' ' . $student_copy['courses_taken'][$j]['academic_year'] . ' Ac. Yr' . '</strong></td>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:2px solid #000000"></td>
			</tr>';
		$semester_cr_hrs = 0;
		$semester_gr_pts = 0;
		$full_gr_pts = true;
		foreach ($student_copy['courses_taken'][$j]['courses_and_grades'] as $key => $courses_and_grade) {
			$semester_cr_hrs += $courses_and_grade['credit'];
			if (isset($courses_and_grade['point_value']))
				$semester_gr_pts += ($courses_and_grade['used_in_gpa'] == 1 ? $courses_and_grade['point_value'] : 0);
			else
				$full_gr_pts = false;
			$student_copy_content .= '
				<tr>
					<td style="border-right:1px solid #000000; text-align:center">' . strtoupper(substr($courses_and_grade['course_code'], 0, strpos($courses_and_grade['course_code'], '-'))) . '</td>
					<td style="border-right:1px solid #000000; text-align:center">' . (substr($courses_and_grade['course_code'], - (strpos($courses_and_grade['course_code'], '-') - 1))) . '</td>
					<td style="border-right:1px solid #000000"> ' . ($courses_and_grade['course_title']) . '</td>
					<td style="border-right:1px solid #000000; text-align:center">' . ($courses_and_grade['credit']) . '</td>
					<td style="border-right:1px solid #000000; text-align:center">' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] : '**') . '</td>
					<td style="border-right:2px solid #000000; text-align:center">' . (isset($courses_and_grade['point_value']) ? ($courses_and_grade['used_in_gpa'] == 1 ? $courses_and_grade['point_value'] : '---') : '') . '</td>
				</tr>';
		}
		$student_copy_content .= '<tr>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000; text-align:right; font-weight:bold">TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td style="border-right:1px solid #000000; border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . $semester_cr_hrs . '</td>
				<td style="border-right:1px solid #000000; border-top:1px solid #000000; border-bottom:2px solid #000000"></td>
				<td style="border-right:2px solid #000000; border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . ($full_gr_pts ? $semester_gr_pts : '') . '</td>
			</tr>';
		if (!empty($student_copy['courses_taken'][$j]['status'])) {
			$student_copy_content .= '<tr>
					<td style="border-right:1px solid #000000"></td>
					<td style="border-right:1px solid #000000"></td>
					<td style="border-right:1px solid #000000; font-weight:bold"> Semester Average: ' . (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['StudentExamStatus']['sgpa'] : '') . '' . ($j > 0 ? (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['StudentExamStatus']['cgpa'] . '<br />' : '') : '') . '<br />&nbsp;Academic Status: ' . (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['AcademicStatus']['name'] : '') . '</td>
					<td style="border-right:1px solid #000000;"></td>
					<td style="border-right:1px solid #000000;"></td>
					<td style="border-right:2px solid #000000;"></td>
				</tr>';
		}
	}
	$student_copy_content .= '</table></td>
													</tr>
												</table></td>


												<td style="width:50%"><table style="width:100%; height:1000px; border-bottom:2px solid #000000">
													<tr>
<td><table style="padding:' . $course_justification . 'px; height:100%">
		<tr>
			<td style="width:13%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;Dept.</td>
			<td style="width:9%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;Co.No.</td>
			<td style="width:50%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;Course Title</td>
			<td style="width:10%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;' . $creditType . '</td>
			<td style="width:8%; border-bottom:0px solid #000000; border-right:1px solid #000000">&nbsp;Grade</td>
			<td style="width:10%; border-bottom:0px solid #000000; border-right:2px solid #000000">&nbsp;Gr.Pts</td>
		</tr>';
	for ($j = (($i * $no_of_semester * 2) + $no_of_semester); $j < (($i * $no_of_semester * 2) + $no_of_semester + $no_of_semester) && $j < count($student_copy['courses_taken']); $j++) {
		$student_copy_content .= '
			<tr>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000; text-align:center"><strong>' . ($student_copy['courses_taken'][$j]['semester'] == 'I' ? 'First Semester' : ($student_copy['courses_taken'][$j]['semester'] == 'II' ? 'Second Semester' : 'Kiremet Semester')) . ' ' . $student_copy['courses_taken'][$j]['academic_year'] . ' Ac. Yr' . '</strong></td>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:2px solid #000000"></td>
			</tr>';
		$semester_cr_hrs = 0;
		$semester_gr_pts = 0;
		$full_gr_pts = true;
		foreach ($student_copy['courses_taken'][$j]['courses_and_grades'] as $key => $courses_and_grade) {
			$semester_cr_hrs += $courses_and_grade['credit'];
			if (isset($courses_and_grade['point_value']))
				$semester_gr_pts += ($courses_and_grade['used_in_gpa'] == 1 ? $courses_and_grade['point_value'] : 0);
			else
				$full_gr_pts = false;
			$student_copy_content .= '
				<tr>
					<td style="border-right:1px solid #000000; text-align:center">' . strtoupper(substr($courses_and_grade['course_code'], 0, strpos($courses_and_grade['course_code'], '-'))) . '</td>
					<td style="border-right:1px solid #000000; text-align:center">' . (substr($courses_and_grade['course_code'], - (strpos($courses_and_grade['course_code'], '-') - 1))) . '</td>
					<td style="border-right:1px solid #000000"> ' . ($courses_and_grade['course_title']) . '</td>
					<td style="border-right:1px solid #000000; text-align:center">' . ($courses_and_grade['credit']) . '</td>
					<td style="border-right:1px solid #000000; text-align:center">' . (isset($courses_and_grade['grade']) ? $courses_and_grade['grade'] : '**') . '</td>
					<td style="border-right:2px solid #000000; text-align:center">' . (isset($courses_and_grade['point_value']) ? ($courses_and_grade['used_in_gpa'] == 1 ? $courses_and_grade['point_value'] : '---') : '') . '</td>
				</tr>';
		}
		$student_copy_content .= '<tr>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000"></td>
				<td style="border-right:1px solid #000000; text-align:right; font-weight:bold">TOTAL&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td style="border-right:1px solid #000000; border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . $semester_cr_hrs . '</td>
				<td style="border-right:1px solid #000000; border-top:1px solid #000000; border-bottom:2px solid #000000"></td>
				<td style="border-right:2px solid #000000; border-top:1px solid #000000; border-bottom:2px solid #000000; text-align:center; font-weight:bold">' . ($full_gr_pts ? $semester_gr_pts : '') . '</td>
			</tr>';
		if (!empty($student_copy['courses_taken'][$j]['status'])) {
			$student_copy_content .= '<tr>
					<td style="border-right:1px solid #000000"></td>
					<td style="border-right:1px solid #000000"></td>
					<td style="border-right:1px solid #000000; font-weight:bold"> Semester Average: ' . (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['StudentExamStatus']['sgpa'] : '') . '' . ($j > 0 ? (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['StudentExamStatus']['cgpa'] . '<br />' : '') : '') . '<br />&nbsp;Academic Status: ' . (!empty($student_copy['courses_taken'][$j]['status']) ? $student_copy['courses_taken'][$j]['status']['AcademicStatus']['name'] : '') . '</td>
					<td style="border-right:1px solid #000000;"></td>
					<td style="border-right:1px solid #000000;"></td>
					<td style="border-right:2px solid #000000;"></td>
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
	$student_copy_content .= '<br /><table style="width:100%; padding-top:10px">
    	<tr>
    		<td style="width:50%">Date of Issue: <u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</u></td>
    		<td style="width:50%">Registrar: <u>&nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp; &nbsp;</u></td>
    	</tr>
    </table>
    ';
	$this->Pdf->writeHTML($student_copy_content);
}
// reset pointer to the last page
$this->Pdf->lastPage();

//output the PDF to the browser

$this->Pdf->Output('Student Copy - ' . $student_copy['student_detail']['Student']['first_name'] . ' ' . $student_copy['student_detail']['Student']['middle_name'] . ' ' . $student_copy['student_detail']['Student']['last_name'] . '.pdf', 'I');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */