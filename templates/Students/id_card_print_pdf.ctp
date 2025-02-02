<?php
     App::import('Vendor', 'tcpdf/tcpdf');
     $pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

     $pdf->SetPrintHeader(false);
     $pdf->SetPrintFooter(false);
     $profilePicture = $_SERVER['DOCUMENT_ROOT'] . '/app/webroot/img/noimage.jpg';
     $defaultProfile = $_SERVER['DOCUMENT_ROOT'] . '/app/webroot/img/Portrait_placeholder.jpg';

     $logoPath = $_SERVER['DOCUMENT_ROOT'] . '/app/webroot/img/temporary-id-logo.jpg';

     if (!empty($studentsList) && count($studentsList) > 0) {
          foreach ($studentsList as $k => $student_detail) {
               $pdf->SetMargins(0, 10, 0);

               $pdf->setPageOrientation('L', true, 0);
               $pdf->AddPage("L");
               //$pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));

               //Image processing
              /*  if (strcasecmp($student_detail['University']['Attachment']['0']['group'], 'logo') == 0) {
                    $logo_index = 0;
                    $bg_index = 1;
               } else {
                    $logo_index = 1;
                    $bg_index = 0;
               }
               
               $logo_path = $this->Media->file($student_detail['University']['Attachment'][$logo_index]['dirname'] . DS . $student_detail['University']['Attachment'][$logo_index]['basename']);
               $logo_mime = $this->Media->mimeType($student_detail['University']['Attachment'][$logo_index]['dirname'] . DS . $student_detail['University']['Attachment'][$logo_index]['basename']);
               $logo_mime = explode('/', $logo_mime);
               $logo_mime = strtoupper($logo_mime[1]);
               $bg_path = $this->Media->file($student_detail['University']['Attachment'][$bg_index]['dirname'] . DS . $student_detail['University']['Attachment'][$bg_index]['basename']);

               $bg_mime = $this->Media->mimeType($student_detail['University']['Attachment'][$bg_index]['dirname'] . DS . $student_detail['University']['Attachment'][$bg_index]['basename']);

               $bg_mime = explode('/', $bg_mime);
               $bg_mime = strtoupper($bg_mime[1]); */

               $pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/img/border-background-images-id-card.gif', 3, 3, 580, 400, $bg_mime, '', '', false, 600, 'C', false, false, 0);

               //$pdf->Image($bg_path, 0, 15, 180, 180, $bg_mime, '', '', false, 300, 'C', false, false, 0);
               $pdf->Image($_SERVER['DOCUMENT_ROOT'] . UNIVERSITY_FULL_PAGE_TRANSPARENT_LOGO_FOR_TCPDF, 0, 15, 180, 180, '', '', '', false, 300, 'C', false, false, 0);
               //$pdf->Image($logo_path, 0, '13', 30, 14, $logo_mime, '', 'N', true, 100, 'C');

               $pdf->SetXY(5, 10);
               // $pdf->Image($logo_path, '', '', 75, 60, '', '', 'T', false, 300, '', false, false, 1, false, false, false); 

               if (file_exists($logoPath)) {
                    //$pdf->Image($logoPath, 0, '13', 30, 14, '', '', 'N', true,100, 'C');
                    // $pdf->Image($logoPath, 0, '13', 30, 14, $logo_mime, '', 'N', true, 100, 'C');
                   // $pdf->Image($logo_path, '', '', 75, 60, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
                    $pdf->Image($_SERVER['DOCUMENT_ROOT']. UNIVERSITY_LOGO_HEADER_FOR_TCPDF, '', '', 45, 45, '', '', 'T', false, 300, '', false, false, 0, false, false, false);
               }

               //$pdf->MultiCell(107, 7, strtoupper($student_detail['University']['amharic_name']), 0, 'L', false, 0, 30, 14);

               $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/jiret.ttf');
               $pdf->SetFont($fontPath, '', 40, '', true);

               $pdf->MultiCell(400, 10, strtoupper($student_detail['University']['University']['name']), 0, 'L', false, 0, 105, 30);

               $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/jiret.ttf');
               $pdf->SetFont($fontPath, '', 65, '', true);

               $pdf->MultiCell(400, 10, strtoupper($student_detail['University']['University']['amharic_name']), 0, 'L', false, 0, 105, 10);
               $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/jiret.ttf');
               $pdf->SetFont($fontPath, '', 15, '', true);

               $pdf->MultiCell(107, 7, "Student ID Card", 0, 'L', false, 0, 230, 55);
               $pdf->MultiCell(107, 7, "_______________", 0, 'L', false, 0, 230, 57);

               //$pdf->setCellMargins(0, 5,0, 0);
               //$pdf->SetMargins(0,12); 
               //$pdf->SetTopMargin(30);
               //$pdf->writeHTML($certificate_content);

               $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/jiret.ttf');
               $pdf->SetFont($fontPath, '', 15, '', true);
               $pdf->SetXY(20, 80);

               if (isset($student_detail['Student']['Attachment'])) {
                    foreach ($student_detail['Student']['Attachment'] as $ak => $atv) {
                         if ($atv['group'] == "profile") {
                              $profilePicture = $this->Media->file($atv['dirname'] . DS . $atv['basename']);
                              break;
                         }
                    }
               }

               if (file_exists($profilePicture)) {
                    $pdf->Image($profilePicture, '', '', 75, 60, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
               } else {
                    if (file_exists($defaultProfile)) {
                         $pdf->Image($defaultProfile, '', '', 75, 60, '', '', 'T', false, 300, '', false, false, 1, false, false, false);
                    }
               }

               $fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/jiret.ttf');
               $pdf->SetFont($fontPath, '', 24, '', true);

               $pdf->MultiCell(400, 10, "Full Name:", 0, 'L', false, 0, 105, 80);
               $pdf->MultiCell(400, 10, $student_detail['AcceptedStudent']['full_name'], 0, 'L', false, 0, 160, 80);

               $pdf->MultiCell(400, 10, "ሙሉ ስም:", 0, 'L', false, 0, 105, 90);

               if (!empty($student_detail['Student']['amharic_full_name'])) {

                    $pdf->MultiCell(400, 10, $student_detail['Student']['amharic_full_name'], 0, 'L', false, 0, 160, 90);
               }

               $pdf->MultiCell(400, 10, "ID No:", 0, 'L', false, 0, 105, 100);
               if (!empty($student_detail['AcceptedStudent']['studentnumber'])) {
                    $pdf->MultiCell(400, 10, $student_detail['AcceptedStudent']['studentnumber'], 0, 'L', false, 0, 160, 100);
               }

               $pdf->MultiCell(400, 10, "መለያ ቁጥር:", 0, 'L', false, 0, 105, 110);
               if (!empty($student_detail['AcceptedStudent']['studentnumber'])) {
                    $pdf->MultiCell(400, 10, $student_detail['AcceptedStudent']['studentnumber'], 0, 'L', false, 0, 160, 110);
               }

               $pdf->SetFont($fontPath, '', 15, '', true);

               if (!empty($student_detail['Department']['name'])) {
                    $pdf->MultiCell(400, 10, "Department:", 0, 'L', false, 0, 105, 120);
                    $pdf->MultiCell(400, 10, $student_detail['Department']['name'], 0, 'L', false, 0, 160, 120);
               } else if (empty($student_detail['AcceptedStudent']['department_id'])) {
                    $pdf->MultiCell(400, 10, "College:", 0, 'L', false, 0, 105, 120);
                    $pdf->MultiCell(400, 10, $student_detail['College']['name'], 0, 'L', false, 0, 160, 120);
               }

               $pdf->MultiCell(400, 10, "የት/ት ክፍል:", 0, 'L', false, 0, 105, 130);
               $pdf->SetFont($fontPath, '', 15, '', true);


               if (!empty($student_detail['Department']['amharic_name'])) {
                    $pdf->MultiCell(400, 10, $student_detail['Department']['amharic_name'], 0, 'L', false, 0, 160, 130);
               } else if (empty($student_detail['AcceptedStudent']['department_id'])) {
                    $pdf->MultiCell(400, 10, $student_detail['College']['amharic_name'], 0, 'L', false, 0, 160, 130);
               }


               // reset pointer to the last page
               $pdf->lastPage();
          }
     }
     //output the PDF to the browser

     $pdf->Output('id_card.' . date('Y') . '.pdf', 'I');
     /*
     I: send the file inline to the browser.
     D: send to the browser and force a file download with the name given by name.
     F: save to a local file with the name given by name.
     S: return the document as a string.
     */