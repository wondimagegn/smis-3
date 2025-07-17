<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use App\Controller\Component\AcademicYearComponent;

class ExamResultsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('exam_results');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');


        $this->belongsTo('CourseRegistrations', [
            'foreignKey' => 'course_registration_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('CourseAdds', [
            'foreignKey' => 'course_registration_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('MakeupExams', [
            'foreignKey' => 'makeup_exam_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ExamTypes', [
            'foreignKey' => 'exam_type_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->numeric('result')
            ->allowEmptyString('result')
            ->add('result', 'numeric', [
                'rule' => ['numeric'],
                'message' => 'Use only number.',
                'last' => true,
            ])
            ->add('result', 'comparison', [
                'rule' => ['comparison', '>=', 0],
                'message' => 'Invalid result.',
                'last' => true,
            ]);

        return $validator;
    }

    public function isExamResultSubmitted(?array $published_course_ids = null): int
    {
        $published_courses_student_registred_score_grade = 0;

        $grade_submitted_registred_courses = $this->CourseRegistrations->find('list')
            ->where(['CourseRegistrations.published_course_id IN' => $published_course_ids])
            ->select(['CourseRegistrations.id'])
            ->toArray();

        if (!empty($grade_submitted_registred_courses)) {
            $published_courses_student_registred_score_grade = $this->find()
                ->where(['ExamResults.course_registration_id IN' => $grade_submitted_registred_courses])
                ->count();

            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        $grade_submitted_add_courses = $this->CourseAdds->find('list')
            ->where([
                'CourseAdds.published_course_id IN' => $published_course_ids,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1,
            ])
            ->select(['CourseAdds.id'])
            ->toArray();

        if (!empty($grade_submitted_add_courses)) {
            $published_courses_student_registred_score_grade = $this->find()
                ->where(['ExamResults.course_add_id IN' => $grade_submitted_add_courses])
                ->count();

            if ($published_courses_student_registred_score_grade > 0) {
                return $published_courses_student_registred_score_grade;
            }
        }

        return $published_courses_student_registred_score_grade;
    }

    public function isStudentSectionChangePossible(?int $student_id = null, ?int $section_id = null): bool
    {
        $check = $this->CourseRegistrations->find()
            ->where(['CourseRegistrations.student_id' => $student_id, 'CourseRegistrations.section_id' => $section_id])
            ->count();

        return $check == 0;
    }

    public function isRegistredInNameOfSectionAndSubmittedGrade(?int $student_id = null, ?int $section_id = null): bool
    {
        $course_registration_ids = $this->CourseRegistrations->find('list')
            ->where(['CourseRegistrations.student_id' => $student_id, 'CourseRegistrations.section_id' => $section_id])
            ->select(['CourseRegistrations.id'])
            ->toArray();

        if (!empty($course_registration_ids)) {
            $check = $this->CourseRegistrations->ExamGrades->find()
                ->where(['ExamGrades.course_registration_id IN' => $course_registration_ids])
                ->count();

            return true;
        }

        return false;
    }

    public function calculateGradeAndReturnPassOrFail(array $exam_result = []): string
    {
        $test = $this->GradeScales->find()->toArray();
        // debug($test);
        return "passed";
    }

    public function getTotalResultGrade(?float $result = null, ?int $published_course_id = null): string
    {
        if (!$result) {
            return 'NG';
        }

        $grade_scales = $this->CourseRegistrations->PublishedCourses->getGradeScaleDetail($published_course_id);

        foreach ($grade_scales['GradeScaleDetail'] as $grade_scale) {
            if ($result >= $grade_scale['minimum_result'] && $result <= $grade_scale['maximum_result']) {
                return $grade_scale['grade'];
            }
        }

        return 'NG';
    }

    public function generateCourseGrade(array $students = [], array $grade_scales = [], array $exam_types = []): array
    {
        $mandatory_exams = [];
        $exam_types_of_published_course = [];

        foreach ($exam_types as $exam_type) {
            if ($exam_type['ExamType']['mandatory'] == 1) {
                $mandatory_exams[] = $exam_type['ExamType']['id'];
            }
            $exam_types_of_published_course[] = $exam_type['ExamType']['id'];
        }

        // debug($mandatory_exams);

        foreach ($students as $stu_key => $student) {
            if (!isset($student['MakeupExam'])) {
                if (!isset($student['ExamGrade']) || empty($student['ExamGrade']) || $student['ExamGrade'][0]['department_approval'] == -1) {
                    $exam_sum = 0;
                    $taken_exams = [];
                    $grade = [];

                    foreach ($student['ExamResult'] as $examResult) {
                        if (!in_array($examResult['exam_type_id'], $taken_exams) && in_array($examResult['exam_type_id'], $exam_types_of_published_course)) {
                            $exam_sum += $examResult['result'];
                            $taken_exams[] = $examResult['exam_type_id'];
                        } else {
                            $this->delete($examResult['id']);
                        }
                    }

                    foreach ($mandatory_exams as $mandatory_exam) {
                        if (!in_array($mandatory_exam, $taken_exams)) {
                            $grade['grade'] = 'NG';
                            break;
                        }
                    }

                    if (empty($grade)) {
                        foreach ($grade_scales['GradeScaleDetail'] as $grade_scale) {
                            if ($exam_sum >= $grade_scale['minimum_result'] && $exam_sum <= $grade_scale['maximum_result']) {
                                $grade['grade'] = $grade_scale['grade'];
                                break;
                            }
                        }
                    }

                    // debug($grade_scales);
                    // debug($grade);

                    $grade['fully_taken'] = count($taken_exams) >= count($exam_types);
                    $grade['course_registration_id'] = $student['CourseRegistration']['id'] ?? null;
                    $grade['course_add_id'] = $student['CourseAdd']['id'] ?? null;

                    $students[$stu_key]['GeneratedExamGrade'] = $grade;
                }
            } else {
                if (!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['department_approval'] == -1) {
                    $grade = [];

                    if ($student['MakeupExam']['course_registration_id'] != null) {
                        $grade['exam_grade_id'] = $this->CourseRegistrations->ExamGrades->find()
                            ->where(['ExamGrades.course_registration_id' => $student['MakeupExam']['course_registration_id']])
                            ->order(['ExamGrades.created' => 'DESC'])
                            ->first()
                            ->id ?? null;
                    } else {
                        $grade['exam_grade_id'] = $this->CourseRegistrations->ExamGrades->find()
                            ->where(['ExamGrades.course_add_id' => $student['MakeupExam']['course_add_id']])
                            ->order(['ExamGrades.created' => 'DESC'])
                            ->first()
                            ->id ?? null;
                    }

                    $grade['minute_number'] = $student['MakeupExam']['minute_number'];
                    $grade['makeup_exam_id'] = $student['MakeupExam']['id'];
                    $grade['makeup_exam_result'] = $student['MakeupExam']['result'];
                    $grade['initiated_by_department'] = 0;

                    if ($student['MakeupExam']['result'] == null) {
                        $grade['grade'] = 'NG';
                    } else {
                        foreach ($grade_scales['GradeScaleDetail'] as $grade_scale) {
                            if ($student['MakeupExam']['result'] >= $grade_scale['minimum_result'] && $student['MakeupExam']['result'] <= $grade_scale['maximum_result']) {
                                $grade['grade'] = $grade_scale['grade'];
                                break;
                            }
                        }
                    }

                    // debug($grade_scales);
                    // debug($grade);

                    $grade['fully_taken'] = $student['MakeupExam']['result'] != null;

                    $students[$stu_key]['GeneratedExamGrade'] = $grade;
                }
            }
        }

        return $students;
    }

    public function generateGradeEntryCourseGrade(array $students = [], array $grade_scales = []): array
    {
        foreach ($students as $stu_key => $student) {
            if (!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['department_approval'] == -1) {
                $grade = [];

                $grade['course_registration_id'] = $student['ResultEntryAssignment']['course_registration_id'] ?? null;
                $grade['course_add_id'] = $student['ResultEntryAssignment']['course_add_id'] ?? null;

                if ($student['ResultEntryAssignment']['result'] == null) {
                    $grade['grade'] = 'NG';
                } else {
                    foreach ($grade_scales['GradeScaleDetail'] as $grade_scale) {
                        if ($student['ResultEntryAssignment']['result'] >= $grade_scale['minimum_result'] && $student['ResultEntryAssignment']['result'] <= $grade_scale['maximum_result']) {
                            $grade['grade'] = $grade_scale['grade'];
                            break;
                        }
                    }
                }

                $grade['fully_taken'] = $student['ResultEntryAssignment']['result'] != null;

                $students[$stu_key]['GeneratedExamGrade'] = $grade;
            }
        }

        // debug($students);
        return $students;
    }

    public function generateCourseGradeWithoutScale(array $students = [], array $exam_types = []): array
    {
        $mandatory_exams = [];
        $exam_types_of_published_course = [];

        foreach ($exam_types as $exam_type) {
            if ($exam_type['ExamType']['mandatory'] == 1) {
                $mandatory_exams[] = $exam_type['ExamType']['id'];
            }
            $exam_types_of_published_course[] = $exam_type['ExamType']['id'];
        }

        // debug($mandatory_exams);

        foreach ($students as $stu_key => $student) {
            if (!isset($student['MakeupExam'])) {
                if (!isset($student['ExamGrade']) || empty($student['ExamGrade']) || $student['ExamGrade'][0]['department_approval'] == -1) {
                    $exam_sum = 0;
                    $taken_exams = [];
                    $grade = [];

                    foreach ($student['ExamResult'] as $examResult) {
                        if (!in_array($examResult['exam_type_id'], $taken_exams) && in_array($examResult['exam_type_id'], $exam_types_of_published_course)) {
                            $exam_sum += $examResult['result'];
                            $taken_exams[] = $examResult['exam_type_id'];
                        } else {
                            $this->delete($examResult['id']);
                        }
                    }

                    foreach ($mandatory_exams as $mandatory_exam) {
                        if (!in_array($mandatory_exam, $taken_exams)) {
                            $grade['grade'] = 'NG';
                            break;
                        }
                    }

                    if (empty($grade)) {
                        $grade['grade'] = $exam_sum;
                    }

                    // debug($grade);

                    $grade['fully_taken'] = count($taken_exams) >= count($exam_types);
                    $grade['course_registration_id'] = $student['CourseRegistration']['id'] ?? null;
                    $grade['course_add_id'] = $student['CourseAdd']['id'] ?? null;

                    $students[$stu_key]['GeneratedExamGrade'] = $grade;
                }
            } else {
                if (!isset($student['ExamGradeChange']) || empty($student['ExamGradeChange']) || $student['ExamGradeChange'][0]['department_approval'] == -1) {
                    $grade = [];

                    if ($student['MakeupExam']['course_registration_id'] != null) {
                        $grade['exam_grade_id'] = $this->CourseRegistrations->ExamGrades->find()
                            ->where(['ExamGrades.course_registration_id' => $student['MakeupExam']['course_registration_id']])
                            ->order(['ExamGrades.created' => 'DESC'])
                            ->first()
                            ->id ?? null;
                    } else {
                        $grade['exam_grade_id'] = $this->CourseRegistrations->ExamGrades->find()
                            ->where(['ExamGrades.course_add_id' => $student['MakeupExam']['course_add_id']])
                            ->order(['ExamGrades.created' => 'DESC'])
                            ->first()
                            ->id ?? null;
                    }

                    $grade['minute_number'] = $student['MakeupExam']['minute_number'];
                    $grade['makeup_exam_id'] = $student['MakeupExam']['id'];
                    $grade['makeup_exam_result'] = $student['MakeupExam']['result'];
                    $grade['initiated_by_department'] = 0;

                    $grade['grade'] = $student['MakeupExam']['result'] == null ? 'NG' : $student['MakeupExam']['result'];

                    // debug($grade);

                    $grade['fully_taken'] = $student['MakeupExam']['result'] != null;

                    $students[$stu_key]['GeneratedExamGrade'] = $grade;
                }
            }
        }

        return $students;
    }

    public function submitGrade(array $student_course_register_and_adds, array $students_course_in_progress, array $grade_scales, array $exam_types): array
    {
        $exam_grades = [];
        $exam_grade_changes = [];

        $students_register = $this->generateCourseGrade($student_course_register_and_adds['register'], $grade_scales, $exam_types);
        $students_add = $this->generateCourseGrade($student_course_register_and_adds['add'], $grade_scales, $exam_types);
        $students_makeup = $this->generateCourseGrade($student_course_register_and_adds['makeup'], $grade_scales, $exam_types);

        // debug($students_register);

        foreach ($students_register as $student) {
            if (isset($student['GeneratedExamGrade']) && !in_array($student['Student']['id'], $students_course_in_progress)) {
                unset($student['GeneratedExamGrade']['fully_taken']);
                $student['GeneratedExamGrade']['grade_scale_id'] = $grade_scales['GradeScale']['id'];
                $exam_grades[] = $student['GeneratedExamGrade'];
            }
        }

        foreach ($students_add as $student) {
            if (isset($student['GeneratedExamGrade']) && !in_array($student['Student']['id'], $students_course_in_progress)) {
                unset($student['GeneratedExamGrade']['fully_taken']);
                $student['GeneratedExamGrade']['grade_scale_id'] = $grade_scales['GradeScale']['id'];
                $exam_grades[] = $student['GeneratedExamGrade'];
            }
        }

        // debug($exam_grades);

        foreach ($students_makeup as $student) {
            if (isset($student['GeneratedExamGrade']) && !in_array($student['Student']['id'], $students_course_in_progress)) {
                unset($student['GeneratedExamGrade']['fully_taken']);
                $student['GeneratedExamGrade']['grade_scale_id'] = $grade_scales['GradeScale']['id'];
                $exam_grade_changes[] = $student['GeneratedExamGrade'];
            }
        }

        $grade_submit_status = [];

        if (!empty($exam_grades)) {
            if ($this->CourseRegistrations->ExamGrades->saveMany($exam_grades, ['validate' => false])) {
                if (!empty($exam_grade_changes)) {
                    if (!$this->CourseRegistrations->ExamGrades->ExamGradeChanges->saveMany($exam_grade_changes, ['validate' => false])) {
                        $grade_submit_status['error'] = "Exam grade for " . count($exam_grades) . " students is submitted but failed to record makeup exam grade result. Please make your makeup exam grade submission again.";
                    }
                }
            } else {
                $grade_submit_status['error'] = "Exam grade submission is failed. Please try again.";
            }
        } elseif (!empty($exam_grade_changes)) {
            if (!$this->CourseRegistrations->ExamGrades->ExamGradeChanges->saveMany($exam_grade_changes, ['validate' => false])) {
                $grade_submit_status['error'] = "Makeup exam grade submission is failed. Please try again.";
            }
        }

        $grade_submit_status['course_registration_add'] = $exam_grades;
        $grade_submit_status['makeup_exam'] = $exam_grade_changes;

        return $grade_submit_status;
    }

    public function submitGradeEntryAssignment(array $student_course_register_and_adds, array $students_course_in_progress, array $grade_scales, AcademicYearComponent $academicYear): array
    {
        $exam_grades = [];
        $exam_grade_changes = [];

        $students_makeup = $this->generateGradeEntryCourseGrade($student_course_register_and_adds['makeup'], $grade_scales);

        foreach ($students_makeup as $student) {
            if (isset($student['GeneratedExamGrade']) && !in_array($student['Student']['id'], $students_course_in_progress)) {
                $academicYearValue = "";
                $semester = "";

                if (!empty($student['GeneratedExamGrade']['course_add_id'])) {
                    $regAddDetail = $this->CourseAdds->find()
                        ->where(['CourseAdds.id' => $student['GeneratedExamGrade']['course_add_id']])
                        ->first();
                    $academicYearValue = $regAddDetail->academic_year;
                    $semester = $regAddDetail->semester;
                } else {
                    $regAddDetail = $this->CourseRegistrations->find()
                        ->where(['CourseRegistrations.id' => $student['GeneratedExamGrade']['course_registration_id']])
                        ->first();
                    $academicYearValue = $regAddDetail->academic_year;
                    $semester = $regAddDetail->semester;
                }

                unset($student['GeneratedExamGrade']['fully_taken']);
                $student['GeneratedExamGrade']['grade_scale_id'] = $grade_scales['GradeScale']['id'];

                $student['GeneratedExamGrade']['created'] = $academicYear->getAcademicYearBegainingDate($academicYearValue, $semester);
                $student['GeneratedExamGrade']['modified'] = $academicYear->getAcademicYearBegainingDate($academicYearValue, $semester);

                $exam_grades[] = $student['GeneratedExamGrade'];
            }
        }

        $grade_submit_status = [];

        if (!empty($exam_grades)) {
            if (!$this->CourseRegistrations->ExamGrades->saveMany($exam_grades, ['validate' => false])) {
                $grade_submit_status['error'] = "Exam grade submission is failed. Please try again.";
            }
        }

        $grade_submit_status['course_registration_add'] = $exam_grades;
        $grade_submit_status['makeup_exam'] = $exam_grade_changes;

        return $grade_submit_status;
    }

    public function getExamGradeSubmissionStatus(?int $published_course_id = null, ?array $student_course_register_and_adds = null): array
    {
        $grade_submission_status = [
            'scale_defined' => false,
            'grade_submited' => false,
            'grade_submited_partially' => false,
            'grade_submited_fully' => false,
            'grade_dpt_approved' => false,
            'grade_dpt_approved_partially' => false,
            'grade_dpt_approved_fully' => false,
            'grade_reg_approved' => false,
            'grade_reg_approved_partially' => false,
            'grade_reg_approved_fully' => false,
            'grade_dpt_rejected' => false,
            'grade_dpt_rejected_partially' => false,
            'grade_dpt_rejected_fully' => false,
            'grade_dpt_accepted' => false,
            'grade_dpt_accepted_partially' => false,
            'grade_dpt_accepted_fully' => false,
            'grade_reg_rejected' => false,
            'grade_reg_rejected_partially' => false,
            'grade_reg_rejected_fully' => false,
            'grade_reg_accepted' => false,
            'grade_reg_accepted_partially' => false,
            'grade_reg_accepted_fully' => false,
        ];

        if ($student_course_register_and_adds == null) {
            $student_course_register_and_adds = $this->CourseRegistrations->PublishedCourses->getStudentsTakingPublishedCourse($published_course_id);
        }

        $count_submited_grade = 0;
        $count_dpt_approved_grade = 0;
        $count_reg_approved_grade = 0;
        $count_dpt_accepted_grade = 0;
        $count_dpt_rejected_grade = 0;
        $count_reg_accepted_grade = 0;
        $count_reg_rejected_grade = 0;

        $students_register = $student_course_register_and_adds['register'];
        $students_add = $student_course_register_and_adds['add'];
        $students_makeup = $student_course_register_and_adds['makeup'];

        foreach ($students_register as $student) {
            if (!empty($student['ExamGrade'])) {
                $count_submited_grade++;
                if ($student['ExamGrade'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    $count_dpt_accepted_grade += $student['ExamGrade'][0]['department_approval'] == 1 ? 1 : 0;
                    $count_dpt_rejected_grade += $student['ExamGrade'][0]['department_approval'] != 1 ? 1 : 0;
                }
                if ($student['ExamGrade'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    $count_reg_accepted_grade += $student['ExamGrade'][0]['registrar_approval'] == 1 ? 1 : 0;
                    $count_reg_rejected_grade += $student['ExamGrade'][0]['registrar_approval'] != 1 ? 1 : 0;
                }
            }
        }

        foreach ($students_add as $student) {
            if (!empty($student['ExamGrade'])) {
                $count_submited_grade++;
                if ($student['ExamGrade'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    $count_dpt_accepted_grade += $student['ExamGrade'][0]['department_approval'] == 1 ? 1 : 0;
                    $count_dpt_rejected_grade += $student['ExamGrade'][0]['department_approval'] != 1 ? 1 : 0;
                }
                if ($student['ExamGrade'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    $count_reg_accepted_grade += $student['ExamGrade'][0]['registrar_approval'] == 1 ? 1 : 0;
                    $count_reg_rejected_grade += $student['ExamGrade'][0]['registrar_approval'] != 1 ? 1 : 0;
                }
            }
        }

        // debug($students_makeup);

        foreach ($students_makeup as $student) {
            if (!empty($student['ExamGradeChange'])) {
                $count_submited_grade++;
                if ($student['ExamGradeChange'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    $count_dpt_accepted_grade += $student['ExamGradeChange'][0]['department_approval'] == 1 ? 1 : 0;
                    $count_dpt_rejected_grade += $student['ExamGradeChange'][0]['department_approval'] != 1 ? 1 : 0;
                }
                if ($student['ExamGradeChange'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    $count_reg_accepted_grade += $student['ExamGradeChange'][0]['registrar_approval'] == 1 ? 1 : 0;
                    $count_reg_rejected_grade += $student['ExamGradeChange'][0]['registrar_approval'] != 1 ? 1 : 0;
                }
            }
        }

        $grade_scale = $this->CourseRegistrations->PublishedCourses->getGradeScaleDetail($published_course_id);
        if (!isset($grade_scale['error'])) {
            $grade_submission_status['scale_defined'] = true;
        }

        if ($count_submited_grade > 0) {
            $grade_submission_status['grade_submited'] = true;
        }
        if ($count_submited_grade == (count($students_add) + count($students_register) + count($students_makeup))) {
            $grade_submission_status['grade_submited_fully'] = true;
        } elseif ($count_submited_grade < (count($students_add) + count($students_register) + count($students_makeup))) {
            $grade_submission_status['grade_submited_partially'] = true;
        }

        // debug($count_dpt_approved_grade);
        // debug((count($students_add) + count($students_register)));
        // debug($students_add);
        // debug($students_register);

        if ($count_dpt_approved_grade > 0) {
            $grade_submission_status['grade_dpt_approved'] = true;
            if ($count_dpt_approved_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_approved_fully'] = true;
            } elseif ($count_dpt_approved_grade < $count_submited_grade) {
                $grade_submission_status['grade_dpt_approved_partially'] = true;
            }
        }

        if ($count_reg_approved_grade > 0) {
            $grade_submission_status['grade_reg_approved'] = true;
            if ($count_reg_approved_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_approved_fully'] = true;
            } elseif ($count_reg_approved_grade < $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_approved_partially'] = true;
            }
        }

        if ($count_dpt_rejected_grade > 0) {
            $grade_submission_status['grade_dpt_rejected'] = true;
            if ($count_dpt_rejected_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_rejected_fully'] = true;
            } else {
                $grade_submission_status['grade_dpt_rejected_partially'] = true;
            }
        }

        if ($count_dpt_accepted_grade > 0) {
            $grade_submission_status['grade_dpt_accepted'] = true;
            if ($count_dpt_accepted_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_accepted_fully'] = true;
            } else {
                $grade_submission_status['grade_dpt_accepted_partially'] = true;
            }
        }

        if ($count_reg_accepted_grade > 0) {
            $grade_submission_status['grade_reg_accepted'] = true;
            if ($count_reg_accepted_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_accepted_fully'] = true;
            } else {
                $grade_submission_status['grade_reg_accepted_partially'] = true;
            }
        }

        if ($count_reg_rejected_grade > 0) {
            $grade_submission_status['grade_reg_rejected'] = true;
            if ($count_reg_rejected_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_rejected_fully'] = true;
            } else {
                $grade_submission_status['grade_reg_rejected_partially'] = true;
            }
        }

        // debug($grade_submission_status);

        return $grade_submission_status;
    }

    public function getExamGradeEntrySubmissionStatus(?int $published_course_id = null, ?array $student_course_register_and_adds = null): array
    {
        $grade_submission_status = [
            'scale_defined' => false,
            'grade_submited' => false,
            'grade_submited_partially' => false,
            'grade_submited_fully' => false,
            'grade_dpt_approved' => false,
            'grade_dpt_approved_partially' => false,
            'grade_dpt_approved_fully' => false,
            'grade_reg_approved' => false,
            'grade_reg_approved_partially' => false,
            'grade_reg_approved_fully' => false,
            'grade_dpt_rejected' => false,
            'grade_dpt_rejected_partially' => false,
            'grade_dpt_rejected_fully' => false,
            'grade_dpt_accepted' => false,
            'grade_dpt_accepted_partially' => false,
            'grade_dpt_accepted_fully' => false,
            'grade_reg_rejected' => false,
            'grade_reg_rejected_partially' => false,
            'grade_reg_rejected_fully' => false,
            'grade_reg_accepted' => false,
            'grade_reg_accepted_partially' => false,
            'grade_reg_accepted_fully' => false,
        ];

        if ($student_course_register_and_adds == null) {
            $student_course_register_and_adds = $this->CourseRegistrations->PublishedCourses->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);
        }

        $count_submited_grade = 0;
        $count_dpt_approved_grade = 0;
        $count_reg_approved_grade = 0;
        $count_dpt_accepted_grade = 0;
        $count_dpt_rejected_grade = 0;
        $count_reg_accepted_grade = 0;
        $count_reg_rejected_grade = 0;

        $students_makeup = $student_course_register_and_adds['makeup'];

        foreach ($students_makeup as $student) {
            if (!empty($student['ExamGrade']) && !empty($student['CourseRegistration']['id'])) {
                $count_submited_grade++;
                if ($student['ExamGrade'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    $count_dpt_accepted_grade += $student['ExamGrade'][0]['department_approval'] == 1 ? 1 : 0;
                    $count_dpt_rejected_grade += $student['ExamGrade'][0]['department_approval'] != 1 ? 1 : 0;
                }
                if ($student['ExamGrade'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    $count_reg_accepted_grade += $student['ExamGrade'][0]['registrar_approval'] == 1 ? 1 : 0;
                    $count_reg_rejected_grade += $student['ExamGrade'][0]['registrar_approval'] != 1 ? 1 : 0;
                }
            }

            if (!empty($student['ExamGrade']) && !empty($student['CourseAdd']['id'])) {
                $count_submited_grade++;
                if ($student['ExamGrade'][0]['department_approval'] != null) {
                    $count_dpt_approved_grade++;
                    $count_dpt_accepted_grade += $student['ExamGrade'][0]['department_approval'] == 1 ? 1 : 0;
                    $count_dpt_rejected_grade += $student['ExamGrade'][0]['department_approval'] != 1 ? 1 : 0;
                }
                if ($student['ExamGrade'][0]['registrar_approval'] != null) {
                    $count_reg_approved_grade++;
                    $count_reg_accepted_grade += $student['ExamGrade'][0]['registrar_approval'] == 1 ? 1 : 0;
                    $count_reg_rejected_grade += $student['ExamGrade'][0]['registrar_approval'] != 1 ? 1 : 0;
                }
            }
        }

        $grade_scale = $this->CourseRegistrations->PublishedCourses->getGradeScaleDetail($published_course_id);
        if (!isset($grade_scale['error'])) {
            $grade_submission_status['scale_defined'] = true;
        }

        if ($count_submited_grade > 0) {
            $grade_submission_status['grade_submited'] = true;
        }
        if ($count_submited_grade == count($students_makeup)) {
            $grade_submission_status['grade_submited_fully'] = true;
        } elseif ($count_submited_grade < count($students_makeup)) {
            $grade_submission_status['grade_submited_partially'] = true;
        }

        if ($count_dpt_approved_grade > 0) {
            $grade_submission_status['grade_dpt_approved'] = true;
            if ($count_dpt_approved_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_approved_fully'] = true;
            } elseif ($count_dpt_approved_grade < $count_submited_grade) {
                $grade_submission_status['grade_dpt_approved_partially'] = true;
            }
        }

        if ($count_reg_approved_grade > 0) {
            $grade_submission_status['grade_reg_approved'] = true;
            if ($count_reg_approved_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_approved_fully'] = true;
            } elseif ($count_reg_approved_grade < $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_approved_partially'] = true;
            }
        }

        if ($count_dpt_rejected_grade > 0) {
            $grade_submission_status['grade_dpt_rejected'] = true;
            if ($count_dpt_rejected_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_rejected_fully'] = true;
            } else {
                $grade_submission_status['grade_dpt_rejected_partially'] = true;
            }
        }

        if ($count_dpt_accepted_grade > 0) {
            $grade_submission_status['grade_dpt_accepted'] = true;
            if ($count_dpt_accepted_grade == $count_submited_grade) {
                $grade_submission_status['grade_dpt_accepted_fully'] = true;
            } else {
                $grade_submission_status['grade_dpt_accepted_partially'] = true;
            }
        }

        if ($count_reg_accepted_grade > 0) {
            $grade_submission_status['grade_reg_accepted'] = true;
            if ($count_reg_accepted_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_accepted_fully'] = true;
            } else {
                $grade_submission_status['grade_reg_accepted_partially'] = true;
            }
        }

        if ($count_reg_rejected_grade > 0) {
            $grade_submission_status['grade_reg_rejected'] = true;
            if ($count_reg_rejected_grade == $count_dpt_approved_grade) {
                $grade_submission_status['grade_reg_rejected_fully'] = true;
            } else {
                $grade_submission_status['grade_reg_rejected_partially'] = true;
            }
        }

        // debug($grade_submission_status);

        return $grade_submission_status;
    }

    public function cancelSubmitedGrade(?int $published_course_id = null, ?array $student_course_register_and_adds = null): array
    {
        if ($student_course_register_and_adds == null) {
            $student_course_register_and_adds = $this->CourseRegistrations->PublishedCourses->getStudentsTakingPublishedCourse($published_course_id);
        }

        $exam_grades_for_deletion = [];
        $exam_grade_changes_for_deletion = [];

        $students_register = $student_course_register_and_adds['register'];
        $students_add = $student_course_register_and_adds['add'];
        $students_makeup = $student_course_register_and_adds['makeup'];

        if (!empty($students_register) && is_array($students_register)) {
            foreach ($students_register as $student) {
                if (!empty($student['ExamGrade']) && $student['ExamGrade'][0]['department_approval'] == null) {
                    $exam_grades_for_deletion[] = $student['ExamGrade'][0]['id'];
                }
            }
        }

        if (!empty($students_add) && is_array($students_add)) {
            foreach ($students_add as $student) {
                if (!empty($student['ExamGrade']) && $student['ExamGrade'][0]['department_approval'] == null) {
                    $exam_grades_for_deletion[] = $student['ExamGrade'][0]['id'];
                }
            }
        }

        if (!empty($students_makeup) && is_array($students_makeup)) {
            foreach ($students_makeup as $student) {
                if (!empty($student['ExamGradeChange']) && $student['ExamGradeChange'][0]['department_approval'] == null) {
                    $exam_grade_changes_for_deletion[] = $student['ExamGradeChange'][0]['id'];
                }
            }
        }

        $grade_cancelation_status = [];

        if (!empty($exam_grades_for_deletion)) {
            if ($this->CourseRegistrations->ExamGrades->deleteAll(['ExamGrades.id IN' => $exam_grades_for_deletion], false)) {
                if (!empty($exam_grade_changes_for_deletion)) {
                    if (!$this->CourseRegistrations->ExamGrades->ExamGradeChanges->deleteAll(['ExamGradeChanges.id IN' => $exam_grade_changes_for_deletion], false)) {
                        $grade_cancelation_status['error'] = "Exam grade cancellation for " . count($exam_grades_for_deletion) . " students is done but failed to cancel makeup exam grade. Please make your makeup exam grade cancellation again.";
                    }
                }
            } else {
                $grade_cancelation_status['error'] = "Exam grade cancellation is failed. Please try again.";
            }
        } elseif (!empty($exam_grade_changes_for_deletion)) {
            if (!$this->CourseRegistrations->ExamGrades->ExamGradeChanges->deleteAll(['ExamGradeChanges.id IN' => $exam_grade_changes_for_deletion], false)) {
                $grade_cancelation_status['error'] = "Makeup exam grade cancellation is failed. Please try again.";
            }
        }

        $grade_cancelation_status['course_registration_add'] = $exam_grades_for_deletion;
        $grade_cancelation_status['makeup_exam'] = $exam_grade_changes_for_deletion;

        return $grade_cancelation_status;
    }

    public function cancelSubmitedGradeEntry(?int $published_course_id = null, ?array $student_course_register_and_adds = null): array
    {
        if ($student_course_register_and_adds == null) {
            $student_course_register_and_adds = $this->CourseRegistrations->PublishedCourses->getStudentsRequiresGradeEntryExamPublishedCourse($published_course_id);
        }

        $exam_grades_for_deletion = [];
        $exam_grade_changes_for_deletion = [];

        $students_makeup = $student_course_register_and_adds['makeup'];

        if (!empty($students_makeup) && is_array($students_makeup)) {
            foreach ($students_makeup as $student) {
                if (!empty($student['CourseRegistration']['ExamGrade']) && $student['CourseRegistration']['ExamGrade'][0]['department_approval'] == null) {
                    $exam_grades_for_deletion[] = $student['ExamGrade'][0]['id'];
                } elseif (!empty($student['CourseAdd']['ExamGrade']) && $student['CourseAdd']['ExamGrade'][0]['department_approval'] == null) {
                    $exam_grades_for_deletion[] = $student['ExamGrade'][0]['id'];
                }
            }
        }

        $grade_cancelation_status = [];

        if (!empty($exam_grades_for_deletion)) {
            if ($this->CourseRegistrations->ExamGrades->deleteAll(['ExamGrades.id IN' => $exam_grades_for_deletion], false)) {
                if (!empty($exam_grade_changes_for_deletion)) {
                    if (!$this->CourseRegistrations->ExamGrades->ExamGradeChanges->deleteAll(['ExamGradeChanges.id IN' => $exam_grade_changes_for_deletion], false)) {
                        $grade_cancelation_status['error'] = "Exam grade cancellation for " . count($exam_grades_for_deletion) . " students is done but failed to cancel exam grade entry. Please make your exam grade entry cancellation again.";
                    }
                }
            } else {
                $grade_cancelation_status['error'] = "Exam grade cancellation is failed. Please try again.";
            }
        }

        $grade_cancelation_status['course_registration_add'] = $exam_grades_for_deletion;
        $grade_cancelation_status['makeup_exam'] = $exam_grade_changes_for_deletion;

        return $grade_cancelation_status;
    }
}
?>
