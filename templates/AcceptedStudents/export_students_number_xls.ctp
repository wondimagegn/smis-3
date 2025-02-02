<?php

// Export all member records in .xls format with the help of the xlsHelper

$this->Xls->setHeader('Student IDs for ' . $selected_college_name .' '. (isset($selected_department_name) && !is_null($selected_department_name) ? $selected_department_name : 'Pre/Freshman'));
$this->Xls->addXmlHeader();
$this->Xls->setWorkSheetName('Student IDs for ' . $selected_college_name .' '. (isset($selected_department_name) && !is_null($selected_department_name) ? $selected_department_name : 'Pre/Freshman'));

if (!empty($acceptedStudents)) {
    $this->Xls->openRow();
    $this->Xls->writeString('College: ' . $selected_college_name);
    $this->Xls->closeRow();

    $this->Xls->openRow();
    $this->Xls->writeString('Campus: ' . $selected_campus_name);
    $this->Xls->closeRow();

   /*  $this->Xls->openRow();
    $this->Xls->writeString('Department: ' . (isset($selected_department_name) && !is_null($selected_department_name) ? $selected_department_name : 'Pre/Freshman'));
    $this->Xls->closeRow(); */
    $this->Xls->openRow();
    $this->Xls->writeString('Program: ' . $selected_program_name);
    $this->Xls->closeRow();
    $this->Xls->openRow();
    $this->Xls->writeString('Program Type: ' . $selected_program_type_name);
    $this->Xls->closeRow();
    $this->Xls->openRow();
    $this->Xls->writeString('Academic Year: ' . $selected_acdemicyear);
    $this->Xls->closeRow();
    $this->Xls->openRow();
    $this->Xls->closeRow();

    // headers

    $this->Xls->openRow();
    $this->Xls->writeString('#');
    $this->Xls->writeString('Full Name');
    $this->Xls->writeString('Sex');
    $this->Xls->writeString('Student Id');
    $this->Xls->writeString('Department');
    $this->Xls->writeString('Region');
    $this->Xls->writeString('National ID');
    $this->Xls->closeRow();

    // End headers

    // start data rendering

    $count = 1;
    foreach ($acceptedStudents as $acceptedStudent) {
        $this->Xls->openRow();
        $this->Xls->writeString($count++);
        $this->Xls->writeString($acceptedStudent['AcceptedStudent']['full_name']);
        $this->Xls->writeString((strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'male') == 0 ? 'M' : (strcasecmp(trim($acceptedStudent['AcceptedStudent']['sex']), 'female') == 0 ? 'F' : '')));
        $this->Xls->writeString($acceptedStudent['AcceptedStudent']['studentnumber']);
        $this->Xls->writeString((isset($acceptedStudent['Department']) && !is_null($acceptedStudent['Department']['name']) ? $acceptedStudent['Department']['name'] : 'Pre/Freshman'));
        $this->Xls->writeString($acceptedStudent['Region']['name']);
        $this->Xls->writeString((isset($acceptedStudent['Student']) && !empty($acceptedStudent['Student']['student_national_id']) ? $acceptedStudent['Student']['student_national_id'] : ''));
        $this->Xls->closeRow();
    }
    $this->Xls->openRow();
    $this->Xls->closeRow();
    // start data rendering

}
$this->Xls->addXmlFooter();
exit();
