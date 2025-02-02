<?php

if (isset($students_per_section) && !empty($students_per_section)) {

    $section_name = $students_per_section[0]['Section']['name'];
    $academic_year = $students_per_section[0]['Section']['academicyear'];
    $program_name = $students_per_section[0]['Program']['name'];
    $section_program_id = $students_per_section[0]['Program']['id'];
    $program_type_name = $students_per_section[0]['ProgramType']['name'];
    $college_type = $students_per_section[0]['College']['type'];
    $department_type = $students_per_section[0]['Department']['type'];
    $year_level_name = (!empty($students_per_section[0]['YearLevel']['name']) ? $students_per_section[0]['YearLevel']['name'] : ($section_program_id == PROGRAM_REMEDIAL ? 'Pre/Remedial' : 'Pre/1st'));
    $curriculum_name = (isset($students_per_section[0]['Curriculum']['name']) ?  $students_per_section[0]['Curriculum']['name'] . ' - ' .  $students_per_section[0]['Curriculum']['year_introduced'] . ' (' .  (count(explode('ECTS', $students_per_section[0]['Curriculum']['type_credit'])) >= 2 ? 'ECTS' : 'Credit') . ')' : '');
    $student_count = count($students_per_section[0]['Student']);
    $sheet_name = (isset($department_name) ?  (str_replace(' ', '_', $department_name)) . '_' .(str_replace(' ', '_', $section_name)) . '_' . (str_replace('/', '-', $academic_year)) . '_'. $year_level_name : (str_replace(' ', '_', $college_name)) . '_' . (str_replace(' ', '_', $section_name))  . '_' . (str_replace('/', '-', $academic_year)) . '_'. (!empty($year_level_name) ? $year_level_name : ($section_program_id == PROGRAM_REMEDIAL ? '_Pre_Remedial' : '_Pre_1st')));
    
    //input the export file name

    //$this->Xls->setHeader('Section_' . $section_name);
    $this->Xls->setHeader($sheet_name);

    $this->Xls->addXmlHeader();
    $this->Xls->setWorkSheetName('Section_' . (str_replace(' ', '_', $section_name)));
   
    //Headings 
    $this->Xls->openRow();
    $this->Xls->writeString();
    $this->Xls->writeString("$college_type: " . $college_name . (empty($department_name) ? ($section_program_id == PROGRAM_REMEDIAL ? 'Remedial Program' : ' Pre/Freshman') : ''));
    $this->Xls->closeRow();

    if (!empty($department_name)) {
        $this->Xls->openRow();
        $this->Xls->writeString();
        $this->Xls->writeString("$department_type: " . $department_name);
        $this->Xls->closeRow();
    }

    $this->Xls->openRow();
    $this->Xls->writeString();
    $this->Xls->writeString("Section: " . $section_name);
    $this->Xls->closeRow();

    $this->Xls->openRow();
    $this->Xls->writeString();
    $this->Xls->writeString("Academic Year: " . $academic_year);
    $this->Xls->closeRow();

    $this->Xls->openRow();
    $this->Xls->writeString();
    $this->Xls->writeString("Year Level: " . $year_level_name);
    $this->Xls->closeRow();

    $this->Xls->openRow();
    $this->Xls->writeString();
    $this->Xls->writeString("Program: " . $program_name);
    $this->Xls->closeRow();

    $this->Xls->openRow();
    $this->Xls->writeString();
    $this->Xls->writeString("Program Type: " . $program_type_name );
    $this->Xls->closeRow();

    if (!empty($curriculum_name)) {
        $this->Xls->openRow();
        $this->Xls->writeString();
        $this->Xls->writeString("Curriculum: " . $curriculum_name);
        $this->Xls->closeRow();
    }

    $this->Xls->openRow();
    $this->Xls->writeString();
    $this->Xls->writeString("Section Hosted: " . $student_count . ' Student(s)');
    $this->Xls->closeRow();

    $this->Xls->openRow();
    $this->Xls->closeRow();
    
    //2nd row for columns name
    $this->Xls->openRow();
    $this->Xls->writeString('#');
    $this->Xls->writeString('Student Name');
    $this->Xls->writeString('Sex');
    $this->Xls->writeString('Student ID');
    $this->Xls->closeRow();

    //rows for data

    if (isset($student_count) && $student_count > 0) {
        foreach ($students_per_section as $students) {
            for ($i = 0; $i < $student_count; $i++) {
                $this->Xls->openRow();
                $this->Xls->writeString($i + 1);
                $this->Xls->writeString($students['Student'][$i]['full_name']);
                $this->Xls->writeString((strcasecmp(trim($students['Student'][$i]['gender']), 'male') == 0 ? 'M' :(strcasecmp(trim($students['Student'][$i]['gender']), 'female') == 0 ? 'F' : $students['Student'][$i]['gender'])));
                $this->Xls->writeString($students['Student'][$i]['studentnumber']);
                $this->Xls->closeRow();
            }
        }
    }

    $this->Xls->addXmlFooter();
    exit();
}