<?php
use Cake\I18n\I18n;

$this->set('title', __('Export Student IDs'));

if (!empty($acceptedStudents)) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Student IDs for ' . $selectedCollegeName . ' ' . ($selectedDepartmentName ?? 'Pre/Freshman'));

    $row = 1;
    $sheet->setCellValue("A{$row}", __('College: {0}', $selectedCollegeName));
    $row++;
    $sheet->setCellValue("A{$row}", __('Campus: {0}', $selectedCampusName));
    $row++;
    $sheet->setCellValue("A{$row}", __('Program: {0}', $selectedProgramName));
    $row++;
    $sheet->setCellValue("A{$row}", __('Program Type: {0}', $selectedProgramTypeName));
    $row++;
    $sheet->setCellValue("A{$row}", __('Academic Year: {0}', $selectedAcademicYear));
    $row += 2;

    $sheet->setCellValue("A{$row}", __('#'));
    $sheet->setCellValue("B{$row}", __('Full Name'));
    $sheet->setCellValue("C{$row}", __('Sex'));
    $sheet->setCellValue("D{$row}", __('Student ID'));
    $sheet->setCellValue("E{$row}", __('Department'));
    $sheet->setCellValue("F{$row}", __('Region'));
    $sheet->setCellValue("G{$row}", __('National ID'));
    $row++;

    $count = 1;
    foreach ($acceptedStudents as $acceptedStudent) {
        $sheet->setCellValue("A{$row}", $count++);
        $sheet->setCellValue("B{$row}", $acceptedStudent->full_name);
        $sheet->setCellValue("C{$row}", strcasecmp(trim($acceptedStudent->sex), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent->sex), 'female') == 0 ? 'F' : ''));
        $sheet->setCellValue("D{$row}", $acceptedStudent->studentnumber);
        $sheet->setCellValue("E{$row}", $acceptedStudent->Department->name ?? 'Pre/Freshman');
        $sheet->setCellValue("F{$row}", $acceptedStudent->Region->name);
        $sheet->setCellValue("G{$row}", $acceptedStudent->Student->student_national_id ?? '');
        $row++;
    }

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
    $this->response = $this->response->withType('application/vnd.ms-excel');
    $this->response = $this->response->withHeader('Content-Disposition', 'attachment;filename="Student_IDs_for_' . $selectedCollegeName . '_' . ($selectedDepartmentName ?? 'Pre-Freshman') . '.xls"');
    ob_start();
    $writer->save('php://output');
    $output = ob_get_clean();
    echo $output;
}
?>
