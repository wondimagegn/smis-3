<?php
// ==================== NOT BEING USED, USING FILE FROM ELEMENTS ====================
//debug($temporary_degree);
App::import('Vendor', 'tcpdf/tcpdf');
// create new PDF document
//$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, PDF_PAGE_FORMAT, true);
$pdf = new TCPDF(PDF_PAGE_ORIENTATION, PDF_UNIT, 'A4', true, 'UTF-8', false);

//show header or footer
$pdf->SetPrintHeader(false);
$pdf->SetPrintFooter(false);
//$textfont = 'freesans'; // looks better, finer, and more condensed than 'dejavusans'
// set default header data
/*
    $pdf->SetHeaderData(PDF_HEADER_LOGO, PDF_HEADER_LOGO_WIDTH, PDF_HEADER_TITLE, PDF_HEADER_STRING);

    // set header and footer fonts
    $pdf->setHeaderFont(Array(PDF_FONT_NAME_MAIN, '', PDF_FONT_SIZE_MAIN));
    $pdf->setFooterFont(Array(PDF_FONT_NAME_DATA, '', PDF_FONT_SIZE_DATA));

    //set margins

    $pdf->SetMargins(PDF_MARGIN_LEFT, PDF_MARGIN_TOP, PDF_MARGIN_RIGHT);
    //$pdf->SetMargins(15,15,15);
    $pdf->SetHeaderMargin(PDF_MARGIN_HEADER);
    $pdf->SetFooterMargin(PDF_MARGIN_FOOTER);
    //set auto page breaks
    $pdf->SetAutoPageBreak(TRUE, PDF_MARGIN_BOTTOM);

    //set image scale factor
    $pdf->setImageScale(PDF_IMAGE_SCALE_RATIO);
    */
// set font
//SetMargins(Left, Top, Right)
$pdf->SetMargins(0, 10, 0);
//Font Family, Style, Size
//$pdf->SetFont("pdfacourier", "", 11);
$pdf->setPageOrientation('L', true, 0);
$pdf->AddPage("L");
//$pdf->SetLineStyle(array('dash' => 0, 'width' => '1'));
//$pdf->Line(5, 5, 292, 5);
//$pdf->Line(5, 205, 292, 205);
//$pdf->Line(5, 4.5, 5, 205.5);
//$pdf->Line(292, 4.5, 292, 205.5);

//Image processing
if (strcasecmp($temporary_degree['student_detail']['University']['Attachment']['0']['group'], 'logo') == 0) {
    $logo_index = 0;
    $bg_index = 1;
} else {
    $logo_index = 1;
    $bg_index = 0;
}
$logo_path = $this->Media->file($temporary_degree['student_detail']['University']['Attachment'][$logo_index]['dirname'] . DS . $temporary_degree['student_detail']['University']['Attachment'][$logo_index]['basename']);
$bg_path = $this->Media->file($temporary_degree['student_detail']['University']['Attachment'][$bg_index]['dirname'] . DS . $temporary_degree['student_detail']['University']['Attachment'][$bg_index]['basename']);

