
<?php 
    if (isset($student_academic_profile) && !empty($student_academic_profile)) {
        echo $this->element('student_academic_profile');
        $this->set(compact('studentAttendedSections', 'student_academic_profile', 'student_section_exam_status'));
		$this->set('isTheStudentDismissed', $isTheStudentDismissed);
        $this->set('isTheStudentReadmitted', $isTheStudentReadmitted);
        $this->set('academicYR', $academicYR);
    }
?>

