<?php
App::import('Vendor','tcpdf/tcpdf');
 $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false); 
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

    $header = '<table style="width:100%;">
    	<tr>
    		<td style="text-align:center; font-weight:bold">ARBA MINCH UNIVERSITY</td>
    	</tr>
<tr>
    		<td style="text-align:center; font-weight:bold">OFFICE OF THE REGISTRAR</td>
    	</tr>
<tr>
    		<td style="text-align:center; font-weight:bold;text-decoration:underline;">GRADUATING CLASS NAME CHECK LIST</td>
    	</tr>
    	</table>';
  
   $count = 1;
   $pdf->AddPage("L");
   $nameLists=null;
  
   foreach($students_for_name_list_pdf as $c_id => $students) 
   {

	 $department_and_admission_year = explode('~',$c_id); 
	
        // add a page
        // $pdf->AddPage("L");
         $nameLists .= $header;
	 $nameLists .= '<table class="fs13 summery">
	       <tr>
			<td style="width:22%">Department</td>
			<td style="width:78%; font-weight:bold">'.$department_and_admission_year[0].'</td>
		</tr>
		 <tr>
			<td style="width:22%">Program</td>
			<td style="width:78%; font-weight:bold">'.$department_and_admission_year[1].'</td>
		</tr>

		 <tr>
			<td style="width:22%">Program Type</td>
			<td style="width:78%; font-weight:bold">'.$department_and_admission_year[2].'</td>
		</tr>
		
		<tr>
			<td style="width:22%">Admission Year</td>
			<td style="width:78%; font-weight:bold">'.$department_and_admission_year[3].'</td>
		</tr>

		
         </table>';
          $nameLists.= '<br/><table cellpadding="1" style="padding-left:2px;text-align:left;" >
	<tr>
		<td style="width:3%;border:1px solid #000000; border-bottom:2px solid #000000;text-align:center"></td>
		<td style="width:25%; border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">ID</td>
		<td style="width:30%;border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">English</td>
		<td style="width:30%; border:1px solid #000000; border-bottom:2px solid #000000;text-align:center">Amharic</td>
	</tr>';
        foreach($students as $k=>$student) {
          $nameLists.='
		<tr><td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center; " rowspan=2>'.$count++.'</td>

		<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:center; " rowspan=2>'.$student['Student']['studentnumber'].'</td>
		<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">
		<table  cellpadding="1" style="padding-left:2px;text-align:left;">
		 <tr>
		   <td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">First Name</td>
		   <td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">'.$student['Student']['first_name'].'</td>
		</tr>                     
		 <tr>
			<td  style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">Middle Name</td>
			<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">'.$student['Student']['middle_name'].'</td>
		 </tr>
	          <tr>
			<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">Last Name</td>
			<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">'.$student['Student']['last_name'].'</td>
		 </tr>
		 </table>
		</td>

		<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">
		<table  cellpadding="1" style="padding-left:3px;text-align:left;">
		 <tr>
		   <td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">First Name</td>
		   <td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">'.$student['Student']['amharic_first_name'].'</td>
		</tr>                     
		 <tr>
			<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">Middle Name</td>
			<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">'.$student['Student']['amharic_middle_name'].'</td>
		 </tr>
	          <tr>
			<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">Last Name</td>
			<td style="border:1px solid #000000; border-bottom:2px solid #000000;text-align:left; ">'.$student['Student']['amharic_last_name'].'</td>
		 </tr>
		 </table>
		</td>
		</tr>';
           }
	  $nameLists .= '</table>';
      $pdf->writeHTML($nameLists);
   }
   	
   // reset pointer to the last page
   $pdf->lastPage();	
      
     
     	
    //output the PDF to the browser

    $pdf->Output('NameListReport.'.date('Y').'.pdf', 'I');

    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?>
