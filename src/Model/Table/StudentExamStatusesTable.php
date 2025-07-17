<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;
use Cake\Datasource\FactoryLocator;

class StudentExamStatusesTable extends Table
{
    /**
     * Initialize method
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('student_exam_statuses');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('AcademicStatuses', [
            'foreignKey' => 'academic_status_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Default validation rules
     */
    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('id')
            ->allowEmpty('id', 'create')
            ->integer('student_id')
            ->requirePresence('student_id', 'create')
            ->notEmptyString('student_id')
            ->integer('academic_status_id')
            ->allowEmptyString('academic_status_id')
            ->scalar('academic_year')
            ->maxLength('academic_year', 9)
            ->allowEmptyString('academic_year')
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->allowEmptyString('semester');

        return $validator;
    }

    /**
     * Returns student's exam status based on course registration and scores
     */
    public function getStudentExamStatus(?int $studentId = null, ?string $academicYear = null, ?string $semester = null)
    {
        $result = $this->isStudentPassed($studentId, $academicYear);


        if ($result) {
            return $result; // Returns 1, 2, 3, 4, or 5 based on isStudentPassed
        }

        $readmitted = $this->Students->Readmissions->isReadmitted($studentId, $academicYear, $semester);
        $probation = 0; // TODO: Replace with probation logic

        return ($readmitted || $probation) ? 3 : false;
    }

    /**
     * Gets the last exam status for a student
     */
    public function getStudentLastExamStatus(?int $studentId = null, ?string $academicYear = null): int
    {
        $result = $this->find()
            ->where(['student_id' => $studentId])
            ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
            ->first();

        if (!$result) {
            return 1; // First time
        }

        if ($result->academic_status_id == 4) {
            $readmitted = $this->Students->Readmissions->isReadmitted($studentId, $academicYear);
            $probation = 0; // TODO: Replace with probation logic
            return ($readmitted || $probation) ? 3 : 4; // Dismissed
        }

        return 3; // Okay
    }

    /**
     * Gets academic rules for students
     */
    public function academicRulesOfStudents(string $academicYear): array
    {
        return $this->Students->find()
            ->innerJoinWith('GraduateLists', function (Query $q) {
                return $q->where(['Students.id = GraduateLists.student_id']);
            })
            ->toArray();
    }

    /**
     * Checks student eligibility for services
     */
    public function eligibleForService(?int $studentId = null, ?string $currentAcademicYear = null): int
    {
        $courseRegistrationTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $listOfSemesterAcademicYear = $courseRegistrationTable->ExamGrades->getListOfAyAndSemester($studentId);
        $lastRegisteredSemesterAcademicYear = end($listOfSemesterAcademicYear);

        $studentLastStatus = $this->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $lastRegisteredSemesterAcademicYear['academic_year'],
                'semester' => $lastRegisteredSemesterAcademicYear['semester']
            ])
            ->first();

        $lastStatusSemesterAcademicYear = $lastRegisteredSemesterAcademicYear;

        if (!$studentLastStatus && count($listOfSemesterAcademicYear) > 1) {
            $lastStatusSemesterAcademicYear = $listOfSemesterAcademicYear[count($listOfSemesterAcademicYear) - 2];
            $studentLastStatus = $this->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_year' => $lastStatusSemesterAcademicYear['academic_year'],
                    'semester' => $lastStatusSemesterAcademicYear['semester']
                ])
                ->first();
        }

        if (!$studentLastStatus) {
            if ($this->Students->Dismissals->dismissedBecauseOfDisciplinaryAfterRegistrationNotReadmitted($studentId, $currentAcademicYear)) {
                $this->invalidate('error', 'The student is dismissed because of disciplinary after registration. S/he is not eligible to get meal service.');
                return 0;
            }
            if ($this->Students->Clearances->clearedAfterRegistration($studentId)) {
                $this->invalidate('error', 'The student is cleared after registration. S/he is not eligible to get meal service.');
                return 0;
            }
            if ($this->Students->DropOuts->dropOutAfterLastRegistration($studentId, $currentAcademicYear)) {
                $this->invalidate('error', 'The student is drop out after registration. S/he is not eligible to get meal service.');
                return 0;
            }
            return 1;
        }

        if ($this->isStudentPassed($studentId, $lastStatusSemesterAcademicYear['academic_year'])) {
            if ($this->Students->Clearances->withDrawAfterLastStatusButNotReadmitted($studentId, $currentAcademicYear)) {
                $this->invalidate('error', 'The student is withdrawn after his/her last academic status, and not readmitted. S/he is not eligible to get meal service.');
                return 0;
            }
            if ($this->Students->Dismissals->dismissedBecauseOfDisciplinaryNotReadmitted($studentId, $currentAcademicYear)) {
                $this->invalidate('error', 'The student is dismissed because of disciplinary. S/he is not eligible to get meal service.');
                return 0;
            }
            if ($this->Students->DropOuts->dropOutAfterLastRegistration($studentId, $currentAcademicYear)) {
                $this->invalidate('error', 'The student is drop out after last registration. S/he is not eligible to get meal service.');
                return 0;
            }
            return 1;
        }

        if ($this->Students->Readmissions->isReadmitted($studentId, $currentAcademicYear)) {
            return 1; // Allow
        }

        $this->invalidate('error', 'The student is dismissed. S/he is not eligible to get meal service.');
        return 0; // Deny
    }

    /**
     * Checks eligibility for service with specific conditions
     */
    public function isEligibleForService(int $studentId, ?string $currentAcademicYear = null): int
    {
        $studentLastStatus = $this->find()
            ->where(['student_id' => $studentId])
            ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
            ->first();

        if (!$studentLastStatus) {
            if ($this->Students->Dismissals->dismissedBecauseOfDisciplinaryAfterRegistrationNotReadmitted($studentId, $currentAcademicYear)) {
                return 1;
            }
            if ($this->Students->Clearances->clearedAfterRegistration($studentId)) {
                return 2;
            }
            if ($this->Students->DropOuts->dropOutAfterLastRegistration($studentId, $currentAcademicYear)) {
                return 3;
            }
            return 1;
        }

        if ($this->isStudentPassed($studentId, $studentLastStatus->academic_year)) {
            if ($this->Students->Clearances->withDrawAfterLastStatusButNotReadmitted($studentId, $currentAcademicYear)) {
                return 4;
            }
            if ($this->Students->Dismissals->dismissedBecauseOfDisciplinaryNotReadmitted($studentId, $currentAcademicYear)) {
                return 5;
            }
            if ($this->Students->DropOuts->dropOutAfterLastRegistration($studentId, $currentAcademicYear)) {
                return 6;
            }

            $previousAc = $this->getPreviousSemester($currentAcademicYear);
            $previousStatus = $this->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_status_id !=' => 4,
                    'academic_year' => $previousAc['academic_year']
                ])
                ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
                ->first();

            return empty($previousStatus) ? 7 : 1;
        }

        if ($this->Students->Readmissions->isReadmitted($studentId, $currentAcademicYear)) {
            return 1; // Allow
        }
        if ($this->checkFxPresenceInStatus($studentId) == 0) {
            return 1; // Allow
        }
        return 7; // Deny
    }

    /**
     * Checks if student has passed
     */
    public function isStudentPassed(?int $studentId = null, ?string $academicYear = null): int
    {
        $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $listOfSemesterAcademicYear = $courseRegistrationTable->ExamGrades->getListOfAyAndSemester($studentId);

        if (empty($listOfSemesterAcademicYear)) {
            return 1; // First time
        }

        $lastRegisteredSemesterAcademicYear = null;
        if (!empty($academicYear)) {
            $sem = "I";
            foreach ($listOfSemesterAcademicYear as $v) {
                if ($v["academic_year"] == $academicYear && $sem >= $v['semester']) {
                    $lastRegisteredSemesterAcademicYear = [
                        'academic_year' => $v["academic_year"],
                        'semester' => $v["semester"]
                    ];
                }
            }
        }

        if (empty($lastRegisteredSemesterAcademicYear)) {
            $lastRegisteredSemesterAcademicYear = end($listOfSemesterAcademicYear);
        }

        $previousACSem = $this->getPreviousSemester(
            $lastRegisteredSemesterAcademicYear['academic_year'],
            $lastRegisteredSemesterAcademicYear['semester']
        );

        if (!empty($previousACSem['academic_year'])) {
            $courseAddTable =  TableRegistry::getTableLocator()->get('CourseAdds');
            $firstAdded = $courseAddTable->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_year' => $previousACSem['academic_year'],
                    'semester' => $previousACSem['semester'],
                    'department_approval' => 1,
                    'registrar_confirmation' => 1
                ])
                ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
                ->first();

            $firstRegistered = $courseRegistrationTable->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_year' => $previousACSem['academic_year'],
                    'semester' => $previousACSem['semester']
                ])
                ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
                ->first();

            if (empty($firstAdded) && empty($firstRegistered)) {
                return 1; // First time
            }
        }

        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $studentDetail = $studentsTable->find()
            ->where(['id' => $studentId])
            ->first();

        $programTypeTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
        $programTypeId = $programTypeTable->getParentProgramType($studentDetail->program_type_id);
        $pattern = $programTypeTable->StudentStatusPatterns->getProgramTypePattern(
            $studentDetail->program_id,
            $programTypeId,
            $lastRegisteredSemesterAcademicYear['academic_year']
        );

        $query = $this->find()
            ->where(['student_id' => $studentId])
            ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC', 'created' => 'DESC', 'academic_status_id' => 'DESC']);

        if ($pattern <= 1) {
            $query->where([
                'semester' => $lastRegisteredSemesterAcademicYear['semester'],
                'academic_year' => $lastRegisteredSemesterAcademicYear['academic_year']
            ]);
        }

        $studentStatuses = $query->first();

        if (!$studentStatuses) {
            $check = $this->onlyRegisteredPassFailGradeType(
                $studentId,
                $lastRegisteredSemesterAcademicYear['academic_year'],
                $lastRegisteredSemesterAcademicYear['semester']
            );
            return $check ? 3 : 2; // Status not generated
        }

        if (!empty($studentStatuses->academic_status_id) && $studentStatuses->academic_status_id != DISMISSED_ACADEMIC_STATUS_ID) {
            return 3; // Okay
        }

        if ($this->onlyRegisteredPassFailGradeType(
            $studentId,
            $lastRegisteredSemesterAcademicYear['academic_year'],
            $lastRegisteredSemesterAcademicYear['semester']
        )) {
            return 3; // Okay
        }

        $academicCalendarTable = TableRegistry::getTableLocator()->get('AcademicCalendars');
        if ($studentsTable->maxCreditExcludingI(
                $studentId,
                $lastRegisteredSemesterAcademicYear['semester'],
                $lastRegisteredSemesterAcademicYear['academic_year']
            ) < $academicCalendarTable->minimumCreditForStatus($studentId)) {
            return 3; // Okay
        }

        if (!empty($studentStatuses) && is_null($studentStatuses->academic_status_id)) {
            $readmitted = $studentsTable->Readmissions->isReadmitted($studentId, $academicYear);
            return $readmitted ? 3 : 5; // Status assignment not done
        }

        $readmitted = $studentsTable->Readmissions->isReadmitted($studentId, $academicYear);
        return $readmitted ? 3 : 4; // Dismissed
    }

    /**
     * Checks if only pass/fail grade types are registered
     */
    public function onlyRegisteredPassFailGradeType(int $studentId, string $academicYear, string $semester): bool
    {
        $courseRegistrationTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $registrationList = $courseRegistrationTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['GradeTypes']
                ]
            ])
            ->toArray();

        if (!empty($registrationList)) {
            foreach ($registrationList as $registration) {
                if (!empty($registration->published_course->course->grade_type->id) &&
                    $registration->published_course->course->grade_type->used_in_gpa) {
                    return false;
                }
            }
            return true;
        }

        return false;
    }

    /**
     * Gets previous semester
     */
    public function getPreviousSemester(?string $academicYear = null, ?string $semester = null): array
    {
        $result = [];

        if ($semester == 'III') {
            $result['academic_year'] = $academicYear;
            $result['semester'] = 'II';
        } elseif ($semester == 'II') {
            $result['academic_year'] = $academicYear;
            $result['semester'] = 'I';
        } else {
            $result['academic_year'] = (substr($academicYear, 0, 4) - 1) . '/' . substr($academicYear, 2, 2);
            $result['semester'] = 'III';
        }

        return $result;
    }

    /**
     * Gets next semester
     */
    public function getNextSemester(?string $academicYear = null, ?string $semester = null): array
    {
        $result = [];

        if ($semester == 'I') {
            $result['academic_year'] = $academicYear;
            $result['semester'] = 'II';
        } elseif ($semester == 'II') {
            $result['academic_year'] = $academicYear;
            $result['semester'] = 'III';
        } else {
            $year = substr($academicYear, 0, 4) + 1;
            $result['academic_year'] = $year . '/' . substr($year + 1, 2, 2);
            $result['semester'] = 'I';
        }

        return $result;
    }

    /**
     * Gets academic year and semester list for status generation
     */
    public function getAcademicYearAndSemesterListToGenerateStatus(?int $studentId = null, ?string $academicYear = null, ?string $semester = null): array
    {
        $ayAndSList = [];
        $nextAyAndS = [];

        $lastExamStatus = $this->find()
            ->where(['student_id' => $studentId])
            ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC', 'created' => 'DESC'])
            ->first();

        $prepared = $this->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->count();

        if ($prepared > 0) {
            return $ayAndSList;
        }

        $courseAddTable =  TableRegistry::getTableLocator()->get('CourseAdds');
        $courseRegistrationTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        if (!$lastExamStatus) {
            $firstAdded = $courseAddTable->find()
                ->where([
                    'student_id' => $studentId,
                    'registrar_confirmation' => 1
                ])
                ->contain(['PublishedCourses'])
                ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
                ->toArray();

            if (!empty($firstAdded)) {
                foreach ($firstAdded as $value) {
                    if ($value->published_course->add == 1 || ($value->department_approval == 1 && $value->registrar_confirmation == 1)) {
                        $firstAdded = $value;
                        unset($firstAdded->published_course);
                        break;
                    }
                }
            }

            $firstRegistered = $courseRegistrationTable->find()
                ->where(['student_id' => $studentId])
                ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
                ->first();

            $clearanceTable =  TableRegistry::getTableLocator()->get('Clearances');
            $withdrawalForTheFirstTimeAfterRegistration = $clearanceTable->withDrawAfterFirstTimeRegistration(
                $studentId,
                $academicYear,
                $semester
            );

            if (empty($firstAdded) && empty($firstRegistered)) {
                return $nextAyAndS;
            }

            if (!empty($firstRegistered) && (empty($firstAdded) || ($firstAdded->created > $firstRegistered->created))) {
                $previousAyAndS = $this->getPreviousSemester(
                    $firstRegistered->academic_year,
                    $firstRegistered->semester
                );
                $nextAyAndS = [
                    'academic_year' => $previousAyAndS['academic_year'],
                    'semester' => $previousAyAndS['semester']
                ];
            } else {
                $previousAyAndS = $this->getPreviousSemester(
                    $firstAdded->academic_year,
                    $firstAdded->semester
                );
                $nextAyAndS = [
                    'academic_year' => $previousAyAndS['academic_year'],
                    'semester' => $previousAyAndS['semester']
                ];
            }
        } else {
            $nextAyAndS = [
                'academic_year' => $lastExamStatus->academic_year,
                'semester' => $lastExamStatus->semester
            ];
        }

        $count = 1;

        do {
            $count++;
            if ($count > 100) {
                break;
            }

            $nextAyAndS = $this->getNextSemester($nextAyAndS['academic_year'], $nextAyAndS['semester']);

            $courseRegistered = $courseRegistrationTable->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_year' => $nextAyAndS['academic_year'],
                    'semester' => $nextAyAndS['semester']
                ])
                ->count();

            $courseAdds = $courseAddTable->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_year' => $nextAyAndS['academic_year'],
                    'semester' => $nextAyAndS['semester']
                ])
                ->contain(['PublishedCourses'])
                ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
                ->toArray();

            $courseAdded = 0;
            if (!empty($courseAdds)) {
                foreach ($courseAdds as $value) {
                    if ($value->published_course->add == 1 || ($value->department_approval == 1 && $value->registrar_confirmation == 1)) {
                        $courseAdded++;
                    }
                }
            }

            if ($courseRegistered > 0 || $courseAdded > 0) {
                $index = count($ayAndSList);
                $ayAndSList[$index] = [
                    'academic_year' => $nextAyAndS['academic_year'],
                    'semester' => $nextAyAndS['semester']
                ];
            }
        } while (!(strcasecmp($academicYear, $nextAyAndS['academic_year']) == 0 && strcasecmp($semester, $nextAyAndS['semester']) == 0));

        if (empty($ayAndSList) && empty($lastExamStatus)) {
            $index = count($ayAndSList);
            $first = $this->getStudentFirstAyAndSemester($studentId);
            $ayAndSList[$index] = [
                'academic_year' => $first['academic_year'],
                'semester' => $first['semester']
            ];
        }

        if (!empty($ayAndSList)) {
            $examGradeTable =  TableRegistry::getTableLocator()->get('ExamGrades');
            $clearanceTable =  TableRegistry::getTableLocator()->get('Clearances');

            foreach ($ayAndSList as $k => &$v) {
                $withdrawalAfterRegistration = $clearanceTable->withDrawAfterRegistration(
                    $studentId,
                    $v['academic_year'],
                    $v['semester']
                );
                $gradeSubmittedForAnyCourse = $examGradeTable->gradeSubmittedForAYSem(
                    $studentId,
                    $v['academic_year'],
                    $v['semester']
                );

                if ($withdrawalAfterRegistration && $gradeSubmittedForAnyCourse == 0) {
                    unset($ayAndSList[$k]);
                }
            }
        }

        return $ayAndSList;
    }

    /**
     * Gets student's first academic year and semester
     */
    public function getStudentFirstAyAndSemester(?int $studentId = null, ?string $admissionAY = null): array
    {
        $courseRegistrationTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $acSemesterList = $courseRegistrationTable->ExamGrades->getListOfAyAndSemester($studentId);
        $aySemester = [];

        if (!empty($acSemesterList)) {
            $first = reset($acSemesterList);
            $aySemester = [
                'academic_year' => $first['academic_year'],
                'semester' => $first['semester']
            ];
        } elseif (!empty($admissionAY)) {
            $aySemester = [
                'academic_year' => $admissionAY,
                'semester' => 'I'
            ];
        }

        return $aySemester;
    }

    /**
     * Gets academic year and semester list for status update
     */
    public function getAcademicYearAndSemesterListToUpdateStatus(?int $studentId = null, ?string $academicYear = null, ?string $semester = null): array
    {
        $ayAndSList = [];
        $nextAyAndS = [];

        $studentStatuses = $this->find()
            ->where(['StudentExamStatuses.student_id' => $studentId])
            ->order(['StudentExamStatuses.academic_year' => 'ASC', 'StudentExamStatuses.semester' => 'ASC',
                'StudentExamStatuses.id' => 'ASC', 'StudentExamStatuses.created' => 'ASC'])
            ->toArray();

        $lastExamStatus = [];
        if (!empty($studentStatuses)) {
            foreach ($studentStatuses as $studentStatus) {
                if (strcasecmp($studentStatus->academic_year, $academicYear) == 0 &&
                    strcasecmp($studentStatus->semester, $semester) == 0) {
                    break;
                }
                $lastExamStatus = $studentStatus;
            }
        }

        $courseAddTable =  TableRegistry::getTableLocator()->get('CourseAdds');
        $courseRegistrationTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');

        if (empty($lastExamStatus)) {
            $firstAdded = $courseAddTable->find()
                ->where(['student_id' => $studentId])
                ->contain(['PublishedCourses'])
                ->order(['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'])
                ->toArray();

            if (!empty($firstAdded)) {
                foreach ($firstAdded as $value) {
                    if ($value->published_course->add == 1 || ($value->department_approval == 1 && $value->registrar_confirmation == 1)) {
                        $firstAdded = $value;
                        unset($firstAdded->published_course);
                        break;
                    }
                }
            }

            $firstRegistered = $courseRegistrationTable->find()
                ->where(['student_id' => $studentId])
                ->order(['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC'])
                ->first();

            if (empty($firstAdded) && empty($firstRegistered)) {
                return $nextAyAndS;
            }

            if (!empty($firstRegistered) && (empty($firstAdded) || ($firstAdded->created > $firstRegistered->created))) {
                $previousAyAndS = $this->getPreviousSemester(
                    $firstRegistered->academic_year,
                    $firstRegistered->semester
                );
                $nextAyAndS = [
                    'academic_year' => $previousAyAndS['academic_year'],
                    'semester' => $previousAyAndS['semester']
                ];
            } else {
                $previousAyAndS = $this->getPreviousSemester(
                    $firstAdded->academic_year,
                    $firstAdded->semester
                );
                $nextAyAndS = [
                    'academic_year' => $previousAyAndS['academic_year'],
                    'semester' => $previousAyAndS['semester']
                ];
            }
        } else {
            $nextAyAndS = [
                'academic_year' => $lastExamStatus->academic_year,
                'semester' => $lastExamStatus->semester
            ];
        }

        $count = 1;

        do {
            $count++;
            if ($count > 100) {
                break;
            }

            $nextAyAndS = $this->getNextSemester($nextAyAndS['academic_year'], $nextAyAndS['semester']);

            $courseRegistered = $courseRegistrationTable->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_year' => $nextAyAndS['academic_year'],
                    'semester' => $nextAyAndS['semester']
                ])
                ->count();

            $courseAdds = $courseAddTable->find()
                ->where([
                    'CourseAdds.student_id' => $studentId,
                    'CourseAdds.academic_year' => $nextAyAndS['academic_year'],
                    'CourseAdds.semester' => $nextAyAndS['semester']
                ])
                ->contain(['PublishedCourses'])
                ->order(['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'])
                ->toArray();

            $courseAdded = 0;
            if (!empty($courseAdds)) {
                foreach ($courseAdds as $value) {
                    if ($value->published_course->add == 1 || ($value->department_approval == 1 && $value->registrar_confirmation == 1)) {
                        $courseAdded++;
                    }
                }
            }

            if ($courseRegistered > 0 || $courseAdded > 0) {
                $index = count($ayAndSList);
                $ayAndSList[$index] = [
                    'academic_year' => $nextAyAndS['academic_year'],
                    'semester' => $nextAyAndS['semester']
                ];
            }
        } while (!(strcasecmp($academicYear, $nextAyAndS['academic_year']) == 0 && strcasecmp($semester, $nextAyAndS['semester']) == 0));

        return $ayAndSList;
    }

    /**
     * Gets student year and semester level of status
     */
    public function studentYearAndSemesterLevelOfStatus(int $studentId, string $academicYear, string $semester): array
    {
        $studentStatuses = $this->find()
            ->where([
                'student_id' => $studentId,
                'academic_status_id IS NOT NULL',
                'academic_status_id !=' => 4
            ])
            ->order(['academic_year' => 'ASC', 'semester' => 'ASC'])
            ->toArray();

        $semesterCount = 0;
        foreach ($studentStatuses as $studentStatus) {
            if (strcasecmp($studentStatus->academic_year, $academicYear) == 0 &&
                strcasecmp($studentStatus->semester, $semester) == 0) {
                break;
            }
            $semesterCount++;
        }

        $yearLevel = ((int)($semesterCount / 2)) + 1;
        $semesterLevel = ($semesterCount % 2 > 0) ? 'II' : 'I';

        return [
            'year' => $yearLevel,
            'semester' => $semesterLevel
        ];
    }

    /**
     * Checks for TCW rule in dismissal
     */
    public function isThereTcwRuleInDismissal(?int $studentId = null, ?int $programId = null, ?string $academicYear = null,
        ?string $semester = null, ?string $yearLevel = null, ?string $semesterLevel = null,
        ?string $admissionYear = null): int
    {
        $academicStandTable =  TableRegistry::getTableLocator()->get('AcademicStands');
        $academicStands = $academicStandTable->find()
            ->where([
                'academic_status_id' => 4,
                'program_id' => $programId
            ])
            ->order(['academic_year_from' => 'ASC'])
            ->toArray();

        $as = null;
        foreach ($academicStands as $academicStand) {
            $standYearLevels = unserialize($academicStand->year_level_id);
            $standSemesters = unserialize($academicStand->semester);

            if (in_array($yearLevel, $standYearLevels) && in_array($semesterLevel, $standSemesters)) {
                if ((substr($admissionYear, 0, 4) >= $academicStand->academic_year_from) ||
                    ($academicStand->applicable_for_all_current_student == 1 && substr($academicYear, 0, 4) >= $academicStand->academic_year_from)) {
                    $as = $academicStand;
                }
            }
        }

        if (!empty($as)) {
            $academicRuleTable =  TableRegistry::getTableLocator()->get('AcademicRules');
            $academicRules = $academicRuleTable->find()
                ->where(['academic_stand_id' => $as->id])
                ->toArray();

            foreach ($academicRules as $academicRule) {
                if ($academicRule->tcw == 1) {
                    return 1;
                }
            }
        }

        return 0;
    }

    /**
     * Checks for PFW rule in dismissal
     */
    public function isTherePfwRuleInDismissal(?int $studentId = null, ?int $programId = null, ?string $academicYear = null,
        ?string $semester = null, ?string $yearLevel = null, ?string $semesterLevel = null,
        ?string $admissionYear = null): bool
    {
        $academicStandTable =  TableRegistry::getTableLocator()->get('AcademicStands');
        $academicStands = $academicStandTable->find()
            ->where([
                'academic_status_id' => 4,
                'program_id' => $programId
            ])
            ->order(['academic_year_from' => 'ASC'])
            ->toArray();

        $as = null;
        foreach ($academicStands as $academicStand) {
            $standYearLevels = unserialize($academicStand->year_level_id);
            $standSemesters = unserialize($academicStand->semester);

            if (in_array($yearLevel, $standYearLevels) && in_array($semesterLevel, $standSemesters)) {
                if ((substr($admissionYear, 0, 4) >= $academicStand->academic_year_from) ||
                    ($academicStand->applicable_for_all_current_student == 1 && substr($academicYear, 0, 4) >= $academicStand->academic_year_from)) {
                    $as = $academicStand;
                }
            }
        }

        if (!empty($as)) {
            $academicRuleTable =  TableRegistry::getTableLocator()->get('AcademicRules');
            $academicRules = $academicRuleTable->find()
                ->where(['academic_stand_id' => $as->id])
                ->toArray();

            foreach ($academicRules as $academicRule) {
                if ($academicRule->pfw == 1) {
                    return true;
                }
            }
        }

        return false;
    }



    /**
     * Updates academic status by published course
     */
    public function updateAcademicStatusByPublishedCourse(?int $publishedCourseId = null): bool
    {
        $fullySaved = true;
        $studentExamStatus = [];

        $publishedCourseTable =  TableRegistry::getTableLocator()->get('PublishedCourses');
        $registeredStudents = $publishedCourseTable->find()
            ->where(['id' => $publishedCourseId])
            ->contain([
                'CourseRegistrations' => [
                    'Students' => function (Query $q) {
                        return $q->where(['graduated' => 0]);
                    }
                ]
            ])
            ->first();

        $addedStudents = $publishedCourseTable->find()
            ->where(['id' => $publishedCourseId])
            ->contain([
                'CourseAdds' => [
                    'Students' => function (Query $q) {
                        return $q->where(['graduated' => 0]);
                    }
                ]
            ])
            ->first();

        $registeredAddedStudents = [];
        if (!empty($registeredStudents->course_registrations)) {
            foreach ($registeredStudents->course_registrations as $value) {
                if (!empty($value->student->id) && !$this->isStudentInArray($value->student->id, $registeredAddedStudents)) {
                    $registeredAddedStudents[] = $value;
                }
            }
        }

        if (!empty($addedStudents->course_adds)) {
            foreach ($addedStudents->course_adds as $value) {
                if (!empty($value->student->id) && !$this->isStudentInArray($value->student->id, $registeredAddedStudents)) {
                    $registeredAddedStudents[] = $value;
                }
            }
        }

        $academicYear = $registeredStudents->academic_year ?? null;
        $semester = $registeredStudents->semester ?? null;

        if (!empty($registeredAddedStudents)) {
            foreach ($registeredAddedStudents as $courseRegistration) {
                if ($courseRegistration->student->graduated == 0) {
                    $programTypeTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
                    $programTypeTransferTable =  TableRegistry::getTableLocator()->get('ProgramTypeTransfers');
                    $programTypeId = $programTypeTransferTable->getStudentProgramType(
                        $courseRegistration->student->id,
                        $academicYear,
                        $semester
                    );
                    $programTypeId = $programTypeTable->getParentProgramType($programTypeId);

                    $pattern = $programTypeTable->StudentStatusPatterns->getProgramTypePattern(
                        $courseRegistration->student->program_id,
                        $programTypeId,
                        $academicYear
                    );

                    $ayAndSList = $this->getAcademicYearAndSemesterListToGenerateStatus(
                        $courseRegistration->student->id,
                        $academicYear,
                        $semester
                    );

                    $courseRegistrationTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
                    $lastPattern = $programTypeTable->StudentStatusPatterns->isLastSemesterInCurriculum(
                        $courseRegistration->student->id
                    );

                    $lastRegisteredSem = $courseRegistrationTable->find()
                        ->where(['student_id' => $courseRegistration->student->id])
                        ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
                        ->first();

                    if ($lastPattern && $lastRegisteredSem->academic_year == $academicYear &&
                        $lastRegisteredSem->semester == $semester) {
                        $pattern = 1;
                    }

                    if (empty($ayAndSList)) {
                        // Status already generated
                        continue;
                    }

                    if (count($ayAndSList) >= $pattern) {
                        $creditHourSum = 0;
                        $gradePointSum = 0;
                        $mCreditHourSum = 0;
                        $mGradePointSum = 0;
                        $deductCreditHourSum = 0;
                        $deductGradePointSum = 0;
                        $mDeductCreditHourSum = 0;
                        $mDeductGradePointSum = 0;
                        $complete = true;
                        $firstAcademicYear = null;
                        $firstSemester = null;
                        $processedCourseReg = [];
                        $processedCourseAdd = [];

                        $allAySList = $courseRegistrationTable->ExamGrades->getListOfAyAndSemester(
                            $courseRegistration->student->id,
                            $ayAndSList[0]['academic_year'],
                            $ayAndSList[0]['semester']
                        );

                        foreach ($ayAndSList as $ayAndS) {
                            $aysIndex = count($allAySList);
                            $allAySList[$aysIndex] = [
                                'academic_year' => $ayAndS['academic_year'],
                                'semester' => $ayAndS['semester']
                            ];

                            if ($firstAcademicYear === null) {
                                $firstAcademicYear = $ayAndS['academic_year'];
                                $firstSemester = $ayAndS['semester'];
                            }

                            $courseAndGrades = $courseRegistrationTable->ExamGrades->getStudentCoursesAndFinalGrade(
                                $courseRegistration->student->id,
                                $ayAndS['academic_year'],
                                $ayAndS['semester']
                            );

                            if (!empty($courseAndGrades)) {
                                foreach ($courseAndGrades as $registeredAddedCourse) {
                                    if (!(isset($registeredAddedCourse['grade']) &&
                                        (isset($registeredAddedCourse['point_value']) ||
                                            strcasecmp($registeredAddedCourse['grade'], 'I') == 0 ||
                                            strcasecmp($registeredAddedCourse['grade'], 'W') == 0))) {
                                        $complete = false;
                                        break 2;
                                    }

                                    if (isset($registeredAddedCourse['grade']) &&
                                        (strcasecmp($registeredAddedCourse['grade'], 'I') == 0 ||
                                            strcasecmp($registeredAddedCourse['grade'], 'W') == 0 ||
                                            strcasecmp($registeredAddedCourse['grade'], 'NG') == 0)) {
                                        $complete = false;
                                        break 2;
                                    }

                                    if (strcasecmp($registeredAddedCourse['grade'], 'I') != 0 &&
                                        isset($registeredAddedCourse['used_in_gpa']) &&
                                        $registeredAddedCourse['used_in_gpa']) {
                                        $creditHourSum += $registeredAddedCourse['credit'];
                                        $gradePointSum += ($registeredAddedCourse['credit'] * $registeredAddedCourse['point_value']);

                                        if ($registeredAddedCourse['major'] == 1) {
                                            $mCreditHourSum += $registeredAddedCourse['credit'];
                                            $mGradePointSum += ($registeredAddedCourse['credit'] * $registeredAddedCourse['point_value']);
                                        }
                                    }
                                }
                            }
                        }

                        if ($complete && $creditHourSum > 0) {
                            $courseAddTable =  TableRegistry::getTableLocator()->get('CourseAdds');
                            $creditAndPointDeduction = $courseAddTable->ExamGrades->getTotalCreditAndPointDeduction(
                                $courseRegistration->student->id,
                                $allAySList
                            );

                            $deductCreditHourSum = $creditAndPointDeduction['deduct_credit_hour_sum'];
                            $deductGradePointSum = $creditAndPointDeduction['deduct_grade_point_sum'];
                            $mDeductCreditHourSum = $creditAndPointDeduction['m_deduct_credit_hour_sum'];
                            $mDeductGradePointSum = $creditAndPointDeduction['m_deduct_grade_point_sum'];

                            $statIndex = count($studentExamStatus);
                            $studentExamStatus[$statIndex] = $this->newEntity([
                                'student_id' => $courseRegistration->student->id,
                                'academic_year' => $academicYear,
                                'semester' => $semester,
                                'grade_point_sum' => $gradePointSum,
                                'credit_hour_sum' => $creditHourSum,
                                'm_grade_point_sum' => $mGradePointSum,
                                'm_credit_hour_sum' => $mCreditHourSum,
                                'sgpa' => ($gradePointSum > 0 && $creditHourSum > 0) ? $gradePointSum / $creditHourSum : 0
                            ]);

                            $statusHistories = $this->find()
                                ->where(['student_id' => $courseRegistration->student->id])
                                ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'created' => 'ASC'])
                                ->toArray();

                            $lastExamStatus = [];
                            $cumulativeGradePoint = $studentExamStatus[$statIndex]->grade_point_sum;
                            $cumulativeCreditHour = $studentExamStatus[$statIndex]->credit_hour_sum;
                            $mCumulativeGradePoint = $studentExamStatus[$statIndex]->m_grade_point_sum;
                            $mCumulativeCreditHour = $studentExamStatus[$statIndex]->m_credit_hour_sum;

                            foreach ($statusHistories as $statusHistory) {
                                if (!(strcasecmp($statusHistory->academic_year, $academicYear) == 0 &&
                                    strcasecmp($statusHistory->semester, $semester) == 0)) {
                                    $cumulativeGradePoint += $statusHistory->grade_point_sum;
                                    $cumulativeCreditHour += $statusHistory->credit_hour_sum;
                                    $mCumulativeGradePoint += $statusHistory->m_grade_point_sum;
                                    $mCumulativeCreditHour += $statusHistory->m_credit_hour_sum;
                                    $lastExamStatus = $statusHistory;
                                } else {
                                    break;
                                }
                            }

                            $studentExamStatus[$statIndex]->cgpa = (($cumulativeGradePoint - $deductGradePointSum) > 0 &&
                                ($cumulativeCreditHour - $deductCreditHourSum) > 0) ?
                                (($cumulativeGradePoint - $deductGradePointSum) / ($cumulativeCreditHour - $deductCreditHourSum)) : 0;

                            $studentExamStatus[$statIndex]->mcgpa = (($mCumulativeGradePoint - $mDeductGradePointSum) > 0 &&
                                ($mCumulativeCreditHour - $mDeductCreditHourSum) > 0) ?
                                (($mCumulativeGradePoint - $mDeductGradePointSum) / ($mCumulativeCreditHour - $mDeductCreditHourSum)) : 0;

                            $studentLevel = $this->studentYearAndSemesterLevelOfStatus(
                                $courseRegistration->student->id,
                                $academicYear,
                                $semester
                            );

                            $yearLevelFormatted = '';
                            switch ($studentLevel['year']) {
                                case 1:
                                    $yearLevelFormatted = $studentLevel['year'] . 'st';
                                    break;
                                case 2:
                                    $yearLevelFormatted = $studentLevel['year'] . 'nd';
                                    break;
                                case 3:
                                    $yearLevelFormatted = $studentLevel['year'] . 'rd';
                                    break;
                                default:
                                    $yearLevelFormatted = $studentLevel['year'] . 'th';
                                    break;
                            }

                            $academicStatusTable =  TableRegistry::getTableLocator()->get('AcademicStatuses');
                            $academicStatuses = $academicStatusTable->find()
                                ->where(['computable' => 1])
                                ->order(['order' => 'ASC'])
                                ->toArray();

                            foreach ($academicStatuses as $academicStatus) {
                                $academicStandTable =  TableRegistry::getTableLocator()->get('AcademicStands');
                                $academicStands = $academicStandTable->find()
                                    ->where([
                                        'academic_status_id' => $academicStatus->id,
                                        'program_id' => $courseRegistration->student->program_id
                                    ])
                                    ->order(['academic_year_from' => 'ASC'])
                                    ->toArray();

                                $as = null;
                                foreach ($academicStands as $academicStand) {
                                    $standYearLevels = unserialize($academicStand->year_level_id);
                                    $standSemesters = unserialize($academicStand->semester);

                                    if (in_array($yearLevelFormatted, $standYearLevels) &&
                                        in_array($studentLevel['semester'], $standSemesters)) {
                                        if ((substr($courseRegistration->student->academicyear, 0, 4) >= $academicStand->academic_year_from) ||
                                            ($academicStand->applicable_for_all_current_student == 1 &&
                                                substr($academicYear, 0, 4) >= $academicStand->academic_year_from)) {
                                            $as = $academicStand;
                                        }
                                    }

                                    if (!empty($as)) {
                                        $academicRuleTable =  TableRegistry::getTableLocator()->get('AcademicRules');
                                        $academicRules = $academicRuleTable->find()
                                            ->where(['academic_stand_id' => $as->id])
                                            ->toArray();

                                        if (!empty($academicRules)) {
                                            $statusFound = false;
                                            foreach ($academicRules as $academicRule) {
                                                $sgpa = round($studentExamStatus[$statIndex]->sgpa, 2);
                                                $cgpa = round($studentExamStatus[$statIndex]->cgpa, 2);
                                                $sgpaTest = true;
                                                $cgpaTest = true;

                                                if (!empty($academicRule->sgpa)) {
                                                    switch ($academicRule->scmo) {
                                                        case '>':
                                                            $sgpaTest = $sgpa > $academicRule->sgpa;
                                                            break;
                                                        case '>=':
                                                            $sgpaTest = $sgpa >= $academicRule->sgpa;
                                                            break;
                                                        case '<':
                                                            $sgpaTest = $sgpa < $academicRule->sgpa;
                                                            break;
                                                        case '<=':
                                                            $sgpaTest = $sgpa <= $academicRule->sgpa;
                                                            break;
                                                        default:
                                                            $sgpaTest = false;
                                                            break;
                                                    }
                                                }

                                                if (!empty($academicRule->cgpa)) {
                                                    switch ($academicRule->ccmo) {
                                                        case '>':
                                                            $cgpaTest = $cgpa > $academicRule->cgpa;
                                                            break;
                                                        case '>=':
                                                            $cgpaTest = $cgpa >= $academicRule->cgpa;
                                                            break;
                                                        case '<':
                                                            $cgpaTest = $cgpa < $academicRule->cgpa;
                                                            break;
                                                        case '<=':
                                                            $cgpaTest = $cgpa <= $academicRule->cgpa;
                                                            break;
                                                        default:
                                                            $cgpaTest = false;
                                                            break;
                                                    }
                                                }

                                                if ($sgpaTest && $cgpaTest) {
                                                    $statusFound = true;
                                                    break;
                                                }
                                            }

                                            if ($statusFound) {
                                                $academicCalendarTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');
                                                $academicStatusId = null;

                                                if ($creditHourSum < $academicCalendarTable->minimumCreditForStatus($courseRegistration->student->id)) {
                                                    if (!empty($statusHistories) && empty($lastExamStatus->academic_status_id)) {
                                                        $academicStatusId = $as->academic_status_id;
                                                    }
                                                } elseif ($academicStatus->id == 3 && !empty($lastExamStatus)) {
                                                    if ($lastExamStatus->academic_status_id == 3) {
                                                        if ($this->isThereTcwRuleInDismissal(
                                                            $studentExamStatus[$statIndex]->student_id,
                                                            $courseRegistration->student->program_id,
                                                            $studentExamStatus[$statIndex]->academic_year,
                                                            $studentExamStatus[$statIndex]->semester,
                                                            $yearLevelFormatted,
                                                            $studentLevel['semester'],
                                                            $courseRegistration->student->academicyear
                                                        )) {
                                                            $academicStatusId = 4; // Dismissal
                                                        } else {
                                                            $academicStatusId = $academicStatus->id;
                                                        }
                                                    } elseif ($lastExamStatus->academic_status_id == 6) {
                                                        if ($this->isTherePfwRuleInDismissal(
                                                            $studentExamStatus[$statIndex]->student_id,
                                                            $courseRegistration->student->program_id,
                                                            $studentExamStatus[$statIndex]->academic_year,
                                                            $studentExamStatus[$statIndex]->semester,
                                                            $yearLevelFormatted,
                                                            $studentLevel['semester'],
                                                            $courseRegistration->student->academicyear
                                                        )) {
                                                            $academicStatusId = 4; // Dismissal
                                                        } else {
                                                            $academicStatusId = $academicStatus->id;
                                                        }
                                                    } else {
                                                        $academicStatusId = $academicStatus->id;
                                                    }
                                                } else {
                                                    if ($creditHourSum < $academicCalendarTable->minimumCreditForStatus($courseRegistration->student->id)) {
                                                        if (!empty($statusHistories) && empty($lastExamStatus->academic_status_id)) {
                                                            $academicStatusId = $as->academic_status_id;
                                                        }
                                                    } else {
                                                        $academicStatusId = $academicStatus->id;
                                                    }
                                                }

                                                $studentExamStatus[$statIndex]->academic_status_id = $academicStatusId;
                                                break 2;
                                            }
                                        }
                                    }
                                }
                            }

                            $otherAcademicRuleTable =  TableRegistry::getTableLocator()->get('OtherAcademicRules');
                            $otherAcademicRule = $otherAcademicRuleTable->whatIsTheStatus(
                                $courseAndGrades,
                                $courseRegistration->student,
                                $studentLevel
                            );

                            if (!empty($otherAcademicRule)) {
                                $studentExamStatus[$statIndex]->academic_status_id = $otherAcademicRule;
                            }
                        }
                    }
                }
            }
        }

        if (!empty($studentExamStatus)) {
            if (!$this->saveMany($studentExamStatus, ['validate' => false])) {
                $fullySaved = false;
            }
        }

        return $fullySaved;
    }
    /**
     * Helper method to check if student exists in array
     */
    private function isStudentInArray(int $studentId, array $students): bool
    {
        foreach ($students as $student) {
            if (isset($student->student->id) && $student->student->id == $studentId) {
                return true;
            }
        }
        return false;
    }
    /**
     * Updates academic status for grade change
     */
    public function updateAcademicStatusForGradeChange(?int $id = null, string $type = 'change'): bool
    {
        $successfullyUpdated = true;
        $studentExamStatus = [];

        $academicYearTable =  TableRegistry::getTableLocator()->get('AcademicYears');

        if (strcasecmp($type, 'change') === 0) {
            $examGradeChangeTable =  TableRegistry::getTableLocator()->get('ExamGradeChanges');
            $gradeChangeDetail = $examGradeChangeTable->find()
                ->where(['ExamGradeChanges.id' => $id])
                ->contain([
                    'ExamGrades' => [
                        'CourseRegistrations' => [
                            'PublishedCourses',
                            'Students'
                        ],
                        'CourseAdds' => [
                            'PublishedCourses',
                            'Students'
                        ]
                    ]
                ])
                ->first();

            if (!empty($gradeChangeDetail->exam_grade->course_registration->id)) {
                $student = $gradeChangeDetail->exam_grade->course_registration->student;
                $publishedCourse = $gradeChangeDetail->exam_grade->course_registration->published_course;
            } else {
                $student = $gradeChangeDetail->exam_grade->course_add->student;
                $publishedCourse = $gradeChangeDetail->exam_grade->course_add->published_course;
            }
        } elseif (strcasecmp($type, 'add') === 0) {
            $courseAddTable =  TableRegistry::getTableLocator()->get('CourseAdds');
            $gradeChangeDetail = $courseAddTable->find()
                ->where(['CourseAdds.id' => $id])
                ->contain(['PublishedCourses', 'Students'])
                ->first();

            $student = $gradeChangeDetail->student;
            $publishedCourse = $gradeChangeDetail->published_course;
        } else {
            $courseRegistrationTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
            $gradeChangeDetail = $courseRegistrationTable->find()
                ->where(['CourseRegistrations.id' => $id])
                ->contain(['PublishedCourses', 'Students'])
                ->first();

            $student = $gradeChangeDetail->student;
            $publishedCourse = $gradeChangeDetail->published_course;
        }

        $academicYear = $publishedCourse->academic_year;
        $semester = $publishedCourse->semester;

        $previousStudentExamStatus = $this->find()
            ->where([
                'student_id' => $student->id,
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->first();

        if (!empty($previousStudentExamStatus)) {
            $programTypeTransferTable =  TableRegistry::getTableLocator()->get('ProgramTypeTransfers');
            $programTypeTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
            $programTypeId = $programTypeTransferTable->getStudentProgramType($student->id, $academicYear, $semester);
            $programTypeId = $programTypeTable->getParentProgramType($programTypeId);
            $pattern = $programTypeTable->StudentStatusPatterns->getProgramTypePattern(
                $student->program_id,
                $programTypeId,
                $academicYear
            );
            $ayAndSList = $this->getAcademicYearAndSemesterListToUpdateStatus($student->id, $academicYear, $semester);

            $creditHourSum = 0;
            $gradePointSum = 0;
            $mCreditHourSum = 0;
            $mGradePointSum = 0;
            $deductCreditHourSum = 0;
            $deductGradePointSum = 0;
            $mDeductCreditHourSum = 0;
            $mDeductGradePointSum = 0;
            $complete = true;
            $firstAcademicYear = null;
            $firstSemester = null;
            $processedCourseReg = [];
            $processedCourseAdd = [];

            if (!empty($ayAndSList)) {
                foreach ($ayAndSList as $ayAndS) {
                    if ($firstAcademicYear === null) {
                        $firstAcademicYear = $ayAndS['academic_year'];
                        $firstSemester = $ayAndS['semester'];
                    }

                    $courseRegistrationTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
                    $courseAndGrades = $courseRegistrationTable->ExamGrades->getStudentCoursesAndFinalGrade(
                        $student->id,
                        $ayAndS['academic_year'],
                        $ayAndS['semester']
                    );

                    if (!empty($courseAndGrades)) {
                        foreach ($courseAndGrades as $registeredAddedCourse) {
                            if (!(isset($registeredAddedCourse['grade']) && (isset($registeredAddedCourse['point_value']) ||
                                    strcasecmp($registeredAddedCourse['grade'], 'I') === 0))) {
                                $complete = false;
                                break 2;
                            }

                            if (isset($registeredAddedCourse['grade']) && (strcasecmp($registeredAddedCourse['grade'], 'I') === 0 ||
                                    strcasecmp($registeredAddedCourse['grade'], 'W') === 0 ||
                                    strcasecmp($registeredAddedCourse['grade'], 'NG') === 0)) {
                                $complete = false;
                                break 2;
                            }

                            if (strcasecmp($registeredAddedCourse['grade'], 'I') !== 0 &&
                                isset($registeredAddedCourse['used_in_gpa']) &&
                                $registeredAddedCourse['used_in_gpa']) {
                                $creditHourSum += $registeredAddedCourse['credit'];
                                $gradePointSum += ($registeredAddedCourse['credit'] * $registeredAddedCourse['point_value']);

                                if ($registeredAddedCourse['major'] == 1) {
                                    $mCreditHourSum += $registeredAddedCourse['credit'];
                                    $mGradePointSum += ($registeredAddedCourse['credit'] * $registeredAddedCourse['point_value']);
                                }

                                if ($registeredAddedCourse['repeated_new'] === true) {
                                    $previousAyAndS = $courseRegistrationTable->ExamGrades->getListOfAyAndSemester(
                                        $student->id,
                                        $ayAndS['academic_year'],
                                        $ayAndS['semester']
                                    );
                                    $courseRegistrations = $courseRegistrationTable->getCourseRegistrations(
                                        $student->id,
                                        $previousAyAndS,
                                        $registeredAddedCourse['course_id'],
                                        1,
                                        1
                                    );
                                    $courseAddTable =  TableRegistry::getTableLocator()->get('CourseAdds');
                                    $courseAdds = $courseAddTable->getCourseAdds(
                                        $student->id,
                                        $previousAyAndS,
                                        $registeredAddedCourse['course_id'],
                                        1
                                    );

                                    if (!empty($courseRegistrations)) {
                                        foreach ($courseRegistrations as $crValue) {
                                            if (!in_array($crValue['CourseRegistration']['id'], $processedCourseReg)) {
                                                $gradeDetail = $courseRegistrationTable->ExamGrades->getApprovedGrade(
                                                    $crValue['CourseRegistration']['id'],
                                                    1
                                                );
                                                $deductCreditHourSum += $crValue['PublishedCourse']['Course']['credit'];
                                                $deductGradePointSum += ($gradeDetail['point_value'] * $crValue['PublishedCourse']['Course']['credit']);

                                                if ($crValue['PublishedCourse']['Course']['major'] == 1) {
                                                    $mDeductCreditHourSum += $crValue['PublishedCourse']['Course']['credit'];
                                                    $mDeductGradePointSum += ($gradeDetail['point_value'] * $crValue['PublishedCourse']['Course']['credit']);
                                                }

                                                $processedCourseReg[] = $crValue['CourseRegistration']['id'];
                                            }
                                        }
                                    }

                                    if (!empty($courseAdds)) {
                                        foreach ($courseAdds as $caValue) {
                                            if (!in_array($caValue['CourseAdd']['id'], $processedCourseAdd)) {
                                                $gradeDetail = $courseAddTable->ExamGrades->getApprovedGrade(
                                                    $caValue['CourseAdd']['id'],
                                                    0
                                                );
                                                $deductCreditHourSum += $caValue['PublishedCourse']['Course']['credit'];
                                                $deductGradePointSum += ($gradeDetail['point_value'] * $caValue['PublishedCourse']['Course']['credit']);

                                                if ($caValue['PublishedCourse']['Course']['major'] == 1) {
                                                    $mDeductCreditHourSum += $caValue['PublishedCourse']['Course']['credit'];
                                                    $mDeductGradePointSum += ($gradeDetail['point_value'] * $caValue['PublishedCourse']['Course']['credit']);
                                                }

                                                $processedCourseAdd[] = $caValue['CourseAdd']['id'];
                                            }
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            if (count($ayAndSList) >= $pattern && $complete && $creditHourSum > 0) {
                $studentExamStatus = $this->newEntity([
                    'id' => $previousStudentExamStatus->id,
                    'student_id' => $student->id,
                    'academic_year' => $academicYear,
                    'semester' => $semester,
                    'grade_point_sum' => $gradePointSum,
                    'credit_hour_sum' => $creditHourSum,
                    'm_grade_point_sum' => $mGradePointSum,
                    'm_credit_hour_sum' => $mCreditHourSum,
                    'created' => $academicYearTable->getAcademicYearBeginningDate($academicYear, $semester),
                    'sgpa' => ($creditHourSum > 0) ? $gradePointSum / $creditHourSum : 0
                ]);

                $statusHistories = $this->find()
                    ->where(['student_id' => $student->id])
                    ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'created' => 'ASC'])
                    ->toArray();

                $cumulativeGradePoint = $studentExamStatus->grade_point_sum;
                $cumulativeCreditHour = $studentExamStatus->credit_hour_sum;
                $mCumulativeGradePoint = $studentExamStatus->m_grade_point_sum;
                $mCumulativeCreditHour = $studentExamStatus->m_credit_hour_sum;
                $lastExamStatus = [];

                foreach ($statusHistories as $statusHistory) {
                    if (!(strcasecmp($statusHistory->academic_year, $academicYear) === 0 &&
                        strcasecmp($statusHistory->semester, $semester) === 0)) {
                        $cumulativeGradePoint += $statusHistory->grade_point_sum;
                        $cumulativeCreditHour += $statusHistory->credit_hour_sum;
                        $mCumulativeGradePoint += $statusHistory->m_grade_point_sum;
                        $mCumulativeCreditHour += $statusHistory->m_credit_hour_sum;
                        $lastExamStatus = $statusHistory;
                    } else {
                        break;
                    }
                }

                $studentExamStatus->cgpa = (($cumulativeGradePoint - $deductGradePointSum) > 0 &&
                    ($cumulativeCreditHour - $deductCreditHourSum) > 0) ?
                    (($cumulativeGradePoint - $deductGradePointSum) / ($cumulativeCreditHour - $deductCreditHourSum)) : 0;

                $studentExamStatus->mcgpa = (($mCumulativeGradePoint - $mDeductGradePointSum) > 0 &&
                    ($mCumulativeCreditHour - $mDeductCreditHourSum) > 0) ?
                    (($mCumulativeGradePoint - $mDeductGradePointSum) / ($mCumulativeCreditHour - $mDeductCreditHourSum)) : 0;

                $studentLevel = $this->studentYearAndSemesterLevelOfStatus($student->id, $academicYear, $semester);

                $yearLevelFormatted = '';
                switch ($studentLevel['year']) {
                    case 1:
                        $yearLevelFormatted = $studentLevel['year'] . 'st';
                        break;
                    case 2:
                        $yearLevelFormatted = $studentLevel['year'] . 'nd';
                        break;
                    case 3:
                        $yearLevelFormatted = $studentLevel['year'] . 'rd';
                        break;
                    default:
                        $yearLevelFormatted = $studentLevel['year'] . 'th';
                        break;
                }

                $academicStatusTable =  TableRegistry::getTableLocator()->get('AcademicStatuses');
                $academicStatuses = $academicStatusTable->find()
                    ->where(['computable' => 1])
                    ->order(['order' => 'ASC'])
                    ->toArray();

                if (!empty($academicStatuses)) {
                    foreach ($academicStatuses as $academicStatus) {
                        $academicStandTable =  TableRegistry::getTableLocator()->get('AcademicStands');
                        $academicStands = $academicStandTable->find()
                            ->where([
                                'academic_status_id' => $academicStatus->id,
                                'program_id' => $student->program_id
                            ])
                            ->order(['academic_year_from' => 'ASC'])
                            ->toArray();

                        $as = null;
                        foreach ($academicStands as $academicStand) {
                            $standYearLevels = unserialize($academicStand->year_level_id);
                            $standSemesters = unserialize($academicStand->semester);

                            if (in_array($yearLevelFormatted, $standYearLevels) &&
                                in_array($studentLevel['semester'], $standSemesters)) {
                                if ((substr($student->academicyear, 0, 4) >= $academicStand->academic_year_from) ||
                                    ($academicStand->applicable_for_all_current_student == 1 &&
                                        substr($academicYear, 0, 4) >= $academicStand->academic_year_from)) {
                                    $as = $academicStand;
                                }
                            }

                            if (!empty($as)) {
                                $academicRuleTable =  TableRegistry::getTableLocator()->get('AcademicRules');
                                $academicRules = $academicRuleTable->find()
                                    ->where(['academic_stand_id' => $as->id])
                                    ->toArray();

                                if (!empty($academicRules)) {
                                    $statusFound = false;
                                    foreach ($academicRules as $academicRule) {
                                        $sgpa = round($studentExamStatus->sgpa, 2);
                                        $cgpa = round($studentExamStatus->cgpa, 2);
                                        $sgpaTest = true;
                                        $cgpaTest = true;

                                        if (!empty($academicRule->sgpa)) {
                                            switch ($academicRule->scmo) {
                                                case '>':
                                                    $sgpaTest = $sgpa > $academicRule->sgpa;
                                                    break;
                                                case '>=':
                                                    $sgpaTest = $sgpa >= $academicRule->sgpa;
                                                    break;
                                                case '<':
                                                    $sgpaTest = $sgpa < $academicRule->sgpa;
                                                    break;
                                                case '<=':
                                                    $sgpaTest = $sgpa <= $academicRule->sgpa;
                                                    break;
                                                default:
                                                    $sgpaTest = false;
                                                    break;
                                            }
                                        }

                                        if (!empty($academicRule->cgpa)) {
                                            switch ($academicRule->ccmo) {
                                                case '>':
                                                    $cgpaTest = $cgpa > $academicRule->cgpa;
                                                    break;
                                                case '>=':
                                                    $cgpaTest = $cgpa >= $academicRule->cgpa;
                                                    break;
                                                case '<':
                                                    $cgpaTest = $cgpa < $academicRule->cgpa;
                                                    break;
                                                case '<=':
                                                    $cgpaTest = $cgpa <= $academicRule->cgpa;
                                                    break;
                                                default:
                                                    $cgpaTest = false;
                                                    break;
                                            }
                                        }

                                        if ($sgpaTest && $cgpaTest) {
                                            $statusFound = true;
                                            break;
                                        }
                                    }

                                    if ($statusFound) {
                                        $academicCalendarTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');
                                        $academicStatusId = null;

                                        if ($creditHourSum < $academicCalendarTable->minimumCreditForStatus($student->id)) {
                                            if (!empty($statusHistories) && empty($lastExamStatus->academic_status_id)) {
                                                $academicStatusId = $as->academic_status_id;
                                            }
                                        } elseif ($academicStatus->id == 3 && !empty($lastExamStatus)) {
                                            if ($lastExamStatus->academic_status_id == 3) {
                                                if ($this->isThereTcwRuleInDismissal(
                                                    $studentExamStatus->student_id,
                                                    $student->program_id,
                                                    $studentExamStatus->academic_year,
                                                    $studentExamStatus->semester,
                                                    $yearLevelFormatted,
                                                    $studentLevel['semester'],
                                                    $student->academicyear
                                                )) {
                                                    $academicStatusId = 4; // Dismissal
                                                } else {
                                                    $academicStatusId = $academicStatus->id;
                                                }
                                            } elseif ($lastExamStatus->academic_status_id == 6) {
                                                if ($this->isTherePfwRuleInDismissal(
                                                    $studentExamStatus->student_id,
                                                    $student->program_id,
                                                    $studentExamStatus->academic_year,
                                                    $studentExamStatus->semester,
                                                    $yearLevelFormatted,
                                                    $studentLevel['semester'],
                                                    $student->academicyear
                                                )) {
                                                    $academicStatusId = 4; // Dismissal
                                                } else {
                                                    $academicStatusId = $academicStatus->id;
                                                }
                                            } else {
                                                $academicStatusId = $academicStatus->id;
                                            }
                                        } else {
                                            $academicStatusId = $academicStatus->id;
                                        }

                                        $studentExamStatus->academic_status_id = $academicStatusId;
                                        break 2;
                                    }
                                }
                            }
                        }
                    }
                }

                $otherAcademicRuleTable =  TableRegistry::getTableLocator()->get('OtherAcademicRules');
                $otherAcademicRule = $otherAcademicRuleTable->whatIsTheStatus(
                    $courseAndGrades,
                    $student,
                    $studentLevel
                );

                if (!empty($otherAcademicRule)) {
                    $studentExamStatus->academic_status_id = $otherAcademicRule;
                }
            }
        }

        if (!empty($studentExamStatus)) {
            if (!$this->save($studentExamStatus, ['validate' => false])) {
                $successfullyUpdated = false;
            }
        }

        return $successfullyUpdated;
    }

    /**
     * Returns student's academic status before the given academic year and semester
     * @return mixed 1 (first time), 2 (status not generated), 0 (null status), or array (status object)
     */
    public function getStudentAcademicStatus(?int $studentId = null, ?string $academicYear = null, ?string $semester = null)
    {
        $lastStudentStatus = $this->find()
            ->where(['student_id' => $studentId])
            ->order(['created' => 'DESC'])
            ->first();

        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $studentDetail = $studentsTable->find()
            ->where(['id' => $studentId])
            ->first();

        $examGradeTable =  TableRegistry::getTableLocator()->get('ExamGrades');
        $pAyAndSList = $examGradeTable->getListOfAyAndSemester($studentId, $academicYear, $semester);

        if (empty($pAyAndSList)) {
            return 1;
        }

        $previousAyAndS = $this->getPreviousSemester($academicYear, $semester);

        $programTypeTransferTable =  TableRegistry::getTableLocator()->get('ProgramTypeTransfers');
        $programTypeTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
        $programTypeId = $programTypeTransferTable->getStudentProgramType(
            $studentId,
            $previousAyAndS['academic_year'],
            $previousAyAndS['semester']
        );
        $programTypeId = $programTypeTable->getParentProgramType($programTypeId);

        $pattern = $programTypeTable->StudentStatusPatterns->getProgramTypePattern(
            $studentDetail->program_id,
            $programTypeId,
            $previousAyAndS['academic_year']
        );

        $ayAndSList = $this->getAcademicYearAndSemesterListToGenerateStatus(
            $studentDetail->id,
            $previousAyAndS['academic_year'],
            $previousAyAndS['semester']
        );

        if (empty($lastStudentStatus)) {
            return (count($ayAndSList) >= $pattern) ? 2 : 1;
        }

        if (count($ayAndSList) >= $pattern) {
            return 2;
        } elseif (!empty($lastStudentStatus) && is_null($lastStudentStatus->academic_status_id)) {
            return 0;
        }

        return $lastStudentStatus->toArray();
    }

    /**
     * Displays student year and semester level of status
     */
    public function studentYearAndSemesterLevelOfStatusDisplay(int $studentId, string $academicYear, string $semester): array
    {
        return $this->studentYearAndSemesterLevelOfStatus($studentId, $academicYear, $semester);
    }

    /**
     * Gets student year and semester level
     */
    public function studentYearAndSemesterLevel(int $studentId, ?string $academicYear = null, ?string $semester = null): array
    {
        $studentStatuses = $this->find()
            ->where([
                'student_id' => $studentId,
                'academic_status_id !=' => 4
            ])
            ->order(['created' => 'ASC'])
            ->toArray();

        $semesterCount = 0;
        foreach ($studentStatuses as $studentStatus) {
            if (strcasecmp($studentStatus->academic_year, $academicYear) === 0 &&
                strcasecmp($studentStatus->semester, $semester) === 0) {
                break;
            }
            $semesterCount++;
        }

        $yearLevel = ((int)($semesterCount / 2)) + 1;
        $semesterLevel = ($semesterCount % 2 > 0) ? 'II' : 'I';

        $name = '';
        switch ($yearLevel) {
            case 1:
                $name = $yearLevel . 'st';
                break;
            case 2:
                $name = $yearLevel . 'nd';
                break;
            case 3:
                $name = $yearLevel . 'rd';
                break;
            default:
                $name = $yearLevel . 'th';
                break;
        }

        return [
            'year' => $name,
            'semester' => $semesterLevel
        ];
    }

    /**
     * Checks if student deserves service
     */
    public function isServiceDeserved(?int $studentId = null, ?string $academicYear = null): bool
    {
        // Logic intentionally left empty as per original method's commented state
        // Implement service eligibility logic as needed
        return false;
    }

    /**
     * Updates academic status by student and published course
     */
    public function updateAcademicStatusByStudent(int $studentId, int $publishedCourseId): bool
    {
        $fullySaved = true;
        $studentExamStatus = [];

        $academicYearTable =  TableRegistry::getTableLocator()->get('AcademicYears');
        $publishedCourseTable =  TableRegistry::getTableLocator()->get('PublishedCourses');

        $registeredStudents = $publishedCourseTable->find()
            ->where(['id' => $publishedCourseId])
            ->contain([
                'CourseRegistrations' => [
                    'conditions' => ['student_id' => $studentId],
                    'order' => ['id' => 'ASC'],
                    'Students' => [
                        'fields' => [
                            'id',
                            'full_name',
                            'program_id',
                            'admissionyear',
                            'program_type_id'
                        ],
                        'GraduateLists'
                    ]
                ]
            ])
            ->first();

        $addedStudents = $publishedCourseTable->find()
            ->where(['id' => $publishedCourseId])
            ->contain([
                'CourseAdds' => [
                    'conditions' => ['student_id' => $studentId],
                    'order' => ['id' => 'ASC'],
                    'Students' => [
                        'fields' => [
                            'id',
                            'full_name',
                            'program_id',
                            'admissionyear',
                            'program_type_id'
                        ],
                        'GraduateLists'
                    ]
                ]
            ])
            ->first();

        $registeredAddedStudents = [];
        if (!empty($registeredStudents->course_registrations)) {
            foreach ($registeredStudents->course_registrations as $value) {
                if (!in_array($value->student->id, array_column($registeredAddedStudents, 'student_id'))) {
                    $registeredAddedStudents[] = $value;
                }
            }
        }

        if (!empty($addedStudents->course_adds)) {
            foreach ($addedStudents->course_adds as $value) {
                if (!in_array($value->student->id, array_column($registeredAddedStudents, 'student_id'))) {
                    $registeredAddedStudents[] = $value;
                }
            }
        }

        $academicYear = $registeredStudents->published_course->academic_year;
        $semester = $registeredStudents->published_course->semester;

        foreach ($registeredAddedStudents as $courseRegistration) {
            $programTypeTransferTable =  TableRegistry::getTableLocator()->get('ProgramTypeTransfers');
            $programTypeTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
            $programTypeId = $programTypeTransferTable->getStudentProgramType(
                $courseRegistration->student->id,
                $academicYear,
                $semester
            );
            $programTypeId = $programTypeTable->getParentProgramType($programTypeId);

            $pattern = $programTypeTable->StudentStatusPatterns->getProgramTypePattern(
                $courseRegistration->student->program_id,
                $programTypeId,
                $academicYear
            );

            $courseRegistrationTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
            $lastPattern = $programTypeTable->StudentStatusPatterns->isLastSemesterInCurriculum(
                $courseRegistration->student->id
            );

            $lastRegisteredSem = $courseRegistrationTable->find()
                ->where(['student_id' => $courseRegistration->student->id])
                ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
                ->first();

            if ($lastPattern && $lastRegisteredSem->academic_year === $academicYear &&
                $lastRegisteredSem->semester === $semester) {
                $pattern = 1;
            }

            $ayAndSList = $this->getAcademicYearAndSemesterListToGenerateStatus(
                $courseRegistration->student->id,
                $academicYear,
                $semester
            );

            if (empty($ayAndSList)) {
                continue;
            }

            if (count($ayAndSList) >= $pattern) {
                $creditHourSum = 0;
                $gradePointSum = 0;
                $mCreditHourSum = 0;
                $mGradePointSum = 0;
                $deductCreditHourSum = 0;
                $deductGradePointSum = 0;
                $mDeductCreditHourSum = 0;
                $mDeductGradePointSum = 0;
                $complete = true;
                $firstAcademicYear = null;
                $firstSemester = null;
                $processedCourseReg = [];
                $processedCourseAdd = [];

                $allAySList = $courseRegistrationTable->ExamGrades->getListOfAyAndSemester(
                    $courseRegistration->student->id,
                    $ayAndSList[0]['academic_year'],
                    $ayAndSList[0]['semester']
                );

                foreach ($ayAndSList as $ayAndS) {
                    $aysIndex = count($allAySList);
                    $allAySList[$aysIndex] = [
                        'academic_year' => $ayAndS['academic_year'],
                        'semester' => $ayAndS['semester']
                    ];

                    if ($firstAcademicYear === null) {
                        $firstAcademicYear = $ayAndS['academic_year'];
                        $firstSemester = $ayAndS['semester'];
                    }

                    $courseAndGrades = $courseRegistrationTable->ExamGrades->getStudentCoursesAndFinalGrade(
                        $courseRegistration->student->id,
                        $ayAndS['academic_year'],
                        $ayAndS['semester']
                    );

                    if (!empty($courseAndGrades)) {
                        foreach ($courseAndGrades as $registeredAddedCourse) {
                            if (!(isset($registeredAddedCourse['grade']) && (isset($registeredAddedCourse['point_value']) ||
                                    strcasecmp($registeredAddedCourse['grade'], 'I') === 0 ||
                                    strcasecmp($registeredAddedCourse['grade'], 'W') === 0))) {
                                $complete = false;
                                break 2;
                            }

                            if (isset($registeredAddedCourse['grade']) && (strcasecmp($registeredAddedCourse['grade'], 'I') === 0 ||
                                    strcasecmp($registeredAddedCourse['grade'], 'W') === 0 ||
                                    strcasecmp($registeredAddedCourse['grade'], 'NG') === 0)) {
                                $complete = false;
                                break 2;
                            }

                            if (strcasecmp($registeredAddedCourse['grade'], 'I') !== 0 &&
                                isset($registeredAddedCourse['used_in_gpa']) &&
                                $registeredAddedCourse['used_in_gpa']) {
                                $creditHourSum += $registeredAddedCourse['credit'];
                                $gradePointSum += ($registeredAddedCourse['credit'] * $registeredAddedCourse['point_value']);

                                if ($registeredAddedCourse['major'] == 1) {
                                    $mCreditHourSum += $registeredAddedCourse['credit'];
                                    $mGradePointSum += ($registeredAddedCourse['credit'] * $registeredAddedCourse['point_value']);
                                }
                            }
                        }
                    }
                }

                if ($complete && $creditHourSum > 0) {
                    $courseAddTable =  TableRegistry::getTableLocator()->get('CourseAdds');
                    $creditAndPointDeduction = $courseAddTable->ExamGrades->getTotalCreditAndPointDeduction(
                        $courseRegistration->student->id,
                        $allAySList
                    );

                    $deductCreditHourSum = $creditAndPointDeduction['deduct_credit_hour_sum'];
                    $deductGradePointSum = $creditAndPointDeduction['deduct_grade_point_sum'];
                    $mDeductCreditHourSum = $creditAndPointDeduction['m_deduct_credit_hour_sum'];
                    $mDeductGradePointSum = $creditAndPointDeduction['m_deduct_grade_point_sum'];

                    $statIndex = count($studentExamStatus);
                    $studentExamStatus[$statIndex] = $this->newEntity([
                        'student_id' => $courseRegistration->student->id,
                        'academic_year' => $academicYear,
                        'semester' => $semester,
                        'grade_point_sum' => $gradePointSum,
                        'credit_hour_sum' => $creditHourSum,
                        'm_grade_point_sum' => $mGradePointSum,
                        'm_credit_hour_sum' => $mCreditHourSum,
                        'created' => $academicYearTable->getAcademicYearBeginningDate($academicYear, $semester),
                        'sgpa' => ($creditHourSum > 0) ? $gradePointSum / $creditHourSum : 0
                    ]);

                    $statusHistories = $this->find()
                        ->where(['student_id' => $courseRegistration->student->id])
                        ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'created' => 'ASC'])
                        ->toArray();

                    $lastExamStatus = [];
                    $cumulativeGradePoint = $studentExamStatus[$statIndex]->grade_point_sum;
                    $cumulativeCreditHour = $studentExamStatus[$statIndex]->credit_hour_sum;
                    $mCumulativeGradePoint = $studentExamStatus[$statIndex]->m_grade_point_sum;
                    $mCumulativeCreditHour = $studentExamStatus[$statIndex]->m_credit_hour_sum;

                    foreach ($statusHistories as $statusHistory) {
                        if (!(strcasecmp($statusHistory->academic_year, $academicYear) === 0 &&
                            strcasecmp($statusHistory->semester, $semester) === 0)) {
                            $cumulativeGradePoint += $statusHistory->grade_point_sum;
                            $cumulativeCreditHour += $statusHistory->credit_hour_sum;
                            $mCumulativeGradePoint += $statusHistory->m_grade_point_sum;
                            $mCumulativeCreditHour += $statusHistory->m_credit_hour_sum;
                            $lastExamStatus = $statusHistory;
                        } else {
                            break;
                        }
                    }

                    $studentExamStatus[$statIndex]->cgpa = (($cumulativeGradePoint - $deductGradePointSum) > 0 &&
                        ($cumulativeCreditHour - $deductCreditHourSum) > 0) ?
                        (($cumulativeGradePoint - $deductGradePointSum) / ($cumulativeCreditHour - $deductCreditHourSum)) : 0;

                    $studentExamStatus[$statIndex]->mcgpa = (($mCumulativeGradePoint - $mDeductGradePointSum) > 0 &&
                        ($mCumulativeCreditHour - $mDeductCreditHourSum) > 0) ?
                        (($mCumulativeGradePoint - $mDeductGradePointSum) / ($mCumulativeCreditHour - $mDeductCreditHourSum)) : 0;

                    $studentLevel = $this->studentYearAndSemesterLevelOfStatus(
                        $courseRegistration->student->id,
                        $academicYear,
                        $semester
                    );

                    $academicStatusTable =  TableRegistry::getTableLocator()->get('AcademicStatuses');
                    $academicStatuses = $academicStatusTable->find()
                        ->where(['computable' => 1])
                        ->order(['order' => 'ASC'])
                        ->toArray();

                    foreach ($academicStatuses as $academicStatus) {
                        $academicStandTable =  TableRegistry::getTableLocator()->get('AcademicStands');
                        $academicStands = $academicStandTable->find()
                            ->where([
                                'academic_status_id' => $academicStatus->id,
                                'program_id' => $courseRegistration->student->program_id
                            ])
                            ->order(['academic_year_from' => 'ASC'])
                            ->toArray();

                        $as = null;
                        foreach ($academicStands as $academicStand) {
                            $standYearLevels = unserialize($academicStand->year_level_id);
                            $standSemesters = unserialize($academicStand->semester);

                            if (in_array($studentLevel['year'], $standYearLevels) &&
                                in_array($studentLevel['semester'], $standSemesters)) {
                                if ((substr($courseRegistration->student->academicyear, 0, 4) >= $academicStand->academic_year_from) ||
                                    ($academicStand->applicable_for_all_current_student == 1 &&
                                        substr($academicYear, 0, 4) >= $academicStand->academic_year_from)) {
                                    $as = $academicStand;
                                }
                            }

                            if (!empty($as)) {
                                $academicRuleTable =  TableRegistry::getTableLocator()->get('AcademicRules');
                                $academicRules = $academicRuleTable->find()
                                    ->where(['academic_stand_id' => $as->id])
                                    ->toArray();

                                if (!empty($academicRules)) {
                                    $statusFound = false;
                                    foreach ($academicRules as $academicRule) {
                                        $sgpa = round($studentExamStatus[$statIndex]->sgpa, 2);
                                        $cgpa = round($studentExamStatus[$statIndex]->cgpa, 2);
                                        $sgpaTest = true;
                                        $cgpaTest = true;

                                        if (!empty($academicRule->sgpa)) {
                                            switch ($academicRule->scmo) {
                                                case '>':
                                                    $sgpaTest = $sgpa > $academicRule->sgpa;
                                                    break;
                                                case '>=':
                                                    $sgpaTest = $sgpa >= $academicRule->sgpa;
                                                    break;
                                                case '<':
                                                    $sgpaTest = $sgpa < $academicRule->sgpa;
                                                    break;
                                                case '<=':
                                                    $sgpaTest = $sgpa <= $academicRule->sgpa;
                                                    break;
                                                default:
                                                    $sgpaTest = false;
                                                    break;
                                            }
                                        }

                                        if (!empty($academicRule->cgpa)) {
                                            switch ($academicRule->ccmo) {
                                                case '>':
                                                    $cgpaTest = $cgpa > $academicRule->cgpa;
                                                    break;
                                                case '>=':
                                                    $cgpaTest = $cgpa >= $academicRule->cgpa;
                                                    break;
                                                case '<':
                                                    $cgpaTest = $cgpa < $academicRule->cgpa;
                                                    break;
                                                case '<=':
                                                    $cgpaTest = $cgpa <= $academicRule->cgpa;
                                                    break;
                                                default:
                                                    $cgpaTest = false;
                                                    break;
                                            }
                                        }

                                        if ($sgpaTest && $cgpaTest) {
                                            $statusFound = true;
                                            break;
                                        }
                                    }

                                    if ($statusFound) {
                                        $academicCalendarTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');
                                        $academicStatusId = null;

                                        if ($creditHourSum < $academicCalendarTable->minimumCreditForStatus($courseRegistration->student->id)) {
                                            if (!empty($statusHistories) && empty($lastExamStatus->academic_status_id)) {
                                                $academicStatusId = $as->academic_status_id;
                                            }
                                        } elseif ($academicStatus->id == 3 && !empty($lastExamStatus)) {
                                            if ($lastExamStatus->academic_status_id == 3) {
                                                if ($this->isThereTcwRuleInDismissal(
                                                    $studentExamStatus[$statIndex]->student_id,
                                                    $courseRegistration->student->program_id,
                                                    $studentExamStatus[$statIndex]->academic_year,
                                                    $studentExamStatus[$statIndex]->semester,
                                                    $studentLevel['year'],
                                                    $studentLevel['semester'],
                                                    $courseRegistration->student->academicyear
                                                )) {
                                                    $academicStatusId = 4; // Dismissal
                                                } else {
                                                    $academicStatusId = $academicStatus->id;
                                                }
                                            } elseif ($lastExamStatus->academic_status_id == 6) {
                                                if ($this->isTherePfwRuleInDismissal(
                                                    $studentExamStatus[$statIndex]->student_id,
                                                    $courseRegistration->student->program_id,
                                                    $studentExamStatus[$statIndex]->academic_year,
                                                    $studentExamStatus[$statIndex]->semester,
                                                    $studentLevel['year'],
                                                    $studentLevel['semester'],
                                                    $courseRegistration->student->academicyear
                                                )) {
                                                    $academicStatusId = 4; // Dismissal
                                                } else {
                                                    $academicStatusId = $academicStatus->id;
                                                }
                                            } else {
                                                $academicStatusId = $academicStatus->id;
                                            }
                                        } else {
                                            $academicStatusId = $academicStatus->id;
                                        }

                                        $studentExamStatus[$statIndex]->academic_status_id = $academicStatusId;
                                        break 2;
                                    }
                                }
                            }
                        }
                    }

                    $otherAcademicRuleTable =  TableRegistry::getTableLocator()->get('OtherAcademicRules');
                    $otherAcademicRule = $otherAcademicRuleTable->whatIsTheStatus(
                        $courseAndGrades,
                        $courseRegistration->student,
                        $studentLevel
                    );

                    if (!empty($otherAcademicRule)) {
                        $studentExamStatus[$statIndex]->academic_status_id = $otherAcademicRule;
                    }
                }
            }
        }

        if (!empty($studentExamStatus)) {
            if (!$this->saveMany($studentExamStatus, ['validate' => false])) {
                $fullySaved = false;
            }
        }

        return $fullySaved;
    }

    /**
     * Gets list of grade changes
     */
    public function getGradeChangeList(?string $academicYear = null, ?string $semester = null, ?string $programId = null, ?string $programTypeId = null, ?string $departmentId = null, ?string $yearLevelId = null, int $freshman = 0): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $gradeChangeList = [];
        $conditions = [];
        $addConditions = [];
        $makeupConditions = [];

        if (!empty($programId)) {
            $programIds = explode('~', $programId);
            $programCondition = count($programIds) > 1 ? $programIds[1] : $programId;
            $conditions['PublishedCourses.program_id'] = $programCondition;
            $addConditions['PublishedCourses.program_id'] = $programCondition;
            $makeupConditions['PublishedCourses.program_id'] = $programCondition;
        }

        if (!empty($programTypeId)) {
            $programTypeIds = explode('~', $programTypeId);
            $programTypeCondition = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
            $conditions['PublishedCourses.program_type_id'] = $programTypeCondition;
            $addConditions['PublishedCourses.program_type_id'] = $programTypeCondition;
            $makeupConditions['PublishedCourses.program_type_id'] = $programTypeCondition;
        }

        if (!empty($academicYear)) {
            $conditions['PublishedCourses.academic_year'] = $academicYear;
            $conditions['CourseRegistrations.academic_year'] = $academicYear;
            $addConditions['PublishedCourses.academic_year'] = $academicYear;
            $addConditions['CourseAdds.academic_year'] = $academicYear;
            $makeupConditions['PublishedCourses.academic_year'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['PublishedCourses.semester'] = $semester;
            $conditions['CourseRegistrations.semester'] = $semester;
            $addConditions['PublishedCourses.semester'] = $semester;
            $addConditions['CourseAdds.semester'] = $semester;
            $makeupConditions['PublishedCourses.semester'] = $semester;
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');

        if ($freshman == 0) {
            $deptConditions = ['Departments.active' => 1];
            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $deptConditions['Departments.college_id'] = $collegeId[1];
                } else {
                    $deptConditions['Departments.id'] = $departmentId;
                }
            }
            $departments = $departmentsTable->find()
                ->where($deptConditions)
                ->contain(['Colleges', 'YearLevels'])
                ->toArray();
        } else {
            $collegeConditions = ['Colleges.active' => 1];
            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $collegeConditions['Colleges.id'] = $collegeId[1];
                }
            }
            $colleges = $collegesTable->find()
                ->where($collegeConditions)
                ->select(['id'])
                ->toArray();
        }

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                foreach ($department->year_levels as $yearLevel) {
                    if (!empty($yearLevelId) && $yearLevel->name != $yearLevelId) {
                        continue;
                    }

                    $internalConditions = array_merge($conditions, [
                        'PublishedCourses.year_level_id' => $yearLevel->id,
                        'PublishedCourses.given_by_department_id' => $department->id
                    ]);
                    $internalAddConditions = array_merge($addConditions, [
                        'PublishedCourses.year_level_id' => $yearLevel->id,
                        'PublishedCourses.given_by_department_id' => $department->id
                    ]);
                    $internalMakeupConditions = array_merge($makeupConditions, [
                        'PublishedCourses.year_level_id' => $yearLevel->id,
                        'PublishedCourses.given_by_department_id' => $department->id
                    ]);

                    $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
                    $registrationChanges = $courseRegistrationsTable->find()
                        ->select([
                            'CourseRegistrations.id',
                            'PublishedCourses.department_id',
                            'ExamGrades.grade',
                            'ExamGradeChanges.grade',
                            'ExamGradeChanges.reason',
                            'CourseInstructorAssignments.staff_id',
                            'ExamGradeChanges.department_approved_by',
                            'ExamGradeChanges.college_approved_by',
                            'ExamGradeChanges.registrar_approved_by',
                            'ExamGradeChanges.college_reason',
                            'ExamGradeChanges.registrar_reason',
                            'ExamGradeChanges.department_reason',
                            'ExamGradeChanges.department_approval_date',
                            'ExamGradeChanges.registrar_approval_date',
                            'ExamGradeChanges.college_approval_date',
                            'ExamGradeChanges.manual_ng_conversion',
                            'ExamGradeChanges.auto_ng_conversion',
                            'ExamGradeChanges.makeup_exam_result',
                            'ExamGradeChanges.result',
                            'ExamGradeChanges.initiated_by_department'
                        ])
                        ->innerJoinWith('ExamGrades', function ($q) {
                            return $q->where(['ExamGrades.registrar_approval' => 1]);
                        })
                        ->innerJoinWith('ExamGrades.ExamGradeChanges', function ($q) {
                            return $q->where(['ExamGradeChanges.registrar_approval' => 1]);
                        })
                        ->innerJoinWith('PublishedCourses')
                        ->innerJoinWith('PublishedCourses.CourseInstructorAssignments')
                        ->where($internalConditions)
                        ->toArray();

                    foreach ($registrationChanges as $change) {
                        $details = $courseRegistrationsTable->find()
                            ->where(['CourseRegistrations.id' => $change->id])
                            ->contain([
                                'PublishedCourses' => [
                                    'CourseInstructorAssignments' => [
                                        'Staffs' => ['Titles', 'Positions']
                                    ],
                                    'GivenByDepartments',
                                    'Courses'
                                ],
                                'Students'
                            ])
                            ->first();

                        $gradeDetail = [
                            'oldGrade' => $change->exam_grade->grade,
                            'course' => $details->published_course->course->course_title . ' (' . $details->published_course->course->course_code . ')',
                            'grade' => $change->exam_grade_change->grade,
                            'reason' => $change->exam_grade_change->reason,
                            'department_approved_by' => $this->getStaffFullName($change->exam_grade_change->department_approved_by),
                            'college_approved_by' => $this->getStaffFullName($change->exam_grade_change->college_approved_by),
                            'registrar_approved_by' => $this->getStaffFullName($change->exam_grade_change->registrar_approved_by),
                            'department_approval_date' => $change->exam_grade_change->department_approval_date,
                            'registrar_approval_date' => $change->exam_grade_change->registrar_approval_date,
                            'college_approval_date' => $change->exam_grade_change->college_approval_date,
                            'manual_ng_conversion' => $change->exam_grade_change->manual_ng_conversion,
                            'auto_ng_conversion' => $change->exam_grade_change->auto_ng_conversion,
                            'makeup_exam_result' => $change->exam_grade_change->makeup_exam_result,
                            'result' => $change->exam_grade_change->result,
                            'initiated_by_department' => $change->exam_grade_change->initiated_by_department,
                            'full_name' => $details->student->full_name,
                            'student_id' => $details->student->id,
                            'studentnumber' => $details->student->studentnumber,
                            'gender' => $details->student->gender,
                            'graduated' => $details->student->graduated
                        ];

                        $staffFull = $details->published_course->course_instructor_assignments[0]->staff->title->title . '. ' . $details->published_course->course_instructor_assignments[0]->staff->full_name;
                        $gradeChangeList[$staffFull][] = $gradeDetail;
                    }

                    $courseAddsTable =  TableRegistry::getTableLocator()->get('CourseAdds');
                    $addChanges = $courseAddsTable->find()
                        ->select([
                            'CourseAdds.id',
                            'PublishedCourses.department_id',
                            'ExamGrades.grade',
                            'ExamGradeChanges.grade',
                            'ExamGradeChanges.reason',
                            'CourseInstructorAssignments.staff_id',
                            'ExamGradeChanges.department_approved_by',
                            'ExamGradeChanges.college_approved_by',
                            'ExamGradeChanges.registrar_approved_by',
                            'ExamGradeChanges.college_reason',
                            'ExamGradeChanges.registrar_reason',
                            'ExamGradeChanges.department_reason',
                            'ExamGradeChanges.department_approval_date',
                            'ExamGradeChanges.registrar_approval_date',
                            'ExamGradeChanges.college_approval_date',
                            'ExamGradeChanges.manual_ng_conversion',
                            'ExamGradeChanges.auto_ng_conversion',
                            'ExamGradeChanges.makeup_exam_result',
                            'ExamGradeChanges.result',
                            'ExamGradeChanges.initiated_by_department'
                        ])
                        ->innerJoinWith('ExamGrades', function ($q) {
                            return $q->where(['ExamGrades.registrar_approval' => 1, 'ExamGrades.course_add_id IS NOT NULL']);
                        })
                        ->innerJoinWith('ExamGrades.ExamGradeChanges', function ($q) {
                            return $q->where(['ExamGradeChanges.registrar_approval' => 1]);
                        })
                        ->innerJoinWith('PublishedCourses')
                        ->innerJoinWith('PublishedCourses.CourseInstructorAssignments')
                        ->where($internalAddConditions)
                        ->where(['CourseAdds.department_approval' => 1, 'CourseAdds.registrar_confirmation' => 1])
                        ->toArray();

                    foreach ($addChanges as $change) {
                        $details = $courseAddsTable->find()
                            ->where(['CourseAdds.id' => $change->id])
                            ->contain([
                                'PublishedCourses' => [
                                    'CourseInstructorAssignments' => [
                                        'Staffs' => ['Titles', 'Positions']
                                    ],
                                    'GivenByDepartments',
                                    'Courses'
                                ],
                                'Students'
                            ])
                            ->first();

                        $gradeDetail = [
                            'oldGrade' => $change->exam_grade->grade,
                            'course' => $details->published_course->course->course_title . ' (' . $details->published_course->course->course_code . ')',
                            'grade' => $change->exam_grade_change->grade,
                            'reason' => $change->exam_grade_change->reason,
                            'department_approved_by' => $this->getStaffFullName($change->exam_grade_change->department_approved_by),
                            'college_approved_by' => $this->getStaffFullName($change->exam_grade_change->college_approved_by),
                            'registrar_approved_by' => $this->getStaffFullName($change->exam_grade_change->registrar_approved_by),
                            'department_approval_date' => $change->exam_grade_change->department_approval_date,
                            'registrar_approval_date' => $change->exam_grade_change->registrar_approval_date,
                            'college_approval_date' => $change->exam_grade_change->college_approval_date,
                            'manual_ng_conversion' => $change->exam_grade_change->manual_ng_conversion,
                            'auto_ng_conversion' => $change->exam_grade_change->auto_ng_conversion,
                            'makeup_exam_result' => $change->exam_grade_change->makeup_exam_result,
                            'result' => $change->exam_grade_change->result,
                            'initiated_by_department' => $change->exam_grade_change->initiated_by_department,
                            'full_name' => $details->student->full_name,
                            'student_id' => $details->student->id,
                            'studentnumber' => $details->student->studentnumber,
                            'gender' => $details->student->gender,
                            'graduated' => $details->student->graduated
                        ];

                        $staffFull = $details->published_course->course_instructor_assignments[0]->staff->title->title . '. ' . $details->published_course->course_instructor_assignments[0]->staff->full_name;
                        $gradeChangeList[$staffFull][] = $gradeDetail;
                    }

                    $makeupExamsTable =  TableRegistry::getTableLocator()->get('MakeupExams');
                    $makeupChanges = $makeupExamsTable->find()
                        ->select([
                            'MakeupExams.id',
                            'PublishedCourses.department_id',
                            'ExamGrades.grade',
                            'ExamGradeChanges.grade',
                            'ExamGradeChanges.reason',
                            'CourseInstructorAssignments.staff_id',
                            'ExamGradeChanges.department_approved_by',
                            'ExamGradeChanges.college_approved_by',
                            'ExamGradeChanges.registrar_approved_by',
                            'ExamGradeChanges.college_reason',
                            'ExamGradeChanges.registrar_reason',
                            'ExamGradeChanges.department_reason',
                            'ExamGradeChanges.department_approval_date',
                            'ExamGradeChanges.registrar_approval_date',
                            'ExamGradeChanges.college_approval_date',
                            'ExamGradeChanges.manual_ng_conversion',
                            'ExamGradeChanges.auto_ng_conversion',
                            'ExamGradeChanges.makeup_exam_result',
                            'ExamGradeChanges.result',
                            'ExamGradeChanges.initiated_by_department'
                        ])
                        ->innerJoinWith('ExamGrades', function ($q) {
                            return $q->where(['ExamGrades.registrar_approval' => 1]);
                        })
                        ->innerJoinWith('ExamGrades.ExamGradeChanges', function ($q) {
                            return $q->where(['ExamGradeChanges.registrar_approval' => 1]);
                        })
                        ->innerJoinWith('PublishedCourses')
                        ->innerJoinWith('PublishedCourses.CourseInstructorAssignments')
                        ->where($internalMakeupConditions)
                        ->toArray();

                    foreach ($makeupChanges as $change) {
                        $details = $makeupExamsTable->find()
                            ->where(['MakeupExams.id' => $change->id])
                            ->contain([
                                'PublishedCourses' => [
                                    'CourseInstructorAssignments' => [
                                        'Staffs' => ['Titles', 'Positions']
                                    ],
                                    'GivenByDepartments',
                                    'Courses'
                                ],
                                'Students'
                            ])
                            ->first();

                        $gradeDetail = [
                            'oldGrade' => $change->exam_grade->grade,
                            'course' => $details->published_course->course->course_title . ' (' . $details->published_course->course->course_code . ')',
                            'grade' => $change->exam_grade_change->grade,
                            'reason' => $change->exam_grade_change->reason,
                            'department_approved_by' => $this->getStaffFullName($change->exam_grade_change->department_approved_by),
                            'college_approved_by' => $this->getStaffFullName($change->exam_grade_change->college_approved_by),
                            'registrar_approved_by' => $this->getStaffFullName($change->exam_grade_change->registrar_approved_by),
                            'department_approval_date' => $change->exam_grade_change->department_approval_date,
                            'registrar_approval_date' => $change->exam_grade_change->registrar_approval_date,
                            'college_approval_date' => $change->exam_grade_change->college_approval_date,
                            'manual_ng_conversion' => $change->exam_grade_change->manual_ng_conversion,
                            'auto_ng_conversion' => $change->exam_grade_change->auto_ng_conversion,
                            'makeup_exam_result' => $change->exam_grade_change->makeup_exam_result,
                            'result' => $change->exam_grade_change->result,
                            'initiated_by_department' => $change->exam_grade_change->initiated_by_department,
                            'full_name' => $details->student->full_name,
                            'student_id' => $details->student->id,
                            'studentnumber' => $details->student->studentnumber,
                            'gender' => $details->student->gender,
                            'graduated' => $details->student->graduated
                        ];

                        $staffFull = $details->published_course->course_instructor_assignments[0]->staff->title->title . '. ' . $details->published_course->course_instructor_assignments[0]->staff->full_name;
                        $gradeChangeList[$staffFull][] = $gradeDetail;
                    }
                }
            }
        } else {
            $collegeId = explode('~', $departmentId);
            $collegeIds = empty($collegeId[1]) ? array_column($colleges, 'id') : [$collegeId[1]];
            $internalConditions = array_merge($conditions, [
                'PublishedCourses.college_id IN' => $collegeIds,
                'PublishedCourses.department_id IS' => null
            ]);
            $internalAddConditions = array_merge($addConditions, [
                'PublishedCourses.college_id IN' => $collegeIds,
                'PublishedCourses.department_id IS' => null
            ]);
            $internalMakeupConditions = array_merge($makeupConditions, [
                'PublishedCourses.college_id IN' => $collegeIds,
                'PublishedCourses.department_id IS' => null
            ]);

            $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
            $registrationChanges = $courseRegistrationsTable->find()
                ->select([
                    'CourseRegistrations.id',
                    'PublishedCourses.department_id',
                    'ExamGrades.grade',
                    'ExamGradeChanges.grade',
                    'ExamGradeChanges.reason',
                    'CourseInstructorAssignments.staff_id',
                    'ExamGradeChanges.department_approved_by',
                    'ExamGradeChanges.college_approved_by',
                    'ExamGradeChanges.registrar_approved_by',
                    'ExamGradeChanges.college_reason',
                    'ExamGradeChanges.registrar_reason',
                    'ExamGradeChanges.department_reason',
                    'ExamGradeChanges.department_approval_date',
                    'ExamGradeChanges.registrar_approval_date',
                    'ExamGradeChanges.college_approval_date',
                    'ExamGradeChanges.manual_ng_conversion',
                    'ExamGradeChanges.auto_ng_conversion',
                    'ExamGradeChanges.makeup_exam_result',
                    'ExamGradeChanges.result',
                    'ExamGradeChanges.initiated_by_department'
                ])
                ->innerJoinWith('ExamGrades', function ($q) {
                    return $q->where(['ExamGrades.registrar_approval' => 1]);
                })
                ->innerJoinWith('ExamGrades.ExamGradeChanges', function ($q) {
                    return $q->where(['ExamGradeChanges.registrar_approval' => 1]);
                })
                ->innerJoinWith('PublishedCourses')
                ->innerJoinWith('PublishedCourses.CourseInstructorAssignments')
                ->where($internalConditions)
                ->toArray();

            foreach ($registrationChanges as $change) {
                $details = $courseRegistrationsTable->find()
                    ->where(['CourseRegistrations.id' => $change->id])
                    ->contain([
                        'PublishedCourses' => [
                            'CourseInstructorAssignments' => [
                                'Staffs' => ['Titles', 'Positions']
                            ],
                            'GivenByDepartments',
                            'Courses'
                        ],
                        'Students'
                    ])
                    ->first();

                $gradeDetail = [
                    'oldGrade' => $change->exam_grade->grade,
                    'course' => $details->published_course->course->course_title . ' (' . $details->published_course->course->course_code . ')',
                    'grade' => $change->exam_grade_change->grade,
                    'reason' => $change->exam_grade_change->reason,
                    'department_approved_by' => $this->getStaffFullName($change->exam_grade_change->department_approved_by),
                    'college_approved_by' => $this->getStaffFullName($change->exam_grade_change->college_approved_by),
                    'registrar_approved_by' => $this->getStaffFullName($change->exam_grade_change->registrar_approved_by),
                    'department_approval_date' => $change->exam_grade_change->department_approval_date,
                    'registrar_approval_date' => $change->exam_grade_change->registrar_approval_date,
                    'college_approval_date' => $change->exam_grade_change->college_approval_date,
                    'manual_ng_conversion' => $change->exam_grade_change->manual_ng_conversion,
                    'auto_ng_conversion' => $change->exam_grade_change->auto_ng_conversion,
                    'makeup_exam_result' => $change->exam_grade_change->makeup_exam_result,
                    'result' => $change->exam_grade_change->result,
                    'initiated_by_department' => $change->exam_grade_change->initiated_by_department,
                    'full_name' => $details->student->full_name,
                    'student_id' => $details->student->id,
                    'studentnumber' => $details->student->studentnumber,
                    'gender' => $details->student->gender,
                    'graduated' => $details->student->graduated
                ];

                $staffFull = $details->published_course->course_instructor_assignments[0]->staff->title->title . ' ' . $details->published_course->course_instructor_assignments[0]->staff->full_name;
                $gradeChangeList[$staffFull][] = $gradeDetail;
            }

            $courseAddsTable =  TableRegistry::getTableLocator()->get('CourseAdds');
            $addChanges = $courseAddsTable->find()
                ->select([
                    'CourseAdds.id',
                    'PublishedCourses.department_id',
                    'ExamGrades.grade',
                    'ExamGradeChanges.grade',
                    'ExamGradeChanges.reason',
                    'CourseInstructorAssignments.staff_id',
                    'ExamGradeChanges.department_approved_by',
                    'ExamGradeChanges.college_approved_by',
                    'ExamGradeChanges.registrar_approved_by',
                    'ExamGradeChanges.college_reason',
                    'ExamGradeChanges.registrar_reason',
                    'ExamGradeChanges.department_reason',
                    'ExamGradeChanges.department_approval_date',
                    'ExamGradeChanges.registrar_approval_date',
                    'ExamGradeChanges.college_approval_date',
                    'ExamGradeChanges.manual_ng_conversion',
                    'ExamGradeChanges.auto_ng_conversion',
                    'ExamGradeChanges.makeup_exam_result',
                    'ExamGradeChanges.result',
                    'ExamGradeChanges.initiated_by_department'
                ])
                ->innerJoinWith('ExamGrades', function ($q) {
                    return $q->where(['ExamGrades.registrar_approval' => 1, 'ExamGrades.course_add_id IS NOT NULL']);
                })
                ->innerJoinWith('ExamGrades.ExamGradeChanges', function ($q) {
                    return $q->where(['ExamGradeChanges.registrar_approval' => 1]);
                })
                ->innerJoinWith('PublishedCourses')
                ->innerJoinWith('PublishedCourses.CourseInstructorAssignments')
                ->where($internalAddConditions)
                ->where(['CourseAdds.department_approval' => 1, 'CourseAdds.registrar_confirmation' => 1])
                ->toArray();

            foreach ($addChanges as $change) {
                $details = $courseAddsTable->find()
                    ->where(['CourseAdds.id' => $change->id])
                    ->contain([
                        'PublishedCourses' => [
                            'CourseInstructorAssignments' => [
                                'Staffs' => ['Titles', 'Positions']
                            ],
                            'GivenByDepartments',
                            'Courses'
                        ],
                        'Students'
                    ])
                    ->first();

                $gradeDetail = [
                    'oldGrade' => $change->exam_grade->grade,
                    'course' => $details->published_course->course->course_title . ' (' . $details->published_course->course->course_code . ')',
                    'grade' => $change->exam_grade_change->grade,
                    'reason' => $change->exam_grade_change->reason,
                    'department_approved_by' => $this->getStaffFullName($change->exam_grade_change->department_approved_by),
                    'college_approved_by' => $this->getStaffFullName($change->exam_grade_change->college_approved_by),
                    'registrar_approved_by' => $this->getStaffFullName($change->exam_grade_change->registrar_approved_by),
                    'department_approval_date' => $change->exam_grade_change->department_approval_date,
                    'registrar_approval_date' => $change->exam_grade_change->registrar_approval_date,
                    'college_approval_date' => $change->exam_grade_change->college_approval_date,
                    'manual_ng_conversion' => $change->exam_grade_change->manual_ng_conversion,
                    'auto_ng_conversion' => $change->exam_grade_change->auto_ng_conversion,
                    'makeup_exam_result' => $change->exam_grade_change->makeup_exam_result,
                    'result' => $change->exam_grade_change->result,
                    'initiated_by_department' => $change->exam_grade_change->initiated_by_department,
                    'full_name' => $details->student->full_name,
                    'student_id' => $details->student->id,
                    'studentnumber' => $details->student->studentnumber,
                    'gender' => $details->student->gender,
                    'graduated' => $details->student->graduated
                ];

                $staffFull = $details->published_course->course_instructor_assignments[0]->staff->title->title . '. ' . $details->published_course->course_instructor_assignments[0]->staff->full_name;
                $gradeChangeList[$staffFull][] = $gradeDetail;
            }

            $makeupExamsTable =  TableRegistry::getTableLocator()->get('MakeupExams');
            $makeupChanges = $makeupExamsTable->find()
                ->select([
                    'MakeupExams.id',
                    'PublishedCourses.department_id',
                    'ExamGrades.grade',
                    'ExamGradeChanges.grade',
                    'ExamGradeChanges.reason',
                    'CourseInstructorAssignments.staff_id',
                    'ExamGradeChanges.department_approved_by',
                    'ExamGradeChanges.college_approved_by',
                    'ExamGradeChanges.registrar_approved_by',
                    'ExamGradeChanges.college_reason',
                    'ExamGradeChanges.registrar_reason',
                    'ExamGradeChanges.department_reason',
                    'ExamGradeChanges.department_approval_date',
                    'ExamGradeChanges.registrar_approval_date',
                    'ExamGradeChanges.college_approval_date',
                    'ExamGradeChanges.manual_ng_conversion',
                    'ExamGradeChanges.auto_ng_conversion',
                    'ExamGradeChanges.makeup_exam_result',
                    'ExamGradeChanges.result',
                    'ExamGradeChanges.initiated_by_department'
                ])
                ->innerJoinWith('ExamGrades', function ($q) {
                    return $q->where(['ExamGrades.registrar_approval' => 1]);
                })
                ->innerJoinWith('ExamGrades.ExamGradeChanges', function ($q) {
                    return $q->where(['ExamGradeChanges.registrar_approval' => 1]);
                })
                ->innerJoinWith('PublishedCourses')
                ->innerJoinWith('PublishedCourses.CourseInstructorAssignments')
                ->where($internalMakeupConditions)
                ->toArray();

            foreach ($makeupChanges as $change) {
                $details = $makeupExamsTable->find()
                    ->where(['MakeupExams.id' => $change->id])
                    ->contain([
                        'PublishedCourses' => [
                            'CourseInstructorAssignments' => [
                                'Staffs' => ['Titles', 'Positions']
                            ],
                            'GivenByDepartments',
                            'Courses'
                        ],
                        'Students'
                    ])
                    ->first();

                $gradeDetail = [
                    'oldGrade' => $change->exam_grade->grade,
                    'course' => $details->published_course->course->course_title . ' (' . $details->published_course->course->course_code . ')',
                    'grade' => $change->exam_grade_change->grade,
                    'reason' => $change->exam_grade_change->reason,
                    'department_approved_by' => $this->getStaffFullName($change->exam_grade_change->department_approved_by),
                    'college_approved_by' => $this->getStaffFullName($change->exam_grade_change->college_approved_by),
                    'registrar_approved_by' => $this->getStaffFullName($change->exam_grade_change->registrar_approved_by),
                    'department_approval_date' => $change->exam_grade_change->department_approval_date,
                    'registrar_approval_date' => $change->exam_grade_change->registrar_approval_date,
                    'college_approval_date' => $change->exam_grade_change->college_approval_date,
                    'manual_ng_conversion' => $change->exam_grade_change->manual_ng_conversion,
                    'auto_ng_conversion' => $change->exam_grade_change->auto_ng_conversion,
                    'makeup_exam_result' => $change->exam_grade_change->makeup_exam_result,
                    'result' => $change->exam_grade_change->result,
                    'initiated_by_department' => $change->exam_grade_change->initiated_by_department,
                    'full_name' => $details->student->full_name,
                    'student_id' => $details->student->id,
                    'studentnumber' => $details->student->studentnumber,
                    'gender' => $details->student->gender,
                    'graduated' => $details->student->graduated
                ];

                $staffFull = $details->published_course->course_instructor_assignments[0]->staff->title->title . '. ' . $details->published_course->course_instructor_assignments[0]->staff->full_name;
                $gradeChangeList[$staffFull][] = $gradeDetail;
            }
        }

        return $gradeChangeList;
    }

    /**
     * Gets list of courses with no grades submitted
     */
    public function getNotGradeSubmittedList(?string $academicYear = null, ?string $semester = null, ?string $programId = null, ?string $programTypeId = null, ?string $departmentId = null, ?string $yearLevelId = null, int $freshman = 0): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $lateGradeSubmissionList = [];
        $conditions = [];

        if (!empty($programId)) {
            $programIds = explode('~', $programId);
            $conditions['PublishedCourses.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }

        if (!empty($programTypeId)) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['PublishedCourses.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        if (!empty($academicYear)) {
            $conditions['PublishedCourses.academic_year'] = $academicYear;
            $conditions['CourseRegistrations.academic_year'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['PublishedCourses.semester'] = $semester;
            $conditions['CourseRegistrations.semester'] = $semester;
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');

        if ($freshman == 0) {
            $deptConditions = [];
            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $deptConditions['Departments.college_id'] = $collegeId[1];
                    $colleges = $collegesTable->find()
                        ->where(['id' => $collegeId[1]])
                        ->toArray();
                } else {
                    $deptConditions['Departments.id'] = $departmentId;
                }
            }
            $departments = $departmentsTable->find()
                ->where($deptConditions)
                ->contain(['Colleges', 'YearLevels'])
                ->toArray();
            if (isset($colleges)) {
                $departments = array_merge($departments, $colleges);
            } else {
                $colleges = $collegesTable->find()->toArray();
                $departments = array_merge($departments, $colleges);
            }
        }

        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable =  TableRegistry::getTableLocator()->get('ExamGrades');
        $academicCalendarsTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');

        if ($freshman == 0) {
            foreach ($departments as $value) {
                $internalConditions = $conditions;
                $yearLevels = isset($value->year_levels) ? $value->year_levels : [];
                if (!isset($value->year_levels)) {
                    $internalConditions['PublishedCourses.year_level_id IS'] = null;
                    $internalConditions['PublishedCourses.department_id IS'] = null;
                    $internalConditions['PublishedCourses.college_id'] = $value->id;

                    $publishedCourses = $courseRegistrationsTable->find()
                        ->select(['published_course_id'])
                        ->distinct(['published_course_id'])
                        ->innerJoinWith('PublishedCourses')
                        ->innerJoinWith('PublishedCourses.CourseInstructorAssignments', function ($q) use ($semester, $academicYear) {
                            return $q->where(['CourseInstructorAssignments.semester' => $semester, 'CourseInstructorAssignments.academic_year' => $academicYear, 'CourseInstructorAssignments.isprimary' => 1]);
                        })
                        ->where($internalConditions)
                        ->where(['CourseRegistrations.id NOT IN' => $examGradesTable->find()->select(['course_registration_id'])->where(['course_registration_id IS NOT NULL'])])
                        ->toArray();

                    foreach ($publishedCourses as $course) {
                        $gradeSubmitted = $examGradesTable->isGradeSubmitted($course->published_course_id);
                        if (empty($gradeSubmitted)) {
                            $courseInstructorAssignment = $courseRegistrationsTable->PublishedCourses->CourseInstructorAssignments->find()
                                ->where(['CourseInstructorAssignments.published_course_id' => $course->published_course_id])
                                ->contain([
                                    'Sections' => ['YearLevels', 'Programs', 'ProgramTypes', 'Departments' => ['Colleges']],
                                    'PublishedCourses' => ['GivenByDepartments' => ['Colleges'], 'Courses', 'YearLevels'],
                                    'Staffs' => ['Titles', 'Positions']
                                ])
                                ->first();

                            $gradeSubmissionDeadline = $academicCalendarsTable->recentAcademicYearSchedule(
                                $courseInstructorAssignment->academic_year,
                                $courseInstructorAssignment->semester,
                                $courseInstructorAssignment->published_course->program_id,
                                $courseInstructorAssignment->published_course->program_type_id,
                                $courseInstructorAssignment->published_course->department_id,
                                $courseInstructorAssignment->published_course->year_level->name
                            );
                            $courseInstructorAssignment->grade_submission_deadline = $gradeSubmissionDeadline->grade_submission_end_date;

                            $lateGradeSubmissionList[$courseInstructorAssignment->published_course->given_by_department->name]
                            [$courseInstructorAssignment->published_course->course->course_title . '(' . $courseInstructorAssignment->published_course->course->course_code . ')']
                            [$course->published_course_id] = $courseInstructorAssignment;
                        }
                    }
                } else {
                    $yearLevels = !empty($yearLevelId) ? array_filter($yearLevels, function ($yl) use ($yearLevelId) {
                        return strcasecmp($yl->name, $yearLevelId) === 0;
                    }) : $yearLevels;

                    foreach ($yearLevels as $yearLevel) {
                        $internalConditions['PublishedCourses.year_level_id'] = $yearLevel->id;
                        $internalConditions['PublishedCourses.department_id'] = $value->id;

                        $publishedCourses = $courseRegistrationsTable->find()
                            ->select(['published_course_id'])
                            ->distinct(['published_course_id'])
                            ->innerJoinWith('PublishedCourses')
                            ->innerJoinWith('PublishedCourses.CourseInstructorAssignments', function ($q) use ($semester, $academicYear) {
                                return $q->where(['CourseInstructorAssignments.semester' => $semester, 'CourseInstructorAssignments.academic_year' => $academicYear, 'CourseInstructorAssignments.isprimary' => 1]);
                            })
                            ->where($internalConditions)
                            ->where(['CourseRegistrations.id NOT IN' => $examGradesTable->find()->select(['course_registration_id'])->where(['course_registration_id IS NOT NULL'])])
                            ->toArray();

                        foreach ($publishedCourses as $course) {
                            $gradeSubmitted = $examGradesTable->isGradeSubmitted($course->published_course_id);
                            if (empty($gradeSubmitted)) {
                                $courseInstructorAssignment = $courseRegistrationsTable->PublishedCourses->CourseInstructorAssignments->find()
                                    ->where(['CourseInstructorAssignments.published_course_id' => $course->published_course_id])
                                    ->contain([
                                        'Sections' => ['YearLevels', 'Programs', 'ProgramTypes', 'Departments' => ['Colleges']],
                                        'PublishedCourses' => ['GivenByDepartments' => ['Colleges'], 'Courses', 'YearLevels'],
                                        'Staffs' => ['Titles', 'Positions']
                                    ])
                                    ->first();

                                $gradeSubmissionDeadline = $academicCalendarsTable->recentAcademicYearSchedule(
                                    $courseInstructorAssignment->academic_year,
                                    $courseInstructorAssignment->semester,
                                    $courseInstructorAssignment->published_course->program_id,
                                    $courseInstructorAssignment->published_course->program_type_id,
                                    $courseInstructorAssignment->published_course->department_id,
                                    $courseInstructorAssignment->published_course->year_level->name
                                );
                                $courseInstructorAssignment->grade_submission_deadline = $gradeSubmissionDeadline->grade_submission_end_date;

                                $lateGradeSubmissionList[$courseInstructorAssignment->published_course->given_by_department->name]
                                [$courseInstructorAssignment->published_course->course->course_title . '(' . $courseInstructorAssignment->published_course->course->course_code . ')']
                                [$course->published_course_id] = $courseInstructorAssignment;
                            }
                        }
                    }
                }
            }
        } else {
            $collegeId = explode('~', $departmentId);
            $internalConditions = array_merge($conditions, [
                'PublishedCourses.college_id' => !empty($collegeId[1]) ? $collegeId[1] : null,
                'PublishedCourses.department_id IS' => null
            ]);

            $publishedCourses = $courseRegistrationsTable->find()
                ->select(['published_course_id'])
                ->distinct(['published_course_id'])
                ->innerJoinWith('PublishedCourses')
                ->innerJoinWith('PublishedCourses.CourseInstructorAssignments', function ($q) use ($semester, $academicYear) {
                    return $q->where(['CourseInstructorAssignments.semester' => $semester, 'CourseInstructorAssignments.academic_year' => $academicYear, 'CourseInstructorAssignments.isprimary' => 1]);
                })
                ->where($internalConditions)
                ->where(['CourseRegistrations.id NOT IN' => $examGradesTable->find()->select(['course_registration_id'])->where(['course_registration_id IS NOT NULL'])])
                ->toArray();

            foreach ($publishedCourses as $course) {
                $gradeSubmitted = $examGradesTable->isGradeSubmitted($course->published_course_id);
                if (empty($gradeSubmitted)) {
                    $courseInstructorAssignment = $courseRegistrationsTable->PublishedCourses->CourseInstructorAssignments->find()
                        ->where(['CourseInstructorAssignments.published_course_id' => $course->published_course_id])
                        ->contain([
                            'Sections' => ['YearLevels', 'Programs', 'ProgramTypes', 'Departments' => ['Colleges']],
                            'PublishedCourses' => ['GivenByDepartments' => ['Colleges'], 'Courses', 'YearLevels'],
                            'Staffs' => ['Titles', 'Positions']
                        ])
                        ->first();

                    $gradeSubmissionDeadline = $academicCalendarsTable->recentAcademicYearSchedule(
                        $courseInstructorAssignment->academic_year,
                        $courseInstructorAssignment->semester,
                        $courseInstructorAssignment->published_course->program_id,
                        $courseInstructorAssignment->published_course->program_type_id,
                        $courseInstructorAssignment->published_course->department_id,
                        $courseInstructorAssignment->published_course->year_level->name
                    );
                    $courseInstructorAssignment->grade_submission_deadline = $gradeSubmissionDeadline->grade_submission_end_date;

                    $lateGradeSubmissionList[$courseInstructorAssignment->published_course->given_by_department->name]
                    [$courseInstructorAssignment->published_course->course->course_title . '(' . $courseInstructorAssignment->published_course->course->course_code . ')']
                    [$course->published_course_id] = $courseInstructorAssignment;
                }
            }
        }

        return $lateGradeSubmissionList;
    }

    /**
     * Gets list of instructors who submitted grades
     */
    public function getGradeSubmittedInstructorList(?string $academicYear = null, ?string $semester = null, ?string $programId = null, ?string $programTypeId = null, ?string $departmentId = null, ?string $yearLevelId = null, int $freshman = 0): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $lateGradeSubmissionList = [];
        $conditions = [];
        $addConditions = [];

        if (!empty($programId)) {
            $programIds = explode('~', $programId);
            $conditions['PublishedCourses.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
            $addConditions['PublishedCourses.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }

        if (!empty($programTypeId)) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['PublishedCourses.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
            $addConditions['PublishedCourses.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        if (!empty($academicYear)) {
            $conditions['PublishedCourses.academic_year'] = $academicYear;
            $conditions['CourseRegistrations.academic_year'] = $academicYear;
            $addConditions['PublishedCourses.academic_year'] = $academicYear;
            $addConditions['CourseAdds.academic_year'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['PublishedCourses.semester'] = $semester;
            $conditions['CourseRegistrations.semester'] = $semester;
            $addConditions['PublishedCourses.semester'] = $semester;
            $addConditions['CourseAdds.semester'] = $semester;
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');

        $deptConditions = ['Departments.active' => 1];
        $collegeConditions = ['Colleges.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeConditions['Colleges.id'] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();
        $colleges = $collegesTable->find()
            ->where($collegeConditions)
            ->toArray();

        if ($freshman == 1) {
            $departments = [];
        }

        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable =  TableRegistry::getTableLocator()->get('ExamGrades');
        $academicCalendarsTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $value) {
                $yearLevels = !empty($yearLevelId) ? array_filter($value->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $value->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $internalConditions = array_merge($conditions, [
                        'PublishedCourses.year_level_id' => $yearLevel->id,
                        'PublishedCourses.department_id' => $value->id
                    ]);
                    $internalAddConditions = array_merge($addConditions, [
                        'PublishedCourses.year_level_id' => $yearLevel->id,
                        'PublishedCourses.department_id' => $value->id
                    ]);

                    $publishedCourses = $courseRegistrationsTable->find()
                        ->select(['published_course_id'])
                        ->distinct(['published_course_id'])
                        ->innerJoinWith('PublishedCourses')
                        ->innerJoinWith('PublishedCourses.CourseInstructorAssignments', function ($q) use ($semester, $academicYear) {
                            return $q->where(['CourseInstructorAssignments.semester' => $semester, 'CourseInstructorAssignments.academic_year' => $academicYear, 'CourseInstructorAssignments.isprimary' => 1]);
                        })
                        ->where($internalConditions)
                        ->where(['CourseRegistrations.id IN' => $examGradesTable->find()->select(['course_registration_id'])->where(['course_registration_id IS NOT NULL'])])
                        ->toArray();

                    foreach ($publishedCourses as $course) {
                        $gradeSubmitted = $examGradesTable->isGradeSubmitted($course->published_course_id);
                        if (!empty($gradeSubmitted)) {
                            $courseInstructorAssignment = $courseRegistrationsTable->PublishedCourses->CourseInstructorAssignments->find()
                                ->where(['CourseInstructorAssignments.published_course_id' => $course->published_course_id])
                                ->contain([
                                    'Sections' => ['YearLevels', 'Programs', 'ProgramTypes', 'Departments' => ['Colleges']],
                                    'PublishedCourses' => ['GivenByDepartments' => ['Colleges'], 'Courses', 'YearLevels'],
                                    'Staffs' => ['Titles', 'Positions']
                                ])
                                ->first();

                            $gradeSubmissionDeadline = $academicCalendarsTable->recentAcademicYearSchedule(
                                $courseInstructorAssignment->academic_year,
                                $courseInstructorAssignment->semester,
                                $courseInstructorAssignment->published_course->program_id,
                                $courseInstructorAssignment->published_course->program_type_id,
                                $courseInstructorAssignment->published_course->department_id,
                                $courseInstructorAssignment->published_course->year_level->name
                            );
                            $courseInstructorAssignment->grade_submission_deadline = $gradeSubmissionDeadline->grade_submission_end_date;

                            $lateGradeSubmissionList[$courseInstructorAssignment->published_course->given_by_department->name]
                            [$courseInstructorAssignment->published_course->course->course_title . '(' . $courseInstructorAssignment->published_course->course->course_code . ')']
                            [$course->published_course_id] = $courseInstructorAssignment;
                        }
                    }

                    $courseAddsTable =  TableRegistry::getTableLocator()->get('CourseAdds');
                    $publishedAddCourses = $courseAddsTable->find()
                        ->select(['published_course_id'])
                        ->distinct(['published_course_id'])
                        ->innerJoinWith('PublishedCourses')
                        ->innerJoinWith('PublishedCourses.CourseInstructorAssignments', function ($q) use ($semester, $academicYear) {
                            return $q->where(['CourseInstructorAssignments.semester' => $semester, 'CourseInstructorAssignments.academic_year' => $academicYear, 'CourseInstructorAssignments.isprimary' => 1]);
                        })
                        ->where($internalAddConditions)
                        ->where(['CourseAdds.id IN' => $examGradesTable->find()->select(['course_add_id'])->where(['course_add_id IS NOT NULL'])])
                        ->toArray();

                    foreach ($publishedAddCourses as $course) {
                        $gradeSubmitted = $examGradesTable->isGradeSubmitted($course->published_course_id);
                        if (!empty($gradeSubmitted)) {
                            $courseInstructorAssignment = $courseRegistrationsTable->PublishedCourses->CourseInstructorAssignments->find()
                                ->where(['CourseInstructorAssignments.published_course_id' => $course->published_course_id])
                                ->contain([
                                    'Sections' => ['YearLevels', 'Programs', 'ProgramTypes', 'Departments' => ['Colleges']],
                                    'PublishedCourses' => ['GivenByDepartments' => ['Colleges'], 'Courses', 'YearLevels'],
                                    'Staffs' => ['Titles', 'Positions']
                                ])
                                ->first();

                            $gradeSubmissionDeadline = $academicCalendarsTable->recentAcademicYearSchedule(
                                $courseInstructorAssignment->academic_year,
                                $courseInstructorAssignment->semester,
                                $courseInstructorAssignment->published_course->program_id,
                                $courseInstructorAssignment->published_course->program_type_id,
                                $courseInstructorAssignment->published_course->department_id,
                                $courseInstructorAssignment->published_course->year_level->name
                            );
                            $courseInstructorAssignment->grade_submission_deadline = $gradeSubmissionDeadline->grade_submission_end_date;

                            $lateGradeSubmissionList[$courseInstructorAssignment->published_course->given_by_department->name]
                            [$courseInstructorAssignment->published_course->course->course_title . '(' . $courseInstructorAssignment->published_course->course->course_code . ')']
                            [$course->published_course_id] = $courseInstructorAssignment;
                        }
                    }
                }
            }
        } else {
            foreach ($colleges as $value) {
                $internalConditions = array_merge($conditions, [
                    'PublishedCourses.year_level_id IS' => null,
                    'PublishedCourses.department_id IS' => null
                ]);
                $internalAddConditions = array_merge($addConditions, [
                    'PublishedCourses.year_level_id IS' => null,
                    'PublishedCourses.department_id IS' => null
                ]);

                $publishedCourses = $courseRegistrationsTable->find()
                    ->select(['published_course_id'])
                    ->distinct(['published_course_id'])
                    ->innerJoinWith('PublishedCourses')
                    ->innerJoinWith('PublishedCourses.CourseInstructorAssignments', function ($q) use ($semester, $academicYear) {
                        return $q->where(['CourseInstructorAssignments.semester' => $semester, 'CourseInstructorAssignments.academic_year' => $academicYear, 'CourseInstructorAssignments.isprimary' => 1]);
                    })
                    ->where($internalConditions)
                    ->where(['CourseRegistrations.id IN' => $examGradesTable->find()->select(['course_registration_id'])->where(['course_registration_id IS NOT NULL'])])
                    ->toArray();

                foreach ($publishedCourses as $course) {
                    $gradeSubmitted = $examGradesTable->isGradeSubmitted($course->published_course_id);
                    if (!empty($gradeSubmitted)) {
                        $courseInstructorAssignment = $courseRegistrationsTable->PublishedCourses->CourseInstructorAssignments->find()
                            ->where(['CourseInstructorAssignments.published_course_id' => $course->published_course_id])
                            ->contain([
                                'Sections' => ['YearLevels', 'Programs', 'ProgramTypes', 'Departments' => ['Colleges']],
                                'PublishedCourses' => ['GivenByDepartments' => ['Colleges'], 'Courses', 'YearLevels'],
                                'Staffs' => ['Titles', 'Positions']
                            ])
                            ->first();

                        $collegeId = $courseRegistrationsTable->PublishedCourses->find()
                            ->where(['PublishedCourses.id' => $course->published_course_id])
                            ->select(['college_id'])
                            ->first()
                            ->college_id;

                        $gradeSubmissionDeadline = $academicCalendarsTable->recentAcademicYearSchedule(
                            $courseInstructorAssignment->academic_year,
                            $courseInstructorAssignment->semester,
                            $courseInstructorAssignment->published_course->program_id,
                            $courseInstructorAssignment->published_course->program_type_id,
                            $courseInstructorAssignment->published_course->department_id,
                            '1st',
                            1,
                            $collegeId
                        );
                        $courseInstructorAssignment->grade_submission_deadline = $gradeSubmissionDeadline->grade_submission_end_date;

                        $lateGradeSubmissionList[$courseInstructorAssignment->published_course->given_by_department->name]
                        [$courseInstructorAssignment->published_course->course->course_title . '(' . $courseInstructorAssignment->published_course->course->course_code . ')']
                        [$course->published_course_id] = $courseInstructorAssignment;
                    }
                }

                $courseAddsTable =  TableRegistry::getTableLocator()->get('CourseAdds');
                $publishedAddCourses = $courseAddsTable->find()
                    ->select(['published_course_id'])
                    ->distinct(['published_course_id'])
                    ->innerJoinWith('PublishedCourses')
                    ->innerJoinWith('PublishedCourses.CourseInstructorAssignments', function ($q) use ($semester, $academicYear) {
                        return $q->where(['CourseInstructorAssignments.semester' => $semester, 'CourseInstructorAssignments.academic_year' => $academicYear, 'CourseInstructorAssignments.isprimary' => 1]);
                    })
                    ->where($internalAddConditions)
                    ->where(['CourseAdds.id IN' => $examGradesTable->find()->select(['course_add_id'])->where(['course_add_id IS NOT NULL'])])
                    ->toArray();

                foreach ($publishedAddCourses as $course) {
                    $gradeSubmitted = $examGradesTable->isGradeSubmitted($course->published_course_id);
                    if (!empty($gradeSubmitted)) {
                        $courseInstructorAssignment = $courseRegistrationsTable->PublishedCourses->CourseInstructorAssignments->find()
                            ->where(['CourseInstructorAssignments.published_course_id' => $course->published_course_id])
                            ->contain([
                                'Sections' => ['YearLevels', 'Programs', 'ProgramTypes', 'Departments' => ['Colleges']],
                                'PublishedCourses' => ['GivenByDepartments' => ['Colleges'], 'Courses', 'YearLevels'],
                                'Staffs' => ['Titles', 'Positions']
                            ])
                            ->first();

                        $collegeId = $courseRegistrationsTable->PublishedCourses->find()
                            ->where(['PublishedCourses.id' => $course->published_course_id])
                            ->select(['college_id'])
                            ->first()
                            ->college_id;

                        $gradeSubmissionDeadline = $academicCalendarsTable->recentAcademicYearSchedule(
                            $courseInstructorAssignment->academic_year,
                            $courseInstructorAssignment->semester,
                            $courseInstructorAssignment->published_course->program_id,
                            $courseInstructorAssignment->published_course->program_type_id,
                            $courseInstructorAssignment->published_course->department_id,
                            '1st',
                            1,
                            $collegeId
                        );
                        $courseInstructorAssignment->grade_submission_deadline = $gradeSubmissionDeadline->grade_submission_end_date;

                        $lateGradeSubmissionList[$courseInstructorAssignment->published_course->given_by_department->name]
                        [$courseInstructorAssignment->published_course->course->course_title . '(' . $courseInstructorAssignment->published_course->course->course_code . ')']
                        [$course->published_course_id] = $courseInstructorAssignment;
                    }
                }
            }
        }

        return $lateGradeSubmissionList;
    }

    /**
     * Gets list of courses with delayed grade submissions
     */
    public function getDelayedGradeSubmissionList(?string $academicYear = null, ?string $semester = null, ?string $programId = null, ?string $programTypeId = null, ?string $departmentId = null, ?string $yearLevelId = null, int $freshman = 0): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $lateGradeSubmissionList = [];
        $conditions = [];

        if (!empty($programId)) {
            $programIds = explode('~', $programId);
            $conditions['PublishedCourses.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }

        if (!empty($programTypeId)) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['PublishedCourses.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        if (!empty($academicYear)) {
            $conditions['PublishedCourses.academic_year'] = $academicYear;
            $conditions['CourseRegistrations.academic_year'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['PublishedCourses.semester'] = $semester;
            $conditions['CourseRegistrations.semester'] = $semester;
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $academicCalendarsTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable =  TableRegistry::getTableLocator()->get('ExamGrades');

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }
        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $gradeSubmissionDeadline = $academicCalendarsTable->getGradeSubmissionDate(
            $academicYear,
            $semester,
            $programId,
            $programTypeId,
            $departmentId,
            $yearLevelId
        );

        $deadlineWhere = !empty($gradeSubmissionDeadline) ? ['ExamGrades.created >=' => $gradeSubmissionDeadline] : [];

        if ($freshman == 0) {
            foreach ($departments as $value) {
                $yearLevels = !empty($yearLevelId) ? array_filter($value->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $value->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $internalConditions = array_merge($conditions, [
                        'PublishedCourses.year_level_id' => $yearLevel->id,
                        'PublishedCourses.given_by_department_id' => $value->id
                    ]);

                    $publishedCourses = $courseRegistrationsTable->find()
                        ->select(['published_course_id'])
                        ->distinct(['published_course_id'])
                        ->innerJoinWith('PublishedCourses')
                        ->innerJoinWith('PublishedCourses.CourseInstructorAssignments', function ($q) use ($semester, $academicYear) {
                            return $q->where(['CourseInstructorAssignments.semester' => $semester, 'CourseInstructorAssignments.academic_year' => $academicYear, 'CourseInstructorAssignments.isprimary' => 1]);
                        })
                        ->where($internalConditions)
                        ->where(['CourseRegistrations.id IN' => $examGradesTable->find()->select(['course_registration_id'])->where(['course_registration_id IS NOT NULL'])->where($deadlineWhere)])
                        ->toArray();

                    foreach ($publishedCourses as $course) {
                        $gradeSubmittedDate = $examGradesTable->getGradeSubmissionDate($course->published_course_id);
                        $courseInstructorAssignment = $courseRegistrationsTable->PublishedCourses->CourseInstructorAssignments->find()
                            ->where(['CourseInstructorAssignments.published_course_id' => $course->published_course_id])
                            ->contain([
                                'Sections' => ['YearLevels', 'Programs', 'ProgramTypes', 'Departments' => ['Colleges']],
                                'PublishedCourses' => ['GivenByDepartments' => ['Colleges'], 'Courses', 'YearLevels'],
                                'Staffs' => ['Titles', 'Positions']
                            ])
                            ->first();

                        $gradeSubmissionDeadline = $academicCalendarsTable->recentAcademicYearSchedule(
                            $courseInstructorAssignment->academic_year,
                            $courseInstructorAssignment->semester,
                            $courseInstructorAssignment->published_course->program_id,
                            $courseInstructorAssignment->published_course->program_type_id,
                            $courseInstructorAssignment->published_course->department_id,
                            $courseInstructorAssignment->published_course->year_level->name
                        );
                        $courseInstructorAssignment->grade_submission_deadline = $gradeSubmittedDate->created;

                        $lateGradeSubmissionList[$courseInstructorAssignment->published_course->given_by_department->name]
                        [$courseInstructorAssignment->published_course->course->course_title . '(' . $courseInstructorAssignment->published_course->course->course_code . ')']
                        [$course->published_course_id] = $courseInstructorAssignment;
                    }
                }
            }
        } else {
            $collegeId = explode('~', $departmentId);
            $internalConditions = array_merge($conditions, [
                'PublishedCourses.college_id' => !empty($collegeId[1]) ? $collegeId[1] : null,
                'PublishedCourses.department_id IS' => null,
                'OR' => [
                    'PublishedCourses.year_level_id IS' => null,
                    'PublishedCourses.year_level_id' => 0,
                    'PublishedCourses.year_level_id' => ''
                ]
            ]);

            $publishedCourses = $courseRegistrationsTable->find()
                ->select(['published_course_id'])
                ->distinct(['published_course_id'])
                ->innerJoinWith('PublishedCourses')
                ->innerJoinWith('PublishedCourses.CourseInstructorAssignments', function ($q) use ($semester, $academicYear) {
                    return $q->where(['CourseInstructorAssignments.semester' => $semester, 'CourseInstructorAssignments.academic_year' => $academicYear, 'CourseInstructorAssignments.isprimary' => 1]);
                })
                ->where($internalConditions)
                ->where(['CourseRegistrations.id IN' => $examGradesTable->find()->select(['course_registration_id'])->where(['course_registration_id IS NOT NULL'])])
                ->toArray();

            foreach ($publishedCourses as $course) {
                $gradeSubmittedDate = $examGradesTable->getGradeSubmissionDate($course->published_course_id);
                $courseInstructorAssignment = $courseRegistrationsTable->PublishedCourses->CourseInstructorAssignments->find()
                    ->where(['CourseInstructorAssignments.published_course_id' => $course->published_course_id])
                    ->contain([
                        'Sections' => ['YearLevels', 'Programs', 'ProgramTypes', 'Departments' => ['Colleges']],
                        'PublishedCourses' => ['GivenByDepartments' => ['Colleges'], 'Courses', 'YearLevels'],
                        'Staffs' => ['Titles', 'Positions']
                    ])
                    ->first();

                $gradeSubmissionDeadline = $academicCalendarsTable->recentAcademicYearSchedule(
                    $courseInstructorAssignment->academic_year,
                    $courseInstructorAssignment->semester,
                    $courseInstructorAssignment->published_course->program_id,
                    $courseInstructorAssignment->published_course->program_type_id,
                    $courseInstructorAssignment->published_course->department_id,
                    $courseInstructorAssignment->published_course->year_level->name
                );
                $courseInstructorAssignment->grade_submission_deadline = $gradeSubmittedDate->created;

                $lateGradeSubmissionList[$courseInstructorAssignment->published_course->given_by_department->name]
                [$courseInstructorAssignment->published_course->course->course_title . '(' . $courseInstructorAssignment->published_course->course->course_code . ')']
                [$course->published_course_id] = $courseInstructorAssignment;
            }
        }

        return $lateGradeSubmissionList;
    }

    /**
     * Gets list of dismissed students
     */
    public function getDismissedStudent(?string $academicYear = null, ?string $semester = null, $programId = 0, $programTypeId = 0, ?string $departmentId = null, string $sex = 'all', ?string $yearLevelId = null, ?int $regionId = null, int $freshman = 0, int $excludeGraduated = 0): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $dismissedLists = [];
        $conditions = ['StudentExamStatuses.academic_status_id' => DISMISSED_ACADEMIC_STATUS_ID];
        $sectionConditions = ['Sections.id IS NOT NULL'];

        if (!empty($regionId) && $regionId > 0) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        if ($excludeGraduated == 1) {
            $conditions['Students.graduated'] = 0;
        }

        if (!empty($programId)) {
            if (is_array($programId)) {
                $conditions['Students.program_id IN'] = $programId;
                $sectionConditions['Sections.program_id IN'] = $programId;
            } elseif ($programId != 0) {
                $conditions['Students.program_id'] = $programId;
                $sectionConditions['Sections.program_id'] = $programId;
            } else {
                $conditions['Students.program_id'] = 0;
                $sectionConditions['Sections.program_id'] = 0;
            }
        }

        if (!empty($programTypeId)) {
            if (is_array($programTypeId)) {
                $conditions['Students.program_type_id IN'] = $programTypeId;
                $sectionConditions['Sections.program_type_id IN'] = $programTypeId;
            } elseif ($programTypeId != 0) {
                $conditions['Students.program_type_id'] = $programTypeId;
                $sectionConditions['Sections.program_type_id'] = $programTypeId;
            } else {
                $conditions['Students.program_type_id'] = 0;
                $sectionConditions['Sections.program_type_id'] = 0;
            }
        }

        if (!empty($academicYear)) {
            $conditions['StudentExamStatuses.academic_year'] = $academicYear;
            $sectionConditions['Sections.academicyear'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['StudentExamStatuses.semester'] = $semester;
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');

        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');

        $deptConditions = ['Departments.active' => 1];
        $collegeIds = [];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programsList = array_column($programs, 'name', 'id');
        $programTypesList = array_column($programTypes, 'name', 'id');

        $count = 0;

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $collegeIds[$department->college_id] = $department->college_id;
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $deptId = $department->id;
                    $sectionConditions['Sections.year_level_id'] = $yearLevel->id;
                    $sectionConditions['Sections.department_id'] = $deptId;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->distinct(['student_id', 'section_id'])
                        ->order(['id' => 'DESC', 'modified' => 'DESC', 'section_id' => 'DESC'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $dismissedStudents = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year',
                            'Students.academicyear',
                            'Students.graduated'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students', function ($q) use ($deptId) {
                            return $q->where(['Students.department_id' => $deptId]);
                        })
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->group(['StudentExamStatuses.academic_year', 'StudentExamStatuses.semester', 'StudentExamStatuses.student_id'])
                        ->order(['Students.first_name', 'StudentExamStatuses.cgpa'])
                        ->toArray();

                    foreach ($dismissedStudents as $student) {
                        $creditType = 'Credit';
                        $section = $sectionsTable->find()
                            ->where(['id' => $studentSectionMap[$student->student->id]])
                            ->contain(['Curriculums' => ['id', 'type_credit']])
                            ->first();

                        if (!empty($section->curriculum->id) && strpos($section->curriculum->type_credit, 'ECTS') !== false) {
                            $creditType = 'ECTS';
                        }

                        $mergedData = array_merge($student->student->toArray(), $student->toArray());
                        $dismissedLists[$department->college->name . '~' . $department->name . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id] . '~' . $section->name . '~' . $student->academic_year . '~' . $student->semester . '~' . $yearLevel->name . '~' . $creditType][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        } else {
            $collegeConditions = ['Colleges.active' => 1];
            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $collegeConditions['Colleges.id'] = $collegeId[1];
                } elseif ($departmentId == 0) {
                    $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                    $collegeIds = array_column($collegeIds, 'id');
                }
            } else {
                $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                $collegeIds = array_column($collegeIds, 'id');
            }

            if (!empty($collegeIds)) {
                $colleges = $collegesTable->find()->where(['id IN' => $collegeIds])->toArray();
                foreach ($colleges as $college) {
                    $sectionConditions['Sections.college_id'] = $college->id;
                    $sectionConditions['Sections.department_id IS'] = null;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->distinct(['student_id', 'section_id'])
                        ->order(['id' => 'DESC', 'modified' => 'DESC', 'section_id' => 'DESC'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $dismissedStudents = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year',
                            'Students.academicyear',
                            'Students.graduated'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students', function ($q) use ($college) {
                            return $q->where(['Students.college_id' => $college->id, 'Students.department_id IS' => null]);
                        })
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->group(['StudentExamStatuses.academic_year', 'StudentExamStatuses.semester', 'StudentExamStatuses.student_id'])
                        ->order(['Students.first_name', 'StudentExamStatuses.cgpa'])
                        ->toArray();

                    foreach ($dismissedStudents as $student) {
                        $creditType = 'Credit';
                        $section = $sectionsTable->find()
                            ->where(['id' => $studentSectionMap[$student->student->id]])
                            ->first();

                        $programLabel = $section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman';
                        $yearLabel = $section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st';

                        $mergedData = array_merge($student->student->toArray(), $student->toArray());
                        $dismissedLists[$college->name . '~' . $programLabel . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id] . '~' . $section->name . '~' . $student->academic_year . '~' . $student->semester . '~' . $yearLabel . '~' . $creditType][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        }

        return $dismissedLists;
    }
    /**
     * Gets list of active students
     */
    public function getActiveStudent(?string $academicYear = null, ?string $semester = null, $programId = 0, $programTypeId = 0, ?string $departmentId = null, string $sex = 'all', ?string $yearLevelId = null, ?int $regionId = null, int $freshman = 0, int $excludeGraduated = 0): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $activeLists = [];
        $conditions = [];
        $sectionConditions = ['Sections.id IS NOT NULL'];
        $collegeIds = [];

        if (!empty($regionId)) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        if ($excludeGraduated == 1) {
            $conditions['Students.graduated'] = 0;
        }

        if (!empty($programId)) {
            if (is_array($programId)) {
                $conditions['Students.program_id IN'] = $programId;
                $sectionConditions['Sections.program_id IN'] = $programId;
            } elseif ($programId != 0) {
                $conditions['Students.program_id'] = $programId;
                $sectionConditions['Sections.program_id'] = $programId;
            } else {
                $conditions['Students.program_id'] = 0;
                $sectionConditions['Sections.program_id'] = 0;
            }
        }

        if (!empty($programTypeId)) {
            if (is_array($programTypeId)) {
                $conditions['Students.program_type_id IN'] = $programTypeId;
                $sectionConditions['Sections.program_type_id IN'] = $programTypeId;
            } elseif ($programTypeId != 0) {
                $conditions['Students.program_type_id'] = $programTypeId;
                $sectionConditions['Sections.program_type_id'] = $programTypeId;
            } else {
                $conditions['Students.program_type_id'] = 0;
                $sectionConditions['Sections.program_type_id'] = 0;
            }
        }

        if (!empty($academicYear)) {
            $conditions['StudentExamStatuses.academic_year'] = $academicYear;
            $sectionConditions['Sections.academicyear'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['StudentExamStatuses.semester'] = $semester;
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programsList = array_column($programs, 'name', 'id');
        $programTypesList = array_column($programTypes, 'name', 'id');

        $count = 0;

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $collegeIds[$department->college_id] = $department->college_id;
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $deptId = $department->id;
                    $sectionConditions['Sections.year_level_id'] = $yearLevel->id;
                    $sectionConditions['Sections.department_id'] = $deptId;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->distinct(['student_id', 'section_id'])
                        ->order(['id' => 'DESC', 'modified' => 'DESC', 'section_id' => 'DESC'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $activeStudents = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year',
                            'Students.academicyear',
                            'Students.graduated'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students', function ($q) use ($deptId) {
                            return $q->where(['Students.department_id' => $deptId]);
                        })
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->group(['StudentExamStatuses.academic_year', 'StudentExamStatuses.semester', 'StudentExamStatuses.student_id'])
                        ->order(['Students.first_name', 'StudentExamStatuses.cgpa'])
                        ->toArray();

                    foreach ($activeStudents as $student) {
                        $creditType = 'Credit';
                        $section = $sectionsTable->find()
                            ->where(['id' => $studentSectionMap[$student->student->id]])
                            ->contain(['Curriculums' => ['id', 'type_credit']])
                            ->first();

                        if (!empty($section->curriculum->id) && strpos($section->curriculum->type_credit, 'ECTS') !== false) {
                            $creditType = 'ECTS';
                        }

                        $mergedData = array_merge($student->student->toArray(), $student->toArray());
                        $activeLists[$department->college->name . '~' . $department->name . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id] . '~' . $section->name . '~' . $student->academic_year . '~' . $student->semester . '~' . $yearLevel->name . '~' . $creditType][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        } else {
            $collegeConditions = ['Colleges.active' => 1];
            $programsAvailable = Configure::read('programs_available_for_registrar_college_level_permissions') ?: 0;
            $programTypesAvailable = Configure::read('program_types_available_for_registrar_college_level_permissions') ?: 0;

            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $collegeConditions['Colleges.id'] = $collegeId[1];
                } elseif ($departmentId == 0) {
                    $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                    $collegeIds = array_column($collegeIds, 'id');
                }
            } else {
                $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                $collegeIds = array_column($collegeIds, 'id');
            }

            if (!empty($collegeIds)) {
                $colleges = $collegesTable->find()->where(['id IN' => $collegeIds])->toArray();
                foreach ($colleges as $college) {
                    $sectionConditions['Sections.college_id'] = $college->id;
                    $sectionConditions['Sections.department_id IS'] = null;
                    $sectionConditions['Sections.program_id'] = !empty($programId) ? $programId : $programsAvailable;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->distinct(['student_id', 'section_id'])
                        ->order(['id' => 'DESC', 'modified' => 'DESC', 'section_id' => 'DESC'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $activeStudents = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year',
                            'Students.academicyear',
                            'Students.graduated'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students', function ($q) use ($college) {
                            return $q->where(['Students.college_id' => $college->id]);
                        })
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->group(['StudentExamStatuses.academic_year', 'StudentExamStatuses.semester', 'StudentExamStatuses.student_id'])
                        ->order(['Students.first_name', 'StudentExamStatuses.cgpa'])
                        ->toArray();

                    foreach ($activeStudents as $student) {
                        $creditType = 'Credit';
                        $section = $sectionsTable->find()
                            ->where(['id' => $studentSectionMap[$student->student->id]])
                            ->first();

                        $programLabel = $section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman';
                        $yearLabel = $section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st';

                        $mergedData = array_merge($student->student->toArray(), $student->toArray());
                        $activeLists[$college->name . '~' . $programLabel . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id] . '~' . $section->name . '~' . $student->academic_year . '~' . $student->semester . '~' . $yearLabel . '~' . $creditType][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        }

        return $activeLists;
    }

    /**
     * Gets list of active students not registered
     */
    public function getActiveStudentNotRegistered(?string $academicYear = null, ?string $semester = null, $programId = null, $programTypeId = null, ?string $departmentId = null, string $sex = 'all', ?string $yearLevelId = null, ?int $regionId = null, ?string $currentAcademicYear = null, ?string $currentSemester = null, int $freshman = 0, string $excludeGraduated = ''): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $activeLists = [];
        $conditions = [];
        $sectionConditions = ['Sections.id IS NOT NULL'];
        $collegeIds = [];

        if (!empty($regionId)) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        if ($excludeGraduated == '1') {
            $conditions['Students.graduated'] = 0;
        }

        if (!empty($programId)) {
            if (is_array($programId)) {
                $conditions['Students.program_id IN'] = $programId;
                $sectionConditions['Sections.program_id IN'] = $programId;
            } elseif ($programId != 0) {
                $conditions['Students.program_id'] = $programId;
                $sectionConditions['Sections.program_id'] = $programId;
            } else {
                $conditions['Students.program_id'] = 0;
                $sectionConditions['Sections.program_id'] = 0;
            }
        }

        if (!empty($programTypeId)) {
            if (is_array($programTypeId)) {
                $conditions['Students.program_type_id IN'] = $programTypeId;
                $sectionConditions['Sections.program_type_id IN'] = $programTypeId;
            } elseif ($programTypeId != 0) {
                $conditions['Students.program_type_id'] = $programTypeId;
                $sectionConditions['Sections.program_type_id'] = $programTypeId;
            } else {
                $conditions['Students.program_type_id'] = 0;
                $sectionConditions['Sections.program_type_id'] = 0;
            }
        }

        if (!empty($academicYear)) {
            $conditions['StudentExamStatuses.academic_year'] = $academicYear;
            $sectionConditions['Sections.academicyear'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['StudentExamStatuses.semester'] = $semester;
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $graduateListsTable =  TableRegistry::getTableLocator()->get('GraduateLists');
        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programsList = array_column($programs, 'name', 'id');
        $programTypesList = array_column($programTypes, 'name', 'id');

        $count = 0;

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $collegeIds[$department->college_id] = $department->college_id;
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $deptId = $department->id;
                    $sectionConditions['Sections.year_level_id'] = $yearLevel->id;
                    $sectionConditions['Sections.department_id'] = $deptId;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->distinct(['student_id', 'section_id'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $activeStudents = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year',
                            'Students.academicyear',
                            'Students.graduated'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students', function ($q) use ($deptId) {
                            return $q->where(['Students.department_id' => $deptId, 'Students.graduated' => 0]);
                        })
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->group(['StudentExamStatuses.academic_year', 'StudentExamStatuses.semester', 'StudentExamStatuses.student_id'])
                        ->order(['Students.first_name', 'StudentExamStatuses.cgpa'])
                        ->toArray();

                    foreach ($activeStudents as $student) {
                        $registeredCheck = $courseRegistrationsTable->find()
                            ->where([
                                'student_id' => $student->student->id,
                                'semester' => $currentSemester,
                                'academic_year' => $currentAcademicYear
                            ])
                            ->count();

                        $graduationCheck = $graduateListsTable->find()
                            ->where(['student_id' => $student->student->id])
                            ->count();

                        if ($graduationCheck == 0 && $registeredCheck == 0 && $student->student->id) {
                            $creditType = 'Credit';
                            $section = $sectionsTable->find()
                                ->where(['id' => $studentSectionMap[$student->student->id]])
                                ->contain(['Curriculums' => ['id', 'type_credit']])
                                ->first();

                            if (!empty($section->curriculum->id) && strpos($section->curriculum->type_credit, 'ECTS') !== false) {
                                $creditType = 'ECTS';
                            }

                            $mergedData = array_merge($student->student->toArray(), $student->toArray());
                            $activeLists[$department->college->name . '~' . $department->name . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id] . '~' . $section->name . '~' . $student->academic_year . '~' . $student->semester . '~' . $yearLevel->name . '~' . $creditType][$count] = $mergedData;
                            $count++;
                        }
                    }
                }
            }
        } else {
            $collegeConditions = ['Colleges.active' => 1];
            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $collegeConditions['Colleges.id'] = $collegeId[1];
                } elseif ($departmentId == 0) {
                    $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                    $collegeIds = array_column($collegeIds, 'id');
                }
            } else {
                $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                $collegeIds = array_column($collegeIds, 'id');
            }

            if (!empty($collegeIds)) {
                $colleges = $collegesTable->find()->where(['id IN' => $collegeIds])->toArray();
                foreach ($colleges as $college) {
                    $sectionConditions['Sections.college_id'] = $college->id;
                    $sectionConditions['OR'] = [
                        'Sections.department_id IS NULL',
                        'Sections.department_id' => 0,
                        'Sections.department_id' => ''
                    ];
                    $sectionConditions['Sections.program_id'] = !empty($programId) ? $programId : (Configure::read('programs_available_for_registrar_college_level_permissions') ?: 0);

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->distinct(['student_id', 'section_id'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $activeStudents = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year',
                            'Students.academicyear',
                            'Students.graduated'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students', function ($q) use ($college) {
                            return $q->where(['Students.college_id' => $college->id, 'Students.graduated' => 0]);
                        })
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->group(['StudentExamStatuses.academic_year', 'StudentExamStatuses.semester', 'StudentExamStatuses.student_id'])
                        ->order(['Students.first_name', 'StudentExamStatuses.cgpa'])
                        ->toArray();

                    foreach ($activeStudents as $student) {
                        $registeredCheck = $courseRegistrationsTable->find()
                            ->where([
                                'student_id' => $student->student->id,
                                'semester' => $currentSemester,
                                'academic_year' => $currentAcademicYear,
                                'OR' => [
                                    'year_level_id IS NULL',
                                    'year_level_id' => 0,
                                    'year_level_id' => ''
                                ]
                            ])
                            ->count();

                        $graduationCheck = $graduateListsTable->find()
                            ->where(['student_id' => $student->student->id])
                            ->count();

                        if ($graduationCheck == 0 && $registeredCheck == 0 && $student->student->id) {
                            $creditType = 'Credit';
                            $section = $sectionsTable->find()
                                ->where(['id' => $studentSectionMap[$student->student->id]])
                                ->first();

                            $programLabel = $section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman';
                            $yearLabel = $section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st';

                            $mergedData = array_merge($student->student->toArray(), $student->toArray());
                            $activeLists[$college->name . '~' . $programLabel . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id] . '~' . $section->name . '~' . $student->academic_year . '~' . $student->semester . '~' . $yearLabel . '~' . $creditType][$count] = $mergedData;
                            $count++;
                        }
                    }
                }
            }
        }

        return $activeLists;
    }

    /**
     * Gets list of registered students
     */
    public function getRegisteredStudentList(?string $academicYear = null, ?string $semester = null, $programId = 0, $programTypeId = 0, ?string $departmentId = null, string $sex = 'all', ?string $yearLevelId = null, ?int $regionId = null, int $freshman = 0, string $excludeGraduated = ''): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $studentListRegistered = [];
        $conditions = [];
        $sectionConditions = ['Sections.id IS NOT NULL'];
        $collegeIds = [];

        if (!empty($regionId)) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($sex !== 'all') {
            $conditions['Students.gender LIKE'] = $sex . '%';
        }

        if ($excludeGraduated == '1') {
            $conditions['Students.graduated'] = 0;
        }

        if (!empty($programId)) {
            if (is_array($programId)) {
                $conditions['Students.program_id IN'] = $programId;
                $sectionConditions['Sections.program_id IN'] = $programId;
            } elseif ($programId != 0) {
                $conditions['Students.program_id'] = $programId;
                $sectionConditions['Sections.program_id'] = $programId;
            } else {
                $conditions['Students.program_id'] = 0;
                $sectionConditions['Sections.program_id'] = 0;
            }
        }

        if (!empty($programTypeId)) {
            if (is_array($programTypeId)) {
                $conditions['Students.program_type_id IN'] = $programTypeId;
                $sectionConditions['Sections.program_type_id IN'] = $programTypeId;
            } elseif ($programTypeId != 0) {
                $conditions['Students.program_type_id'] = $programTypeId;
                $sectionConditions['Sections.program_type_id'] = $programTypeId;
            } else {
                $conditions['Students.program_type_id'] = 0;
                $sectionConditions['Sections.program_type_id'] = 0;
            }
        }

        if (!empty($academicYear)) {
            $conditions['CourseRegistrations.academic_year'] = $academicYear;
            $sectionConditions['Sections.academicyear'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['CourseRegistrations.semester'] = $semester;
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programsList = array_column($programs, 'name', 'id');
        $programTypesList = array_column($programTypes, 'name', 'id');

        $count = 0;

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $collegeIds[$department->college_id] = $department->college_id;
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $ylId = $yearLevel->id;
                    $deptId = $department->id;
                    $sectionConditions['Sections.year_level_id'] = $ylId;
                    $sectionConditions['Sections.department_id'] = $deptId;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->distinct(['student_id', 'section_id'])
                        ->order(['id' => 'DESC', 'modified' => 'DESC', 'section_id' => 'DESC'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $registeredStudents = $courseRegistrationsTable->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.program_id',
                            'Students.program_type_id',
                            'CourseRegistrations.semester',
                            'CourseRegistrations.academic_year',
                            'CourseRegistrations.section_id',
                            'Students.academicyear',
                            'Students.graduated',
                            'Students.admissionyear',
                            'Students.curriculum_id',
                            'Students.department_id',
                            'Students.college_id'
                        ])
                        ->distinct(['CourseRegistrations.student_id'])
                        ->innerJoinWith('Students', function ($q) use ($deptId, $ylId) {
                            return $q->where(['Students.department_id' => $deptId]);
                        })
                        ->where($conditions)
                        ->where(['CourseRegistrations.student_id IN' => $studentIds, 'CourseRegistrations.year_level_id' => $ylId])
                        ->group(['CourseRegistrations.semester', 'CourseRegistrations.academic_year', 'CourseRegistrations.section_id', 'CourseRegistrations.student_id'])
                        ->order(['CourseRegistrations.academic_year' => 'DESC', 'CourseRegistrations.semester' => 'DESC', 'CourseRegistrations.section_id', 'Students.first_name', 'Students.middle_name', 'Students.last_name'])
                        ->toArray();

                    foreach ($registeredStudents as $student) {
                        $creditType = 'Credit';
                        $section = $sectionsTable->find()
                            ->where(['id' => $student->section_id])
                            ->contain(['Curriculums' => ['id', 'type_credit']])
                            ->select(['id', 'name'])
                            ->first();

                        if (!empty($section->curriculum->id) && strpos($section->curriculum->type_credit, 'ECTS') !== false) {
                            $creditType = 'ECTS';
                        }

                        $load = $studentsTable->calculateStudentLoad($student->student->id, $semester, $academicYear, 1);
                        $mergedData = array_merge($student->student->toArray(), $student->toArray(), $load);

                        $studentListRegistered[$department->college->name . '~' . $department->name . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id] . '~' . $section->name . '~' . $student->academic_year . '~' . $student->semester . '~' . $yearLevel->name . '~' . $creditType][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        } else {
            $collegeConditions = ['Colleges.active' => 1];
            $programsAvailable = Configure::read('programs_available_for_registrar_college_level_permissions') ?: 0;
            $programTypesAvailable = Configure::read('program_types_available_for_registrar_college_level_permissions') ?: 0;

            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $collegeConditions['Colleges.id'] = $collegeId[1];
                } elseif ($departmentId == 0) {
                    $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                    $collegeIds = array_column($collegeIds, 'id');
                }
            } else {
                $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                $collegeIds = array_column($collegeIds, 'id');
            }

            if (!empty($collegeIds)) {
                $colleges = $collegesTable->find()->where(['id IN' => $collegeIds])->toArray();
                foreach ($colleges as $college) {
                    $sectionConditions['Sections.college_id'] = $college->id;
                    $sectionConditions['OR'] = [
                        'Sections.department_id IS NULL',
                        'Sections.department_id' => 0,
                        'Sections.department_id' => ''
                    ];
                    $sectionConditions['Sections.program_id'] = !empty($programId) ? $programId : $programsAvailable;

                    $collegeSections = $sectionsTable->find()
                        ->where($sectionConditions)
                        ->select(['id'])
                        ->toArray();

                    if (empty($collegeSections)) {
                        continue;
                    }

                    $collegeSectionIds = array_column($collegeSections, 'id');

                    $registeredStudents = $courseRegistrationsTable->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'CourseRegistrations.semester',
                            'CourseRegistrations.academic_year',
                            'CourseRegistrations.section_id',
                            'Students.academicyear',
                            'Students.graduated'
                        ])
                        ->distinct(['CourseRegistrations.student_id'])
                        ->innerJoinWith('Students')
                        ->where($conditions)
                        ->where(['CourseRegistrations.section_id IN' => $collegeSectionIds])
                        ->group(['CourseRegistrations.semester', 'CourseRegistrations.academic_year', 'CourseRegistrations.section_id', 'CourseRegistrations.student_id'])
                        ->order(['CourseRegistrations.academic_year' => 'DESC', 'CourseRegistrations.semester' => 'DESC', 'CourseRegistrations.section_id', 'Students.first_name', 'Students.middle_name', 'Students.last_name'])
                        ->toArray();

                    foreach ($registeredStudents as $student) {
                        $creditType = 'Credit';
                        $section = $sectionsTable->find()
                            ->where(['id' => $student->section_id, 'department_id IS' => null])
                            ->select(['id', 'name', 'program_id'])
                            ->first();

                        $load = $studentsTable->calculateStudentLoad($student->student->id, $semester, $academicYear, 1);
                        $mergedData = array_merge($student->student->toArray(), $student->toArray(), $load);

                        $programLabel = $section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Fresh';
                        $yearLabel = $section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st';

                        $studentListRegistered[$college->name . '~' . $programLabel . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id] . '~' . $section->name . '~' . $student->academic_year . '~' . $student->semester . '~' . $yearLabel . '~' . $creditType][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        }

        return $studentListRegistered;
    }

    /**
     * Gets number of dismissed students
     */
    public function getNumberOfDismissedStudent(?string $academicYear = null, ?string $semester = null, $departmentId = null): array
    {
        $acSem = [
            'prevACSem' => [],
            'dismissedTotalCount' => 0,
            'dismissedFemaleTotalCount' => 0,
            'dismissedMaleTotalCount' => 0,
            'totalRegistrationInPrevSemAc' => 0
        ];

        if (empty($academicYear) && empty($semester)) {
            return $acSem;
        }

        $prevSemester = $this->getPreviousSemester($academicYear, $semester);
        $conditions = ['academic_status_id' => 4];
        $maleConditions = ['academic_status_id' => 4];
        $femaleConditions = ['academic_status_id' => 4];
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');

        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $conditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['college_id' => $collegeId[1]]);
                $maleConditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['college_id' => $collegeId[1]]);
                $femaleConditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['college_id' => $collegeId[1]]);
            } else {
                $conditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['department_id' => $departmentId]);
                $maleConditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['department_id' => $departmentId]);
                $femaleConditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['department_id' => $departmentId]);
            }
        }

        if (!empty($prevSemester['academic_year'])) {
            $conditions['academic_year'] = $prevSemester['academic_year'];
            $maleConditions['academic_year'] = $prevSemester['academic_year'];
            $femaleConditions['academic_year'] = $prevSemester['academic_year'];
        }

        if (!empty($prevSemester['semester'])) {
            $conditions['semester'] = $prevSemester['semester'];
            $maleConditions['semester'] = $prevSemester['semester'];
            $femaleConditions['semester'] = $prevSemester['semester'];
        }

        $maleConditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['gender' => 'male']);
        $femaleConditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['gender' => 'female']);

        $acSem['prevACSem'] = $prevSemester;
        $acSem['dismissedTotalCount'] = $this->find()
            ->where($conditions)
            ->group(['student_id'])
            ->count();
        $acSem['dismissedFemaleTotalCount'] = $this->find()
            ->where($femaleConditions)
            ->group(['student_id'])
            ->count();
        $acSem['dismissedMaleTotalCount'] = $this->find()
            ->where($maleConditions)
            ->group(['student_id'])
            ->count();

        $regConditions = [
            'academic_year' => $prevSemester['academic_year'],
            'semester' => $prevSemester['semester']
        ];

        if (!empty($departmentId)) {
            if (is_array($departmentId)) {
                $regConditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['department_id IN' => $departmentId]);
            } else {
                $regConditions['student_id IN'] = $studentsTable->find()->select(['id'])->where(['department_id' => $departmentId]);
            }
        }

        $acSem['totalRegistrationInPrevSemAc'] = $courseRegistrationsTable->find()
            ->where($regConditions)
            ->group(['student_id'])
            ->count();

        return $acSem;
    }

    /**
     * Gets student rank
     */
    public function getRank(int $studentId, string $type = 'cgpa')
    {
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');

        $recentRegistration = $courseRegistrationsTable->getMostRecentRegistration($studentId);

        if (!empty($recentRegistration)) {
            $conditions = [
                'academic_year' => $recentRegistration->academic_year,
                'semester' => $recentRegistration->semester,
                'student_id' => $studentId
            ];
            $sectionConditions = [
                'academicyear' => $recentRegistration->academic_year,
                'program_type_id' => $recentRegistration->program_type_id
            ];

            if (!empty($recentRegistration->year_level_id)) {
                $sectionConditions['year_level_id'] = $recentRegistration->year_level_id;
                $sectionConditions['department_id'] = $recentRegistration->published_course->department_id;
                $sectionConditions['program_id'] = $recentRegistration->published_course->program_id;
            } else {
                $sectionConditions['year_level_id IS'] = null;
                $sectionConditions['department_id IS'] = null;
                $sectionConditions['college_id'] = $recentRegistration->published_course->college_id;
                $sectionConditions['program_id'] = $recentRegistration->published_course->program_id;
            }

            $sectionIds = $sectionsTable->find()
                ->where($sectionConditions)
                ->select(['id'])
                ->toArray();
            $sectionIds = array_column($sectionIds, 'id');

            $sectionRankConditions = array_merge($conditions, [
                'student_id IN' => $studentsSectionsTable->find()->select(['student_id'])->where(['section_id' => $recentRegistration->section_id])
            ]);
            $batchRankConditions = !empty($sectionIds) ? array_merge($conditions, [
                'student_id IN' => $studentsSectionsTable->find()->select(['student_id'])->where(['section_id IN' => $sectionIds])
            ]) : $conditions;

            $selectedStudentStatus = $this->find()
                ->where($conditions)
                ->order([$type => 'DESC'])
                ->first();

            if (!empty($selectedStudentStatus)) {
                $sectionRankConditions[$type . ' >'] = $selectedStudentStatus->$type;
                $batchRankConditions[$type . ' >'] = $selectedStudentStatus->$type;

                $ownSectionStatusAbove = $this->find()
                    ->where($sectionRankConditions)
                    ->count();
                $ownBatchStatusAbove = $this->find()
                    ->where($batchRankConditions)
                    ->count();

                return [
                    'Section' => ['rank' => $this->rankName($ownSectionStatusAbove + 1)],
                    'Batch' => ['rank' => $this->rankName($ownBatchStatusAbove + 1)],
                    'ACSem' => [
                        'academic_year' => $recentRegistration->academic_year,
                        'semester' => $recentRegistration->semester
                    ],
                    'cgpa' => $selectedStudentStatus->cgpa,
                    'sgpa' => $selectedStudentStatus->sgpa
                ];
            } else {
                $prevSemester = $this->getPreviousSemester($recentRegistration->academic_year, $recentRegistration->semester);
                $recentRegistration = $courseRegistrationsTable->getRegistration($studentId, $prevSemester['academic_year'], $prevSemester['semester']);

                if (!empty($recentRegistration)) {
                    $conditions = [
                        'academic_year' => $recentRegistration->academic_year,
                        'semester' => $recentRegistration->semester,
                        'student_id' => $studentId
                    ];
                    $sectionConditions = [
                        'academicyear' => $recentRegistration->academic_year,
                        'program_id' => $recentRegistration->program_id,
                        'program_type_id' => $recentRegistration->program_type_id
                    ];

                    if (!empty($recentRegistration->year_level_id)) {
                        $sectionConditions['year_level_id'] = $recentRegistration->year_level_id;
                        $sectionConditions['department_id'] = $recentRegistration->department_id;
                    } else {
                        $sectionConditions['year_level_id IS'] = null;
                        $sectionConditions['department_id IS'] = null;
                        $sectionConditions['college_id'] = $recentRegistration->college_id;
                    }

                    $sectionIds = $sectionsTable->find()
                        ->where($sectionConditions)
                        ->select(['id'])
                        ->toArray();
                    $sectionIds = array_column($sectionIds, 'id');

                    $sectionRankConditions = array_merge($conditions, [
                        'student_id IN' => $studentsSectionsTable->find()->select(['student_id'])->where(['section_id' => $recentRegistration->section_id])
                    ]);
                    $batchRankConditions = !empty($sectionIds) ? array_merge($conditions, [
                        'student_id IN' => $studentsSectionsTable->find()->select(['student_id'])->where(['section_id IN' => $sectionIds])
                    ]) : $conditions;

                    $selectedStudentStatus = $this->find()
                        ->where($conditions)
                        ->order([$type => 'DESC'])
                        ->first();

                    if (!empty($selectedStudentStatus)) {
                        $sectionRankConditions[$type . ' >'] = $selectedStudentStatus->$type;
                        $batchRankConditions[$type . ' >'] = $selectedStudentStatus->$type;

                        $ownSectionStatusAbove = $this->find()
                            ->where($sectionRankConditions)
                            ->count();
                        $ownBatchStatusAbove = $this->find()
                            ->where($batchRankConditions)
                            ->count();

                        return [
                            'Section' => ['rank' => $this->rankName($ownSectionStatusAbove + 1)],
                            'Batch' => ['rank' => $this->rankName($ownBatchStatusAbove + 1)],
                            'ACSem' => [
                                'academic_year' => $prevSemester['academic_year'],
                                'semester' => $prevSemester['semester']
                            ],
                            'cgpa' => $selectedStudentStatus->cgpa,
                            'sgpa' => $selectedStudentStatus->sgpa
                        ];
                    }
                }
            }
        }

        return false;
    }

    /**
     * Checks for Fx presence in student status
     */
    public function checkFxPresenceInStatus(int $studentId): int
    {
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable =  TableRegistry::getTableLocator()->get('ExamGrades');

        $recentRegistration = $courseRegistrationsTable->getMostRecentRegistration($studentId);

        if (empty($recentRegistration)) {
            return 1;
        }

        $gradeLists = $examGradesTable->getStudentCoursesAndFinalGrade(
            $studentId,
            $recentRegistration->academic_year,
            $recentRegistration->semester
        );

        foreach ($gradeLists as $grade) {
            if (isset($grade['grade']) && strcasecmp($grade['grade'], 'Fx') === 0) {
                return 0;
            }
        }

        return 1;
    }

    /**
     * Gets students by result
     */
    public function getStudentByResult(string $academicYear, string $semester, ?string $programId = null, ?string $programTypeId = null, ?string $departmentId = null, string $sex = 'all', ?string $yearLevelId = null, ?int $regionId = null, float $from = 0, float $to = 4, int $academicStatusId = 4, string $type = 'gpa', int $freshman = 0, string $excludeGraduated = ''): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $activeLists = [];
        $conditions = [];
        $sectionConditions = ['Sections.id IS NOT NULL'];
        $collegeIds = [];

        if ($type === 'sgpa' && !empty($from) && !empty($to)) {
            $conditions['StudentExamStatuses.sgpa >='] = $from;
            $conditions['StudentExamStatuses.sgpa <='] = $to;
        } elseif ($type === 'cgpa' && !empty($from) && !empty($to)) {
            $conditions['StudentExamStatuses.cgpa >='] = $from;
            $conditions['StudentExamStatuses.cgpa <='] = $to;
        }

        if (!empty($academicStatusId)) {
            $conditions['StudentExamStatuses.academic_status_id'] = $academicStatusId;
        }

        if ($excludeGraduated == '1') {
            $conditions['Students.graduated'] = 0;
        }

        if (!empty($regionId)) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        if (!empty($programId)) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
            $sectionConditions['Sections.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }

        if (!empty($programTypeId)) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
            $sectionConditions['Sections.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        if (!empty($academicYear)) {
            $conditions['StudentExamStatuses.academic_year'] = $academicYear;
            $sectionConditions['Sections.academicyear'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['StudentExamStatuses.semester'] = $semester;
        }

        $orderBy = $type === 'sgpa' ? 'StudentExamStatuses.sgpa DESC' : 'StudentExamStatuses.cgpa DESC';

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programsList = array_column($programs, 'name', 'id');
        $programTypesList = array_column($programTypes, 'name', 'id');

        $count = 0;

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $collegeIds[$department->college_id] = $department->college_id;
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $sectionConditions['Sections.year_level_id'] = $yearLevel->id;
                    $sectionConditions['Sections.department_id'] = $department->id;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->distinct(['student_id', 'section_id'])
                        ->group(['student_id', 'section_id'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $activeStudents = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year',
                            'Students.academicyear',
                            'Students.graduated'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students')
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->order([$orderBy])
                        ->toArray();

                    foreach ($activeStudents as $student) {
                        $section = $sectionsTable->find()
                            ->where(['id' => $studentSectionMap[$student->student->id]])
                            ->contain(['YearLevels' => ['id', 'name']])
                            ->first();

                        $studentData = [
                            'Department' => $department->name,
                            'AcademicYear' => $student->academic_year,
                            'Semester' => $student->semester,
                            'Section' => $section->name,
                            'YearLevel' => $section->year_level->name
                        ];

                        $mergedData = array_merge($student->student->toArray(), $student->toArray(), $studentData);
                        $activeLists[$department->college->name . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id]][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        } else {
            $collegeConditions = ['Colleges.active' => 1];
            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $collegeConditions['Colleges.id'] = $collegeId[1];
                } elseif ($departmentId == 0) {
                    $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                    $collegeIds = array_column($collegeIds, 'id');
                }
            } else {
                $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                $collegeIds = array_column($collegeIds, 'id');
            }

            if (!empty($collegeIds)) {
                $colleges = $collegesTable->find()->where(['id IN' => $collegeIds])->toArray();
                foreach ($colleges as $college) {
                    $sectionConditions['Sections.college_id'] = $college->id;
                    $sectionConditions['Sections.department_id IS'] = null;
                    $sectionConditions['OR'] = [
                        'Sections.year_level_id IS NULL',
                        'Sections.year_level_id' => 0,
                        'Sections.year_level_id' => ''
                    ];

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->distinct(['student_id', 'section_id'])
                        ->group(['student_id', 'section_id'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $activeStudents = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year',
                            'Students.academicyear',
                            'Students.graduated'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students')
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->order([$orderBy])
                        ->toArray();

                    foreach ($activeStudents as $student) {
                        $section = $sectionsTable->find()
                            ->where(['id' => $studentSectionMap[$student->student->id]])
                            ->first();

                        $studentData = [
                            'Department' => 'Pre/Freshman',
                            'AcademicYear' => $student->academic_year,
                            'Semester' => $student->semester,
                            'Section' => $section->name,
                            'YearLevel' => 'Pre/1st'
                        ];

                        $mergedData = array_merge($student->student->toArray(), $student->toArray(), $studentData);
                        $activeLists[$college->name . '~' . $programsList[$student->student->program_id] . '~' . $programTypesList[$student->student->program_type_id]][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        }

        return $activeLists;
    }

    /**
     * Gets academic year range
     */
    public function getAcademicYearRange(string $from, string $to): array
    {
        $list = [$from => $from];

        if ($from === $to) {
            return $list;
        }

        $nextAyAndS = ['academic_year' => $from];
        do {
            $nextAyAndS = $this->getNextSemester($nextAyAndS['academic_year'], null);
            $list[$nextAyAndS['academic_year']] = $nextAyAndS['academic_year'];
        } while (strcasecmp($to, $nextAyAndS['academic_year']) !== 0);

        return $list;
    }

    /**
     * Checks if student has 3 F grades and taken required credit
     */
    public function has3FAndTakenRequiredCredit(int $studentId, string $academicYear, string $semester): bool
    {
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $examGradesTable =  TableRegistry::getTableLocator()->get('ExamGrades');

        $courseRegistrations = $courseRegistrationsTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->contain(['PublishedCourses' => ['Courses']])
            ->toArray();

        $fCount = 0;
        $totalCredit = 0;

        foreach ($courseRegistrations as $registration) {
            $gradeDetail = $examGradesTable->getApprovedGrade($registration->id, 1);
            if (!empty($gradeDetail) && in_array($gradeDetail['grade'], ['F', 'Fx'])) {
                $fCount++;
            }
            $totalCredit += $registration->published_course->course->credit;
        }

        return $totalCredit >= 12 && $fCount >= 3;
    }

    /**
     * Gets academic year and semester rank
     */
    public function getACSemRank(int $studentId, string $academicYear, string $semester, string $type = 'cgpa'): array
    {
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');

        $recentRegistration = $courseRegistrationsTable->find()
            ->where([
                'student_id' => $studentId,
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->contain(['PublishedCourses'])
            ->first();

        $studentDetail = $studentsTable->find()
            ->where(['id' => $studentId])
            ->contain(['AcceptedStudents'])
            ->first();

        if (!empty($recentRegistration) && !empty($studentDetail)) {
            return $this->getRankGivenRegistration($recentRegistration, $studentDetail, $type);
        }

        return [];
    }

    /**
     * Gets rank given registration
     */
    public function getRankGivenRegistration($recentRegistration, $studentDetail, string $type = 'cgpa'): array
    {
        $urRank = [];
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $yearLevelsTable =  TableRegistry::getTableLocator()->get('YearLevels');

        $conditions = [
            'academic_year' => $recentRegistration->academic_year,
            'semester' => $recentRegistration->semester,
            'student_id' => $studentDetail->id
        ];

        $selectedStudentStatus = $this->find()
            ->where($conditions)
            ->order([$type => 'DESC'])
            ->first();

        if (!empty($selectedStudentStatus)) {
            $departmentIds = $departmentsTable->find()
                ->where(['college_id' => $studentDetail->college_id])
                ->select(['id'])
                ->toArray();
            $departmentIds = array_column($departmentIds, 'id');

            $sectionConditions = [
                'academicyear' => $recentRegistration->academic_year,
                'program_id' => $recentRegistration->published_course->program_id,
                'program_type_id' => $recentRegistration->published_course->program_type_id
            ];

            $collegeSectionConditions = $sectionConditions;

            if (!empty($recentRegistration->year_level_id)) {
                $yearLevel = $yearLevelsTable->find()
                    ->where(['id' => $recentRegistration->year_level_id])
                    ->select(['name'])
                    ->first();
                $yearLevelIds = $yearLevelsTable->find()
                    ->where(['name' => $yearLevel->name, 'department_id IN' => $departmentIds])
                    ->select(['id'])
                    ->toArray();
                $yearLevelIds = array_column($yearLevelIds, 'id');

                $sectionConditions['year_level_id'] = $recentRegistration->year_level_id;
                $sectionConditions['department_id'] = $recentRegistration->published_course->department_id;
                $collegeSectionConditions['year_level_id IN'] = $yearLevelIds;
            } else {
                $sectionConditions['year_level_id IS'] = null;
                $sectionConditions['department_id IS'] = null;
                $sectionConditions['college_id'] = $recentRegistration->published_course->college_id;
                $collegeSectionConditions['year_level_id IS'] = null;
                $collegeSectionConditions['college_id'] = $recentRegistration->published_course->college_id;
            }

            $sectionIds = $sectionsTable->find()
                ->where($sectionConditions)
                ->select(['id'])
                ->toArray();
            $sectionIds = array_column($sectionIds, 'id');

            $collegeSectionIds = $sectionsTable->find()
                ->where($collegeSectionConditions)
                ->select(['id'])
                ->toArray();
            $collegeSectionIds = array_column($collegeSectionIds, 'id');

            $sectionRankConditions = array_merge($conditions, [
                'student_id IN' => $studentsSectionsTable->find()->select(['student_id'])->where(['section_id' => $recentRegistration->section_id])
            ]);
            $batchRankConditions = !empty($sectionIds) ? array_merge($conditions, [
                'student_id IN' => $studentsSectionsTable->find()->select(['student_id'])->where(['section_id IN' => $sectionIds])
            ]) : $conditions;
            $collegeRankConditions = !empty($collegeSectionIds) ? array_merge($conditions, [
                'student_id IN' => $studentsSectionsTable->find()->select(['student_id'])->where(['section_id IN' => $collegeSectionIds])
            ]) : $conditions;

            unset($sectionRankConditions['student_id']);
            unset($batchRankConditions['student_id']);
            unset($collegeRankConditions['student_id']);

            $sectionRankConditions[$type . ' >'] = $selectedStudentStatus->$type;
            $batchRankConditions[$type . ' >'] = $selectedStudentStatus->$type;
            $collegeRankConditions[$type . ' >'] = $selectedStudentStatus->$type;

            if (!empty($sectionRankConditions) && !empty($batchRankConditions) && !empty($collegeRankConditions)) {
                $ownSectionStatusAbove = $this->find()
                        ->where($sectionRankConditions)
                        ->count() + 1;
                $ownBatchStatusAbove = $this->find()
                        ->where($batchRankConditions)
                        ->count() + 1;
                $ownCollegeStatusAbove = $this->find()
                        ->where($collegeRankConditions)
                        ->count() + 1;

                $urRank['Rank'] = [
                    'student_id' => $recentRegistration->student_id,
                    'section_rank' => $this->rankName($ownSectionStatusAbove),
                    'batch_rank' => $this->rankName($ownBatchStatusAbove),
                    'college_rank' => $this->rankName($ownCollegeStatusAbove),
                    'academicyear' => $recentRegistration->academic_year,
                    'semester' => $recentRegistration->semester,
                    'cgpa' => $selectedStudentStatus->cgpa,
                    'sgpa' => $selectedStudentStatus->sgpa,
                    'category' => $type
                ];

                return $urRank;
            }
        }

        return [];
    }
    /**
     * Displays student rank for a given academic year
     */
    public function displayStudentRank(int $studentId, string $academicYear): array
    {
        $studentRanksTable =  TableRegistry::getTableLocator()->get('StudentRanks');

        $ranks = $studentRanksTable->find()
            ->where([
                'student_id' => $studentId,
                'academicyear' => $academicYear
            ])
            ->order(['academicyear' => 'DESC'])
            ->toArray();

        if (empty($ranks)) {
            $ranks = $studentRanksTable->find()
                ->where(['student_id' => $studentId])
                ->order(['academicyear' => 'DESC'])
                ->toArray();
        }

        $rankFormatted = [];
        foreach ($ranks as $rank) {
            $rankFormatted[$rank->academicyear . '-' . $rank->semester][$rank->category] = $rank;
        }

        return $rankFormatted;
    }

    /**
     * Formats rank number with appropriate suffix
     */
    public function rankName(int $i): string
    {
        switch ($i) {
            case 1:
                return $i . 'st';
            case 2:
                return $i . 'nd';
            case 3:
                return $i . 'rd';
            default:
                return $i . 'th';
        }
    }

    /**
     * Gets attrition rate
     */
    public function getAttritionRate(
        string $academicYear,
        string $semester,
        ?string $programId = null,
        ?string $programTypeId = null,
        ?string $departmentId = null,
        ?string $yearLevelId = null,
        ?int $regionId = null,
        ?string $sex = null
    ): array
    {
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
        $yearLevelsTable =  TableRegistry::getTableLocator()->get('YearLevels');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');

        $collegeConditions = [];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $collegeConditions['Colleges.id'] = $collegeId[1];
            } else {
                $collegeConditions['Colleges.id IN'] = $collegesTable->find()
                    ->select(['id'])
                    ->where(['college_id IN' => $collegesTable->Departments->find()->select(['college_id'])->where(['department_id' => $departmentId])]);
            }
        }

        $colleges = $collegesTable->find()
            ->where($collegeConditions)
            ->contain(['Departments'])
            ->toArray();

        $programConditions = [];
        if (!empty($programId)) {
            $programIds = explode('~', $programId);
            $programConditions['Programs.id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }

        $programs = $programsTable->find()
            ->where($programConditions)
            ->select(['id', 'name'])
            ->toArray();
        $programsList = array_column($programs, 'name', 'id');

        $programTypeConditions = [];
        if (!empty($programTypeId)) {
            $programTypeIds = explode('~', $programTypeId);
            $programTypeConditions['ProgramTypes.id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        $programTypes = $programTypesTable->find()
            ->where($programTypeConditions)
            ->select(['id', 'name'])
            ->toArray();
        $programTypesList = array_column($programTypes, 'name', 'id');

        $attritionSummary = [];
        $studentConditions = [];
        if (!empty($regionId)) {
            $studentConditions['Students.region_id'] = $regionId;
        }
        if (!empty($sex) && $sex !== 'all') {
            $studentConditions['Students.gender'] = $sex;
        }

        foreach ($colleges as $college) {
            $yearLevelConditions = ['YearLevels.department_id IN' => $collegesTable->Departments->find()->select(['id'])->where(['college_id' => $college->id])];
            if (!empty($yearLevelId) && $yearLevelId !== 'all') {
                $yearLevelConditions['YearLevels.name'] = $yearLevelId;
            }

            $yearLevels = $yearLevelsTable->find()
                ->where($yearLevelConditions)
                ->select(['id', 'name'])
                ->toArray();
            $yearLevelsList = array_column($yearLevels, 'name', 'id');

            foreach ($programTypes as $programType) {
                foreach ($programs as $program) {
                    foreach ($college->departments as $department) {
                        $deptYearLevels = $yearLevelsTable->find()
                            ->where(['department_id' => $department->id])
                            ->select(['id', 'name'])
                            ->toArray();
                        $deptYearLevelsList = array_column($deptYearLevels, 'name', 'id');

                        foreach ($deptYearLevelsList as $ylId => $ylName) {
                            if (
                                !empty($academicYear) &&
                                !empty($semester) &&
                                !empty($ylName) &&
                                !empty($department->name) &&
                                !empty($programType->name) &&
                                !empty($program->name) &&
                                !empty($college->name) &&
                                !empty($department->id)
                            ) {
                                $attritionSummary['yearLevel'][$ylName] = $ylName;
                                $regConditions = array_merge($studentConditions, [
                                    'CourseRegistrations.semester' => $semester,
                                    'CourseRegistrations.academic_year' => $academicYear,
                                    'CourseRegistrations.year_level_id' => $ylId,
                                    'CourseRegistrations.student_id IN' => $studentsTable->find()->select(['id'])->where([
                                        'department_id' => $department->id,
                                        'program_id' => $program->id,
                                        'program_type_id' => $programType->id
                                    ])
                                ]);

                                $totalRegistered = $courseRegistrationsTable->find()
                                    ->where($regConditions)
                                    ->group(['student_id'])
                                    ->select(['student_id'])
                                    ->toArray();
                                $totalRegisteredIds = array_column($totalRegistered, 'student_id');

                                $attritionSummary[$program->name . '~' . $programType->name][$college->name][$department->name][$ylName]['total'] = count($totalRegisteredIds);

                                $femaleConditions = array_merge($regConditions, [
                                    'StudentExamStatuses.semester' => $semester,
                                    'StudentExamStatuses.academic_year' => $academicYear,
                                    'StudentExamStatuses.academic_status_id' => 4,
                                    'StudentExamStatuses.student_id IN' => $totalRegisteredIds,
                                    'StudentExamStatuses.student_id IN' => $studentsTable->find()->select(['id'])->where(['gender' => 'female', 'department_id' => $department->id, 'program_id' => $program->id, 'program_type_id' => $programType->id])
                                ]);

                                $maleConditions = array_merge($regConditions, [
                                    'StudentExamStatuses.semester' => $semester,
                                    'StudentExamStatuses.academic_year' => $academicYear,
                                    'StudentExamStatuses.academic_status_id' => 4,
                                    'StudentExamStatuses.student_id IN' => $totalRegisteredIds,
                                    'StudentExamStatuses.student_id IN' => $studentsTable->find()->select(['id'])->where(['gender' => 'male', 'department_id' => $department->id, 'program_id' => $program->id, 'program_type_id' => $programType->id])
                                ]);

                                $attritionSummary[$program->name . '~' . $programType->name][$college->name][$department->name][$ylName]['female'] = $this->find()
                                    ->where($femaleConditions)
                                    ->count();
                                $attritionSummary[$program->name . '~' . $programType->name][$college->name][$department->name][$ylName]['male'] = $this->find()
                                    ->where($maleConditions)
                                    ->count();
                            }
                        }
                    }
                }
            }
        }

        return $attritionSummary;
    }

    /**
     * Gets top scoring students
     */
    public function getTopScorer(
        string $academicYear,
        string $semester,
        $programId = 0,
        $programTypeId = 0,
        ?string $departmentId = null,
        int $top = 10,
        string $sex = 'all',
        ?string $yearLevelId = null,
        ?int $regionId = null,
        string $by = 'cgpa',
        int $freshman = 0,
        int $excludeGraduated = 0
    ): array
    {
        $top = $top ?: 10;
        $conditions = [];
        $topLists = [];
        $collegeIds = [];

        if ($excludeGraduated) {
            $conditions['Students.graduated'] = 0;
        }

        if (!empty($regionId)) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        if (!empty($programId)) {
            if (is_array($programId)) {
                $conditions['Students.program_id IN'] = $programId;
            } elseif ($programId != 0) {
                $conditions['Students.program_id'] = $programId;
            } else {
                $conditions['Students.program_id'] = 0;
            }
        }

        if (!empty($programTypeId)) {
            if (is_array($programTypeId)) {
                $conditions['Students.program_type_id IN'] = $programTypeId;
            } elseif ($programTypeId != 0) {
                $conditions['Students.program_type_id'] = $programTypeId;
            } else {
                $conditions['Students.program_type_id'] = 0;
            }
        }

        if (!empty($academicYear)) {
            $conditions['StudentExamStatuses.academic_year'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['StudentExamStatuses.semester'] = $semester;
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        if (empty($collegeIds)) {
            $collegeIds = $collegesTable->find()
                ->where(['active' => 1])
                ->select(['id'])
                ->toArray();
            $collegeIds = array_column($collegeIds, 'id');
        }

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programsList = array_column($programs, 'name', 'id');
        $programTypesList = array_column($programTypes, 'name', 'id');

        $sectionConditions = [];
        if (!empty($academicYear)) {
            $sectionConditions['Sections.academicyear'] = $academicYear;
        }
        if (!empty($programId)) {
            $sectionConditions['Sections.program_id'] = $programId;
        }
        if (!empty($programTypeId)) {
            $sectionConditions['Sections.program_type_id'] = $programTypeId;
        }

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $department->year_levels;
                $yearLevelIds = array_column($yearLevels, 'id');

                $deptSectionConditions = array_merge($sectionConditions, [
                    'Sections.department_id' => $department->id,
                    'Sections.year_level_id IN' => $yearLevelIds
                ]);

                $sectionIds = $sectionsTable->find()
                    ->where($deptSectionConditions)
                    ->select(['id'])
                    ->toArray();
                $sectionIds = array_column($sectionIds, 'id');

                if (!empty($sectionIds)) {
                    $studentIds = $studentsSectionsTable->find()
                        ->where(['section_id IN' => $sectionIds])
                        ->select(['student_id'])
                        ->toArray();
                    $studentIds = array_column($studentIds, 'student_id');

                    if (!empty($studentIds)) {
                        $topStudents = $this->find()
                            ->select([
                                'Students.studentnumber',
                                'Students.id',
                                'Students.first_name',
                                'Students.middle_name',
                                'Students.last_name',
                                'Students.gender',
                                'Students.region_id',
                                'Students.program_id',
                                'Students.program_type_id',
                                'StudentExamStatuses.academic_status_id',
                                'StudentExamStatuses.sgpa',
                                'StudentExamStatuses.cgpa',
                                'StudentExamStatuses.semester',
                                'StudentExamStatuses.academic_year'
                            ])
                            ->distinct(['StudentExamStatuses.student_id'])
                            ->innerJoinWith('Students', function ($q) use ($department) {
                                return $q->where(['Students.department_id' => $department->id]);
                            })
                            ->where($conditions)
                            ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                            ->group(['StudentExamStatuses.student_id', 'StudentExamStatuses.academic_year', 'StudentExamStatuses.semester'])
                            ->order(['StudentExamStatuses.' . $by => 'DESC'])
                            ->limit($top)
                            ->toArray();

                        foreach ($topStudents as $student) {
                            $topLists[$student->student->id] = $student->student->id;
                        }
                    }
                }
            }
        } else {
            foreach ($collegeIds as $collegeId) {
                $collegeSectionConditions = array_merge($sectionConditions, [
                    'Sections.college_id' => $collegeId,
                    'OR' => [
                        'Sections.department_id IS NULL',
                        'Sections.department_id' => ['', 0]
                    ]
                ]);

                $sectionIds = $sectionsTable->find()
                    ->where($collegeSectionConditions)
                    ->select(['id'])
                    ->toArray();
                $sectionIds = array_column($sectionIds, 'id');

                if (!empty($sectionIds)) {
                    $studentIds = $studentsSectionsTable->find()
                        ->where(['section_id IN' => $sectionIds])
                        ->select(['student_id'])
                        ->toArray();
                    $studentIds = array_column($studentIds, 'student_id');

                    if (!empty($studentIds)) {
                        $topStudents = $this->find()
                            ->select([
                                'Students.studentnumber',
                                'Students.id',
                                'Students.first_name',
                                'Students.middle_name',
                                'Students.last_name',
                                'Students.gender',
                                'Students.region_id',
                                'Students.program_id',
                                'Students.program_type_id',
                                'StudentExamStatuses.academic_status_id',
                                'StudentExamStatuses.sgpa',
                                'StudentExamStatuses.cgpa',
                                'StudentExamStatuses.semester',
                                'StudentExamStatuses.academic_year'
                            ])
                            ->distinct(['StudentExamStatuses.student_id'])
                            ->innerJoinWith('Students', function ($q) use ($collegeId) {
                                return $q->where(['Students.college_id' => $collegeId, 'Students.department_id IS' => null]);
                            })
                            ->where($conditions)
                            ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                            ->group(['StudentExamStatuses.student_id', 'StudentExamStatuses.academic_year', 'StudentExamStatuses.semester'])
                            ->order(['StudentExamStatuses.' . $by => 'DESC'])
                            ->limit($top)
                            ->toArray();

                        foreach ($topStudents as $student) {
                            $topLists[$student->student->id] = $student->student->id;
                        }
                    }
                }
            }
        }

        $findOptions = [
            'conditions' => [
                'StudentExamStatuses.student_id IN' => $topLists,
                'StudentExamStatuses.academic_year' => $academicYear,
                'StudentExamStatuses.semester' => $semester
            ],
            'contain' => [
                'Students' => [
                    'Departments' => ['id', 'name'],
                    'Colleges' => ['id', 'name', 'shortname', 'stream'],
                    'Programs' => ['id', 'name'],
                    'ProgramTypes' => ['id', 'name'],
                    'Curriculums' => ['id', 'name', 'type_credit', 'english_degree_nomenclature', 'minimum_credit_points', 'specialization_english_degree_nomenclature']
                ],
                'AcademicStatuses' => ['id', 'name']
            ],
            'order' => ['StudentExamStatuses.' . $by => 'DESC'],
            'group' => ['StudentExamStatuses.student_id'],
            'limit' => $top
        ];

        $students = $this->find('all', $findOptions);
        $formattedStudentList = [];

        foreach ($students as $student) {
            if (!empty($academicYear) && !empty($semester)) {
                $student->student->yearLevel = $sectionsTable->getStudentYearLevel($student->student->id)['year'];
            }

            $programName = $student->student->program->name;
            $programTypeName = $student->student->program_type->name;

            $formattedStudentList[$programName][$programTypeName][] = $student;
        }

        return $formattedStudentList;
    }

    /**
     * Updates academic status by published course of student
     */
    public function updateAcademicStatusByPublishedCourseOfStudent(?int $publishedCourseId = null, ?int $studentId = null): bool
    {
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable =  TableRegistry::getTableLocator()->get('CourseAdds');
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $examGradesTable =  TableRegistry::getTableLocator()->get('ExamGrades');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
        $studentStatusPatternsTable =  TableRegistry::getTableLocator()->get('StudentStatusPatterns');
        $academicCalendarsTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');
        $academicStatusesTable =  TableRegistry::getTableLocator()->get('AcademicStatuses');
        $academicStandsTable =  TableRegistry::getTableLocator()->get('AcademicStands');
        $academicRulesTable =  TableRegistry::getTableLocator()->get('AcademicRules');
        $otherAcademicRulesTable =  TableRegistry::getTableLocator()->get('OtherAcademicRules');

        $fullySaved = true;
        $lastExamStatus = [];

        $registeredStudents = $courseRegistrationsTable->PublishedCourses->find()
            ->where(['PublishedCourses.id' => $publishedCourseId])
            ->contain([
                'Courses' => ['CourseCategories'],
                'CourseRegistrations' => [
                    'conditions' => ['CourseRegistrations.student_id' => $studentId],
                    'order' => ['CourseRegistrations.academic_year' => 'ASC', 'CourseRegistrations.semester' => 'ASC', 'CourseRegistrations.id' => 'ASC'],
                    'Students' => [
                        'fields' => ['id', 'full_name', 'program_id', 'admissionyear', 'program_type_id', 'academicyear', 'graduated'],
                        'GraduateLists'
                    ]
                ]
            ])
            ->first();

        $addedStudents = $courseAddsTable->PublishedCourses->find()
            ->where(['PublishedCourses.id' => $publishedCourseId])
            ->contain([
                'Courses' => ['CourseCategories'],
                'CourseAdds' => [
                    'conditions' => ['CourseAdds.student_id' => $studentId],
                    'order' => ['CourseAdds.academic_year' => 'ASC', 'CourseAdds.semester' => 'ASC', 'CourseAdds.id' => 'ASC'],
                    'Students' => [
                        'fields' => ['id', 'full_name', 'program_id', 'admissionyear', 'program_type_id', 'academicyear', 'graduated'],
                        'GraduateLists'
                    ]
                ]
            ])
            ->first();

        $registeredAddedStudents = [];
        if (!empty($registeredStudents->course_registrations)) {
            foreach ($registeredStudents->course_registrations as $registration) {
                if (!in_array($registration->student->id, array_column($registeredAddedStudents, 'student_id'))) {
                    $registeredAddedStudents[] = $registration;
                }
            }
        }

        if (!empty($addedStudents->course_adds)) {
            foreach ($addedStudents->course_adds as $add) {
                if (!in_array($add->student->id, array_column($registeredAddedStudents, 'student_id'))) {
                    $registeredAddedStudents[] = $add;
                }
            }
        }

        $academicYear = $registeredStudents->academic_year ?? null;
        $semester = $registeredStudents->semester ?? null;
        $studentExamStatus = [];

        if (!empty($registeredAddedStudents)) {
            foreach ($registeredAddedStudents as $courseRegistration) {
                $programTypeId = $programTypesTable->ProgramTypeTransfers->getStudentProgramType($courseRegistration->student->id, $academicYear, $semester);
                $programTypeId = $programTypesTable->getParentProgramType($programTypeId);

                $pattern = $studentStatusPatternsTable->getProgramTypePattern($courseRegistration->student->program_id, $programTypeId, $academicYear);
                $lastPattern = $studentStatusPatternsTable->isLastSemesterInCurriculum($courseRegistration->student->id);

                $lastRegisteredSem = $courseRegistrationsTable->find()
                    ->where(['student_id' => $courseRegistration->student->id])
                    ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
                    ->first();

                if ($lastPattern && $lastRegisteredSem->academic_year == $academicYear && $lastRegisteredSem->semester == $semester) {
                    $pattern = 1;
                }

                $ayAndSList = $this->getAcademicYearAndSemesterListToGenerateStatus($courseRegistration->student->id, $academicYear, $semester);

                if (empty($ayAndSList)) {
                    continue;
                } elseif (count($ayAndSList) >= $pattern) {
                    $creditHourSum = 0;
                    $gradePointSum = 0;
                    $mCreditHourSum = 0;
                    $mGradePointSum = 0;
                    $deductCreditHourSum = 0;
                    $deductGradePointSum = 0;
                    $mDeductCreditHourSum = 0;
                    $mDeductGradePointSum = 0;
                    $complete = true;
                    $firstAcademicYear = null;
                    $firstSemester = null;

                    $allAySList = $examGradesTable->getListOfAyAndSemester($courseRegistration->student->id, $ayAndSList[0]['academic_year'], $ayAndSList[0]['semester']);

                    foreach ($ayAndSList as $ayAndS) {
                        $aysIndex = count($allAySList);
                        $allAySList[$aysIndex] = ['academic_year' => $ayAndS['academic_year'], 'semester' => $ayAndS['semester']];

                        if ($firstAcademicYear === null) {
                            $firstAcademicYear = $ayAndS['academic_year'];
                            $firstSemester = $ayAndS['semester'];
                        }

                        $courseAndGrades = $examGradesTable->getStudentCoursesAndFinalGrade($courseRegistration->student->id, $ayAndS['academic_year'], $ayAndS['semester']);

                        if (!empty($courseAndGrades)) {
                            foreach ($courseAndGrades as $course) {
                                if (
                                    !isset($course['grade']) ||
                                    (
                                        !isset($course['point_value']) &&
                                        !in_array(strtolower($course['grade']), ['i', 'w', 'ng']) &&
                                        (!isset($course['used_in_gpa']) || $course['used_in_gpa'])
                                    )
                                ) {
                                    $complete = false;
                                    break 2;
                                }

                                if (in_array(strtolower($course['grade']), ['ng', 'w', 'i'])) {
                                    $complete = false;
                                    break 2;
                                }

                                if (
                                    !in_array(strtolower($course['grade']), ['i', 'w', 'ng']) &&
                                    isset($course['used_in_gpa']) &&
                                    $course['used_in_gpa']
                                ) {
                                    $creditHourSum += $course['credit'];
                                    $gradePointSum += ($course['credit'] * $course['point_value']);
                                    if ($course['major'] == 1) {
                                        $mCreditHourSum += $course['credit'];
                                        $mGradePointSum += ($course['credit'] * $course['point_value']);
                                    }
                                }
                            }
                        }
                    }

                    if ($complete && $creditHourSum > 0) {
                        $creditAndPointDeduction = $courseAddsTable->ExamGrades->getTotalCreditAndPointDeduction($courseRegistration->student->id, $allAySList);

                        $deductCreditHourSum = $creditAndPointDeduction['deduct_credit_hour_sum'];
                        $deductGradePointSum = $creditAndPointDeduction['deduct_grade_point_sum'];
                        $mDeductCreditHourSum = $creditAndPointDeduction['m_deduct_credit_hour_sum'];
                        $mDeductGradePointSum = $creditAndPointDeduction['m_deduct_grade_point_sum'];

                        $statIndex = count($studentExamStatus);
                        $studentExamStatus[$statIndex] = [
                            'student_id' => $courseRegistration->student->id,
                            'created' => $academicCalendarsTable->getAcademicYearBeginningDate($academicYear, $semester),
                            'academic_year' => $academicYear,
                            'semester' => $semester,
                            'grade_point_sum' => $gradePointSum,
                            'credit_hour_sum' => $creditHourSum,
                            'm_grade_point_sum' => $mGradePointSum,
                            'm_credit_hour_sum' => $mCreditHourSum,
                            'sgpa' => $gradePointSum > 0 && $creditHourSum > 0 ? $gradePointSum / $creditHourSum : 0
                        ];

                        $statusHistories = $this->find()
                            ->where(['student_id' => $courseRegistration->student->id])
                            ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'created' => 'ASC'])
                            ->toArray();

                        $cumulativeGradePoint = $studentExamStatus[$statIndex]['grade_point_sum'];
                        $cumulativeCreditHour = $studentExamStatus[$statIndex]['credit_hour_sum'];
                        $mCumulativeGradePoint = $studentExamStatus[$statIndex]['m_grade_point_sum'];
                        $mCumulativeCreditHour = $studentExamStatus[$statIndex]['m_credit_hour_sum'];

                        foreach ($statusHistories as $history) {
                            if (
                                !($history->academic_year == $academicYear && $history->semester == $semester)
                            ) {
                                $cumulativeGradePoint += $history->grade_point_sum;
                                $cumulativeCreditHour += $history->credit_hour_sum;
                                $mCumulativeGradePoint += $history->m_grade_point_sum;
                                $mCumulativeCreditHour += $history->m_credit_hour_sum;
                                $lastExamStatus = $history;
                            } else {
                                break;
                            }
                        }

                        $studentExamStatus[$statIndex]['cgpa'] = ($cumulativeGradePoint - $deductGradePointSum) > 0 && ($cumulativeCreditHour - $deductCreditHourSum) > 0
                            ? ($cumulativeGradePoint - $deductGradePointSum) / ($cumulativeCreditHour - $deductCreditHourSum)
                            : 0;

                        $studentExamStatus[$statIndex]['mcgpa'] = ($mCumulativeGradePoint - $mDeductGradePointSum) > 0 && ($mCumulativeCreditHour - $mDeductCreditHourSum) > 0
                            ? ($mCumulativeGradePoint - $mDeductGradePointSum) / ($mCumulativeCreditHour - $mDeductCreditHourSum)
                            : 0;

                        $studentLevel = $this->studentYearAndSemesterLevelOfStatus($courseRegistration->student->id, $academicYear, $semester);
                        $ordinals = [1 => 'st', 2 => 'nd', 3 => 'rd'];
                        $studentLevel['year'] = isset($ordinals[$studentLevel['year']])
                            ? $studentLevel['year'] . $ordinals[$studentLevel['year']]
                            : $studentLevel['year'] . 'th';

                        $academicStatuses = $academicStatusesTable->find()
                            ->where(['computable' => 1])
                            ->order(['order' => 'ASC'])
                            ->toArray();

                        foreach ($academicStatuses as $academicStatus) {
                            $academicStands = $academicStandsTable->find()
                                ->where([
                                    'academic_status_id' => $academicStatus->id,
                                    'program_id' => $courseRegistration->student->program_id
                                ])
                                ->order(['academic_year_from' => 'ASC'])
                                ->toArray();

                            foreach ($academicStands as $academicStand) {
                                $standYearLevels = unserialize($academicStand->year_level_id);
                                $standSemesters = unserialize($academicStand->semester);

                                if (
                                    in_array($studentLevel['year'], $standYearLevels) &&
                                    in_array($studentLevel['semester'], $standSemesters) &&
                                    (
                                        substr($courseRegistration->student->academicyear, 0, 4) >= $academicStand->academic_year_from ||
                                        ($academicStand->applicable_for_all_current_student == 1 && substr($academicYear, 0, 4) >= $academicStand->academic_year_from)
                                    )
                                ) {
                                    $academicRules = $academicRulesTable->find()
                                        ->where(['academic_stand_id' => $academicStand->id])
                                        ->toArray();

                                    if (!empty($academicRules)) {
                                        $statusFound = false;
                                        foreach ($academicRules as $academicRule) {
                                            $sgpa = round($studentExamStatus[$statIndex]['sgpa'], 2);
                                            $cgpa = round($studentExamStatus[$statIndex]['cgpa'], 2);

                                            $sgpaTest = empty($academicRule->sgpa);
                                            if (!$sgpaTest) {
                                                switch ($academicRule->scmo) {
                                                    case '>':
                                                        $sgpaTest = $sgpa > $academicRule->sgpa;
                                                        break;
                                                    case '>=':
                                                        $sgpaTest = $sgpa >= $academicRule->sgpa;
                                                        break;
                                                    case '<':
                                                        $sgpaTest = $sgpa < $academicRule->sgpa;
                                                        break;
                                                    case '<=':
                                                        $sgpaTest = $sgpa <= $academicRule->sgpa;
                                                        break;
                                                    default:
                                                        $sgpaTest = false;
                                                        break;
                                                }
                                            }

                                            $cgpaTest = empty($academicRule->cgpa);
                                            if (!$cgpaTest) {
                                                switch ($academicRule->ccmo) {
                                                    case '>':
                                                        $cgpaTest = $cgpa > $academicRule->cgpa;
                                                        break;
                                                    case '>=':
                                                        $cgpaTest = $cgpa >= $academicRule->cgpa;
                                                        break;
                                                    case '<':
                                                        $cgpaTest = $cgpa < $academicRule->cgpa;
                                                        break;
                                                    case '<=':
                                                        $cgpaTest = $cgpa <= $academicRule->cgpa;
                                                        break;
                                                    default:
                                                        $cgpaTest = false;
                                                        break;
                                                }
                                            }

                                            if ($sgpaTest && $cgpaTest) {
                                                $statusFound = true;
                                                break;
                                            }
                                        }

                                        if ($statusFound) {
                                            $minCredit = $academicCalendarsTable->minimumCreditForStatus($courseRegistration->student->id);
                                            if ($creditHourSum < $minCredit) {
                                                $academicStatusId = !empty($statusHistories) && empty($lastExamStatus->academic_status_id) ? $academicStand->academic_status_id : null;
                                            } elseif ($academicStatus->id == 3 && !empty($lastExamStatus)) {
                                                if ($lastExamStatus->academic_status_id == 3 && $this->isThereTcwRuleInDismissal($studentExamStatus[$statIndex]['student_id'], $courseRegistration->student->program_id, $academicYear, $semester, $studentLevel['year'], $studentLevel['semester'], $courseRegistration->student->academicyear)) {
                                                    $academicStatusId = 4;
                                                } elseif ($lastExamStatus->academic_status_id == 6 && $this->isTherePfwRuleInDismissal($studentExamStatus[$statIndex]['student_id'], $courseRegistration->student->program_id, $academicYear, $semester, $studentLevel['year'], $studentLevel['semester'], $courseRegistration->student->academicyear)) {
                                                    $academicStatusId = 4;
                                                } else {
                                                    $academicStatusId = $academicStatus->id;
                                                }
                                            } else {
                                                $academicStatusId = $creditHourSum < $minCredit && !empty($statusHistories) && empty($lastExamStatus->academic_status_id) ? $academicStand->academic_status_id : ($creditHourSum < $minCredit ? null : $academicStatus->id);
                                            }

                                            $studentExamStatus[$statIndex]['academic_status_id'] = $academicStatusId;
                                            break 2;
                                        }
                                    }
                                }
                            }
                        }

                        $otherAcademicRule = $otherAcademicRulesTable->whatIsTheStatus($courseAndGrades, $courseRegistration->student, $studentLevel);
                        if (!empty($otherAcademicRule)) {
                            $studentExamStatus[$statIndex]['academic_status_id'] = $otherAcademicRule;
                        }
                    }
                }
            }
        }

        if (!empty($studentExamStatus)) {
            if (!$this->saveMany($studentExamStatus, ['validate' => false])) {
                $fullySaved = false;
            }
        }

        return $fullySaved;
    }

    /**
     * Gets most recent status for SMS
     */
    public function getMostRecentStatusForSMS(string $phoneNumber): string
    {
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $contactsTable =  TableRegistry::getTableLocator()->get('Contacts');

        $studentDetail = $studentsTable->find()
            ->where(['phone_mobile' => $phoneNumber])
            ->contain(['Users'])
            ->first();

        if (!empty($studentDetail)) {
            $mostRecentStatus = $this->find()
                ->where(['student_id' => $studentDetail->id])
                ->contain(['Students', 'AcademicStatuses'])
                ->order(['academic_year' => 'DESC', 'semester' => 'DESC'])
                ->first();

            return $this->formatStatusForSMS($mostRecentStatus);
        }

        $parentPhones = $contactsTable->find()
            ->where(['phone_mobile' => $phoneNumber])
            ->contain(['Students', 'AcademicStatuses'])
            ->toArray();

        if (!empty($parentPhones)) {
            $allOfTheirKids = 'Your child ';
            foreach ($parentPhones as $parent) {
                $mostRecentStatus = $this->find()
                    ->where(['student_id' => $parent->student->id])
                    ->contain(['Students', 'AcademicStatuses'])
                    ->order(['academic_year' => 'DESC', 'semester' => 'DESC'])
                    ->first();
                $allOfTheirKids .= $this->formatStatusForSMS($mostRecentStatus);
            }
            return $allOfTheirKids;
        }

        return "You don't have the privilege to view student status.";
    }

    /**
     * Formats status for SMS
     */
    public function formatStatusForSMS($mostRecentStatus): string
    {
        if (!empty($mostRecentStatus)) {
            $statusName = $mostRecentStatus->academic_status->name ?? 'undetermined';
            return "{$mostRecentStatus->student->first_name} {$mostRecentStatus->student->last_name} ({$mostRecentStatus->student->studentnumber}) has an academic status of {$statusName} with SGPA: {$mostRecentStatus->sgpa} and CGPA: {$mostRecentStatus->cgpa} for an academic year {$mostRecentStatus->academic_year} of semester {$mostRecentStatus->semester}";
        }
        return "There is no academic status to view currently.";
    }

    /**
     * Gets graduating students
     */
    public function getGraduatingStudent(string $academicYear, int $programId, int $programTypeId, string $departmentId, string $gender, ?int $regionId): array
    {
        return [];
    }

    /**
     * Gets graduating rate to entry student
     */
    public function getGraduatingRateToEntryStudent(string $academicYear, ?string $programId, ?string $programTypeId, ?string $departmentId, string $sex = 'all', ?int $regionId = null): array
    {
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $academicCalendarsTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');

        $options = [
            'conditions' => ['Students.graduated' => 1],
            'contain' => [
                'GraduateLists',
                'Departments',
                'Programs',
                'ProgramTypes',
                'AcceptedStudents'
            ],
            'order' => ['Students.first_name' => 'ASC', 'Students.middle_name' => 'ASC', 'Students.last_name' => 'ASC']
        ];

        $admittedOptions = [
            'conditions' => ['Students.graduated' => 0],
            'contain' => [
                'Departments',
                'Programs',
                'ProgramTypes',
                'AcceptedStudents'
            ]
        ];

        if (!empty($regionId)) {
            $options['conditions']['Students.region_id'] = $regionId;
            $admittedOptions['conditions']['Students.region_id'] = $regionId;
        }

        if ($sex !== 'all') {
            $options['conditions']['Students.gender'] = $sex;
            $admittedOptions['conditions']['Students.gender'] = $sex;
        }

        if (!empty($programId)) {
            $programIds = explode('~', $programId);
            $options['conditions']['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
            $admittedOptions['conditions']['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }

        if (!empty($programTypeId)) {
            $programTypeIds = explode('~', $programTypeId);
            $options['conditions']['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
            $admittedOptions['conditions']['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        if (!empty($academicYear)) {
            $graduateDate = $academicCalendarsTable->getAcademicYearBeginningDate($academicYear);
            $options['conditions']['Students.id IN'] = $studentsTable->GraduateLists->find()->select(['student_id'])->where(['graduate_date >=' => $graduateDate]);
        }

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain([
                'Colleges',
                'YearLevels' => ['order' => ['YearLevels.name' => 'ASC']]
            ])
            ->toArray();

        $distributionGraduateEntry = [];

        foreach ($departments as $department) {
            $options['conditions']['Students.department_id'] = $department->id;
            $admittedOptions['conditions']['Students.department_id'] = $department->id;

            $graduateStudents = $studentsTable->find('all', $options)->toArray();

            foreach ($graduateStudents as $graduate) {
                if (!empty($graduate->accepted_student->academicyear) && !empty($graduate->graduate_list->graduate_date)) {
                    $key = "{$department->college->name}~{$department->name}~{$graduate->program->name}~{$graduate->program_type->name}~{$graduate->graduate_list->graduate_date}";
                    $gender = strtolower($graduate->gender);

                    $distributionGraduateEntry[$key][$graduate->accepted_student->academicyear][$gender]['graduated'] = ($distributionGraduateEntry[$key][$graduate->accepted_student->academicyear][$gender]['graduated'] ?? 0) + 1;

                    $admittedOptions['conditions']['AcceptedStudents.academicyear'] = $graduate->accepted_student->academicyear;
                    $admittedOptions['conditions']['Students.program_id'] = $graduate->program_id;
                    $admittedOptions['conditions']['Students.program_type_id'] = $graduate->program_type_id;
                    $admittedOptions['conditions']['Students.gender'] = $graduate->gender;

                    $distributionGraduateEntry[$key][$graduate->accepted_student->academicyear][$gender]['admitted'] = $studentsTable->find('count', $admittedOptions);
                }
            }
        }

        return ['distributionGraduateEntry' => $distributionGraduateEntry];
    }

    /**
     * Gets most recent student status by college
     */
    public function getMostRecentStudentStatus(int $collegeId): array
    {
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');

        $notGraduatedStudents = $studentsTable->find()
            ->where([
                'college_id' => $collegeId,
                'graduated' => 0
            ])
            ->toArray();

        foreach ($notGraduatedStudents as $key => $student) {
            $status = $this->find()
                ->where(['student_id' => $student->id])
                ->order(['academic_year' => 'DESC', 'semester' => 'DESC'])
                ->first();

            if ($status && $status->academic_status_id == 4) {
                unset($notGraduatedStudents[$key]);
            } else {
                $student->status_academic_year = $status->academic_year ?? '';
                $student->status_semester = $status->semester ?? '';
            }
        }

        return array_values($notGraduatedStudents);
    }

    /**
     * Gets most recent student status for Koha
     */
    public function getMostRecentStudentStatusForKoha(array $studentIds, int $acceptedId = 0): array
    {
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');

        $conditions = $acceptedId == 1
            ? ['accepted_student_id IN' => $studentIds, 'graduated' => 0]
            : ['id IN' => $studentIds, 'graduated' => 0];

        $notGraduatedStudents = $studentsTable->find()
            ->where($conditions)
            ->contain(['AcceptedStudents', 'Departments', 'Users'])
            ->toArray();

        foreach ($notGraduatedStudents as $key => $student) {
            $expired = $studentsTable->isBorrowerExpired($student->studentnumber, $student->college_id);
            if ($expired) {
                $status = $this->find()
                    ->where(['student_id' => $student->id])
                    ->order(['academic_year' => 'DESC', 'semester' => 'DESC'])
                    ->first();

                if ($status && $status->academic_status_id == 4) {
                    unset($notGraduatedStudents[$key]);
                } else {
                    $student->status_academic_year = $status->academic_year ?? '';
                    $student->status_semester = $status->semester ?? '';
                }
            }
        }

        return array_values($notGraduatedStudents);
    }
    /**
     * Gets student taken credits for exit exam
     */
    public function getStudentTakenCreditsForExitExam(int $studentId): array
    {
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable =  TableRegistry::getTableLocator()->get('CourseAdds');
        $courseExemptionsTable =  TableRegistry::getTableLocator()->get('CourseExemptions');
        $curriculumsTable =  TableRegistry::getTableLocator()->get('Curriculums');
        $curriculumAttachmentsTable =  TableRegistry::getTableLocator()->get('CurriculumAttachments');
        $attachmentsTable =  TableRegistry::getTableLocator()->get('Attachments');

        $student = $studentsTable->find()
            ->where([
                'Students.id' => $studentId,
                'Students.graduated' => 0
            ])
            ->select(['id', 'graduated', 'curriculum_id'])
            ->contain([
                'CourseRegistrations' => [
                    'select' => ['id'],
                    'PublishedCourses' => [
                        'select' => ['id', 'add', 'drop'],
                        'Courses' => ['credit', 'major', 'thesis', 'exit_exam']
                    ]
                ],
                'CourseAdds' => [
                    'select' => ['id'],
                    'PublishedCourses' => [
                        'select' => ['id', 'add', 'drop'],
                        'Courses' => ['credit', 'major', 'thesis', 'exit_exam']
                    ]
                ],
                'Attachments' => [
                    'where' => ['Attachments.model' => 'Student']
                ]
            ])
            ->first();

        $taken = [
            'credit_sum' => 0,
            'exempted_credit_sum' => 0,
            'exempted_course_count' => 0,
            'taken_course_count' => 0,
            'curriculum_major_course_count' => 0,
            'curriculum_minor_course_count' => 0,
            'taken_major_course_count' => 0,
            'taken_minor_course_count' => 0,
            'taken_major_course_credit' => 0,
            'taken_minor_course_credit' => 0,
            'course_count_registration' => 0,
            'course_count_add' => 0,
            'credit_sum_registration' => 0,
            'credit_sum_add' => 0,
            'thesis_taken' => 0,
            'thesis_credit' => 0,
            'droped_courses_count' => 0,
            'droped_credit_sum' => 0,
            'photo_dirname' => null,
            'photo_basename' => 'noimage.jpg'
        ];

        if (!empty($student->course_registrations)) {
            foreach ($student->course_registrations as $registration) {
                if (
                    !$courseRegistrationsTable->isCourseDropped($registration->id) &&
                    $registration->published_course->drop == 0 &&
                    $courseRegistrationsTable->ExamGrades->isRegistrationAddForFirstTime($registration->id, 1, 1)
                ) {
                    $taken['credit_sum'] += $registration->published_course->course->credit;
                    $taken['taken_course_count']++;
                    $taken['course_count_registration']++;
                    $taken['credit_sum_registration'] += $registration->published_course->course->credit;

                    if ($registration->published_course->course->major == 1) {
                        $taken['taken_major_course_count']++;
                        $taken['taken_major_course_credit'] += $registration->published_course->course->credit;
                        if ($registration->published_course->course->thesis == 1) {
                            $taken['thesis_credit'] = $registration->published_course->course->credit;
                            $taken['thesis_taken'] = 1;
                        }
                    } else {
                        $taken['taken_minor_course_count']++;
                        $taken['taken_minor_course_credit'] += $registration->published_course->course->credit;
                        if ($registration->published_course->course->thesis == 1) {
                            $taken['thesis_credit'] = $registration->published_course->course->credit;
                            $taken['thesis_taken'] = 1;
                        }
                    }
                } else {
                    $taken['droped_credit_sum'] += $registration->published_course->course->credit;
                    $taken['droped_courses_count']++;
                }
            }
        }

        if (!empty($student->course_adds)) {
            foreach ($student->course_adds as $add) {
                if ($courseRegistrationsTable->ExamGrades->isRegistrationAddForFirstTime($add->id, 0, 1)) {
                    $taken['credit_sum'] += $add->published_course->course->credit;
                    $taken['taken_course_count']++;
                    $taken['course_count_add']++;
                    $taken['credit_sum_add'] += $add->published_course->course->credit;

                    if ($add->published_course->course->major == 1) {
                        $taken['taken_major_course_count']++;
                        $taken['taken_major_course_credit'] += $add->published_course->course->credit;
                        if ($add->published_course->course->thesis == 1) {
                            $taken['thesis_credit'] = $add->published_course->course->credit;
                            $taken['thesis_taken'] = 1;
                        }
                    } else {
                        $taken['taken_minor_course_count']++;
                        $taken['taken_minor_course_credit'] += $add->published_course->course->credit;
                        if ($add->published_course->course->thesis == 1) {
                            $taken['thesis_credit'] = $add->published_course->course->credit;
                            $taken['thesis_taken'] = 1;
                        }
                    }
                }
            }
        }

        $allExemptedCourses = $courseExemptionsTable->find()
            ->where([
                'student_id' => $studentId,
                'department_accept_reject' => 1,
                'registrar_confirm_deny' => 1
            ])
            ->toArray();

        if (!empty($student->curriculum_id)) {
            $taken['curriculum_major_course_count'] = $curriculumsTable->Courses->find()
                ->where(['curriculum_id' => $student->curriculum_id, 'major' => 1])
                ->count();
            $taken['curriculum_minor_course_count'] = $curriculumsTable->Courses->find()
                ->where(['curriculum_id' => $student->curriculum_id, 'major' => 0])
                ->count();
        }

        $studentAttachedCurriculumIds = $curriculumAttachmentsTable->find()
            ->where(['student_id' => $studentId])
            ->select(['id', 'curriculum_id'])
            ->group(['student_id', 'curriculum_id'])
            ->toArray();

        $studentCurriculumCourseList = [];
        if (!empty($studentAttachedCurriculumIds)) {
            $curriculumIds = array_column($studentAttachedCurriculumIds, 'curriculum_id');
            $studentCurriculumCourseList = $curriculumsTable->Courses->find()
                ->where(['curriculum_id IN' => $curriculumIds])
                ->select(['id', 'credit', 'major', 'thesis'])
                ->toArray();
        }

        $studentCurriculumCourseIdList = array_column($studentCurriculumCourseList, 'id');

        foreach ($allExemptedCourses as $exemptedCourse) {
            if (in_array($exemptedCourse->course_id, $studentCurriculumCourseIdList)) {
                $course = array_filter($studentCurriculumCourseList, fn($c) => $c->id == $exemptedCourse->course_id)[0];
                $taken['exempted_credit_sum'] += $course->credit;
                $taken['exempted_course_count']++;
            }
        }

        if (!empty($student->attachments)) {
            foreach ($student->attachments as $attachment) {
                if (!empty($attachment->dirname) && !empty($attachment->basename)) {
                    $taken['photo_dirname'] = $attachment->dirname;
                    $taken['photo_basename'] = $attachment->basename;
                }
            }
        }

        return $taken;
    }

    /**
     * Gets student results for HEMIS
     */
    public function getStudentResultsForHemis(
        ?string $academicYear = null,
        ?string $semester = null,
        $programId = null,
        $programTypeId = null,
        ?string $departmentId = null,
        string $sex = 'all',
        ?string $yearLevelId = null,
        ?int $regionId = null,
        int $freshman = 0,
        string $excludeGraduated = '',
        int $onlyWithCompleteData = 0
    ): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $regionsTable =  TableRegistry::getTableLocator()->get('Regions');
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programsList = array_column($programs, 'name', 'id');
        $programTypesList = array_column($programTypes, 'name', 'id');

        $conditions = [];
        $sectionConditions = ['Sections.id IS NOT NULL'];
        $collegeIds = [];

        if (!empty($regionId)) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($excludeGraduated == '1') {
            $conditions['Students.graduated'] = 0;
        }

        if ($sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        if (!empty($programId)) {
            $programIds = explode('~', $programId);
            $conditions['Students.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
            $sectionConditions['Sections.program_id'] = count($programIds) > 1 ? $programIds[1] : $programId;
        }

        if (!empty($programTypeId)) {
            $programTypeIds = explode('~', $programTypeId);
            $conditions['Students.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
            $sectionConditions['Sections.program_type_id'] = count($programTypeIds) > 1 ? $programTypeIds[1] : $programTypeId;
        }

        if (!empty($academicYear)) {
            $conditions['StudentExamStatuses.academic_year'] = $academicYear;
            $sectionConditions['Sections.academicyear'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['StudentExamStatuses.semester'] = $semester;
        }

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $studentResultsHemis = [];
        $count = 0;

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $collegeIds[$department->college_id] = $department->college_id;
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $sectionConditions['Sections.year_level_id'] = $yearLevel->id;
                    $sectionConditions['Sections.department_id'] = $department->id;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->group(['student_id', 'section_id'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');

                    $results = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.department_id',
                            'Students.college_id',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'Students.graduated',
                            'Students.academicyear',
                            'Students.student_national_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students')
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->order([
                            'StudentExamStatuses.academic_year' => 'DESC',
                            'StudentExamStatuses.semester' => 'DESC',
                            'Students.first_name' => 'ASC',
                            'Students.middle_name' => 'ASC'
                        ])
                        ->toArray();

                    foreach ($results as $result) {
                        $studentTakenCreditsSemesters = $this->getStudentTotalAccumulatedCreditsAndSemesterCount($result->student->id, $academicYear, $semester);

                        $institutionCodes = $departmentsTable->find()
                            ->where(['Departments.id' => $result->student->department_id])
                            ->contain([
                                'Colleges' => [
                                    'select' => ['id', 'name', 'shortname', 'institution_code'],
                                    'Campuses' => ['select' => ['id', 'name', 'campus_code']]
                                ]
                            ])
                            ->select(['id', 'name', 'institution_code'])
                            ->first();

                        $studentRegion = ['Region' => $regionsTable->get($result->student->region_id)->name];
                        $studentTakenCreditsSemesters['StudentTakenCreditsSemesters'] = $studentTakenCreditsSemesters;

                        if ($onlyWithCompleteData && (is_null($result->student->student_national_id) || is_null($institutionCodes->institution_code))) {
                            continue;
                        }

                        $mergedData = array_merge(
                            $result->student->toArray(),
                            $result->toArray(),
                            $institutionCodes->toArray(),
                            $studentTakenCreditsSemesters,
                            $studentRegion
                        );

                        $studentResultsHemis[$result->academic_year . '~' . $result->semester][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        } else {
            $collegeConditions = ['Colleges.active' => 1];
            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $collegeConditions['Colleges.id'] = $collegeId[1];
                } elseif ($departmentId == 0) {
                    $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                    $collegeIds = array_column($collegeIds, 'id');
                }
            } else {
                $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                $collegeIds = array_column($collegeIds, 'id');
            }

            if (!empty($collegeIds)) {
                $colleges = $collegesTable->find()->where(['id IN' => $collegeIds])->toArray();
                foreach ($colleges as $college) {
                    $sectionConditions['Sections.college_id'] = $college->id;
                    $sectionConditions['OR'] = [
                        'Sections.department_id IS NULL',
                        'Sections.department_id' => ['', 0],
                        'Sections.year_level_id IS NULL',
                        'Sections.year_level_id' => ['', 0]
                    ];

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->group(['student_id', 'section_id'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');

                    $results = $this->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.department_id',
                            'Students.college_id',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'Students.graduated',
                            'Students.academicyear',
                            'Students.student_national_id',
                            'StudentExamStatuses.academic_status_id',
                            'StudentExamStatuses.sgpa',
                            'StudentExamStatuses.cgpa',
                            'StudentExamStatuses.credit_hour_sum',
                            'StudentExamStatuses.semester',
                            'StudentExamStatuses.academic_year'
                        ])
                        ->distinct(['StudentExamStatuses.student_id'])
                        ->innerJoinWith('Students')
                        ->where($conditions)
                        ->where(['StudentExamStatuses.student_id IN' => $studentIds])
                        ->order([
                            'StudentExamStatuses.academic_year' => 'DESC',
                            'StudentExamStatuses.semester' => 'DESC',
                            'Students.first_name' => 'ASC',
                            'Students.middle_name' => 'ASC'
                        ])
                        ->toArray();

                    foreach ($results as $result) {
                        $studentTakenCreditsSemesters = $this->getStudentTotalAccumulatedCreditsAndSemesterCount($result->student->id, $academicYear, $semester);

                        $institutionCodes = $collegesTable->find()
                            ->where(['Colleges.id' => $result->student->college_id])
                            ->contain(['Campuses' => ['select' => ['id', 'name', 'campus_code']]])
                            ->select(['id', 'name', 'shortname', 'institution_code'])
                            ->first();

                        $studentRegion = ['Region' => $regionsTable->get($result->student->region_id)->name];
                        $studentTakenCreditsSemesters['StudentTakenCreditsSemesters'] = $studentTakenCreditsSemesters;

                        if ($onlyWithCompleteData && (is_null($result->student->student_national_id) || is_null($institutionCodes->institution_code))) {
                            continue;
                        }

                        $mergedData = array_merge(
                            $result->student->toArray(),
                            $result->toArray(),
                            $institutionCodes->toArray(),
                            $studentTakenCreditsSemesters,
                            $studentRegion
                        );

                        $studentResultsHemis[$result->academic_year . '~' . $result->semester][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        }

        return $studentResultsHemis;
    }

    /**
     * Gets student total accumulated credits and semester count
     */
    public function getStudentTotalAccumulatedCreditsAndSemesterCount(?int $studentId = null, ?string $academicYear = null, ?string $semester = null): array
    {
        if (empty($studentId) || empty($academicYear) || empty($semester)) {
            return [];
        }

        $totalAttendedSemesters = $this->find()
            ->where(['student_id' => $studentId])
            ->select(['id', 'academic_year', 'semester'])
            ->group(['academic_year', 'semester'])
            ->order(['academic_year' => 'DESC', 'semester' => 'DESC'])
            ->toArray();

        $lastGPA = $this->find()
            ->where(['student_id' => $studentId])
            ->group(['academic_year', 'semester'])
            ->order(['academic_year' => 'DESC', 'semester' => 'DESC'])
            ->toArray();

        $latestLastStatus = [];
        if (!empty($lastGPA) && ($lastGPA[0]->academic_year != $academicYear || $lastGPA[0]->semester != $semester)) {
            $latestLastStatus = $lastGPA[0];
        }

        $stexstIdsAll = [];
        $currSemStexstId = null;

        foreach ($totalAttendedSemesters as $attended) {
            if ($attended->semester == ($latestLastStatus->semester ?? $semester) && $attended->academic_year == ($latestLastStatus->academic_year ?? $academicYear)) {
                $currSemStexstId = $attended->id;
            }
            $stexstIdsAll[] = $attended->id;
        }

        $stexstIdsSearch = [];
        if ($currSemStexstId && !empty($stexstIdsAll)) {
            foreach ($stexstIdsAll as $id) {
                if ($id <= $currSemStexstId) {
                    $stexstIdsSearch[] = $id;
                }
            }
        }

        $result = [];
        if (!empty($stexstIdsSearch)) {
            $result = $this->find()
                ->where(['id IN' => $stexstIdsSearch])
                ->select([
                    'totalSemesters' => 'COUNT(*)',
                    'totalAccumulatedCredits' => 'SUM(credit_hour_sum)'
                ])
                ->first()
                ->toArray();
        }

        return $result;
    }

    /**
     * Gets student total accumulated credits and semester count for graduated students
     */
    public function getStudentTotalAccumulatedCreditsAndSemesterCountGraduated(?int $studentId = null): array
    {
        if (empty($studentId)) {
            return [];
        }

        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');

        $totalAttendedSemestersCountRegistration = $courseRegistrationsTable->find()
            ->where(['student_id' => $studentId])
            ->group(['semester', 'academic_year', 'student_id'])
            ->order(['id' => 'DESC', 'academic_year' => 'DESC', 'semester' => 'DESC'])
            ->toArray();

        $result = [
            'TotalAcademicPeriods' => count($totalAttendedSemestersCountRegistration),
            'TotalAccumulatedCredits' => $studentsTable->calculateCumulativeStudentRegisteredAddedCredit($studentId, 1, null, null, 0)
        ];

        return $result;
    }

    /**
     * Gets graduated students for HEMIS
     */
    public function getStudentGraduateForHemis(
        ?string $departmentId = null,
        ?string $academicYear = null,
        ?string $semester = null,
        $programId = null,
        $programTypeId = null,
        int $onlyWithCompleteData = 0
    ): array
    {
        $graduateListsTable =  TableRegistry::getTableLocator()->get('GraduateLists');
        $academicCalendarsTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');

        $options = [
            'contain' => [
                'Students' => [
                    'select' => [
                        'id',
                        'full_name',
                        'first_name',
                        'middle_name',
                        'last_name',
                        'gender',
                        'studentnumber',
                        'program_id',
                        'program_type_id',
                        'student_national_id',
                        'graduated',
                        'region_id'
                    ],
                    'order' => ['first_name' => 'ASC', 'middle_name' => 'ASC', 'last_name' => 'ASC'],
                    'Departments' => ['id', 'name', 'institution_code'],
                    'Colleges' => ['id', 'name', 'shortname', 'institution_code'],
                    'Programs' => ['id', 'name'],
                    'ProgramTypes' => ['id', 'name'],
                    'Regions' => ['id', 'name'],
                    'ExitExams' => [
                        'select' => ['id', 'result', 'exam_date', 'modified'],
                        'order' => ['id' => 'DESC', 'modified' => 'DESC', 'exam_date' => 'DESC']
                    ],
                    'StudentExamStatuses' => [
                        'select' => ['student_id', 'cgpa', 'academic_year', 'semester'],
                        'order' => ['academic_year' => 'DESC', 'semester' => 'DESC']
                    ]
                ]
            ],
            'group' => ['GraduateLists.student_id'],
            'order' => ['Students.first_name' => 'ASC']
        ];

        if (!empty($semester) && !empty($academicYear)) {
            $acYearBeginningDate = $academicCalendarsTable->getAcademicYearBeginningDate($academicYear);
            $selectedYear = explode('-', $acYearBeginningDate);

            if (!empty($selectedYear[0])) {
                $minDate = $semester == 'I' ? $acYearBeginningDate : (
                $semester == 'II' ? ($selectedYear[0] + 1) . '-02-21' : ($selectedYear[0] + 1) . '-06-21'
                );
                $maxDate = $semester == 'I' ? ($selectedYear[0] + 1) . '-02-20' : (
                $semester == 'II' ? ($selectedYear[0] + 1) . '-06-20' : ($selectedYear[0] + 1) . '-09-20'
                );

                $options['conditions']['GraduateLists.graduate_date BETWEEN ? AND ?'] = [$minDate, $maxDate];
            }
        }

        if (!empty($departmentId)) {
            $cOrD = explode('~', $departmentId);
            if ($cOrD[0] == 'c') {
                $options['conditions']['Students.college_id'] = $cOrD[1];
            } else {
                $options['conditions']['Students.department_id'] = $departmentId;
            }
        }

        if ($programId != 0) {
            $options['conditions']['Students.program_id'] = $programId;
        }

        if ($programTypeId != 0) {
            $options['conditions']['Students.program_type_id'] = $programTypeId;
        }

        $graduatedStudents = !empty($options['conditions']) ? $graduateListsTable->find('all', $options)->toArray() : [];
        $graduatedStudentsFiltered = [];
        $count = 0;

        foreach ($graduatedStudents as $gradStudent) {
            if ($onlyWithCompleteData && (is_null($gradStudent->student->student_national_id) || is_null($gradStudent->student->department->institution_code))) {
                continue;
            }

            $gradStudent->academic_year = $academicYear;
            $gradStudent->semester = $semester;
            $gradStudent->AccumulatedCreditsAndSemesterCount = $this->getStudentTotalAccumulatedCreditsAndSemesterCountGraduated($gradStudent->student->id);

            $graduatedStudentsFiltered[$academicYear . '~' . $semester][$count] = $gradStudent;
            $count++;
        }

        return $graduatedStudentsFiltered;
    }

    /**
     * Gets student taken credits for HEMIS
     */
    public function getStudentTakenCreditsForHemis(int $studentId, int $graduated = 0): array
    {
        $studentsTable =  TableRegistry::getTableLocator()->get('Students');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable =  TableRegistry::getTableLocator()->get('CourseAdds');
        $courseExemptionsTable =  TableRegistry::getTableLocator()->get('CourseExemptions');
        $curriculumsTable =  TableRegistry::getTableLocator()->get('Curriculums');
        $curriculumAttachmentsTable =  TableRegistry::getTableLocator()->get('CurriculumAttachments');
        $attachmentsTable =  TableRegistry::getTableLocator()->get('Attachments');

        $student = $studentsTable->find()
            ->where([
                'Students.id' => $studentId,
                'Students.graduated' => $graduated
            ])
            ->select(['id', 'graduated', 'curriculum_id'])
            ->contain([
                'CourseRegistrations' => [
                    'select' => ['id'],
                    'PublishedCourses' => [
                        'select' => ['id', 'drop'],
                        'Courses' => ['credit', 'major', 'thesis', 'exit_exam']
                    ]
                ],
                'CourseAdds' => [
                    'select' => ['id'],
                    'PublishedCourses' => [
                        'select' => ['id', 'drop'],
                        'Courses' => ['credit', 'major', 'thesis', 'exit_exam']
                    ]
                ],
                'Attachments' => [
                    'where' => ['Attachments.model' => 'Student']
                ]
            ])
            ->first();

        $taken = [
            'credit_sum' => 0,
            'exempted_credit_sum' => 0,
            'exempted_course_count' => 0,
            'taken_course_count' => 0,
            'curriculum_major_course_count' => 0,
            'curriculum_minor_course_count' => 0,
            'taken_major_course_count' => 0,
            'taken_minor_course_count' => 0,
            'taken_major_course_credit' => 0,
            'taken_minor_course_credit' => 0,
            'course_count_registration' => 0,
            'course_count_add' => 0,
            'credit_sum_registration' => 0,
            'credit_sum_add' => 0,
            'thesis_taken' => 0,
            'thesis_credit' => 0,
            'exit_exam_taken' => 0,
            'exit_exam_credit' => 0,
            'droped_courses_count' => 0,
            'droped_credit_sum' => 0,
            'photo_dirname' => null,
            'photo_basename' => 'noimage.jpg'
        ];

        if (!empty($student->course_registrations)) {
            foreach ($student->course_registrations as $registration) {
                if (
                    !$courseRegistrationsTable->isCourseDropped($registration->id) &&
                    $registration->published_course->drop == 0 &&
                    $courseRegistrationsTable->ExamGrades->isRegistrationAddForFirstTime($registration->id, 1, 1)
                ) {
                    $taken['credit_sum'] += $registration->published_course->course->credit;
                    $taken['taken_course_count']++;
                    $taken['course_count_registration']++;
                    $taken['credit_sum_registration'] += $registration->published_course->course->credit;

                    if ($registration->published_course->course->major == 1) {
                        $taken['taken_major_course_count']++;
                        $taken['taken_major_course_credit'] += $registration->published_course->course->credit;
                        if ($registration->published_course->course->thesis == 1) {
                            $taken['thesis_credit'] = $registration->published_course->course->credit;
                            $taken['thesis_taken'] = 1;
                        }
                        if ($registration->published_course->course->exit_exam == 1) {
                            $taken['exit_exam_credit'] = $registration->published_course->course->credit;
                            $taken['exit_exam_taken'] = 1;
                        }
                    } else {
                        $taken['taken_minor_course_count']++;
                        $taken['taken_minor_course_credit'] += $registration->published_course->course->credit;
                        if ($registration->published_course->course->thesis == 1) {
                            $taken['thesis_credit'] = $registration->published_course->course->credit;
                            $taken['thesis_taken'] = 1;
                        }
                        if ($registration->published_course->course->exit_exam == 1) {
                            $taken['exit_exam_credit'] = $registration->published_course->course->credit;
                            $taken['exit_exam_taken'] = 1;
                        }
                    }
                } else {
                    $taken['droped_credit_sum'] += $registration->published_course->course->credit;
                    $taken['droped_courses_count']++;
                }
            }
        }

        if (!empty($student->course_adds)) {
            foreach ($student->course_adds as $add) {
                if ($courseRegistrationsTable->ExamGrades->isRegistrationAddForFirstTime($add->id, 0, 1)) {
                    $taken['credit_sum'] += $add->published_course->course->credit;
                    $taken['taken_course_count']++;
                    $taken['course_count_add']++;
                    $taken['credit_sum_add'] += $add->published_course->course->credit;

                    if ($add->published_course->course->major == 1) {
                        $taken['taken_major_course_count']++;
                        $taken['taken_major_course_credit'] += $add->published_course->course->credit;
                        if ($add->published_course->course->thesis == 1) {
                            $taken['thesis_credit'] = $add->published_course->course->credit;
                            $taken['thesis_taken'] = 1;
                        }
                        if ($add->published_course->course->exit_exam == 1) {
                            $taken['exit_exam_credit'] = $add->published_course->course->credit;
                            $taken['exit_exam_taken'] = 1;
                        }
                    } else {
                        $taken['taken_minor_course_count']++;
                        $taken['taken_minor_course_credit'] += $add->published_course->course->credit;
                        if ($add->published_course->course->thesis == 1) {
                            $taken['thesis_credit'] = $add->published_course->course->credit;
                            $taken['thesis_taken'] = 1;
                        }
                        if ($add->published_course->course->exit_exam == 1) {
                            $taken['exit_exam_credit'] = $add->published_course->course->credit;
                            $taken['exit_exam_taken'] = 1;
                        }
                    }
                }
            }
        }

        $allExemptedCourses = $courseExemptionsTable->find()
            ->where([
                'student_id' => $student->id,
                'department_accept_reject' => 1,
                'registrar_confirm_deny' => 1
            ])
            ->toArray();

        $taken['curriculum_major_course_count'] = $curriculumsTable->Courses->find()
            ->where(['curriculum_id' => $student->curriculum_id, 'major' => 1])
            ->count();
        $taken['curriculum_minor_course_count'] = $curriculumsTable->Courses->find()
            ->where(['curriculum_id' => $student->curriculum_id, 'major' => 0])
            ->count();

        $studentAttachedCurriculumIds = $curriculumAttachmentsTable->find()
            ->where(['student_id' => $student->id])
            ->select(['id', 'curriculum_id'])
            ->toArray();

        $studentCurriculumCourseList = $curriculumsTable->Courses->find()
            ->where(['curriculum_id IN' => array_column($studentAttachedCurriculumIds, 'curriculum_id')])
            ->select(['id', 'credit', 'major', 'thesis', 'exit_exam'])
            ->toArray();

        $studentCurriculumCourseIdList = array_column($studentCurriculumCourseList, 'id');

        foreach ($allExemptedCourses as $exemptedCourse) {
            if (in_array($exemptedCourse->course_id, $studentCurriculumCourseIdList)) {
                $course = array_filter($studentCurriculumCourseList, fn($c) => $c->id == $exemptedCourse->course_id)[0];
                $taken['exempted_credit_sum'] += $course->credit;
                $taken['exempted_course_count']++;
            }
        }

        if (!empty($student->attachments)) {
            foreach ($student->attachments as $attachment) {
                if (!empty($attachment->dirname) && !empty($attachment->basename)) {
                    $taken['photo_dirname'] = $attachment->dirname;
                    $taken['photo_basename'] = $attachment->basename;
                }
            }
        }

        return $taken;
    }

    /**
     * Gets student list for office
     */
    public function getStudentListForOffice(
        ?string $academicYear = null,
        ?string $semester = null,
        $programId = 0,
        $programTypeId = 0,
        ?string $departmentId = null,
        string $sex = 'all',
        ?string $yearLevelId = null,
        ?int $regionId = null,
        int $freshman = 0,
        string $excludeGraduated = ''
    ): array
    {
        if (empty($academicYear) && empty($semester)) {
            return [];
        }

        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $curriculumsTable =  TableRegistry::getTableLocator()->get('Curriculums');
        $campusesTable =  TableRegistry::getTableLocator()->get('Campuses');

        $programs = $programsTable->find()->select(['id', 'name'])->toArray();
        $programTypes = $programTypesTable->find()->select(['id', 'name'])->toArray();
        $programsList = array_column($programs, 'name', 'id');
        $programTypesList = array_column($programTypes, 'name', 'id');

        $conditions = [];
        $sectionConditions = ['Sections.id IS NOT NULL'];
        $collegeIds = [];

        if (!empty($regionId)) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        if ($excludeGraduated == '1') {
            $conditions['Students.graduated'] = 0;
        }

        if (!empty($programId)) {
            if (is_array($programId)) {
                $conditions['Students.program_id IN'] = $programId;
                $sectionConditions['Sections.program_id IN'] = $programId;
            } elseif ($programId != 0) {
                $conditions['Students.program_id'] = $programId;
                $sectionConditions['Sections.program_id'] = $programId;
            } else {
                $conditions['Students.program_id'] = 0;
                $sectionConditions['Sections.program_id'] = 0;
            }
        }

        if (!empty($programTypeId)) {
            if (is_array($programTypeId)) {
                $conditions['Students.program_type_id IN'] = $programTypeId;
                $sectionConditions['Sections.program_type_id IN'] = $programTypeId;
            } elseif ($programTypeId != 0) {
                $conditions['Students.program_type_id'] = $programTypeId;
                $sectionConditions['Sections.program_type_id'] = $programTypeId;
            } else {
                $conditions['Students.program_type_id'] = 0;
                $sectionConditions['Sections.program_type_id'] = 0;
            }
        }

        if (!empty($academicYear)) {
            $conditions['CourseRegistrations.academic_year'] = $academicYear;
            $sectionConditions['Sections.academicyear'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['CourseRegistrations.semester'] = $semester;
        }

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $studentListRegistered = [];
        $count = 0;

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $collegeIds[$department->college_id] = $department->college_id;
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, function ($yl) use ($yearLevelId) {
                    return strcasecmp($yl->name, $yearLevelId) === 0;
                }) : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $ylId = $yearLevel->id;
                    $deptId = $department->id;
                    $sectionConditions['Sections.year_level_id'] = $ylId;
                    $sectionConditions['Sections.department_id'] = $deptId;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->group(['student_id', 'section_id'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $registeredStudents = $courseRegistrationsTable->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.program_id',
                            'Students.program_type_id',
                            'Students.curriculum_id',
                            'CourseRegistrations.semester',
                            'CourseRegistrations.academic_year',
                            'CourseRegistrations.section_id',
                            'Students.academicyear',
                            'Students.graduated',
                            'Students.email',
                            'Students.email_alternative',
                            'Students.phone_mobile'
                        ])
                        ->distinct(['CourseRegistrations.student_id'])
                        ->innerJoinWith('Students', function ($q) use ($deptId, $ylId) {
                            return $q->where(['Students.department_id' => $deptId]);
                        })
                        ->where($conditions)
                        ->where(['CourseRegistrations.student_id IN' => $studentIds, 'CourseRegistrations.year_level_id' => $ylId])
                        ->group(['CourseRegistrations.semester', 'CourseRegistrations.academic_year', 'CourseRegistrations.section_id', 'CourseRegistrations.student_id'])
                        ->order([
                            'CourseRegistrations.academic_year' => 'DESC',
                            'CourseRegistrations.semester' => 'DESC',
                            'CourseRegistrations.section_id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name'
                        ])
                        ->toArray();

                    foreach ($registeredStudents as $student) {
                        $checkRegistered = $courseRegistrationsTable->find()
                            ->where([
                                'student_id' => $student->student->id,
                                'semester' => $semester,
                                'academic_year' => $academicYear
                            ])
                            ->count();

                        if ($checkRegistered) {
                            $section = $sectionsTable->find()
                                ->where(['id' => $studentSectionMap[$student->student->id]])
                                ->first();

                            $yearLevelData = $sectionsTable->getStudentYearLevel($student->student->id);

                            $curriculumDetails = !empty($student->student->curriculum_id) ? $curriculumsTable->find()
                                ->where(['id' => $student->student->curriculum_id])
                                ->select([
                                    'id',
                                    'name',
                                    'type_credit',
                                    'english_degree_nomenclature',
                                    'minimum_credit_points',
                                    'specialization_english_degree_nomenclature'
                                ])
                                ->first()
                                ->toArray() : [];

                            $studentData = [
                                'Campus' => $campusesTable->field('name', ['id' => $department->college->campus_id]),
                                'College' => $department->college->name,
                                'Department' => $department->name,
                                'Program' => $programsList[$student->student->program_id],
                                'ProgramType' => $programTypesList[$student->student->program_type_id],
                                'AcademicYear' => $yearLevelData['academicyear'],
                                'YearLevel' => $yearLevelData['year']
                            ];

                            $mergedData = array_merge(
                                $student->student->toArray(),
                                ['Curriculum' => $curriculumDetails],
                                $studentData
                            );

                            $studentListRegistered[$department->college->name][$count] = $mergedData;
                            $count++;
                        }
                    }
                }
            }
        } else {
            $collegeConditions = ['Colleges.active' => 1];
            if (!empty($departmentId)) {
                $collegeId = explode('~', $departmentId);
                if (count($collegeId) > 1) {
                    $collegeConditions['Colleges.id'] = $collegeId[1];
                } elseif ($departmentId == 0) {
                    $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                    $collegeIds = array_column($collegeIds, 'id');
                }
            } else {
                $collegeIds = $collegesTable->find()->select(['id'])->where(['active' => 1])->toArray();
                $collegeIds = array_column($collegeIds, 'id');
            }

            if (!empty($collegeIds)) {
                $colleges = $collegesTable->find()->where(['id IN' => $collegeIds])->toArray();
                foreach ($colleges as $college) {
                    $sectionConditions['Sections.college_id'] = $college->id;
                    $sectionConditions['OR'] = [
                        'Sections.department_id IS NULL',
                        'Sections.department_id' => ['', 0],
                        'Sections.year_level_id IS NULL',
                        'Sections.year_level_id' => ['', 0]
                    ];

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->group(['student_id', 'section_id'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $registeredStudents = $courseRegistrationsTable->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.program_id',
                            'Students.program_type_id',
                            'Students.curriculum_id',
                            'CourseRegistrations.semester',
                            'CourseRegistrations.academic_year',
                            'CourseRegistrations.section_id',
                            'Students.academicyear',
                            'Students.graduated',
                            'Students.email',
                            'Students.email_alternative',
                            'Students.phone_mobile'
                        ])
                        ->distinct(['CourseRegistrations.student_id'])
                        ->innerJoinWith('Students', function ($q) use ($college) {
                            return $q->where(['Students.college_id' => $college->id]);
                        })
                        ->where($conditions)
                        ->where([
                            'CourseRegistrations.student_id IN' => $studentIds,
                            'OR' => [
                                'CourseRegistrations.year_level_id IS NULL',
                                'CourseRegistrations.year_level_id' => ['', 0]
                            ]
                        ])
                        ->group(['CourseRegistrations.semester', 'CourseRegistrations.academic_year', 'CourseRegistrations.section_id', 'CourseRegistrations.student_id'])
                        ->order([
                            'CourseRegistrations.academic_year' => 'DESC',
                            'CourseRegistrations.semester' => 'DESC',
                            'CourseRegistrations.section_id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name'
                        ])
                        ->toArray();

                    foreach ($registeredStudents as $student) {
                        $checkRegistered = $courseRegistrationsTable->find()
                            ->where([
                                'student_id' => $student->student->id,
                                'semester' => $semester,
                                'academic_year' => $academicYear
                            ])
                            ->count();

                        if ($checkRegistered) {
                            $section = $sectionsTable->find()
                                ->where(['id' => $studentSectionMap[$student->student->id]])
                                ->first();

                            $studentData = [
                                'Campus' => $campusesTable->field('name', ['id' => $college->campus_id]),
                                'College' => $college->name,
                                'Department' => $section->name . ' - ' . ($section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/Freshman'),
                                'Program' => $programsList[$student->student->program_id],
                                'ProgramType' => $programTypesList[$student->student->program_type_id],
                                'AcademicYear' => $section->academicyear,
                                'YearLevel' => $section->program_id == PROGRAM_REMEDIAL ? 'Remedial' : 'Pre/1st'
                            ];

                            $mergedData = array_merge(
                                $student->student->toArray(),
                                ['Curriculum' => []],
                                $studentData
                            );

                            $studentListRegistered[$college->name][$count] = $mergedData;
                            $count++;
                        }
                    }
                }
            }
        }

        return $studentListRegistered;
    }

    /**
     * Gets student enrollment data for HEMIS
     */
    public function getStudentEnrolmentForHemis(
        ?string $academicYear = null,
        ?string $semester = null,
        $programId = 0,
        $programTypeId = 0,
        ?string $departmentId = null,
        string $sex = 'all',
        ?string $yearLevelId = null,
        ?int $regionId = null,
        int $freshman = 0,
        string $excludeGraduated = '',
        int $onlyWithCompleteData = 0
    ): array
    {
        if (empty($academicYear) || empty($semester)) {
            return [];
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $curriculumsTable =  TableRegistry::getTableLocator()->get('Curriculums');
        $regionsTable =  TableRegistry::getTableLocator()->get('Regions');
        $acceptedStudentsTable =  TableRegistry::getTableLocator()->get('AcceptedStudents');
        $readmissionsTable =  TableRegistry::getTableLocator()->get('Readmissions');
        $academicCalendarsTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');
        $departmentStudyProgramsTable =  TableRegistry::getTableLocator()->get('DepartmentStudyPrograms');
        $studentsTable=  TableRegistry::getTableLocator()->get('Students');

        $conditions = [];
        $sectionConditions = ['Sections.id IS NOT NULL'];
        $collegeIds = [];

        if (!empty($regionId)) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($excludeGraduated == '1') {
            $conditions['Students.graduated'] = 0;
        }

        if ($onlyWithCompleteData) {
            $conditions['Students.student_national_id IS NOT NULL'] = null;
            if (!$freshman) {
                $conditions['Students.curriculum_id IS NOT NULL'] = null;
            }
        }

        if ($sex !== 'all') {
            $conditions['Students.gender'] = $sex;
        }

        if (!empty($programId)) {
            if (is_array($programId)) {
                $conditions['Students.program_id IN'] = $programId;
                $sectionConditions['Sections.program_id IN'] = $programId;
            } elseif ($programId != 0) {
                $conditions['Students.program_id'] = $programId;
                $sectionConditions['Sections.program_id'] = $programId;
            } else {
                $conditions['Students.program_id'] = 0;
                $sectionConditions['Sections.program_id'] = 0;
            }
        }

        if (!empty($programTypeId)) {
            if (is_array($programTypeId)) {
                $conditions['Students.program_type_id IN'] = $programTypeId;
                $sectionConditions['Sections.program_type_id IN'] = $programTypeId;
            } elseif ($programTypeId != 0) {
                $conditions['Students.program_type_id'] = $programTypeId;
                $sectionConditions['Sections.program_type_id'] = $programTypeId;
            } else {
                $conditions['Students.program_type_id'] = 0;
                $sectionConditions['Sections.program_type_id'] = 0;
            }
        }

        if (!empty($academicYear)) {
            $conditions['CourseRegistrations.academic_year'] = $academicYear;
            $sectionConditions['Sections.academicyear'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['CourseRegistrations.semester'] = $semester;
        }

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $studentResultsHemis = [];
        $count = 0;

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $collegeIds[$department->college_id] = $department->college_id;
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, fn($yl) => strcasecmp($yl->name, $yearLevelId) === 0) : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $ylId = $yearLevel->id;
                    $deptId = $department->id;
                    $sectionConditions['Sections.year_level_id'] = $ylId;
                    $sectionConditions['Sections.department_id'] = $deptId;

                    $studentSections = $studentsSectionsTable->find()
                        ->where(['StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)])
                        ->select(['student_id', 'section_id'])
                        ->group(['student_id', 'section_id'])
                        ->order(['id' => 'DESC', 'modified' => 'DESC', 'section_id' => 'DESC'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $results = $courseRegistrationsTable->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.department_id',
                            'Students.college_id',
                            'Students.accepted_student_id',
                            'Students.region_id',
                            'Students.program_id',
                            'Students.program_type_id',
                            'Students.curriculum_id',
                            'Students.graduated',
                            'Students.academicyear',
                            'Students.student_national_id',
                            'CourseRegistrations.academic_year',
                            'CourseRegistrations.section_id',
                            'CourseRegistrations.year_level_id'
                        ])
                        ->distinct(['CourseRegistrations.student_id'])
                        ->innerJoinWith('Students', fn($q) => $q->where(['Students.department_id' => $deptId]))
                        ->where($conditions)
                        ->where(['CourseRegistrations.student_id IN' => $studentIds, 'CourseRegistrations.year_level_id' => $ylId])
                        ->group(['CourseRegistrations.semester', 'CourseRegistrations.academic_year', 'CourseRegistrations.section_id', 'CourseRegistrations.student_id'])
                        ->order([
                            'CourseRegistrations.academic_year' => 'DESC',
                            'CourseRegistrations.semester' => 'DESC',
                            'CourseRegistrations.section_id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name'
                        ])
                        ->toArray();

                    foreach ($results as $result) {
                        $departmentStudyProgramId = $curriculumsTable->field('department_study_program_id', ['id' => $result->student->curriculum_id]);

                        if ($onlyWithCompleteData && is_null($departmentStudyProgramId)) {
                            continue;
                        }

                        $section = $sectionsTable->find()
                            ->where(['id' => $studentSectionMap[$result->student->id]])
                            ->contain(['YearLevels' => ['id', 'name']])
                            ->first();

                        $institutionCodes = $departmentsTable->find()
                            ->where(['id' => $result->student->department_id])
                            ->contain([
                                'Colleges' => [
                                    'select' => ['id', 'name', 'institution_code'],
                                    'Campuses' => ['select' => ['id', 'name', 'campus_code']]
                                ]
                            ])
                            ->select(['id', 'name', 'institution_code'])
                            ->first();

                        $studentData = [
                            'stud_id' => $result->student->id,
                            'Section' => $section->name,
                            'YearLevel' => mb_substr($section->year_level->name, 0, 1),
                            'Region' => $regionsTable->field('name', ['id' => $result->student->region_id])
                        ];

                        $lastRegistration = $courseRegistrationsTable->find()
                            ->where(['student_id' => $result->student->id])
                            ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
                            ->first();

                        $currentRegistration = $courseRegistrationsTable->find()
                            ->where([
                                'student_id' => $result->student->id,
                                'academic_year' => $academicYear,
                                'semester' => $semester
                            ])
                            ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'])
                            ->first();

                        $studentStatusBySelectedAcySem = $this->find()
                            ->where([
                                'student_id' => $result->student->id,
                                'academic_year' => $academicYear,
                                'semester' => $semester,
                                'academic_status_id IS NOT NULL'
                            ])
                            ->count();

                        $lastStudentStatus = $studentStatusBySelectedAcySem
                            ? $this->find()
                                ->where([
                                    'student_id' => $result->student->id,
                                    'academic_year' => $academicYear,
                                    'semester' => $semester
                                ])
                                ->contain(['AcademicStatuses'])
                                ->group(['student_id', 'academic_year', 'semester'])
                                ->order(['id' => 'DESC', 'modified' => 'DESC'])
                                ->first()
                            : $this->find()
                                ->where(['student_id' => $result->student->id])
                                ->contain(['AcademicStatuses'])
                                ->group(['student_id', 'academic_year', 'semester'])
                                ->order(['id' => 'DESC', 'created' => 'DESC'])
                                ->first();

                        $readmitted = [];
                        if (!empty($lastStudentStatus)) {
                            $possibleReadmissionYears = $this->getAcademicYearRange($lastStudentStatus->academic_year, $academicYear);
                            $readmitted = $readmissionsTable->find()
                                ->where([
                                    'student_id' => $result->student->id,
                                    'registrar_approval' => 1,
                                    'academic_commission_approval' => 1,
                                    'academic_year IN' => $possibleReadmissionYears
                                ])
                                ->order(['academic_year' => 'DESC', 'semester' => 'DESC', 'modified' => 'DESC'])
                                ->first();
                        }

                        if (!empty($readmitted) && $lastStudentStatus->academic_status_id == DISMISSED_ACADEMIC_STATUS_ID) {
                            $studentData['EnrollmentType'] = 'AR';
                        } elseif (!empty($readmitted)) {
                            $studentData['EnrollmentType'] = 'PR';
                        } elseif ($section->year_level->name == '1st' && $currentRegistration->semester == 'I') {
                            $studentData['EnrollmentType'] = 'NW';
                        } elseif ($result->student->program_type_id == PROGRAM_TYPE_ADVANCE_STANDING) {
                            $studentData['EnrollmentType'] = 'AS';
                        } else {
                            $studentData['EnrollmentType'] = 'CN';
                        }

                        if (!is_null($departmentStudyProgramId) && is_numeric($departmentStudyProgramId)) {
                            $curriculumStudyProgramDetails = $departmentStudyProgramsTable->find()
                                ->where(['id' => $departmentStudyProgramId])
                                ->contain([
                                    'StudyPrograms' => ['id', 'code'],
                                    'ProgramModalities' => ['id', 'code'],
                                    'Qualifications' => ['id', 'code']
                                ])
                                ->select(['id', 'study_program_id'])
                                ->first();

                            if (!empty($curriculumStudyProgramDetails)) {
                                $studentData['StudyProgram'] = $curriculumStudyProgramDetails->study_program->code;
                                $studentData['ProgramModality'] = $curriculumStudyProgramDetails->program_modality->code;
                                $studentData['TargetQualification'] = $curriculumStudyProgramDetails->qualification->code;
                            }
                        } else {
                            $studentData['StudyProgram'] = '';
                            $studentData['ProgramModality'] = '';
                            $studentData['TargetQualification'] = '';
                        }

                        $regionId = $acceptedStudentsTable->field('region_id', ['id' => $result->student->accepted_student_id]);
                        $countryId = $regionsTable->field('country_id', ['id' => $regionId]);

                        $studentData['ForeignProgram'] = ($countryId && $countryId != COUNTRY_ID_OF_ETHIOPIA) ? 'SCH' : '';
                        $studentData['CostSharingLoan'] = ($result->student->program_id == PROGRAM_UNDEGRADUATE && $result->student->program_type_id == PROGRAM_TYPE_REGULAR && $countryId == COUNTRY_ID_OF_ETHIOPIA) ? 'Y' : 'N';

                        $minimumCreditPointsRequired = $curriculumsTable->field('minimum_credit_points', ['id' => $result->student->curriculum_id]);
                        $studentData['RequiredCredit'] = $minimumCreditPointsRequired ?: 'N/A';

                        $studentData['RequiredAcademicPeriods'] = !is_null($result->student->curriculum_id)
                            ? $courseRegistrationsTable->PublishedCourses->Courses->find()
                                ->where(['curriculum_id' => $result->student->curriculum_id])
                                ->group(['year_level_id', 'semester'])
                                ->count()
                            : 'N/A';

                        $studentData['CurrentRegistredCredit'] = $studentsTable->calculateCumulativeStudentRegistredAddedCredit($result->student->id, 0, $semester, $academicYear, 0);
                        $studentData['CumulativeRegistredCredit'] = $studentsTable->calculateCumulativeStudentRegistredAddedCredit($result->student->id, 1, $semester, $academicYear, 0);
                        $studentData['studentTakenCreditsSemesters'] = $this->getStudentTotalAccumulatedCreditsAndSemesterCount($result->student->id, $academicYear, $semester);
                        $studentData['CumulativeGPA'] = $lastStudentStatus->cgpa ?? 'N/A';

                        if ($result->student->program_id == PROGRAM_UNDEGRADUATE) {
                            if ($result->student->program_type_id == PROGRAM_TYPE_REGULAR) {
                                $studentData['Sponsorship'] = ($countryId == COUNTRY_ID_OF_ETHIOPIA) ? SPONSORED_BY_FEDERAL_GOVERNMENT : SPONSORED_BY_OTHER;
                                $studentData['DormitoryServiceType'] = 'K';
                                $studentData['FoodServiceType'] = $lastRegistration->cafeteria_consumer == 1 ? 'K' : ($lastRegistration->cafeteria_consumer == 0 ? 'C' : 'K');

                                if ($studentData['CostSharingLoan'] == 'Y' && $section->college_id != 3 && $result->student->college_id != 3 && !empty($currentRegistration->semester)) {
                                    $studentData['CurrentCostSharing'] = $currentRegistration->semester == 'I'
                                        ? round(NON_HEALTH_STREAM_COSTSHARING_PAIMENT_YEARLY_FROM_2012_EC / 2, 2)
                                        : NON_HEALTH_STREAM_COSTSHARING_PAIMENT_YEARLY_FROM_2012_EC;

                                    $studentData['AccumulatedCostSharing'] = $studentData['YearLevel'] == 1
                                        ? $studentData['CurrentCostSharing']
                                        : (($studentData['YearLevel'] - 1) * NON_HEALTH_STREAM_COSTSHARING_PAIMENT_YEARLY_FROM_2012_EC + $studentData['CurrentCostSharing']);
                                } else {
                                    $studentData['CurrentCostSharing'] = '';
                                    $studentData['AccumulatedCostSharing'] = '';
                                }
                            } elseif (in_array($result->student->program_type_id, [PROGRAM_TYPE_ADVANCE_STANDING, PROGRAM_TYPE_PART_TIME, PROGRAM_TYPE_SUMMER])) {
                                $studentData['Sponsorship'] = SPONSORED_BY_EMPLOYER;
                            } else {
                                $studentData['Sponsorship'] = SPONSORED_BY_SELF_PRIVATE;
                            }
                        } elseif ($result->student->program_id == PROGRAM_POST_GRADUATE) {
                            if ($result->student->program_type_id == PROGRAM_TYPE_REGULAR) {
                                $studentData['Sponsorship'] = ($countryId == COUNTRY_ID_OF_ETHIOPIA) ? SPONSORED_BY_REGIONAL_GOVERNMENT : SPONSORED_BY_OTHER;
                            } elseif (in_array($result->student->program_type_id, [PROGRAM_TYPE_PART_TIME, PROGRAM_TYPE_SUMMER])) {
                                $studentData['Sponsorship'] = SPONSORED_BY_EMPLOYER;
                            } else {
                                $studentData['Sponsorship'] = SPONSORED_BY_SELF_PRIVATE;
                            }
                            if (in_array($result->student->program_type_id, [PROGRAM_TYPE_REGULAR, PROGRAM_TYPE_SUMMER])) {
                                $studentData['DormitoryServiceType'] = 'K';
                            }
                        } elseif (in_array($result->student->program_id, [PROGRAM_PhD, PROGRAM_PGDT])) {
                            if ($result->student->program_type_id == PROGRAM_TYPE_REGULAR) {
                                $studentData['Sponsorship'] = ($countryId == COUNTRY_ID_OF_ETHIOPIA) ? SPONSORED_BY_EMPLOYER : SPONSORED_BY_OTHER;
                            }
                            $studentData['DormitoryServiceType'] = 'K';
                        }

                        $mergedData = array_merge(
                            $result->student->toArray(),
                            ['stexam' => $lastStudentStatus ? $lastStudentStatus->toArray() : [], 'academic_year' => $academicYear, 'semester' => $semester],
                            $institutionCodes->toArray(),
                            $studentData
                        );

                        $studentResultsHemis[$academicYear . '~' . $semester . '~' . $department->college->shortname][$count] = $mergedData;
                        $count++;
                    }
                }
            }
        }

        return $studentResultsHemis;
    }

    /**
     * Gets registered student list for add/drop
     */
    public function getRegisteredStudentListForAddDrop(
        ?string $academicYear = null,
        ?string $semester = null,
        $programId = null,
        $programTypeId = null,
        ?string $departmentId = null,
        ?string $yearLevelId = null,
        int $freshman = 0,
        string $studentnumber = ''
    ): array
    {
        if (empty($academicYear) || empty($semester)) {
            return [];
        }

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $programsTable =  TableRegistry::getTableLocator()->get('Programs');
        $programTypesTable =  TableRegistry::getTableLocator()->get('ProgramTypes');
        $curriculumsTable =  TableRegistry::getTableLocator()->get('Curriculums');
        $academicCalendarsTable =  TableRegistry::getTableLocator()->get('AcademicCalendars');

        $studentsTable=  TableRegistry::getTableLocator()->get('Students');

        $conditions = ['Students.graduated' => 0];
        $sectionConditions = ['Sections.id IS NOT NULL', 'Sections.archive' => 0];

        if (!empty($programId)) {
            $programIds = is_array($programId) ? $programId : [$programId];
            $conditions['Students.program_id IN'] = $programIds;
            $sectionConditions['Sections.program_id IN'] = $programIds;
        }

        if (!empty($programTypeId)) {
            $programTypeIds = is_array($programTypeId) ? $programTypeId : [$programTypeId];
            $conditions['Students.program_type_id IN'] = $programTypeIds;
            $sectionConditions['Sections.program_type_id IN'] = $programTypeIds;
        }

        if (!empty($studentnumber)) {
            $conditions['Students.studentnumber LIKE'] = trim($studentnumber);
        }

        if (!empty($academicYear)) {
            $conditions['CourseRegistrations.academic_year LIKE'] = $academicYear;
            $sectionConditions['Sections.academicyear LIKE'] = $academicYear;
        }

        if (!empty($semester)) {
            $conditions['CourseRegistrations.semester'] = $semester;
        }

        $deptConditions = ['Departments.active' => 1];
        if (!empty($departmentId) && !$freshman) {
            $deptConditions['Departments.id'] = $departmentId;
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $collegeIds = $freshman && !empty($departmentId)
            ? $collegesTable->find()->where(['id' => $departmentId, 'active' => 1])->select(['id'])->toArray()
            : [];

        $studentListRegistered = [];
        $count = 0;

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $yearLevels = !empty($yearLevelId) ? array_filter($department->year_levels, fn($yl) => strcasecmp($yl->name, $yearLevelId) === 0) : $department->year_levels;

                foreach ($yearLevels as $yearLevel) {
                    $ylId = $yearLevel->id;
                    $sectionConditions['Sections.year_level_id'] = $ylId;
                    $sectionConditions['Sections.department_id'] = $department->id;

                    $studentSections = $studentsSectionsTable->find()
                        ->where([
                            'StudentsSections.archive' => 0,
                            'StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)
                        ])
                        ->select(['student_id', 'section_id'])
                        ->group(['student_id', 'section_id'])
                        ->order(['id' => 'DESC', 'modified' => 'DESC', 'section_id' => 'DESC'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $registeredStudents = $courseRegistrationsTable->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.program_id',
                            'Students.program_type_id',
                            'Students.semester',
                            'Students.academic_year',
                            'Students.section_id',
                            'Students.academicyear',
                            'Students.graduated',
                            'Students.admissionyear',
                            'Students.curriculum_id',
                            'Students.department_id',
                            'Students.college_id'
                        ])
                        ->distinct(['CourseRegistrations.student_id'])
                        ->innerJoinWith('Students')
                        ->where($conditions)
                        ->where(['CourseRegistrations.student_id IN' => $studentIds, 'CourseRegistrations.year_level_id' => $ylId])
                        ->group(['CourseRegistrations.semester', 'CourseRegistrations.academic_year', 'CourseRegistrations.section_id', 'CourseRegistrations.student_id'])
                        ->order(['CourseRegistrations.section_id', 'Students.first_name'])
                        ->toArray();

                    foreach ($registeredStudents as $student) {
                        $checkRegistered = $courseRegistrationsTable->find()
                            ->where([
                                'student_id' => $student->student->id,
                                'semester' => $semester,
                                'academic_year' => $academicYear,
                                'OR' => [
                                    'year_level_id IS NOT NULL',
                                    'year_level_id !=' => '',
                                    'year_level_id !=' => 0
                                ]
                            ])
                            ->count();

                        if ($checkRegistered) {
                            $section = $sectionsTable->find()
                                ->where(['id' => $student->section_id, 'department_id IS NOT NULL'])
                                ->contain(['YearLevels' => ['id', 'name']])
                                ->first();

                            $lastStudentStatus = $this->find()
                                ->where(['student_id' => $student->student->id])
                                ->contain(['AcademicStatuses' => ['id', 'name', 'computable']])
                                ->order(['academic_year' => 'DESC', 'semester' => 'DESC'])
                                ->first();

                            $studentData = [
                                'Student' => array_merge($student->student->toArray(), [
                                    'full_name' => "{$student->student->first_name} {$student->student->middle_name} {$student->student->last_name}"
                                ]),
                                'Department' => ['name' => $departmentsTable->field('name', ['id' => $student->student->department_id])],
                                'Curriculum' => !empty($student->student->curriculum_id) ? $curriculumsTable->find()
                                    ->where(['id' => $student->student->curriculum_id])
                                    ->select(['id', 'name', 'type_credit', 'year_introduced', 'active'])
                                    ->first()
                                    ->toArray() : [],
                                'Program' => ['name' => $programsTable->field('name', ['id' => $student->student->program_id])],
                                'ProgramType' => ['name' => $programTypesTable->field('name', ['id' => $student->student->program_type_id])],
                                'Registration' => $student->toArray(),
                                'LastStatus' => $lastStudentStatus ? $lastStudentStatus->toArray() : [],
                                'Section' => $section->toArray(),
                                'YearLevel' => $section->year_level->toArray(),
                                'Load' => $studentsTable->calculateStudentLoad($student->student->id, $semester, $academicYear),
                                'MaxLoadAllowed' => $academicCalendarsTable->maximumCreditPerSemester($student->student->id)
                            ];

                            $studentListRegistered[$count] = $studentData;
                            $count++;
                        }
                    }
                }
            }
        } else {
            if (!empty($collegeIds)) {
                $colleges = $collegesTable->find()->where(['id IN' => $collegeIds])->toArray();
                foreach ($colleges as $college) {
                    $sectionConditions['Sections.college_id'] = $college->id;
                    $sectionConditions['OR'] = [
                        'Sections.department_id IS NULL',
                        'Sections.year_level_id IS NULL',
                        'Sections.year_level_id' => ['', 0]
                    ];

                    $studentSections = $studentsSectionsTable->find()
                        ->where([
                            'StudentsSections.archive' => 0,
                            'StudentsSections.section_id IN' => $sectionsTable->find()->select(['id'])->where($sectionConditions)
                        ])
                        ->select(['student_id', 'section_id'])
                        ->group(['student_id', 'section_id'])
                        ->order(['id' => 'DESC', 'modified' => 'DESC', 'section_id' => 'DESC'])
                        ->toArray();

                    if (empty($studentSections)) {
                        continue;
                    }

                    $studentIds = array_column($studentSections, 'student_id');
                    $studentSectionMap = array_column($studentSections, 'section_id', 'student_id');

                    $registeredStudents = $courseRegistrationsTable->find()
                        ->select([
                            'Students.studentnumber',
                            'Students.id',
                            'Students.first_name',
                            'Students.middle_name',
                            'Students.last_name',
                            'Students.gender',
                            'Students.program_id',
                            'Students.program_type_id',
                            'CourseRegistrations.semester',
                            'CourseRegistrations.academic_year',
                            'CourseRegistrations.section_id',
                            'Students.academicyear',
                            'Students.graduated',
                            'Students.admissionyear',
                            'Students.curriculum_id',
                            'Students.department_id',
                            'Students.college_id'
                        ])
                        ->distinct(['CourseRegistrations.student_id'])
                        ->innerJoinWith('Students')
                        ->where($conditions)
                        ->where([
                            'CourseRegistrations.student_id IN' => $studentIds,
                            'OR' => [
                                'CourseRegistrations.year_level_id IS NULL',
                                'CourseRegistrations.year_level_id' => ['', 0]
                            ]
                        ])
                        ->group(['CourseRegistrations.semester', 'CourseRegistrations.academic_year', 'CourseRegistrations.section_id', 'CourseRegistrations.student_id'])
                        ->order(['CourseRegistrations.section_id', 'Students.first_name'])
                        ->toArray();

                    foreach ($registeredStudents as $student) {
                        $checkRegistered = $courseRegistrationsTable->find()
                            ->where([
                                'student_id' => $student->student->id,
                                'semester' => $semester,
                                'academic_year' => $academicYear,
                                'OR' => [
                                    'year_level_id IS NULL',
                                    'year_level_id' => ['', 0]
                                ]
                            ])
                            ->count();

                        if ($checkRegistered) {
                            $section = $sectionsTable->find()
                                ->where(['id' => $student->section_id, 'department_id IS NULL'])
                                ->contain(['YearLevels' => ['id', 'name']])
                                ->first();

                            $lastStudentStatus = $this->find()
                                ->where(['student_id' => $student->student->id])
                                ->contain(['AcademicStatuses' => ['id', 'name', 'computable']])
                                ->order(['academic_year' => 'DESC', 'semester' => 'DESC'])
                                ->first();

                            $studentData = [
                                'Student' => array_merge($student->student->toArray(), [
                                    'full_name' => "{$student->student->first_name} {$student->student->middle_name} {$student->student->last_name}"
                                ]),
                                'College' => ['name' => $collegesTable->field('name', ['id' => $student->student->college_id])],
                                'Curriculum' => [],
                                'Program' => ['name' => $programsTable->field('name', ['id' => $student->student->program_id])],
                                'ProgramType' => ['name' => $programTypesTable->field('name', ['id' => $student->student->program_type_id])],
                                'Registration' => $student->toArray(),
                                'LastStatus' => $lastStudentStatus ? $lastStudentStatus->toArray() : [],
                                'Section' => $section->toArray(),
                                'YearLevel' => $section->year_level->toArray(),
                                'Load' => $studentsTable->calculateStudentLoad($student->student->id, $semester, $academicYear),
                                'MaxLoadAllowed' => $academicCalendarsTable->maximumCreditPerSemester($student->student->id)
                            ];

                            $studentListRegistered[$count] = $studentData;
                            $count++;
                        }
                    }
                }
            }
        }

        return $studentListRegistered;
    }

    /**
     * Regenerates all academic statuses for a student by ID
     */
    public function regenerateAllStatusOfStudentByStudentId(?int $studentId = null, int $checkWithinTheWeek = 1)
    {
        if (empty($studentId) || !is_numeric($studentId) || $studentId <= 0) {
            return false;
        }

        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable =  TableRegistry::getTableLocator()->get('CourseAdds');
        $publishedCoursesTable =  TableRegistry::getTableLocator()->get('PublishedCourses');

        if ($checkWithinTheWeek) {
            $lastStatusRegeneratedDate = $this->find()
                ->select(['modified'])
                ->where(['student_id' => $studentId])
                ->first()->modified ?? null;
            if ($lastStatusRegeneratedDate && $lastStatusRegeneratedDate > date("Y-m-d 23:59:59", strtotime("-1 week"))) {
                return 3;
            }
        }

        $alreadyGeneratedStatus = $this->find()
            ->where(['student_id' => $studentId])
            ->select(['id'])
            ->toArray();

        $statusListForDelete = array_column($alreadyGeneratedStatus, 'id');

        $courseRegistered = $courseRegistrationsTable->find()
            ->where(['student_id' => $studentId])
            ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
            ->select(['published_course_id'])
            ->toArray();

        $courseAdded = $courseAddsTable->find()
            ->where(['student_id' => $studentId])
            ->order(['academic_year' => 'ASC', 'semester' => 'ASC', 'id' => 'ASC'])
            ->select(['published_course_id'])
            ->toArray();

        $listOfPuTaken = array_unique(array_merge(
            array_column($courseRegistered, 'published_course_id'),
            array_column($courseAdded, 'published_course_id')
        ));

        $listPublishedCourseTakenBySection = $publishedCoursesTable->find()
            ->where(['id IN' => $listOfPuTaken])
            ->order(['academic_year' => 'ASC', 'semester' => 'ASC'])
            ->toArray();

        if (!empty($statusListForDelete)) {
            $this->deleteAll(['id IN' => $statusListForDelete], false);
        }

        $statusGenerated = false;

        foreach ($listPublishedCourseTakenBySection as $course) {
            $checkIfStatusIsGenerated = $this->find()
                ->where([
                    'student_id' => $studentId,
                    'academic_year' => $course->academic_year,
                    'semester' => $course->semester
                ])
                ->count();

            if (!$checkIfStatusIsGenerated) {
                $statusGenerated = $this->updateAcademicStatusByPublishedCourseOfStudent($course->id, $studentId);
            }
        }

        return $statusGenerated;
    }

    /**
     * Gets eligible students for exit exam
     */
    public function getEligibleStudentsForExitExam(
        string $academicYear = '',
        string $semester = '',
        $programId = '',
        $programTypeId = '',
        ?string $departmentId = null,
        string $top = '',
        string $sex = 'all',
        ?string $yearLevelId = null,
        ?int $regionId = null,
        string $by = 'cgpa',
        int $freshman = 0,
        int $excludeGraduated = 1,
        int $getExtendedReportForExitExam = 0
    ): array
    {
        $programsToLookForExitExamTypes = Configure::read('programs_to_look_for_exit_exam_types');
        if (!empty($programsToLookForExitExamTypes)) {
            $programId = $programsToLookForExitExamTypes;
        }

        $top = $top ?: 10;

        $departmentsTable =  TableRegistry::getTableLocator()->get('Departments');
        $collegesTable =  TableRegistry::getTableLocator()->get('Colleges');
        $sectionsTable =  TableRegistry::getTableLocator()->get('Sections');
        $studentsSectionsTable =  TableRegistry::getTableLocator()->get('StudentsSections');
        $courseRegistrationsTable =  TableRegistry::getTableLocator()->get('CourseRegistrations');
        $studentStatusPatternsTable =  TableRegistry::getTableLocator()->get('StudentStatusPatterns');
        $yearLevelsTable= TableRegistry::getTableLocator()->get('Yearlevels');
        $studentsTable= TableRegistry::getTableLocator()->get('Students');

        $conditions = [];
        if ($excludeGraduated) {
            $conditions['Students.graduated'] = 0;
        }

        if (!empty($regionId)) {
            $conditions['Students.region_id'] = $regionId;
        }

        if ($sex !== 'all') {
            $conditions['Students.gender LIKE'] = $sex . '%';
        }

        if (!empty($programId)) {
            $conditions['Students.program_id'] = $programId;
        }

        if (!empty($programTypeId)) {
            $conditions['Students.program_type_id'] = $programTypeId;
        }

        $deptConditions = ['Departments.active' => 1];
        $collegeIds = [];
        if (!empty($departmentId)) {
            $collegeId = explode('~', $departmentId);
            if (count($collegeId) > 1) {
                $deptConditions['Departments.college_id'] = $collegeId[1];
                $collegeIds[$collegeId[1]] = $collegeId[1];
            } else {
                $deptConditions['Departments.id'] = $departmentId;
            }
        } else {
            $collegeIds = $collegesTable->find()->where(['active' => 1])->select(['id'])->toArray();
            $collegeIds = array_column($collegeIds, 'id');
        }

        $departments = $departmentsTable->find()
            ->where($deptConditions)
            ->contain(['Colleges', 'YearLevels'])
            ->toArray();

        $yearLevelNamesToLookForExitExam = ['4th', '5th', '6th', '7th'];
        $registeredStudentsList = [];

        if ($freshman == 0 && !empty($departments)) {
            foreach ($departments as $department) {
                $sectionConditions = [
                    'Section.department_id' => $department->id,
                    'Section.academicyear' => $academicYear
                ];

                if (!empty($programId)) {
                    $sectionConditions['Section.program_id'] = $programId;
                }

                if (!empty($programTypeId)) {
                    $sectionConditions['Section.program_type_id'] = $programTypeId;
                }

                $yearLevelIds = $yearLevelsTable->find()
                    ->where([
                        'department_id' => $department->id,
                        'name IN' => $yearLevelNamesToLookForExitExam
                    ])
                    ->select(['id'])
                    ->toArray();
                $yearLevelIds = array_column($yearLevelIds, 'id');

                if (empty($yearLevelIds)) {
                    continue;
                }

                $sectionConditions['Section.year_level_id IN'] = $yearLevelIds;

                $sectionIds = $sectionsTable->find()
                    ->where($sectionConditions)
                    ->select(['id'])
                    ->toArray();
                $sectionIds = array_column($sectionIds, 'id');

                if (!empty($sectionIds)) {
                    $studentIds = $studentsSectionsTable->find()
                        ->where(['section_id IN' => $sectionIds])
                        ->select(['student_id'])
                        ->toArray();
                    $studentIds = array_column($studentIds, 'student_id');

                    if (!empty($studentIds)) {
                        $registeredStudentIds = $courseRegistrationsTable->find()
                            ->where([
                                'year_level_id IN' => $yearLevelIds,
                                'section_id IN' => $sectionIds,
                                'student_id IN' => $studentIds,
                                'academic_year' => $academicYear,
                                'semester' => $semester
                            ])
                            ->group(['academic_year', 'semester', 'student_id'])
                            ->select(['student_id'])
                            ->toArray();
                        $registeredStudentIds = array_column($registeredStudentIds, 'student_id');

                        foreach ($registeredStudentIds as $stId) {
                            if ($studentStatusPatternsTable->isEligibleForExitExam($stId) && !in_array($stId, $registeredStudentsList)) {
                                $registeredStudentsList[] = $stId;
                            }
                        }
                    }
                }
            }
        }

        $students = [];
        if (!empty($registeredStudentsList)) {
            $findOptions = [
                'conditions' => array_merge($conditions, ['Students.id IN' => $registeredStudentsList]),
                'order' => [
                    'Students.college_id' => 'ASC',
                    'Students.department_id' => 'ASC',
                    'Students.academicyear' => 'DESC',
                    'Students.full_name' => 'ASC',
                    'Students.id' => 'ASC'
                ],
                'contain' => [
                    'Departments' => ['id', 'name'],
                    'Colleges' => ['id', 'name', 'shortname', 'stream'],
                    'Programs' => ['id', 'name'],
                    'ProgramTypes' => ['id', 'name'],
                    'Regions' => ['id', 'name'],
                    'Zones' => ['id', 'name'],
                    'Woredas' => ['id', 'name'],
                    'Cities' => ['id', 'name'],
                    'Curriculums' => [
                        'select' => [
                            'id',
                            'name',
                            'type_credit',
                            'english_degree_nomenclature',
                            'minimum_credit_points',
                            'specialization_english_degree_nomenclature',
                            'department_study_program_id'
                        ],
                        'DepartmentStudyPrograms' => [
                            'StudyPrograms' => ['id', 'study_program_name', 'code', 'local_band']
                        ]
                    ],
                    'StudentExamStatuses' => [
                        'order' => ['academic_year' => 'DESC', 'semester' => 'DESC', 'id' => 'DESC'],
                        'AcademicStatuses' => ['id', 'name'],
                        'limit' => 1
                    ]
                ]
            ];

            $students = $studentsTable->find('all', $findOptions)->toArray();
        }

        $formattedStudentList = [];

        foreach ($students as $student) {
            if ($getExtendedReportForExitExam) {
                $student->taken = $this->getStudentTakenCreditsForExitExam($student->id);
            }

            if (!empty($academicYear) && !empty($semester)) {
                $student->yearLevel = $sectionsTable->getStudentYearLevel($student->id)['year'];
            }

            if (!empty($student->phone_mobile)) {
                $student->phone_mobile = $this->__formatEthiopianPhoneNumber($student->phone_mobile);
            }

            $programName = $student->program->name;
            $programTypeName = $student->program_type->name;

            $formattedStudentList[$programName][$programTypeName][] = $student;
        }

        return $formattedStudentList;
    }

    /**
     * Formats Ethiopian phone number
     */
    private function __formatEthiopianPhoneNumber(string $number): string
    {
        $originalNumber = $number;
        $number = preg_replace('/\D/', '', $number);

        if (preg_match('/^251(9|7)\d{8}$/', $number)) {
            return '+251' . substr($number, 3);
        }

        if (preg_match('/^0(9|7)\d{8}$/', $number)) {
            return '+251' . substr($number, 1);
        }

        if (preg_match('/^(9|7)\d{8}$/', $number)) {
            return '+251' . $number;
        }

        return "Invalid mobile phone number ($originalNumber)";
    }

}
