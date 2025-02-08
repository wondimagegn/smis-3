<?php
App::import('Vendor','tcpdf/tcpdf');
// create new PDF document
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);
	 
    //show header or footer
    $pdf->SetPrintHeader(false); 
    $pdf->SetPrintFooter(false);
    //SetMargins(Left, Top, Right)
    $pdf->SetMargins(10, 10, 10);
    //$pdf->SetTopMargin(10);
    //Font Family, Style, Size
    //$pdf->SetFont("pdfacourier", "", 11);
    $pdf->setPageOrientation('L', true, 0);

    // add a page - landscape style
    $pdf->AddPage("L");

   //Get Section name 
    foreach ($studentsections as $studentsection):
        $section_name=$studentsection['Section']['name'];
    endforeach;
    // set font
    $pdf->SetFont("freeserif", "", 11);
 
$html = <<<EOF
<!-- EXAMPLE OF CSS STYLE -->
<style>
div.centeralignment {
    text-align:center;
}
</style>
<div class="centeralignment"><h1>$collegename<br/>Department:&nbsp;$department_name<br/>Section:&nbsp;$section_name</h1></div>

EOF;
    $pdf->writeHTML($html, true, 0, true, 0,'');
/*
    /////////////////////////////////////////////
    //        START TABLE HEADER
    /////////////////////////////////////////////
        
    //Colors, line width and bold font for the header
    $pdf->SetFillColor(11, 47, 132); //background color of next Cell
    $pdf->SetTextColor(255); //font color of next cell
    $pdf->SetFont('','B'); //b for bold
    $pdf->SetDrawColor(0); //cell borders - similiar to border color
    $pdf->SetLineWidth(.3); //similiar to cellspacing
    
    $cols=array('No','Students Identification','Students name');//Column titles 
    $width=array(20,50,70); //amount of elements must correspond with $header array above
    
    $pdf->writeHTML("Section: ");
    
    //$pdf->Ln(); //new row
    for($i=0;$i<count($cols);$i++)
    {
        //void Cell( float $w, [float $h = 0], [string $txt = ''], [mixed $border = 0], 
        //[int $ln = 0], [string $align = ''], [int $fill = 0], [mixed $link = ''], [int $stretch = 0]) 
        $pdf->Cell($width[$i],7,$cols[$i],1,0,'C',1); 
        
        //int MultiCell( float $w, float $h, string $txt, [mixed $border = 0], [string $align = 'J'], 
        //[int $fill = 0], [int $ln = 1], [int $x = ''], [int $y = ''], [boolean $reseth = true], 
        //[int $stretch = 0], [boolean $ishtml = false]) 
        //$pdf->MultiCell($width[$i],7,$cols[$i],1,'C',1,0,'','',1,0,1); 
        
        //$maxnocells = 0;
        //$cellcount = 0;
       //write text first
        //$startX = $pdf->GetX();
        //$startY = $pdf->GetY();
        //draw cells and record maximum cellcount
        //$cellcount = $pdf->MultiCell($width[$i],7,$cols[$i],1,'C',1,0,'','',1,0,1);
        //if ($cellcount > $maxnocells ) {$maxnocells = $cellcount + 1;}
        //$pdf->SetXY($startX,$startY);
     
        //now do borders and fill
        //cell height is 7 times the max number of cells
        //$pdf->MultiCell(80,$maxnocells * 6,'','LR','L',0,0);
        //$maxnocells =$maxnocells+1;
        //$pdf->MultiCell($width[$i],$maxnocells *7 ,'',1,'C',1,0,'','',1,0,1);  
    }
    
    $pdf->Ln(); //new row
    
    ////////////////////////////////////////////////
    //        START TABLE BODY
    ////////////////////////////////////////////////

    //styling for normal non header cells
    $pdf->SetTextColor(0); //black
    $pdf->SetFont('',''); 

    //the data - normally would come from DB, web service etc. 
    
    $no = array();
    $studentnumber = array();
    $full_name = array();

    foreach($studentsections as $studentsection){ 
        $students_per_section=count($studentsection['Student']);
        for($i=0;$i<$students_per_section;$i++) {
            $no[] = ($i+1);
            $studentnumber[] = $studentsection['Student'][$i]['studentnumber'];
            $full_name[] = $studentsection['Student'][$i]['full_name'];
        }
    }
    //create & populate table cells
    for($i = 0; $i < count($no); $i++)
    {
            $pdf->SetFillColor(255); //white
        
        //int MultiCell( float $w, float $h, string $txt, [mixed $border = 0], [string $align = 'J'], 
        //[int $fill = 0], [int $ln = 1], [int $x = ''], [int $y = ''], [boolean $reseth = true], 
        //[int $stretch = 0], [boolean $ishtml = false]) 
        
        $pdf->MultiCell($width[0],7,$no[$i],1,'C',1,0,'','',1,0,1); 
        $pdf->MultiCell($width[1],7,$studentnumber[$i],1,'C',1,0,'','',1,0,1); 
        $pdf->MultiCell($width[2],7,$full_name[$i],1,'C',1,0,'','',1,0,1);
        
        $pdf->Ln(); //new row
    }
*/
 if(!empty($studentsections)){			
            $tbl = '<table style="width: 800px;" cellspacing="0">';
            $tbl .= '<tr><th style="border: 1px solid #000000; width: 30px;font-size:40px;font-weight:bold;">No</th>
					<th style="border: 1px solid #000000; width: 170px;font-size:40px;font-weight:bold;">ID</th>
					<th style="border: 1px solid #000000; width: 300px;font-size:40px;font-weight:bold;">Name</th></tr>';
			$count=1;
	$no = array();
    $studentnumber = array();
    $full_name = array();

    foreach($studentsections as $studentsection){ 
        $students_per_section=count($studentsection['Student']);
        for($i=0;$i<$students_per_section;$i++) {
            $no[] = ($i+1);
            $studentnumber[] = $studentsection['Student'][$i]['studentnumber'];
            $full_name[] = $studentsection['Student'][$i]['full_name'];
        }
    }
		    for($i = 0; $i < count($no); $i++){ 
				$tbl .= '
				<tr>
					<td style="border: 1px solid #000000; width: 30px;">'.$no[$i].'</td>
					<td style="border: 1px solid #000000; width: 170px;">'.$studentnumber[$i].'</td>
					<td style="border: 1px solid #000000; width: 300px;">'.$full_name[$i].'</td>
				</tr>';
			}
			  $tbl .= '</table>';
			  $pdf->writeHTML($tbl, true, false, false, false, '');
	}
	
    // reset pointer to the last page
    $pdf->lastPage();
    //output the PDF to the browser

    $pdf->Output($section_name.'.pdf', 'D');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?> 
