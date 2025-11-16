<?php
use Cake\I18n\I18n;

$this->set('title', __('Auto Placement Result'));

if (!empty($autoPlacedStudents)) {
    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setTitle('Auto Placement Result');

    $summary = $autoPlacedStudents['auto_summary'];

    $row = 1;
    $sheet->setCellValue("A{$row}", __('Summary of Auto Placement'));
    $row++;
    $sheet->setCellValue("A{$row}", __('Department'));
    $sheet->setCellValue("B{$row}", __('Competitive Assignment'));
    $sheet->setCellValue("C{$row}", __('Privileged Quota Assignment'));
    $row++;

    foreach ($summary as $sk => $sv) {
        $sheet->setCellValue("A{$row}", $sk);
        $sheet->setCellValue("B{$row}", $sv['C']);
        $sheet->setCellValue("C{$row}", $sv['Q']);
        $row++;
    }

    unset($autoPlacedStudents['auto_summary']);

    foreach ($autoPlacedStudents as $key => $data) {
        $row++;
        $sheet->setCellValue("A{$row}", $key);
        $row++;
        $sheet->setCellValue("A{$row}", __('Full Name'));
        $sheet->setCellValue("B{$row}", __('Sex'));
        $sheet->setCellValue("C{$row}", __('Student Number'));
        $sheet->setCellValue("D{$row}", __('EHEECE Result'));
        $sheet->setCellValue("E{$row}", __('Department'));
        $sheet->setCellValue("F{$row}", __('Preference Order'));
        $sheet->setCellValue("G{$row}", __('Placement Type'));
        $sheet->setCellValue("H{$row}", __('Placement Based'));
        $row++;

        foreach ($data as $acceptedStudent) {
            $sheet->setCellValue("A{$row}", $acceptedStudent->full_name);
            $sheet->setCellValue("B{$row}", $acceptedStudent->sex);
            $sheet->setCellValue("C{$row}", $acceptedStudent->studentnumber);
            $sheet->setCellValue("D{$row}", $acceptedStudent->EHEECE_total_results);
            $sheet->setCellValue("E{$row}", $acceptedStudent->Department->name);

            $preferenceOrder = '';
            if (!empty($acceptedStudent->Preferences)) {
                foreach ($acceptedStudent->Preferences as $preference) {
                    if ($preference->department_id == $acceptedStudent->Department->id) {
                        $preferenceOrder = $preference->preferences_order;
                        break;
                    }
                }
            }
            $sheet->setCellValue("F{$row}", $preferenceOrder);
            $sheet->setCellValue("G{$row}", $acceptedStudent->placementtype);
            $sheet->setCellValue("H{$row}", $acceptedStudent->placement_based == 'C' ? __('Competitive') : __('Privileged Quota'));
            $row++;
        }
    }

    $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xls');
    $this->response = $this->response->withType('application/vnd.ms-excel');
    $this->response = $this->response->withHeader('Content-Disposition', 'attachment;filename="Auto_Placement_Result.xls"');
    ob_start();
    $writer->save('php://output');
    $output = ob_get_clean();
    echo $output;
}
?>
