<?php
//Export all member records in .xls format with the help of the xlsHelper
$this->Xls->setHeader('Courses of ' . $selected_curriculum_name . '_'. date("Y-m-d H-i-s"));
$this->Xls->addXmlHeader();
$this->Xls->setWorkSheetName('Courses of ' . $selected_curriculum_name);

if (!empty($course_associate_array)) {
    $this->Xls->openRow();
    $this->Xls->writeString('College: ' . $this_department_college_name);
    $this->Xls->closeRow();
    $this->Xls->openRow();
    $this->Xls->writeString('Department: ' . $selected_department_name);
    $this->Xls->closeRow();
    $this->Xls->openRow();
    $this->Xls->writeString('Program: ' . $program_name);
    $this->Xls->closeRow();
    $this->Xls->openRow();
    $this->Xls->writeString('Program Type: ' . $program_type_name);
    $this->Xls->closeRow();
    $this->Xls->openRow();
    $this->Xls->writeString('Curriculum: ' . $selected_curriculum_name);
    $this->Xls->closeRow();
    $this->Xls->openRow();
    $this->Xls->closeRow();
    foreach ($course_associate_array as $yearkey => $yearvalue) {
        foreach ($yearvalue as $semesterKey => $semestervalue) {
            $this->Xls->openRow();
            $this->Xls->writeString('Year Level: ' . $yearvalue[$semesterKey][0]['YearLevel']['name']);
            $this->Xls->closeRow();
            $this->Xls->openRow();
            $this->Xls->writeString('Semester: ' . $semesterKey);
            $this->Xls->closeRow();
            $this->Xls->openRow();
            $this->Xls->closeRow();
            $this->Xls->openRow();
            $this->Xls->writeString('No');
            $this->Xls->writeString('Course Title');
            $this->Xls->writeString('Course Code');
            $this->Xls->writeString('Credit');
            $this->Xls->writeString('L T L');
            $this->Xls->writeString('Course Category');
            $this->Xls->writeString('Lecture Attendance Requirement');
            $this->Xls->writeString('Lab Attendance Requirement');
            $this->Xls->writeString('Grade Type'); 
            $this->Xls->writeString('Prerequisite');
            $this->Xls->closeRow();
            $count = 1;
            foreach ($semestervalue as $course) {
                $this->Xls->openRow();
                $this->Xls->writeString($count++);
                $this->Xls->writeString($course['Course']['course_title']);
                $this->Xls->writeString($course['Course']['course_code']);
                $this->Xls->writeString($course['Course']['credit']);
                $this->Xls->writeString($course['Course']['course_detail_hours']);
                $this->Xls->writeString($course['CourseCategory']['name']);
                $this->Xls->writeString($course['Course']['lecture_attendance_requirement']);
                $this->Xls->writeString($course['Course']['lab_attendance_requirement']);
                $this->Xls->writeString($course['GradeType']['type']);

                $prerequisite = '';
                $cnt = 1;

                if (isset($course['Prerequisite']) && !empty($course['Prerequisite'])) {
                    foreach ($course['Prerequisite'] as $k => $v) {
                        $prerequisite .= $cnt++ . '. ' . $v['PrerequisiteCourse']['course_title'] . ' ';
                    }
                } else {
                    $prerequisite = 'none';
                }

                $this->Xls->writeString($prerequisite);
                
                $this->Xls->closeRow();
            }
            $this->Xls->openRow();
            $this->Xls->closeRow();
        }
    }
}
$this->Xls->addXmlFooter();
exit();
