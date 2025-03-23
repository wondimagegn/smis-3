<?php
if (!empty($acceptedStudents)) {
    
    $line = $acceptedStudents[0]['AcceptedStudent'];
    $this->CSV->addRow(array_keys($line));

    foreach ($acceptedStudents as $acceptedStudent) {
        $line = $acceptedStudent['AcceptedStudent'];
        $this->CSV->addRow($line);
    }

    $filename = 'Student_IDs_for_' . $selected_college_name . '_'. $selected_department_name . '_' . str_replace('/', '-', $selected_acdemicyear) . '_' . date('Y-m-d');
    echo  $this->CSV->render($filename);
}

