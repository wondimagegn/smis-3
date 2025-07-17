<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

class StudentStatusPatternsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('student_status_patterns');
        $this->setPrimaryKey('id');

        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'LEFT'
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'LEFT'
        ]);
    }

    public function getProgramTypePattern($programId = null, $programTypeId = null, $academicYear = null)
    {
        if (!$programId || !$programTypeId || !$academicYear) {
            return 1;
        }

        $statusPatterns = $this->find()
            ->select(['pattern', 'acadamic_year'])
            ->where([
                'program_id' => $programId,
                'program_type_id' => $programTypeId
            ])
            ->order(['application_date' => 'ASC'])
            ->toArray();

        if (empty($statusPatterns)) {
            return 1;
        }

        $pattern = $statusPatterns[0]->pattern;
        $sysAcademicYear = $statusPatterns[0]->acadamic_year;

        // Check if pattern was introduced after the given academic year
        if (substr($sysAcademicYear, 0, 4) > substr($academicYear, 0, 4)) {
            return 1;
        }

        while ($sysAcademicYear !== '3000/01') {
            foreach ($statusPatterns as $statusPattern) {
                if ($sysAcademicYear === $statusPattern->acadamic_year) {
                    $pattern = $statusPattern->pattern;
                }
            }

            if ($academicYear === $sysAcademicYear) {
                return $pattern;
            }

            $year = (int)substr($sysAcademicYear, 0, 4) + 1;
            $sysAcademicYear = sprintf('%d/%02d', $year, ($year + 1) % 100);
        }

        return $pattern;
    }

    public function isLastSemesterInCurriculum($studentId)
    {
        if (!$studentId) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');

        $student = $studentsTable->find()
            ->select(['id', 'curriculum_id'])
            ->where(['Students.id' => $studentId])
            ->contain(['Curriculums' => ['fields' => ['id', 'minimum_credit_points']]])
            ->first();

        if (!$student || !$student->curriculum_id) {
            return false;
        }

        $lastYearLevelId = $coursesTable->find()
            ->select(['year_level_id'])
            ->where(['curriculum_id' => $student->curriculum_id])
            ->group(['year_level_id', 'semester'])
            ->order(['year_level_id' => 'DESC', 'semester' => 'DESC'])
            ->limit(1)
            ->extract('year_level_id')
            ->first();

        if (!$lastYearLevelId) {
            return false;
        }

        $hasRegisteredLastYear = $courseRegistrationsTable->find()
            ->where([
                'student_id' => $studentId,
                'year_level_id' => $lastYearLevelId
            ])
            ->count();

        if (!$hasRegisteredLastYear) {
            return false;
        }

        $totalCredits = 0;

        $registeredCredits = $courseRegistrationsTable->find()
            ->select(['credit' => 'Courses.credit'])
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->contain(['PublishedCourses.Courses' => ['fields' => ['credit']]])
            ->sumOf('Courses.credit') ?? 0;

        $addedCredits = $courseAddsTable->find()
            ->select(['credit' => 'Courses.credit'])
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.department_approval' => 1,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->contain(['PublishedCourses.Courses' => ['fields' => ['credit']]])
            ->sumOf('Courses.credit') ?? 0;

        $totalCredits = $registeredCredits + $addedCredits;

        return $totalCredits >= $student->curriculum->minimum_credit_points;
    }

    public function isEligibleForExitExam($studentId)
    {
        if (!$studentId) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');
        $courseExemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');

        $student = $studentsTable->find()
            ->select(['id', 'curriculum_id'])
            ->where(['id' => $studentId])
            ->contain(['Curriculums' => ['fields' => ['id', 'minimum_credit_points']]])
            ->first();

        if (!$student || !$student->curriculum_id) {
            return false;
        }

        $lastYearLevelId = $coursesTable->find()
            ->select(['year_level_id'])
            ->where(['curriculum_id' => $student->curriculum_id])
            ->group(['year_level_id', 'semester'])
            ->order(['year_level_id' => 'DESC', 'semester' => 'DESC'])
            ->limit(1)
            ->extract('year_level_id')
            ->first();

        if (!$lastYearLevelId) {
            return false;
        }

        $semesterCount = $coursesTable->find()
            ->where(['curriculum_id' => $student->curriculum_id])
            ->group(['year_level_id', 'semester'])
            ->count();

        $hasRegisteredLastYear = $courseRegistrationsTable->find()
            ->where([
                'student_id' => $studentId,
                'year_level_id' => $lastYearLevelId
            ])
            ->count();

        if (!$hasRegisteredLastYear) {
            return false;
        }

        $totalRegisteredCredits = $courseRegistrationsTable->find()
            ->select(['total_credits' => 'SUM(Courses.credit)'])
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->contain(['PublishedCourses.Courses' => ['fields' => ['credit']]])
            ->first()->total_credits ?? 0;

        $totalAddedCredits = $courseAddsTable->find()
            ->select(['total_credits' => 'SUM(Courses.credit)'])
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->contain(['PublishedCourses.Courses' => ['fields' => ['credit']]])
            ->first()->total_credits ?? 0;

        $totalDroppedCredits = $courseDropsTable->find()
            ->select(['total_credits' => 'SUM(Courses.credit)'])
            ->where([
                'CourseDrops.student_id' => $studentId,
                'CourseDrops.registrar_confirmation' => 1
            ])
            ->contain([
                'CourseRegistrations.PublishedCourses.Courses' => ['fields' => ['credit']]
            ])
            ->first()->total_credits ?? 0;

        $totalExemptedCredits = $courseExemptionsTable->find()
            ->select(['total_credits' => 'SUM(Courses.credit)'])
            ->where([
                'CourseExemptions.student_id' => $studentId,
                'CourseExemptions.department_accept_reject' => 1,
                'CourseExemptions.registrar_confirm_deny' => 1
            ])
            ->contain(['Courses' => ['fields' => ['credit']]])
            ->first()->total_credits ?? 0;

        $totalCredits = (int)(($totalRegisteredCredits + $totalAddedCredits + $totalExemptedCredits) - $totalDroppedCredits);

        $minimumRequiredCredits = (int)$student->curriculum->minimum_credit_points;
        $creditsPerSemester = $minimumRequiredCredits / $semesterCount;
        $creditThreshold = (int)ceil(($semesterCount - 2) * $creditsPerSemester);

        return $totalCredits >= $creditThreshold;
    }

    public function isGraduatingClassStudent($studentId)
    {
        if (!$studentId) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $coursesTable = TableRegistry::getTableLocator()->get('Courses');
        $courseRegistrationsTable = TableRegistry::getTableLocator()->get('CourseRegistrations');
        $courseAddsTable = TableRegistry::getTableLocator()->get('CourseAdds');
        $courseDropsTable = TableRegistry::getTableLocator()->get('CourseDrops');
        $courseExemptionsTable = TableRegistry::getTableLocator()->get('CourseExemptions');

        $student = $studentsTable->find()
            ->select(['id', 'curriculum_id'])
            ->where(['id' => $studentId])
            ->contain(['Curriculums' => ['fields' => ['id', 'minimum_credit_points']]])
            ->first();

        if (!$student || !$student->curriculum_id) {
            return false;
        }

        $lastYearLevelId = $coursesTable->find()
            ->select(['year_level_id'])
            ->where(['curriculum_id' => $student->curriculum_id])
            ->group(['year_level_id', 'semester'])
            ->order(['year_level_id' => 'DESC', 'semester' => 'DESC'])
            ->limit(1)
            ->extract('year_level_id')
            ->first();

        if (!$lastYearLevelId) {
            return false;
        }

        $semesterCount = $coursesTable->find()
            ->where(['curriculum_id' => $student->curriculum_id])
            ->group(['year_level_id', 'semester'])
            ->count();

        $hasRegisteredLastYear = $courseRegistrationsTable->find()
            ->where([
                'student_id' => $studentId,
                'year_level_id' => $lastYearLevelId
            ])
            ->count();

        if (!$hasRegisteredLastYear) {
            return false;
        }

        $totalRegisteredCredits = $courseRegistrationsTable->find()
            ->select(['total_credits' => 'SUM(Courses.credit)'])
            ->where(['CourseRegistrations.student_id' => $studentId])
            ->contain(['PublishedCourses.Courses' => ['fields' => ['credit']]])
            ->first()->total_credits ?? 0;

        $totalAddedCredits = $courseAddsTable->find()
            ->select(['total_credits' => 'SUM(Courses.credit)'])
            ->where([
                'CourseAdds.student_id' => $studentId,
                'CourseAdds.registrar_confirmation' => 1
            ])
            ->contain(['PublishedCourses.Courses' => ['fields' => ['credit']]])
            ->first()->total_credits ?? 0;

        $totalDroppedCredits = $courseDropsTable->find()
            ->select(['total_credits' => 'SUM(Courses.credit)'])
            ->where([
                'CourseDrops.student_id' => $studentId,
                'CourseDrops.registrar_confirmation' => 1
            ])
            ->contain([
                'CourseRegistrations.PublishedCourses.Courses' => ['fields' => ['credit']]
            ])
            ->first()->total_credits ?? 0;

        $totalExemptedCredits = $courseExemptionsTable->find()
            ->select(['total_credits' => 'SUM(Courses.credit)'])
            ->where([
                'CourseExemptions.student_id' => $studentId,
                'CourseExemptions.department_accept_reject' => 1,
                'CourseExemptions.registrar_confirm_deny' => 1
            ])
            ->contain(['Courses' => ['fields' => ['credit']]])
            ->first()->total_credits ?? 0;

        $totalCredits = (int)(($totalRegisteredCredits + $totalAddedCredits + $totalExemptedCredits) - $totalDroppedCredits);

        $minimumRequiredCredits = (int)$student->curriculum->minimum_credit_points;
        $creditsPerSemester = $minimumRequiredCredits / $semesterCount;
        $creditThreshold = (int)ceil(($semesterCount - 1) * $creditsPerSemester);

        return $totalCredits >= $creditThreshold;
    }

    public function completedFillingProfileInformation($studentId)
    {
        if (!$studentId) {
            return false;
        }

        $studentsTable = TableRegistry::getTableLocator()->get('Students');

        $studentDetails = $studentsTable->find()
            ->select(['id', 'program_id', 'phone_mobile', 'email'])
            ->where(['id' => $studentId])
            ->contain([
                'Contacts' => ['fields' => ['id']],
                'HigherEducationBackgrounds' => ['fields' => ['id']],
                'EheeceResults' => ['fields' => ['id']]
            ])
            ->first();

        if (!$studentDetails) {
            return false;
        }

        if (in_array($studentDetails->program_id, [PROGRAM_REMEDIAL, PROGRAM_PGDT])) {
            return true;
        }

        if (empty($studentDetails->contacts)) {
            return false;
        }

        if ($studentDetails->program_id == PROGRAM_UNDEGRADUATE && empty($studentDetails->eheece_results)) {
            return false;
        }

        if (
            in_array($studentDetails->program_id, [PROGRAM_POST_GRADUATE, PROGRAM_PhD]) &&
            empty($studentDetails->higher_education_backgrounds)
        ) {
            return false;
        }

        if (empty($studentDetails->phone_mobile) || empty($studentDetails->email)) {
            return false;
        }

        return true;
    }
}
