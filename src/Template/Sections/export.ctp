<?php
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\IOFactory;

$this->disableAutoRender();

if (isset($students_per_section) && !empty($students_per_section)) {
    $section_name = $students_per_section[0]['Section']['name'];
    $academic_year = $students_per_section[0]['Section']['academicyear'];
    $program_name = $students_per_section[0]['Program']['name'];
    $section_program_id = $students_per_section[0]['Program']['id'];
    $program_type_name = $students_per_section[0]['ProgramType']['name'];
    $college_type = $students_per_section[0]['College']['type'];
    $department_type = $students_per_section[0]['Department']['type'];
    $year_level_name = !empty($students_per_section[0]['YearLevel']['name']) ? $students_per_section[0]['YearLevel']['name'] : ($section_program_id == PROGRAM_REMEDIAL ? 'Pre/Remedial' : 'Pre/1st');
    $curriculum_name = isset($students_per_section[0]['Curriculum']['name']) ? $students_per_section[0]['Curriculum']['name'] . ' - ' . $students_per_section[0]['Curriculum']['year_introduced'] . ' (' . (count(explode('ECTS', $students_per_section[0]['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') . ')' : '';
    $student_count = count($students_per_section[0]['Student']);
    $sheet_name = isset($department_name) ? str_replace(' ', '_', $department_name) . '_' . str_replace(' ', '_', $section_name) . '_' . str_replace('/', '-', $academic_year) . '_' . $year_level_name : str_replace(' ', '_', $college_name) . '_' . str_replace(' ', '_', $section_name) . '_' . str_replace('/', '-', $academic_year) . '_' . (!empty($year_level_name) ? $year_level_name : ($section_program_id == PROGRAM_REMEDIAL ? '_Pre_Remedial' : '_Pre_1st'));

    // Initialize PhpSpreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Section_' . str_replace(' ', '_', $section_name));

    // Write headers
    $row = 1;
    $sheet->setCellValue("A{$row}", "$college_type: {$college_name}" . (empty($department_name) ? ($section_program_id == PROGRAM_REMEDIAL ? ' Remedial Program' : ' Pre/Freshman') : ''));
    $sheet->mergeCells("A{$row}:D{$row}");
    $row++;
    if (!empty($department_name)) {
        $sheet->setCellValue("A{$row}", "$department_type: {$department_name}");
        $sheet->mergeCells("A{$row}:D{$row}");
        $row++;
    }
    $sheet->setCellValue("A{$row}", "Section: {$section_name}");
    $sheet->mergeCells("A{$row}:D{$row}");
    $row++;
    $sheet->setCellValue("A{$row}", "Academic Year: {$academic_year}");
    $sheet->mergeCells("A{$row}:D{$row}");
    $row++;
    $sheet->setCellValue("A{$row}", "Year Level: {$year_level_name}");
    $sheet->mergeCells("A{$row}:D{$row}");
    $row++;
    $sheet->setCellValue("A{$row}", "Program: {$program_name}");
    $sheet->mergeCells("A{$row}:D{$row}");
    $row++;
    $sheet->setCellValue("A{$row}", "Program Type: {$program_type_name}");
    $sheet->mergeCells("A{$row}:D{$row}");
    $row++;
    if (!empty($curriculum_name)) {
        $sheet->setCellValue("A{$row}", "Curriculum: {$curriculum_name}");
        $sheet->mergeCells("A{$row}:D{$row}");
        $row++;
    }
    $sheet->setCellValue("A{$row}", "Section Hosted: {$student_count} Student(s)");
    $sheet->mergeCells("A{$row}:D{$row}");
    $row++;
    $row++; // Empty row

    // Write column headers
    $sheet->setCellValue("A{$row}", "#");
    $sheet->setCellValue("B{$row}", "Student Name");
    $sheet->setCellValue("C{$row}", "Sex");
    $sheet->setCellValue("D{$row}", "Student ID");
    $row++;

    // Write student data
    if ($student_count > 0) {
        foreach ($students_per_section as $students) {
            for ($i = 0; $i < $student_count; $i++) {
                $sheet->setCellValue("A{$row}", $i + 1);
                $sheet->setCellValue("B{$row}", $students['Student'][$i]['full_name']);
                $sheet->setCellValue("C{$row}", strcasecmp(trim($students['Student'][$i]['gender']), 'male') == 0 ? 'M' : (strcasecmp(trim($students['Student'][$i]['gender']), 'female') == 0 ? 'F' : $students['Student'][$i]['gender']));
                $sheet->setCellValue("D{$row}", $students['Student'][$i]['studentnumber']);
                $row++;
            }
        }
    }

    // Set headers for Excel download
    header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
    header('Content-Disposition: attachment;filename="' . $sheet_name . '.xlsx"');
    header('Cache-Control: max-age=0');

    $writer = IOFactory::createWriter($spreadsheet, 'Xlsx');
    $writer->save('php://output');
    exit();
}
?>
