<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MakeupExamsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('makeup_exams');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('PublishedCourses', [
            'foreignKey' => 'published_course_id',
            'joinType' => 'INNER',
        ]);
        $this->belongsTo('CourseRegistrations', [
            'foreignKey' => 'course_registration_id',
        ]);
        $this->belongsTo('CourseAdds', [
            'foreignKey' => 'course_add_id',
        ]);
        $this->hasMany('ExamGradeChanges', [
            'foreignKey' => 'makeup_exam_id',
        ]);
        $this->hasMany('ExamGrades', [
            'foreignKey' => 'makeup_exam_id',
        ]);
        $this->hasMany('ExamResults', [
            'foreignKey' => 'makeup_exam_id',
        ]);

    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('minute_number', 'Please provide minute number.')
            ->notEmptyString('published_course_id', 'Please select course.')
            ->numeric('published_course_id', 'Invalid course selection.')
            ->notEmptyString('student_id', 'Please select the student who is taking the makeup exam.')
            ->numeric('student_id', 'Invalid student selection.');

        return $validator;
    }

    function getmakeupExams($department_id = "", $acadamic_year = "", $program_id = "", $program_type_id = "0", $semester = "0")
    {
        $makeup_exams_formated = array();
        if ($department_id != "" && $acadamic_year != "" && $program_id != "") {
            $conditions['PublishedCourse.department_id'] = $department_id;
            $conditions['PublishedCourse.academic_year'] = $acadamic_year;
            $conditions['PublishedCourse.program_id'] = $program_id;
            if ($program_type_id != "0")
                $conditions['PublishedCourse.program_type_id'] = $program_type_id;
            if ($semester != "0")
                $conditions['PublishedCourse.semester'] = $semester;

            //Makeup exams which are assigned to the instructor
            $all_makeup_exams = $this->PublishedCourse->find(
                'all',
                array(
                    'conditions' => $conditions,
                    'contain' =>
                        array(
                            'Section',
                            'Course',
                            'MakeupExam' =>
                                array(
                                    'ExamGradeChange',
                                    'ExamResult',
                                    'ExamGrade',
                                    'CourseRegistration' =>
                                        array(
                                            'PublishedCourse' => array('Course'),
                                            'Student'
                                        ),
                                    'CourseAdd' =>
                                        array(
                                            'PublishedCourse' => array('Course'),
                                            'Student'
                                        )
                                ),
                        )
                )
            );
            //debug($all_makeup_exams);
            $count = 0;
            foreach ($all_makeup_exams as $key => $makeup_exams) {
                if (isset($makeup_exams['MakeupExam']) && !empty($makeup_exams['MakeupExam'])) {
                    foreach ($makeup_exams['MakeupExam'] as $me_key => $makeup_exam) { //debug($makeup_exam);
                        if (!empty($makeup_exam['CourseRegistration'])) {
                            $makeup_exams_formated[$count]['student_name'] = $makeup_exam['CourseRegistration']['Student']['first_name'] . ' ' . $makeup_exam['CourseRegistration']['Student']['middle_name'] . ' ' . $makeup_exam['CourseRegistration']['Student']['last_name'];
                            $makeup_exams_formated[$count]['student_id'] = $makeup_exam['CourseRegistration']['Student']['studentnumber'];
                            $makeup_exams_formated[$count]['exam_for'] = $makeup_exam['CourseRegistration']['PublishedCourse']['Course']['course_title'] . ' (' . $makeup_exam['CourseRegistration']['PublishedCourse']['Course']['course_code'] . ') [Registered]';
                        } else {
                            $makeup_exams_formated[$count]['student_name'] = $makeup_exam['CourseAdd']['Student']['first_name'] . ' ' . $makeup_exam['CourseAdd']['Student']['middle_name'] . ' ' . $makeup_exam['CourseAdd']['Student']['last_name'];
                            $makeup_exams_formated[$count]['student_id'] = $makeup_exam['CourseAdd']['Student']['studentnumber'];
                            $makeup_exams_formated[$count]['exam_for'] = $makeup_exam['CourseAdd']['PublishedCourse']['Course']['course_title'] . ' (' . $makeup_exam['CourseAdd']['PublishedCourse']['Course']['course_code'] . ') [Added]';
                        }

                        $makeup_exams_formated[$count]['minute_number'] = $makeup_exam['minute_number'];

                        $makeup_exams_formated[$count]['taken_exam'] = $makeup_exams['Course']['course_title'] . ' (' . $makeup_exams['Course']['course_code'] . ')';
                        $makeup_exams_formated[$count]['section_exam_taken'] = $makeup_exams['Section']['name'];

                        $makeup_exams_formated[$count]['created'] = $makeup_exam['created'];
                        $makeup_exams_formated[$count]['modified'] = $makeup_exam['modified'];

                        $makeup_exams_formated[$count]['ExamGrade'] = $makeup_exam['ExamGrade'];
                        $makeup_exams_formated[$count]['ExamResult'] = $makeup_exam['ExamResult'];
                        if (!empty($makeup_exam['ExamGradeChange'])) {
                            $makeup_exams_formated[$count]['ExamGradeChange'] = $makeup_exam['ExamGradeChange'][0];
                            $status = $this->CourseRegistration->ExamGrade->ExamGradeChange->examGradeChangeStateDescription($makeup_exam['ExamGradeChange'][0]);
                            $makeup_exams_formated[$count]['ExamGradeChange']['state'] = $status['state'];
                            $makeup_exams_formated[$count]['ExamGradeChange']['description'] = $status['description'];
                        }

                        $makeup_exams_formated[$count]['id'] = $makeup_exam['id'];
                        $count++;
                    }
                }
            }

            //Makeup exams which are directly submted by the department
            $all_makeup_exams = $this->PublishedCourse->find(
                'all',
                array(
                    'conditions' => $conditions,
                    'contain' =>
                        array(
                            'Course',
                            'CourseRegistration' =>
                                array(
                                    'Student',
                                    'ExamGrade' =>
                                        array(
                                            'ExamGradeChange' =>
                                                array(
                                                    'conditions' =>
                                                        array(
                                                            'ExamGradeChange.initiated_by_department' => 1
                                                        )
                                                )
                                        )
                                ),
                            'CourseAdd' =>
                                array(
                                    'Student',
                                    'ExamGrade' =>
                                        array(
                                            'ExamGradeChange' =>
                                                array(
                                                    'conditions' =>
                                                        array(
                                                            'ExamGradeChange.initiated_by_department' => 1
                                                        )
                                                )
                                        )
                                )
                        )
                )
            );
            //debug($all_makeup_exams);
            foreach ($all_makeup_exams as $key => $published_course) {
                if (isset($published_course['CourseRegistration'])) {
                    foreach ($published_course['CourseRegistration'] as $key => $course_registration) {
                        if (isset($course_registration['ExamGrade'][0]['ExamGradeChange']) && !empty($course_registration['ExamGrade'][0]['ExamGradeChange'])) {
                            foreach ($course_registration['ExamGrade'][0]['ExamGradeChange'] as $key => $exam_grade_change) {
                                $makeup_exams_formated[$count]['student_name'] = $course_registration['Student']['first_name'] . ' ' . $course_registration['Student']['middle_name'] . ' ' . $course_registration['Student']['last_name'];
                                $makeup_exams_formated[$count]['student_id'] = $course_registration['Student']['studentnumber'];
                                $makeup_exams_formated[$count]['exam_for'] = $published_course['Course']['course_title'] . ' (' . $published_course['Course']['course_code'] . ') [Registered]';

                                $makeup_exams_formated[$count]['taken_exam'] = null;
                                $makeup_exams_formated[$count]['section_exam_taken'] = null;

                                $makeup_exams_formated[$count]['ExamGradeChange'] = $exam_grade_change;
                                $status = $this->CourseRegistration->ExamGrade->ExamGradeChange->examGradeChangeStateDescription($exam_grade_change);
                                $makeup_exams_formated[$count]['ExamGradeChange']['state'] = $status['state'];
                                $makeup_exams_formated[$count]['ExamGradeChange']['description'] = $status['description'];
                                //$makeup_exams_formated[$count]['ExamGradeChange']['status'] = $this->CourseRegistration->getExamGradeChangeStatus($exam_grade_change);
                                $count++;
                            }
                        }
                    }
                }
            }
        }


        return $makeup_exams_formated;
    }

    function BACKUP_getmakeupExams($department_id = "", $acadamic_year = "", $program_id = "", $program_type_id = "0", $semester = "0")
    {
        $makeup_exams_formated = array();
        if ($department_id != "" && $acadamic_year != "" && $program_id != "") {
            $conditions['PublishedCourse.department_id'] = $department_id;
            $conditions['PublishedCourse.academic_year'] = $acadamic_year;
            $conditions['PublishedCourse.program_id'] = $program_id;
            if ($program_type_id != "0")
                $conditions['PublishedCourse.program_type_id'] = $program_type_id;
            if ($semester != "0")
                $conditions['PublishedCourse.semester'] = $semester;

            $all_makeup_exams = $this->PublishedCourse->find(
                'all',
                array(
                    'conditions' => $conditions,
                    'contain' =>
                        array(
                            'Section',
                            'Course',
                            'MakeupExam' =>
                                array(
                                    'ExamResult',
                                    'ExamGrade',
                                    'CourseRegistration' =>
                                        array(
                                            'PublishedCourse' => array('Course'),
                                            'Student'
                                        ),
                                    'CourseAdd' =>
                                        array(
                                            'PublishedCourse' => array('Course'),
                                            'Student'
                                        )
                                )
                        )
                )
            );

            $count = 0;
            foreach ($all_makeup_exams as $key => $makeup_exams) {
                if (isset($makeup_exams['MakeupExam']) && !empty($makeup_exams['MakeupExam'])) {
                    foreach ($makeup_exams['MakeupExam'] as $me_key => $makeup_exam) { //debug($makeup_exam);
                        if (!empty($makeup_exam['CourseRegistration'])) {
                            $makeup_exams_formated[$count]['student_name'] = $makeup_exam['CourseRegistration']['Student']['first_name'] . ' ' . $makeup_exam['CourseRegistration']['Student']['middle_name'] . ' ' . $makeup_exam['CourseRegistration']['Student']['last_name'];
                            $makeup_exams_formated[$count]['student_id'] = $makeup_exam['CourseRegistration']['Student']['studentnumber'];
                            $makeup_exams_formated[$count]['exam_for'] = $makeup_exam['CourseRegistration']['PublishedCourse']['Course']['course_title'] . ' (' . $makeup_exam['CourseRegistration']['PublishedCourse']['Course']['course_code'] . ') [Registered]';
                        } else {
                            $makeup_exams_formated[$count]['student_name'] = $makeup_exam['CourseAdd']['Student']['first_name'] . ' ' . $makeup_exam['CourseAdd']['Student']['middle_name'] . ' ' . $makeup_exam['CourseAdd']['Student']['last_name'];
                            $makeup_exams_formated[$count]['student_id'] = $makeup_exam['CourseAdd']['Student']['studentnumber'];
                            $makeup_exams_formated[$count]['exam_for'] = $makeup_exam['CourseAdd']['PublishedCourse']['Course']['course_title'] . ' (' . $makeup_exam['CourseAdd']['PublishedCourse']['Course']['course_code'] . ') [Added]';
                        }

                        $makeup_exams_formated[$count]['minute_number'] = $makeup_exam['minute_number'];

                        $makeup_exams_formated[$count]['taken_exam'] = $makeup_exams['Course']['course_title'] . ' (' . $makeup_exams['Course']['course_code'] . ')';
                        $makeup_exams_formated[$count]['section_exam_taken'] = $makeup_exams['Section']['name'];

                        $makeup_exams_formated[$count]['created'] = $makeup_exam['created'];
                        $makeup_exams_formated[$count]['modified'] = $makeup_exam['modified'];

                        $makeup_exams_formated[$count]['ExamGrade'] = $makeup_exam['ExamGrade'];
                        $makeup_exams_formated[$count]['ExamResult'] = $makeup_exam['ExamResult'];
                        $makeup_exams_formated[$count]['id'] = $makeup_exam['id'];
                        $count++;
                    }
                }
            }
        }
        //debug($makeup_exams_formated);
        //debug($all_makeup_exams);

        return $makeup_exams_formated;
    }

    public function canItBeDeleted($id = "")
    {
        if ($id != "") {
            $result_and_grade = $this->find(
                'first',
                array(
                    'conditions' =>
                        array(
                            'MakeupExam.id' => $id
                        ),
                    'contain' => array('ExamResult', 'ExamGrade', 'ExamGradeChange')
                )
            );

            if (count($result_and_grade['ExamResult']) > 0 || count($result_and_grade['ExamGrade']) > 0 || count($result_and_grade['ExamGradeChange']))
                return false;
            else
                return true;
        }
        return false;
    }
    public function makeUpExamApplied($student_id, $published_course_id, $reg_add_id, $reg = 0)
    {

        if ($reg == 1) {
            $return = $this->find(
                'first',
                array(
                    'conditions' =>
                        array(
                            'MakeupExam.student_id' => $student_id,
                            //'MakeupExam.published_course_id' => $published_course_id,
                            'MakeupExam.course_registration_id' => $reg_add_id,
                        ),
                    'recursive' => -1
                )
            );
        } else if ($reg == 0) {

            $return = $this->find(
                'first',
                array(
                    'conditions' =>
                        array(
                            'MakeupExam.student_id' => $student_id,
                            //'MakeupExam.published_course_id'=>$published_course_id,
                            'MakeupExam.course_add_id' => $reg_add_id,
                        ),
                    'recursive' => -1
                )
            );

        }
        if (
            isset($return['MakeupExam']['id'])
            && !empty($return['MakeupExam']['id'])
        ) {
            return $return['MakeupExam']['id'];
        }

        return 0;
    }

    public function assignedMakeup($published_course_id)
    {
        $assigned = $this->find(
            'count',
            array(
                'conditions' =>
                    array(

                        'MakeupExam.published_course_id' => $published_course_id,
                    ),
                'recursive' => -1
            )
        );

        return $assigned;
    }
}
