<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class StudentStatusPatternsTable extends Table
{
    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('student_status_patterns');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'INNER',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->integer('program_id')
            ->requirePresence('program_id', 'create')
            ->notEmptyString('program_id', 'Program ID is required');

        $validator
            ->integer('program_type_id')
            ->requirePresence('program_type_id', 'create')
            ->notEmptyString('program_type_id', 'Program Type ID is required');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param \Cake\ORM\RulesChecker $rules The rules object to be modified.
     * @return \Cake\ORM\RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));

        return $rules;
    }

    use Cake\ORM\TableRegistry;

    public function getProgramTypePattern($programId = null, $programTypeId = null, $academicYear = null)
    {
        $patternsTable = TableRegistry::getTableLocator()->get('StudentStatusPatterns');

        $statusPatterns = $patternsTable->find()
            ->select(['program_id', 'program_type_id', 'pattern', 'acadamic_year', 'application_date'])
            ->where([
                'StudentStatusPatterns.program_id' => $programId,
                'StudentStatusPatterns.program_type_id' => $programTypeId
            ])
            ->orderAsc('StudentStatusPatterns.application_date')
            ->enableHydration(false)
            ->toArray();

        if (empty($statusPatterns)) {
            return 1;
        }

        $firstPattern = $statusPatterns[0]['pattern'];
        $sysAcademicYear = $statusPatterns[0]['acadamic_year'];

        if (substr($sysAcademicYear, 0, 4) > substr($academicYear, 0, 4)) {
            return 1;
        }

        $pattern = $firstPattern;
        do {
            foreach ($statusPatterns as $statusPattern) {
                if ($statusPattern['acadamic_year'] === $sysAcademicYear) {
                    $pattern = $statusPattern['pattern'];
                }
            }

            if (strcasecmp($academicYear, $sysAcademicYear) !== 0) {
                $startYear = (int)substr($sysAcademicYear, 0, 4) + 1;
                $endYear = substr((string)($startYear + 1), 2, 2);
                $sysAcademicYear = $startYear . '/' . $endYear;
            } else {
                return $pattern;
            }
        } while ($sysAcademicYear !== '3000/01');

        return $pattern;
    }


    public function isLastSemesterInCurriculum($studentId): bool
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $addsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $regsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');

        $student = $studentsTable->find()
            ->contain(['Curriculums'])
            ->where(['Students.id' => $studentId])
            ->enableHydration(false)
            ->first();

        if (empty($student) || empty($student['Students']['curriculum_id'])) {
            return false;
        }

        $curriculumId = $student['Students']['curriculum_id'];

        $lastYearLevelRow = $coursesTable->find()
            ->select(['year_level_id'])
            ->where(['Courses.curriculum_id' => $curriculumId])
            ->group(['Courses.year_level_id', 'Courses.semester'])
            ->order(['Courses.year_level_id' => 'DESC', 'Courses.semester' => 'DESC'])
            ->enableHydration(false)
            ->first();

        if (empty($lastYearLevelRow)) {
            return false;
        }

        $lastYearLevelId = $lastYearLevelRow['year_level_id'];

        $hasLastLevelCourses = $regsTable->find()
            ->where([
                'CourseRegistrations.student_id' => $studentId,
                'CourseRegistrations.year_level_id' => $lastYearLevelId
            ])
            ->count();

        if (!$hasLastLevelCourses) {
            return false;
        }

        $addedCourses = $addsTable->find()
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['CourseCategories', 'Curriculums']
                ]
            ])
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->enableHydration(false)
            ->toArray();

        $registeredCourses = $regsTable->find()
            ->contain([
                'PublishedCourses' => [
                    'Courses' => ['CourseCategories', 'Curriculums']
                ]
            ])
            ->where([
                'CourseRegistrations.student_id' => $studentId
            ])
            ->enableHydration(false)
            ->toArray();

        $lastCreditSum = 0;

        foreach ($registeredCourses as $reg) {
            $credit = $reg['PublishedCourses']['Courses']['credit'] ?? 0;
            $lastCreditSum += (float)$credit;
        }

        foreach ($addedCourses as $add) {
            $credit = $add['PublishedCourses']['Courses']['credit'] ?? 0;
            $lastCreditSum += (float)$credit;
        }

        $requiredCredits = (float) $student['Curriculums']['minimum_credit_points'];
        return $lastCreditSum >= $requiredCredits;
    }


    public function isEligibleForExitExam($studentId)
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $courseRegsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');
        $courseExemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');

        $student = $studentsTable->find()
            ->contain(['Curriculums'])
            ->where(['Students.id' => $studentId])
            ->first();

        if (empty($student) || empty($student->curriculum_id)) {
            return false;
        }

        $curriculumId = $student->curriculum_id;

        $lastYearLevel = $coursesTable->find()
            ->select(['year_level_id'])
            ->where(['curriculum_id' => $curriculumId])
            ->order(['year_level_id' => 'DESC', 'semester' => 'DESC'])
            ->group(['year_level_id', 'semester'])
            ->limit(1)
            ->first();

        $semesterCount = $coursesTable->find()
            ->where(['curriculum_id' => $curriculumId])
            ->group(['year_level_id', 'semester'])
            ->count();

        $lastYearLevelId = $lastYearLevel ? $lastYearLevel->year_level_id : null;

        if (!$lastYearLevelId) {
            return false;
        }

        $registeredLastYear = $courseRegsTable->find()
            ->where([
                'student_id' => $studentId,
                'year_level_id' => $lastYearLevelId
            ])
            ->count();

        if (!$registeredLastYear) {
            return false;
        }

        $registeredCredits = $courseRegsTable->find()
            ->select(['total' => 'SUM(Course.credit)'])
            ->join([
                'Courses' => [
                    'table' => 'courses',
                    'type' => 'INNER',
                    'conditions' => ['Courses.id = PublishedCourses.course_id']
                ],
                'PublishedCourses' => [
                    'table' => 'published_courses',
                    'type' => 'INNER',
                    'conditions' => ['PublishedCourses.id = CourseRegistrations.published_course_id']
                ]
            ])
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->enableHydration(false)
            ->first()['total'] ?? 0;

        $addedCredits = $courseAddsTable->find()
            ->select(['total' => 'SUM(Course.credit)'])
            ->join([
                'Courses' => [
                    'table' => 'courses',
                    'type' => 'INNER',
                    'conditions' => ['Courses.id = PublishedCourses.course_id']
                ],
                'PublishedCourses' => [
                    'table' => 'published_courses',
                    'type' => 'INNER',
                    'conditions' => ['PublishedCourses.id = CourseAdds.published_course_id']
                ]
            ])
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->enableHydration(false)
            ->first()['total'] ?? 0;

        $droppedCredits = $courseDropsTable->find()
            ->select(['total' => 'SUM(Course.credit)'])
            ->join([
                'CourseRegistrations' => [
                    'table' => 'course_registrations',
                    'type' => 'INNER',
                    'conditions' => ['CourseRegistrations.id = CourseDrops.course_registration_id']
                ],
                'PublishedCourses' => [
                    'table' => 'published_courses',
                    'type' => 'INNER',
                    'conditions' => ['PublishedCourses.id = CourseRegistrations.published_course_id']
                ],
                'Courses' => [
                    'table' => 'courses',
                    'type' => 'INNER',
                    'conditions' => ['Courses.id = PublishedCourses.course_id']
                ]
            ])
            ->where([
                'CourseDrops.student_id' => $studentId,
                'CourseDrops.registrar_confirmation' => 1
            ])
            ->enableHydration(false)
            ->first()['total'] ?? 0;

        $exemptedCredits = $courseExemptionsTable->find()
            ->select(['total' => 'SUM(Course.credit)'])
            ->join([
                'Courses' => [
                    'table' => 'courses',
                    'alias' => 'Course',
                    'type' => 'INNER',
                    'conditions' => ['Course.id = CourseExemptions.course_id']
                ]
            ])
            ->where([
                'CourseExemptions.student_id' => $studentId,
                'CourseExemptions.department_accept_reject' => 1,
                'CourseExemptions.registrar_confirm_deny' => 1
            ])
            ->enableHydration(false)
            ->first()['total'] ?? 0;

        $totalCredits = (int) (($registeredCredits + $addedCredits + $exemptedCredits) - $droppedCredits);

        $minRequired = (int) $student->curriculum->minimum_credit_points;
        $expectedPerSemester = $minRequired / $semesterCount;
        $threshold = (int) ceil(($semesterCount - 2) * $expectedPerSemester);

        return $totalCredits >= $threshold;
    }

    public function completedFillingProfileInfomation($studentId): bool
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        $studentDetails = $studentsTable->find()
            ->contain(['Contacts', 'HigherEducationBackgrounds', 'EheeceResults'])
            ->where(['Students.id' => $studentId])
            ->enableHydration(false)
            ->first();

        if (!$studentDetails) {
            return false;
        }

        $student = $studentDetails['Students'];
        $contact = $studentDetails['Contacts'] ?? null;
        $eheece = $studentDetails['EheeceResults'] ?? null;
        $higherEd = $studentDetails['HigherEducationBackgrounds'] ?? null;

        if (
            in_array($student['program_id'], [PROGRAM_REMEDIAL, PROGRAM_PGDT])
        ) {
            return true;
        }

        if (empty($contact)) {
            return false;
        }

        if ($student['program_id'] === PROGRAM_UNDEGRADUATE && empty($eheece)) {
            return false;
        }

        if (
            $student['program_id'] !== PROGRAM_UNDEGRADUATE &&
            in_array($student['program_id'], [PROGRAM_POST_GRADUATE, PROGRAM_PhD]) &&
            empty($higherEd)
        ) {
            return false;
        }

        if (empty($student['phone_mobile']) || empty($student['email'])) {
            return false;
        }

        return true;
    }

    public function isGraduatingClassStudent($studentId): bool
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $registrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $addsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $dropsTable = TableRegistry::getTableLocator()->get('CourseDrops');
        $exemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');

        $student = $studentsTable->find()
            ->contain(['Curriculums'])
            ->where(['Students.id' => $studentId])
            ->enableHydration(false)
            ->first();

        if (empty($student) || empty($student['Students']['curriculum_id'])) {
            return false;
        }

        $curriculumId = $student['Students']['curriculum_id'];

        $lastYearLevel = $coursesTable->find()
            ->select(['year_level_id'])
            ->where(['Courses.curriculum_id' => $curriculumId])
            ->group(['Courses.year_level_id', 'Courses.semester'])
            ->order(['Courses.year_level_id' => 'DESC', 'Courses.semester' => 'DESC'])
            ->enableHydration(false)
            ->first();

        $semesterCount = $coursesTable->find()
            ->where(['Courses.curriculum_id' => $curriculumId])
            ->group(['Courses.year_level_id', 'Courses.semester'])
            ->count();

        if (empty($lastYearLevel)) {
            return false;
        }

        $lastYearLevelId = $lastYearLevel['year_level_id'];

        $hasLastLevelCourses = $registrationsTable->find()
            ->where([
                'CourseRegistrations.student_id' => $studentId,
                'CourseRegistrations.year_level_id' => $lastYearLevelId
            ])
            ->count();

        if (!$hasLastLevelCourses) {
            return false;
        }

        $totalRegisteredCredits = $registrationsTable->find()
            ->select(['total_credits' => 'SUM(Course.credit)'])
            ->join([
                'table' => 'published_courses',
                'alias' => 'PublishedCourse1',
                'type' => 'INNER',
                'conditions' => 'PublishedCourse1.id = CourseRegistrations.published_course_id'
            ])
            ->join([
                'table' => 'courses',
                'alias' => 'Course',
                'type' => 'INNER',
                'conditions' => 'Course.id = PublishedCourse1.course_id'
            ])
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->enableHydration(false)
            ->first()['total_credits'] ?? 0;

        $totalAddedCredits = $addsTable->find()
            ->select(['total_credits' => 'SUM(Course.credit)'])
            ->join([
                'table' => 'published_courses',
                'alias' => 'PublishedCourse1',
                'type' => 'INNER',
                'conditions' => 'PublishedCourse1.id = CourseAdds.published_course_id'
            ])
            ->join([
                'table' => 'courses',
                'alias' => 'Course',
                'type' => 'INNER',
                'conditions' => 'Course.id = PublishedCourse1.course_id'
            ])
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->enableHydration(false)
            ->first()['total_credits'] ?? 0;

        $totalDroppedCredits = $dropsTable->find()
            ->select(['total_credits' => 'SUM(Course.credit)'])
            ->join([
                'table' => 'course_registrations',
                'alias' => 'CourseRegistration1',
                'type' => 'INNER',
                'conditions' => 'CourseRegistration1.id = CourseDrops.course_registration_id'
            ])
            ->join([
                'table' => 'published_courses',
                'alias' => 'PublishedCourse1',
                'type' => 'INNER',
                'conditions' => 'PublishedCourse1.id = CourseRegistration1.published_course_id'
            ])
            ->join([
                'table' => 'courses',
                'alias' => 'Course',
                'type' => 'INNER',
                'conditions' => 'Course.id = PublishedCourse1.course_id'
            ])
            ->where([
                'CourseDrops.student_id' => $studentId,
                'CourseDrops.registrar_confirmation' => 1
            ])
            ->enableHydration(false)
            ->first()['total_credits'] ?? 0;

        $totalExemptedCredits = $exemptionsTable->find()
            ->select(['total_credits' => 'SUM(CourseAlias.credit)'])
            ->join([
                'table' => 'courses',
                'alias' => 'CourseAlias',
                'type' => 'INNER',
                'conditions' => 'CourseAlias.id = CourseExemptions.course_id'
            ])
            ->where([
                'CourseExemptions.student_id' => $studentId,
                'CourseExemptions.department_accept_reject' => 1,
                'CourseExemptions.registrar_confirm_deny' => 1
            ])
            ->enableHydration(false)
            ->first()['total_credits'] ?? 0;

        $totalCredits = (int) (($totalRegisteredCredits + $totalAddedCredits + $totalExemptedCredits) - $totalDroppedCredits);

        $minimumRequiredCredits = (int) $student['Curriculums']['minimum_credit_points'];
        $creditsPerSemester = $minimumRequiredCredits / $semesterCount;
        $creditThreshold = (int) ceil(($semesterCount - 1) * $creditsPerSemester);

        return $totalCredits >= $creditThreshold;
    }



}
