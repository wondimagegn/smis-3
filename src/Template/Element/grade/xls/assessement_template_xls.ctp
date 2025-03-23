<?php
header("Expires: " . gmdate("D,d M YH:i:s") . " GMT");
header("Last-Modified: " . gmdate("D,d M YH:i:s") . " GMT");
header("Cache-Control: no-cache, must-revalidate");
header("Pragma: no-cache");
header("Content-type: application/vnd.ms-excel");
header("Content-Disposition: attachment; filename=" . $filename . ".xls");
header("Content-Description: Exported as XLS");

$students_process = $students_export;

if (!isset($total_student_count)) {
    $total_student_count = count($students_process);
}

if (!isset($grade_view_only)) {
    $grade_view_only = false;
}

if (!empty($students_process)) { ?>
    <table class="table">
        <thead>
            <tr>
                <th style="width:8%">StudentID</th>
                <?php
                $percent = 10;
                $last_percent = ""; 
                $grade_width = 0;

                if (((100 - 28) / ((count($exam_types) + 1) + $grade_width)) > 10) {
                    $last_percent = (100 - 28) - ((count($exam_types) + 1 + $grade_width) * 10);
                } else {
                    $percent = ((100 - 28) / (count($exam_types) + 1 + $grade_width));
                }

                $count_for_percent = 0;

                if (!empty($exam_types)) {
                    foreach ($exam_types as $key => $exam_type) {
                        $count_for_percent++; ?>
                        <th style="width:<?= ($count_for_percent == (count($exam_types) + 1) && $last_percent != "" && !($grade_submission_status['grade_submited'] || $display_grade || $view_only) ? $last_percent + $percent : $percent); ?>%">
                            <?= $exam_type['ExamType']['exam_name'] . ' -' . $exam_type['ExamType']['percent']; ?>
                        </th>
                        <?php
                    } 
                } ?>
            </tr>
        </thead>
        <tbody>
            <?php
            foreach ($students_process as $key => $student) { ?>
                <tr>
                    <td><?= $student['Student']['studentnumber']; ?></td>
                    <?php
                    //Each mark entry for each exam type (foreach loop)
                    if (!empty($exam_types)) {
                        foreach ($exam_types as $key => $exam_type) { ?>
                            <td>
                                <?php
                                $id = "";
                                $value = "";
                                //Searching for the exam result from the databse returned value
                                if (isset($student['ExamResult']) && !empty($student['ExamResult'])) {
                                    foreach ($student['ExamResult'] as $key => $examResult) {
                                        if ($examResult['exam_type_id'] == $exam_type['ExamType']['id']) {
                                            $id = $examResult['id'];
                                            $value = $examResult['result'];
                                            break;
                                        }
                                    }
                                }
                                echo $value;  ?>
                            </td>
                            <?php
                            //$count++;
                        }
                    } //End of each mark entry for each exam type (foreach loop)
                    ?>
                </tr>
                <?php
            } ?>
        </tbody>
    </table>
    <?php
} ?>