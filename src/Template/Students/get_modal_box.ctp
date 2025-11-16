
<?php
    if (isset($studentAcademicProfile) && !empty($studentAcademicProfile)) {
        echo $this->element('student_academic_profile');
        if(!empty($studentAttendedSections)
            || !empty($isTheStudentDismissed)
            || !empty($studentSectionExamStatus) || !empty($academicYR) || !empty($isTheStudentReadmitted)) {

            $this->set(compact('studentAttendedSections', 'studentAcademicProfile',
                'studentSectionExamStatus'));
            $this->set('isTheStudentDismissed', $isTheStudentDismissed);
            $this->set('isTheStudentReadmitted', $isTheStudentReadmitted);
            $this->set('academicYR', $academicYR);

        }
    }
?>
