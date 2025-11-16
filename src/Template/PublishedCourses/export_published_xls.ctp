<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$this->disableAutoRender();

$spreadsheet = new Spreadsheet();
$sheet = $spreadsheet->getActiveSheet();
$sheet->setTitle('Published courses of ' . $selected_academic_year . ' academic year');

$row = 1;

if (!empty($publishedCourses)) {
    foreach ($publishedCourses as $sk => $sv) {
        // Semester header
        $sheet->setCellValue("A{$row}", "Courses published for registration of semester {$sk} of {$selected_academic_year} academic year");
        $sheet->mergeCells("A{$row}:E{$row}");
        $row++;

        foreach ($sv as $pk => $pv) {
            // Program header
            $sheet->setCellValue("A{$row}", "Program {$pk}");
            $sheet->mergeCells("A{$row}:E{$row}");
            $row++;

            foreach ($pv as $ptk => $ptv) {
                // Program Type header
                $sheet->setCellValue("A{$row}", "Program Type {$ptk}");
                $sheet->mergeCells("A{$row}:E{$row}");
                $row++;

                foreach ($ptv as $yk => $yv) {
                    // Year Level header
                    $sheet->setCellValue("A{$row}", "Year Level {$yk}");
                    $sheet->mergeCells("A{$row}:E{$row}");
                    $row++;

                    foreach ($yv as $section_name => $section_value) {
                        // Section header
                        $sheet->setCellValue("A{$row}", "Section {$section_name}");
                        $sheet->mergeCells("A{$row}:E{$row}");
                        $row++;

                        // Table headers
                        $sheet->setCellValue("A{$row}", "No.");
                        $sheet->setCellValue("B{$row}", "Course Title");
                        $sheet->setCellValue("C{$row}", "Course Code");
                        $sheet->setCellValue("D{$row}", "Credit");
                        $sheet->setCellValue("E{$row}", "L T L");
                        $row++;

                        $count = 1;
                        foreach ($section_value as $type_index => $section_value_detail) {
                            foreach ($section_value_detail as $publishedCourse) {
                                $sheet->setCellValue("A{$row}", $count++);
                                $sheet->setCellValue("B{$row}", $publishedCourse['Course']['course_title']);
                                $sheet->setCellValue("C{$row}", $publishedCourse['Course']['course_code']);
                                $sheet->setCellValue("D{$row}", $publishedCourse['Course']['credit']);
                                $sheet->setCellValue("E{$row}", $publishedCourse['Course']['course_detail_hours']);
                                $row++;
                            }
                        }
                    }
                }
            }
        }
    }
}


// Set headers for Excel download
header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
header('Content-Disposition: attachment;filename="Published_courses_' . $selected_academic_year . '.xlsx"');
header('Cache-Control: max-age=0');

$writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
$writer->save('php://output');
exit();
?>
