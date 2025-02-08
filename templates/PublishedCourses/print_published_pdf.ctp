<?php
    App::import('Vendor','tcpdf/tcpdf');
   // create new PDF document
    $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true); 

   
    //set margins
    
    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //$pdf->SetMargins(15,15,15);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);
    
    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);

    
    // add a page
    $pdf->AddPage();
    if (!empty($publishedCourses)) {
       foreach($publishedCourses as $sk=>$sv){
                              
                      $pdf->writeHTML('<div style="width:700px;text-align:left;font-size:70px">Courses published for registration of semester '.$sk.' of '.$selected_academic_year.' academic year. </div>', true, 0, true, 0,'');
                   
                    foreach ($sv as $pk => $pv) {
                        if (!empty($pk)) {
                                //echo "<div class='smallheading'> Program:".$pk."</div>";
                                $pdf->writeHTML('<div style="width:700px;text-align:left;font-size:30px;font-weight:bold">
                                Program: '.$pk.'</div>', true, 0, true, 0,'');
                           foreach ($pv as $ptk=>$ptv) {
                           
                             if (!empty($ptk)) {
                                     //echo "<div class='smallheading'> Program Type: ".$ptk."</div>";
                                  $pdf->writeHTML('<div style="width:700px;text-align:left;font-size:30px;font-weight:bold">
                                Program Type: '.$ptk.'</div>', true, 0, true, 0,'');
                                  foreach ($ptv as $yk=>$yv) {
                                      if (!empty($yv)) {
                                        // echo "<div class='smallheading'> Year Level: ".$yk."</div>";
                                        $pdf->writeHTML('<div style="width:700px;text-align:left;font-size:30px;font-weight:bold">
                                Year Level : '.$yk.'</div>', true, 0, true, 0,'');
                                         foreach ($yv as $section_name=>$section_value) {
                                            //echo "<div class='smallheading'> Section : ".$section_name."</div>";
                                            $pdf->writeHTML('<div style="text-align:left;
                                            font-size:30px;font-weight:bold">
                                Section : '.$section_name.'</div><br/>', true, 0, true, 0,'');
                                           $count=1;
                                           $course_by_section = '<table margin-right="20px" cellspacing="0" cellpadding="2px">';
                                           $course_by_section .= '<tr><th style="border: 1px solid #000000; width:50px;font-weight:bold;valign:bottom">No.</th><th style="border: 1px solid #000000; width: 200px;font-weight:bold;">Course Title</th><th style="border: 1px solid #000000; width: 70px;font-weight:bold;">Course Code</th><th style="border: 1px solid #000000; width: 50px;font-weight:bold;">Credit</th><th style="border: 1px solid #000000; width: 70px;font-weight:bold;">L T L</th></tr>';
                                          
                                           foreach ($section_value as $type_index=>$section_value_detail) {
                                             $course_by_section .= '<tr><td style="border: 1px solid #000000; width: 440px;">'.$type_index.'</td></tr>';
                                           foreach ($section_value_detail as $publishedCourse) {
                                          
                
                                           $course_by_section .= '
                            <tr>
                                 <td style="border: 1px solid #000000; width: 50px;">'.$count++.'</td>
                                <td style="border: 1px solid #000000; width: 200px;">'.$publishedCourse['Course']['course_title'].'</td>
                                 <td style="border: 1px solid #000000; width: 70px;">'.$publishedCourse['Course']['course_code'].'</td>
                       <td style="border: 1px solid #000000; width: 50px;">'.$publishedCourse['Course']['credit'].'</td>         
                           <td style="border: 1px solid #000000; width: 70px;">'.$publishedCourse['Course']['course_detail_hours'].'</td>              
                            </tr>
                        ';
                                          } // course section end 
                                        }
                                        $course_by_section .= '</table>';
                                        $pdf->writeHTML($course_by_section, true, false, false, false, '');
                                        $course_by_section=null;
                                       } // end year level
                                     } 
                               }
                        
                          }
                       }
                    }
             }   
       }
    }   
    // reset pointer to the last page
    $pdf->lastPage();

    //output the PDF to the browser

    $pdf->Output('Published Coures of -'.$selected_academic_year.'.pdf', 'D');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */
?> 