$pdf->Image($bg_path, 0, 7, 195, 195, '', '', '', false, 300, 'C', false, false, 0);
$pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/img/temp_degree_left-top.gif', 3, 3, 51, 52, '', '', '', false, 300, '', false, false, 0);
$pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/img/temp_degree_right-top.gif', 242, 3, 51, 52, '', '', '', false, 300, '', false, false, 0);
$pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/img/temp_degree_left-bottom.gif', 3, 155, 51, 52, '', '', '', false, 300, '', false, false, 0);
$pdf->Image($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/img/temp_degree_right-bottom.gif', 242, 155, 51, 52, '', '', '', false, 300, '', false, false, 0);

$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/jiret.ttf');
$pdf->SetFont($fontPath, '', 34, '', true);
$pdf->Write(0, $temporary_degree['student_detail']['University']['University']['amharic_name'], '', 0, 'C', false);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/old_english_text_mt.ttf');
$pdf->SetFont($fontPath, '', 34, '', false);
$pdf->Ln(11);
$pdf->Write(0, $temporary_degree['student_detail']['University']['University']['name'], '', 0, 'C', true);
$pdf->Image($logo_path, '', '', 35, 35, 'GIF', '', 'N', true, 300, 'C');
$pdf->SetFont($fontPath, 'U', 24, '', false);
$pdf->Ln(5);
$pdf->Write(0, 'Temporary Certificate of Graduation', '', 0, 'C', true);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/ocraextended.ttf');
$pdf->SetFont($fontPath, '', 13, '', false);
$pdf->Write(0, 'This is to certify that', '', 0, 'C', true);
$pdf->Ln(3);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
$pdf->SetFont($fontPath, '', 15, '', false);
$pdf->Write(0, strtoupper($temporary_degree['student_detail']['Student']['full_name']), '', 0, 'C', true);
$pdf->SetLineStyle(array('dash' => 1));
$pdf->Line(75, 102, 225, 102);
$pdf->Ln(6);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/ocraextended.ttf');
$pdf->SetFont($fontPath, '', 13, '', false);
$pdf->Write(0, 'Graduated from', '', 0, 'C', true);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
$pdf->Ln(3);
$pdf->SetFont($fontPath, '', 15, '', false);
$pdf->Write(0, strtoupper($temporary_degree['student_detail']['College']['name']), '', 0, 'C', true);
$pdf->Line(85, 124, 212, 124);

$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/ocraextended.ttf');
$pdf->SetFont($fontPath, '', 13, '', false);
$pdf->MultiCell(15, 7, 'With', 0, 'L', false, 0, 80, 130);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
$pdf->SetFont($fontPath, '', 15, '', false);
$pdf->MultiCell(107, 7, strtoupper($temporary_degree['student_detail']['Curriculum']['english_degree_nomenclature']), 0, 'C', false, 0, 95, 130);
//    $pdf->Line(93, 136, 205, 136);

$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/ocraextended.ttf');
$pdf->SetFont($fontPath, '', 13, '', false);
//  	 $pdf->MultiCell(15, 7, 'In', 0, 'L', false, 0, 85, 142);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
$pdf->SetFont($fontPath, '', 15, '', false);
//    $pdf->MultiCell(107, 7, strtoupper($temporary_degree['student_detail']['Department']['name']), 0, 'C', false, 0, 95, 142);
$pdf->Line(93, 148, 205, 148);

$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/ocraextended.ttf');
$pdf->SetFont($fontPath, '', 13, '', false);
$pdf->MultiCell(15, 7, 'On', 0, 'L', false, 0, 88, 154);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
$pdf->SetFont($fontPath, '', 15, '', false);
$pdf->MultiCell(107, 7, $this->Format->humanize_date_short_extended_all($temporary_degree['student_detail']['GraduateList']['graduate_date']), 0, 'C', false, 0, 95, 154);

$pdf->MultiCell(120, 7, "Serial No:".$temporary_degree['student_detail']['Student']['code'], 0, 'C', false, 0, 120, 5);

$pdf->Line(95, 160, 200, 160);
$pdf->Ln(15);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/Vendor/tcpdf/fonts/ocraextended.ttf');
$pdf->SetFont($fontPath, '', 13, '', false);
$pdf->Write(0, 'This certificate of graduation has been given', '', 0, 'C', true);
$pdf->Write(0, 'pending the printing and issuance of the actual diploma.', '', 0, 'C', true);
$pdf->Ln(14);
$fontPath = $pdf->addTTFfont($_SERVER['DOCUMENT_ROOT'] . '/app/webroot/fonts/FreeSerifBold.ttf');
$pdf->SetFont($fontPath, '', 15, '', false);
$pdf->Write(0, 'University Registrar', '', 0, 'C', true);
$pdf->Line(95, 194, 200, 194);

$pdf->SetFont('freesans', '', 9, '', false);
$pdf->SetFillColor(238, 233, 233);
if (isset($temporary_degree['student_detail']['University']['University']['name'])) {
    $pdf->MultiCell(40, 20, $temporary_degree['student_detail']['University']['University']['name'] . '
        Registrar Office
        P.O.Box: ' . $temporary_degree['student_detail']['University']['University']['p_o_box'] . '
        Tel: ' . $temporary_degree['student_detail']['University']['University']['telephone'] . '
        Fax: ' . $temporary_degree['student_detail']['University']['University']['fax'] . ' ', 0, 'C', true, 0, 227, 175);
} else {
}


// reset pointer to the last page
$pdf->lastPage();

//output the PDF to the browser

$pdf->Output('Temporary_Degree-' . $temporary_degree['student_detail']['Student']['first_name'] . '_' . $temporary_degree['student_detail']['Student']['middle_name'] . '_' . $temporary_degree['student_detail']['Student']['last_name'] . '.pdf', 'I');
    /*
    I: send the file inline to the browser.
    D: send to the browser and force a file download with the name given by name.
    F: save to a local file with the name given by name.
    S: return the document as a string.
    */