<?php
use Cake\I18n\I18n;

$this->set('title', __('Download CSV'));

if (!empty($acceptedStudents)) {
    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter(new \PhpOffice\PhpSpreadsheet\Spreadsheet(), 'Csv');
    $sheet = $writer->getSpreadsheet()->getActiveSheet();

    $line = $acceptedStudents[0]->toArray();
    $sheet->fromArray(array_keys($line), null, 'A1');

    $row = 2;
    foreach ($acceptedStudents as $acceptedStudent) {
        $line = $acceptedStudent->toArray();
        $sheet->fromArray($line, null, 'A' . $row);
        $row++;
    }

    $filename = 'Student_IDs_for_' . $selectedCollegeName . '_' . $selectedDepartmentName . '_' . str_replace('/', '-', $selectedAcademicYear) . '_' . date('Y-m-d') . '.csv';

    $this->response = $this->response->withType('csv');
    $this->response = $this->response->withHeader('Content-Disposition', 'attachment;filename="' . $filename . '"');
    ob_start();
    $writer->save('php://output');
    $output = ob_get_clean();
    echo $output;
}
?>
