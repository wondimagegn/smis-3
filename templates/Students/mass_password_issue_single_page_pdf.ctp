<?php
App::import('Vendor', 'tcpdf/tcpdf');
	// create new PDF document
	$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

	//show header or footer
	$pdf->SetPrintHeader(false);
	$pdf->SetPrintFooter(false);
	//SetMargins(Left, Top, Right)
	$pdf->SetMargins(6, 6, 6);
	//$pdf->SetTopMargin(10);
	//Font Family, Style, Size
	//$pdf->SetFont("pdfacourier", "", 11);
	//$pdf->setPageOrientation('P', true, 0);


	if (isset($student_passwords) && count($student_passwords) > 0) {

		$pdf->AddPage("P");

		$header = '
		<table cellspacing="2">
			<tr>
				<td style="text-align:center; font-weight:bold">' . (strtoupper(Configure::read('CompanyName'))). '</td>
			</tr>
			<tr>
				<td style="text-align:center; font-weight:bold">'. $student_passwords[0]['College']['name'] . '</td>
			</tr>
			<tr>
				<td style="text-align:center; font-weight:bold">'.(isset($student_passwords[0]['Department']['type'])  && !empty($student_passwords[0]['Department']['type']) ? $student_passwords[0]['Department']['type'] . ' of ' : '') .''. (!empty($student_passwords[0]['Department']['name']) ? $student_passwords[0]['Department']['name'] : ($student_passwords[0]['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman Program')) . '</td>
			</tr>
			<tr>
				<td style="text-align:center; font-weight:bold;text-decoration:underline;">Password Issue/Reset</td>
			</tr>
			<tr>
				<td>&nbsp;</td>
			</tr>
		</table>';

		//debug($student_passwords[0]);

		$count = 1;
		$nameLists = null;
		$nameLists .= $header;
		$i = 1;
		$numbercount = count($student_passwords);
		$count = 0;

		$nameLists .= '
		<table>';
			foreach ($student_passwords as $c_id => $student) {
				$count++;
				if ($i == 1) {
					$nameLists .= '<tr>';
					$nameLists .= '
					<td style="border:1px solid #000000;">
						<table cellpadding="2.5" cellspacing="1">
							<tr>
								<td>'.(isset($student['College']['type']) && !empty($student['College']['type']) ? $student['College']['type'] : 'College').': ' . $student['College']['name'] . '</td>
							</tr>
							<tr>
								<td>'.(isset($student['Department']['type'])  && !empty($student['Department']['type']) ? $student['Department']['type'] : 'Department').': ' . (!empty($student['Department']['name']) ? $student['Department']['name'] : ($student['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')) . '</td>
							</tr>
							<tr>
								<td>Full Name: ' . $student['Student']['full_name'] . '</td>
							</tr>	
							<tr>
								<td>Username/StudentID: ' . $student['Student']['studentnumber'] . '</td>
							</tr>		  
							<tr>
								<td>Temporary Password: ' . $student['Student']['password_flat'] . '</td>
							</tr>
							<tr>
								<td>Portal: ' . PORTAL_URL_HTTPS . '</td>
							</tr>
						</table>
					</td>';

					$i++;
					
				} else {
					$nameLists .= '
					<td style="border:1px solid #000000;">
						<table cellpadding="2.5" cellspacing="1">
							<tr>
								<td>'.(isset($student['College']['type']) && !empty($student['College']['type']) ? $student['College']['type'] : 'College').': ' . $student['College']['name'] . '</td>
							</tr>
							<tr>
								<td>'.(isset($student['Department']['type'])  && !empty($student['Department']['type']) ? $student['Department']['type'] : 'Department').': ' . (!empty($student['Department']['name']) ? $student['Department']['name'] : ($student['Student']['program_id'] == PROGRAM_REMEDIAL ? 'Remedial Program' : 'Pre/Freshman')) . '</td>
							</tr>
							<tr>
								<td>Full Name: ' . $student['Student']['full_name'] . '</td>
							</tr>	
							<tr>
								<td>Username/StudentID: ' . $student['Student']['studentnumber'] . '</td>
							</tr>		  
							<tr>
								<td>Password: ' . $student['Student']['password_flat'] . '</td>
							</tr>
							<tr>
								<td>Portal: ' . PORTAL_URL_HTTPS . '</td>
							</tr>
						</table>
					</td>';

					$i++;
				}

				if ($i == 3 || $numbercount == $count) {
					$nameLists .= '</tr>';
					$i = 1;
				}

			}

			$nameLists .= '
		</table>';

		$pdf->writeHTML($nameLists);

		// reset pointer to the last page
		$pdf->lastPage();
		//output the PDF to the browser

		$pdf->Output('Mass_Password_Issue_Reset_'. (!empty($student_passwords[0]['Department']['name']) ? $student_passwords[0]['Department']['name'] : ($student_passwords[0]['Student']['program_id'] == PROGRAM_REMEDIAL ? $student_passwords[0]['College']['name'] . '_Remedial_Program' : $student_passwords[0]['College']['name'] . '_Pre_Freshman_Program')) . (isset($section_for_file_name) ? '_' . $section_for_file_name : '') .'_'. date('Y-m-d') . '.pdf', 'I');
	}


	/*
	I: send the file inline to the browser.
	D: send to the browser and force a file download with the name given by name.
	F: save to a local file with the name given by name.
	S: return the document as a string.
	*/ 