<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

/**
 * AcademicCalendars Table
 */
class AcademicCalendarsTable extends Table
{
    /**
     * Initialize method
     *
     * @param array $config The configuration for the Table.
     * @return void
     */
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('academic_calendars');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // BelongsTo Associations
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('YearLevels', [
            'foreignKey' => 'year_level_id',
            'joinType' => 'LEFT',
        ]);

        // HasMany Associations
        $this->hasMany('CourseRegistrations', [
            'foreignKey' => 'academic_calendar_id',
            'dependent' => false,
        ]);
        $this->hasMany('ExtendingAcademicCalendars', [
            'foreignKey' => 'academic_calendar_id',
            'dependent' => false,
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
            ->notEmptyString('academic_year', 'Please provide academic year.')
            ->notEmptyString('semester', 'Please provide semester.')
            ->dateTime('course_registration_end_date')
            ->greaterThanField('course_registration_end_date', 'course_registration_start_date', 'Course registration end date should be greater than start date.')
            ->dateTime('course_add_end_date')
            ->greaterThanField('course_add_end_date', 'course_add_start_date', 'Course add end date should be greater than start date.')
            ->greaterThanField('course_add_start_date', 'course_registration_end_date', 'Course add start date should be greater than course registration end date.')
            ->dateTime('course_drop_end_date')
            ->greaterThanField('course_drop_end_date', 'course_drop_start_date', 'Course drop end date should be greater than start date.')
            ->greaterThanField('course_drop_start_date', 'course_registration_end_date', 'Course drop start date should be greater than course registration end date.')
            ->dateTime('grade_submission_end_date')
            ->greaterThanField('grade_submission_end_date', 'grade_submission_start_date', 'Grade submission end date should be greater than start date.')
            ->greaterThanField('grade_submission_start_date', 'course_registration_end_date', 'Grade submission start date should be greater than course registration end date.');

        return $validator;
    }

    /**
     * Checks if registration is allowed
     *
     * @param string|null $academicYear Academic year
     * @param string|null $semester Semester
     * @param string|null $departmentCollegeId Department or college ID
     * @param string|null $yearLevelId Year level ID
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return mixed 1 if allowed, start date if not started, 2 if ended, false otherwise
     */
    public function checkRegistration(?string $academicYear = null, ?string $semester = null, ?string $departmentCollegeId = null, ?string $yearLevelId = null, ?int $programId = null, ?int $programTypeId = null)
    {
        if (!$academicYear || !$semester) {
            return false;
        }

        $yearLevelForSearch = $yearLevelId == '0' ? '1st' : $yearLevelId;
        $conditions = [
            'AcademicCalendars.academic_year' => $academicYear,
            'AcademicCalendars.semester' => $semester
        ];

        if ($programId && $programTypeId && $departmentCollegeId) {
            $conditions['AcademicCalendars.program_id'] = $programId;
            $conditions['AcademicCalendars.program_type_id'] = $programTypeId;
            $conditions['AcademicCalendars.department_id LIKE'] = '%' . serialize($departmentCollegeId) . '%';
            $conditions['AcademicCalendars.year_level_id LIKE'] = '%' . serialize($yearLevelForSearch) . '%';
        } elseif ($programId && $programTypeId) {
            $conditions['AcademicCalendars.program_id'] = $programId;
            $conditions['AcademicCalendars.program_type_id'] = $programTypeId;
        }

        $academicCalendars = $this->find()
            ->where($conditions)
            ->order(['AcademicCalendars.id' => 'DESC'])
            ->toArray();

        foreach ($academicCalendars as &$calendar) {
            $calendar->college_id = unserialize($calendar->college_id) ?: [];
            $calendar->department_id = unserialize($calendar->department_id) ?: [];
            $calendar->year_level_id = unserialize($calendar->year_level_id) ?: [];
        }

        $courseRegistrationStartDate = null;
        foreach ($academicCalendars as $calendar) {
            $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                $calendar->id,
                $yearLevelForSearch,
                $departmentCollegeId,
                $calendar->program_id,
                $calendar->program_type_id,
                'registration'
            );

            $courseRegistrationStartDate = $calendar->course_registration_start_date;
            $endDate = (new Time($calendar->course_registration_end_date))->modify("+$daysAdded days");

            if (
                in_array($departmentCollegeId, $calendar->department_id) &&
                in_array($yearLevelForSearch, $calendar->year_level_id) &&
                date('Y-m-d') >= $calendar->course_registration_start_date &&
                date('Y-m-d') <= $endDate->format('Y-m-d')
            ) {
                return 1;
            }
        }

        if ($courseRegistrationStartDate && date('Y-m-d') < $courseRegistrationStartDate) {
            return $courseRegistrationStartDate;
        } elseif ($courseRegistrationStartDate && date('Y-m-d') > $courseRegistrationStartDate) {
            return 2;
        }

        return false;
    }

    /**
     * Checks if course add period has ended
     *
     * @param string|null $academicYear Academic year
     * @param string|null $semester Semester
     * @param string|null $departmentCollegeId Department or college ID
     * @param string|null $yearLevelId Year level ID
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return mixed 1 if within period, start date if not started, 2 if ended, false otherwise
     */
    public function checkAddDateEnd(?string $academicYear = null, ?string $semester = null, ?string $departmentCollegeId = null, ?string $yearLevelId = null, ?int $programId = null, ?int $programTypeId = null)
    {
        if (!$academicYear || !$semester) {
            return false;
        }

        $yearLevelForSearch = $yearLevelId == '0' ? '1st' : $yearLevelId;
        $conditions = [
            'AcademicCalendars.academic_year' => $academicYear,
            'AcademicCalendars.semester' => $semester
        ];

        if ($programId && $programTypeId && $departmentCollegeId) {
            $conditions['AcademicCalendars.program_id'] = $programId;
            $conditions['AcademicCalendars.program_type_id'] = $programTypeId;
            $conditions['AcademicCalendars.department_id LIKE'] = '%' . serialize($departmentCollegeId) . '%';
            $conditions['AcademicCalendars.year_level_id LIKE'] = '%' . serialize($yearLevelForSearch) . '%';
        } elseif ($programId && $programTypeId) {
            $conditions['AcademicCalendars.program_id'] = $programId;
            $conditions['AcademicCalendars.program_type_id'] = $programTypeId;
        }

        $academicCalendars = $this->find()
            ->where($conditions)
            ->order(['AcademicCalendars.id' => 'DESC'])
            ->toArray();

        foreach ($academicCalendars as &$calendar) {
            $calendar->college_id = unserialize($calendar->college_id) ?: [];
            $calendar->department_id = unserialize($calendar->department_id) ?: [];
            $calendar->year_level_id = unserialize($calendar->year_level_id) ?: [];
        }

        $courseAddStartDate = null;
        foreach ($academicCalendars as $calendar) {
            $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                $calendar->id,
                $yearLevelForSearch,
                $departmentCollegeId,
                $calendar->program_id,
                $calendar->program_type_id,
                'add'
            );

            $courseAddStartDate = $calendar->course_add_start_date;
            $endDate = (new Time($calendar->course_add_end_date))->modify("+$daysAdded days");

            if (
                in_array($departmentCollegeId, $calendar->department_id) &&
                in_array($yearLevelForSearch, $calendar->year_level_id) &&
                date('Y-m-d') >= $calendar->course_add_start_date &&
                date('Y-m-d') <= $endDate->format('Y-m-d')
            ) {
                return 1;
            }
        }

        if ($courseAddStartDate && date('Y-m-d') < $courseAddStartDate) {
            return $courseAddStartDate;
        } elseif ($courseAddStartDate && date('Y-m-d') > $courseAddStartDate) {
            return 2;
        }

        return false;
    }

    /**
     * Checks if course add period has started
     *
     * @param string|null $academicYear Academic year
     * @param string|null $semester Semester
     * @param string|null $departmentCollegeId Department or college ID
     * @param string|null $yearLevelId Year level ID
     * @return mixed Academic calendar ID if within period, false otherwise
     */
    public function checkAddDateStart(?string $academicYear = null, ?string $semester = null, ?string $departmentCollegeId = null, ?string $yearLevelId = null)
    {
        if (!$academicYear || !$semester) {
            return false;
        }

        $academicCalendars = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $academicYear,
                'AcademicCalendars.semester' => $semester
            ])
            ->toArray();

        foreach ($academicCalendars as &$calendar) {
            $calendar->college_id = unserialize($calendar->college_id) ?: [];
            $calendar->department_id = unserialize($calendar->department_id) ?: [];
            $calendar->year_level_id = unserialize($calendar->year_level_id) ?: [];
        }

        foreach ($academicCalendars as $calendar) {
            if (
                in_array($departmentCollegeId, $calendar->department_id) &&
                in_array($yearLevelId, $calendar->year_level_id) &&
                $calendar->course_add_start_date >= date('Y-m-d')
            ) {
                return $calendar->id;
            }
        }

        return false;
    }

    /**
     * Checks if course add/drop period has ended
     *
     * @param string|null $academicYear Academic year
     * @param string|null $semester Semester
     * @param string|null $departmentCollegeId Department or college ID
     * @param string|null $yearLevelId Year level ID
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return mixed 1 if within period, start date if not started, false otherwise
     */
    public function checkAddDropEnd(?string $academicYear = null, ?string $semester = null, ?string $departmentCollegeId = null, ?string $yearLevelId = null, ?int $programId = null, ?int $programTypeId = null)
    {
        if (!$academicYear || !$semester) {
            return false;
        }

        $conditions = [
            'AcademicCalendars.academic_year' => $academicYear,
            'AcademicCalendars.semester' => $semester
        ];

        if ($programId && $programTypeId) {
            $conditions['AcademicCalendars.program_id'] = $programId;
            $conditions['AcademicCalendars.program_type_id'] = $programTypeId;
        }

        $academicCalendars = $this->find()
            ->where($conditions)
            ->toArray();

        foreach ($academicCalendars as &$calendar) {
            $calendar->college_id = unserialize($calendar->college_id) ?: [];
            $calendar->department_id = unserialize($calendar->department_id) ?: [];
            $calendar->year_level_id = unserialize($calendar->year_level_id) ?: [];
        }

        $courseDropStartDate = null;
        foreach ($academicCalendars as $calendar) {
            $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                $calendar->id,
                $yearLevelId,
                $departmentCollegeId,
                $calendar->program_id,
                $calendar->program_type_id,
                'drop'
            );

            $courseDropStartDate = $calendar->course_drop_start_date;
            $endDate = (new Time($calendar->course_drop_end_date))->modify("+$daysAdded days");

            if (
                (in_array($departmentCollegeId, $calendar->department_id) &&
                    in_array($yearLevelId, $calendar->year_level_id) &&
                    date('Y-m-d') >= $calendar->course_drop_start_date &&
                    date('Y-m-d') <= $endDate->format('Y-m-d')) ||
                ((empty($yearLevelId) || $yearLevelId == '0') &&
                    in_array('pre_' . $departmentCollegeId, $calendar->department_id) &&
                    in_array('1st', $calendar->year_level_id) &&
                    date('Y-m-d') >= $calendar->course_drop_start_date &&
                    date('Y-m-d') <= $endDate->format('Y-m-d'))
            ) {
                return 1;
            }
        }

        if ($courseDropStartDate && date('Y-m-d') < $courseDropStartDate) {
            return $courseDropStartDate;
        }

        return false;
    }

    /**
     * Checks registration, add, or drop period
     *
     * @param string|null $academicYear Academic year
     * @param string|null $semester Semester
     * @param string|null $departmentCollegeId Department or college ID
     * @param string|null $yearLevelId Year level ID
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @param string $type Activity type (registration, add, drop)
     * @return mixed 1 if within period, start date if not started, false otherwise
     */
    public function checkRegistrationAddDropStartEnd(?string $academicYear = null, ?string $semester = null, ?string $departmentCollegeId = null, ?string $yearLevelId = null, ?int $programId = null, ?int $programTypeId = null, string $type = '')
    {
        if (!$academicYear || !$semester || !$type) {
            return false;
        }

        $conditions = [
            'AcademicCalendars.academic_year' => $academicYear,
            'AcademicCalendars.semester' => $semester
        ];

        if ($programId && $programTypeId) {
            $conditions['AcademicCalendars.program_id'] = $programId;
            $conditions['AcademicCalendars.program_type_id'] = $programTypeId;
        }

        $academicCalendars = $this->find()
            ->where($conditions)
            ->toArray();

        foreach ($academicCalendars as &$calendar) {
            $calendar->college_id = unserialize($calendar->college_id) ?: [];
            $calendar->department_id = unserialize($calendar->department_id) ?: [];
            $calendar->year_level_id = unserialize($calendar->year_level_id) ?: [];
        }

        $activityStartDate = null;
        $activityEndDate = null;
        foreach ($academicCalendars as $calendar) {
            switch ($type) {
                case 'registration':
                    $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                        $calendar->id,
                        $yearLevelId,
                        $departmentCollegeId,
                        $calendar->program_id,
                        $calendar->program_type_id,
                        'registration'
                    );
                    $activityStartDate = $calendar->course_registration_start_date;
                    $activityEndDate = $calendar->course_registration_end_date;
                    break;
                case 'add':
                    $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                        $calendar->id,
                        $yearLevelId,
                        $departmentCollegeId,
                        $calendar->program_id,
                        $calendar->program_type_id,
                        'add'
                    );
                    $activityStartDate = $calendar->course_add_start_date;
                    $activityEndDate = $calendar->course_add_end_date;
                    break;
                case 'drop':
                    $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                        $calendar->id,
                        $yearLevelId,
                        $departmentCollegeId,
                        $calendar->program_id,
                        $calendar->program_type_id,
                        'drop'
                    );
                    $activityStartDate = $calendar->course_drop_start_date;
                    $activityEndDate = $calendar->course_drop_end_date;
                    break;
                default:
                    return false;
            }

            if ($activityStartDate && $activityEndDate) {
                $endDate = (new Time($activityEndDate))->modify("+$daysAdded days");

                if (
                    (in_array($departmentCollegeId, $calendar->department_id) &&
                        in_array($yearLevelId, $calendar->year_level_id) &&
                        date('Y-m-d') >= $activityStartDate &&
                        date('Y-m-d') <= $endDate->format('Y-m-d')) ||
                    ((empty($yearLevelId) || $yearLevelId == '0') &&
                        in_array('pre_' . $departmentCollegeId, $calendar->department_id) &&
                        in_array('1st', $calendar->year_level_id) &&
                        date('Y-m-d') >= $activityStartDate &&
                        date('Y-m-d') <= $endDate->format('Y-m-d'))
                ) {
                    return 1;
                }
            }
        }

        if ($activityStartDate && date('Y-m-d') < $activityStartDate) {
            return $activityStartDate;
        }

        return false;
    }

    /**
     * Checks if grade submission period has ended
     *
     * @param string|null $academicYear Academic year
     * @param string|null $semester Semester
     * @param string|null $departmentCollegeId Department or college ID
     * @param string|null $yearLevelId Year level ID
     * @return mixed Academic calendar ID if within period, false otherwise
     */
    public function checkGradeSubmissionEnd(?string $academicYear = null, ?string $semester = null, ?string $departmentCollegeId = null, ?string $yearLevelId = null)
    {
        if (!$academicYear || !$semester) {
            return false;
        }

        $academicCalendars = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $academicYear,
                'AcademicCalendars.semester' => $semester
            ])
            ->toArray();

        foreach ($academicCalendars as &$calendar) {
            $calendar->college_id = unserialize($calendar->college_id) ?: [];
            $calendar->department_id = unserialize($calendar->department_id) ?: [];
            $calendar->year_level_id = unserialize($calendar->year_level_id) ?: [];
        }

        foreach ($academicCalendars as $calendar) {
            $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                $calendar->id,
                $yearLevelId,
                $departmentCollegeId,
                $calendar->program_id,
                $calendar->program_type_id,
                'grade_submission'
            );

            $endDate = (new Time($calendar->grade_submission_end_date))->modify("+$daysAdded days");

            if (
                in_array($departmentCollegeId, $calendar->department_id) &&
                in_array($yearLevelId, $calendar->year_level_id) &&
                $endDate->format('Y-m-d') >= date('Y-m-d')
            ) {
                return $calendar->id;
            }
        }

        return false;
    }

    /**
     * Checks for duplicate academic calendar entries
     *
     * @param array|null $data Academic calendar data
     * @return bool True if no duplicates, false otherwise
     */
    public function checkDuplicateEntry(?array $data = null): bool
    {
        if (!$data || empty($data['AcademicCalendar'])) {
            return true;
        }

        $conditions = [
            'AcademicCalendars.academic_year' => $data['AcademicCalendar']['academic_year'],
            'AcademicCalendars.semester' => $data['AcademicCalendar']['semester'],
            'AcademicCalendars.program_id' => $data['AcademicCalendar']['program_id'],
            'AcademicCalendars.program_type_id' => $data['AcademicCalendar']['program_type_id'],
            'AcademicCalendars.year_level_id' => serialize($data['AcademicCalendar']['year_level_id'])
        ];

        if (!empty($data['AcademicCalendar']['id'])) {
            $conditions['AcademicCalendars.id !='] = $data['AcademicCalendar']['id'];
        }

        $academicCalendars = $this->find()
            ->where($conditions)
            ->toArray();

        $existedDept = [];
        foreach ($academicCalendars as $calendar) {
            $departmentIds = unserialize($calendar->department_id) ?: [];
            $yearLevelIds = unserialize($calendar->year_level_id) ?: [];

            foreach ($departmentIds as $depId) {
                foreach ($yearLevelIds as $yearId) {
                    if (in_array($depId, $departmentIds) && in_array($yearId, $yearLevelIds)) {
                        $existedDept[$yearId][] = $depId;
                    }
                }
            }
        }

        if ($existedDept) {
            $alreadyExistedYearLevel = [];
            $depts = [];
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');

            foreach ($existedDept as $year => $departments) {
                foreach ($departments as $value) {
                    if (!in_array($value, $depts)) {
                        $depts[] = $value;
                    }

                    if (is_numeric($value)) {
                        $deptName = $departmentsTable->field('name', ['Departments.id' => $value]);
                        $alreadyExistedYearLevel[] = "You have already setup an academic calendar for {$year} year {$deptName}";
                    } else {
                        $alreadyExistedYearLevel[] = "You have already setup an academic calendar for {$year} year level";
                    }
                }
            }

            if ($alreadyExistedYearLevel) {
                $this->validationErrors['duplicate'] = [$alreadyExistedYearLevel[0] . ' and ' . (count($depts) - 1) . ' others.'];
                $this->validationErrors['departmentduplicate'] = [$departments];
                $this->validationErrors['yearlevelduplicate'] = [$alreadyExistedYearLevel];
                return false;
            }
        }

        return true;
    }

    /**
     * Retrieves days available for grade change
     *
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return int Days available
     */
    public function daysAvaiableForGradeChange(?int $programId = null, ?int $programTypeId = null): int
    {
        if ($programId && $programTypeId) {
            $settingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');
            $settings = $settingsTable->find()
                ->where([
                    'GeneralSettings.program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'GeneralSettings.program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            return $settings && $settings->days_avaiable_for_grade_change
                ? (int)$settings->days_avaiable_for_grade_change
                : DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE;
        }

        return DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE;
    }

    /**
     * Retrieves days available for NG to F conversion
     *
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return int Days available
     */
    public function daysAvaiableForNgToF(?int $programId = null, ?int $programTypeId = null): int
    {
        if ($programId && $programTypeId) {
            $settingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');
            $settings = $settingsTable->find()
                ->where([
                    'GeneralSettings.program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'GeneralSettings.program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            return $settings && $settings->days_avaiable_for_ng_to_f
                ? (int)$settings->days_avaiable_for_ng_to_f
                : DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F;
        }

        return DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F;
    }

    /**
     * Retrieves days available for DO to F conversion
     *
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return int Days available
     */
    public function daysAvaiableForDoToF(?int $programId = null, ?int $programTypeId = null): int
    {
        if ($programId && $programTypeId) {
            $settingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');
            $settings = $settingsTable->find()
                ->where([
                    'GeneralSettings.program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'GeneralSettings.program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            return $settings && $settings->days_avaiable_for_do_to_f
                ? (int)$settings->days_avaiable_for_do_to_f
                : DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F;
        }

        return DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F;
    }

    /**
     * Retrieves days available for FX to F conversion
     *
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return int Days available
     */
    public function daysAvailableForFxToF(?int $programId = null, ?int $programTypeId = null): int
    {
        if ($programId && $programTypeId) {
            $settingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');
            $settings = $settingsTable->find()
                ->where([
                    'GeneralSettings.program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'GeneralSettings.program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            return $settings && $settings->days_available_for_fx_to_f
                ? (int)$settings->days_available_for_fx_to_f
                : DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F;
        }

        return DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F;
    }

    /**
     * Checks if current date is past FX grade submission deadline
     *
     * @param array $academicCalendar Academic calendar data
     * @param string $departmentId Department ID
     * @param array $publishedDetail Published course details
     * @return bool True if past deadline, false otherwise
     */
    public function isFxConversionDate(array $academicCalendar, string $departmentId, array $publishedDetail): bool
    {
        $calendar = $this->getAcademicCalenderDepartment($academicCalendar['academic_year'], $departmentId);
        $yearLevelsTable = TableRegistry::getTableLocator()->get('YearLevels');
        $yearLevelName = $yearLevelsTable->field('name', ['YearLevels.id' => $publishedDetail['year_level_id']]);

        foreach ($calendar as $entry) {
            if (!empty($entry['calendarDetail'])) {
                $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                    $entry['calendarDetail']['id'],
                    $yearLevelName,
                    $publishedDetail['year_level_id'],
                    $entry['calendarDetail']['program_id'],
                    $entry['calendarDetail']['program_type_id'],
                    'fx_grade_submission'
                );

                $endDate = (new Time($entry['calendarDetail']['grade_fx_submission_end_date']))->modify("+$daysAdded days");
                if (date('Y-m-d') > $endDate->format('Y-m-d')) {
                    return true;
                }
            }
        }

        return false;
    }

    /**
     * Retrieves default week count for one semester
     *
     * @return int Week count
     */
    public function weekCountForAcademicYearAndSemester(): int
    {
        return DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER;
    }

    /**
     * Retrieves week count for an academic year
     *
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return int Week count
     */
    public function weekCountForAcademicYear(?int $programId = null, ?int $programTypeId = null): int
    {
        if ($programId && $programTypeId) {
            $settingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');
            $settings = $settingsTable->find()
                ->where([
                    'GeneralSettings.program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'GeneralSettings.program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            return $settings && $settings->week_count_for_academic_year
                ? (int)$settings->week_count_for_academic_year
                : DEFAULT_WEEK_COUNT_FOR_ACADEMIC_YEAR;
        }

        return DEFAULT_WEEK_COUNT_FOR_ACADEMIC_YEAR;
    }

    /**
     * Retrieves week count for one semester
     *
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return int Week count
     */
    public function weekCountForOneSemester(?int $programId = null, ?int $programTypeId = null): int
    {
        if ($programId && $programTypeId) {
            $settingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');
            $settings = $settingsTable->find()
                ->where([
                    'GeneralSettings.program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'GeneralSettings.program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            return $settings && $settings->week_count_for_one_semester
                ? (int)$settings->week_count_for_one_semester
                : DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER;
        }

        return DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER;
    }

    /**
     * Retrieves semester count for an academic year
     *
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return int Semester count
     */
    public function semesterCountForAcademicYear(?int $programId = null, ?int $programTypeId = null): int
    {
        if ($programId && $programTypeId) {
            $settingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');
            $settings = $settingsTable->find()
                ->where([
                    'GeneralSettings.program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'GeneralSettings.program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            return $settings && $settings->semester_count_for_academic_year
                ? (int)$settings->semester_count_for_academic_year
                : DEFAULT_SEMESTER_COUNT_FOR_ACADEMIC_YEAR;
        }

        return DEFAULT_SEMESTER_COUNT_FOR_ACADEMIC_YEAR;
    }

    /**
     * Retrieves current semester for an academic year
     *
     * @param string $academicYear Academic year
     * @return string Semester
     */
    public function currentSemesterInTheDefinedAcademicCalender(string $academicYear): string
    {
        $calendars = $this->find()
            ->where(['AcademicCalendars.academic_year' => $academicYear])
            ->toArray();

        foreach ($calendars as $calendar) {
            $startDate = new Time($calendar->course_registration_start_date);
            $daysDiff = floor((time() - $startDate->getTimestamp()) / (60 * 60 * 24));
            if ($daysDiff < 130) {
                return $calendar->semester;
            }
        }

        return 'I';
    }

    /**
     * Retrieves start and end months for a semester
     *
     * @param string $semester Semester
     * @param string $academicYear Academic year
     * @return array Month array
     */
    public function semesterStartAndEndMonth(string $semester, string $academicYear): array
    {
        $months = array_fill_keys(['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul', 'Aug', 'Sep', 'Oct', 'Nov', 'Dec'], 0);

        $calendars = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $academicYear,
                'AcademicCalendars.semester' => $semester
            ])
            ->toArray();

        foreach ($calendars as $calendar) {
            $startMonth = (new Time($calendar->course_registration_start_date))->format('M');
            $endMonth = (new Time($calendar->grade_submission_end_date))->format('M');
            $months[$startMonth] = 0;
            $months[$endMonth] = 0;
        }

        return $months;
    }

    /**
     * Retrieves academic calendar by year
     *
     * @param string $currentAcademicYear Academic year
     * @return array Calendar data
     */
    public function getAcademicCalender(string $currentAcademicYear): array
    {
        $calendar = [];
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');

        $academicCalendars = $this->find()
            ->where(['AcademicCalendars.academic_year' => $currentAcademicYear])
            ->contain(['Programs', 'ProgramTypes'])
            ->toArray();

        foreach ($academicCalendars as $academicCalendar) {
            $departmentIds = unserialize($academicCalendar->department_id) ?: [];
            $yearLevelIds = unserialize($academicCalendar->year_level_id) ?: [];

            foreach ($departmentIds as $depId) {
                if (strpos($depId, 'pre_') !== false) {
                    $collegeName = $collegesTable->field('name', ['Colleges.id' => $depId]);
                    $calendar[$depId]['departmentname'] = "Pre({$collegeName})";
                    $calendar[$depId]['yearlevel'] = '1st';
                } else {
                    $deptName = $departmentsTable->field('name', ['Departments.id' => $depId]);
                    $calendar[$depId]['departmentname'] = $deptName;
                    $calendar[$depId]['yearlevel'] = $yearLevelIds;
                }
                $calendar[$depId]['calendarDetail'] = $academicCalendar->toArray();
            }
        }

        return $calendar;
    }

    /**
     * Retrieves academic calendar for a student
     *
     * @param string|null $deptCol Department or college ID
     * @param string $yearLevel Year level
     * @param string|null $academicYear Academic year
     * @param string $semester Semester
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return array Calendar data
     */
    public function getAcademicCalenderStudent(?string $deptCol = null, string $yearLevel = '1st', ?string $academicYear = null, string $semester = 'I', ?int $programId = null, ?int $programTypeId = null): array
    {
        $calendar = [];
        if (!$deptCol) {
            return $calendar;
        }

        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');

        $collCheck = explode('pre_', $deptCol);
        $collegeId = count($collCheck) > 1 ? $collCheck[1] : null;
        $yearLevel = $collegeId ? '1st' : $yearLevel;

        $conditions = [
            'AcademicCalendars.academic_year' => $academicYear,
            'AcademicCalendars.semester' => $semester,
            'AcademicCalendars.program_id' => $programId,
            'AcademicCalendars.program_type_id' => $programTypeId,
            'AcademicCalendars.department_id LIKE' => '%' . serialize($deptCol) . '%',
            'AcademicCalendars.year_level_id LIKE' => '%' . serialize($yearLevel) . '%'
        ];

        $currentAcademicCalendar = $this->find()
            ->where($conditions)
            ->contain(['Programs', 'ProgramTypes'])
            ->order(['AcademicCalendars.academic_year' => 'DESC', 'AcademicCalendars.semester' => 'DESC'])
            ->first();

        if ($currentAcademicCalendar) {
            $calendar[$deptCol]['departmentname'] = count($collCheck) > 1
                ? "Pre ({$collegesTable->field('name', ['Colleges.id' => $collCheck[1]])})"
                : $departmentsTable->field('name', ['Departments.id' => $deptCol]);
            $calendar[$deptCol]['academic_year'] = $academicYear;
            $calendar[$deptCol]['semester'] = $semester;
            $calendar[$deptCol]['yearlevel'] = $yearLevel;
            $calendar[$deptCol]['calendarDetail'] = $currentAcademicCalendar->toArray();

            return $calendar;
        }

        $currentAcademicCalendars = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $academicYear,
                'AcademicCalendars.program_id' => $programId,
                'AcademicCalendars.program_type_id' => $programTypeId,
                'AcademicCalendars.department_id LIKE' => '%' . serialize($deptCol) . '%',
                'AcademicCalendars.year_level_id LIKE' => '%' . serialize($yearLevel) . '%'
            ])
            ->contain(['Programs', 'ProgramTypes'])
            ->order(['AcademicCalendars.academic_year' => 'DESC', 'AcademicCalendars.semester' => 'DESC'])
            ->toArray();

        foreach ($currentAcademicCalendars as $academicCalendar) {
            $departmentIds = unserialize($academicCalendar->department_id) ?: [];
            foreach ($departmentIds as $depId) {
                if ($depId == $deptCol) {
                    $cid = explode('pre_', $depId);
                    $calendar[$depId]['departmentname'] = count($cid) > 1
                        ? "Pre ({$collegesTable->field('name', ['Colleges.id' => $cid[1]])})"
                        : $departmentsTable->field('name', ['Departments.id' => $depId]);
                    $calendar[$depId]['academic_year'] = $academicYear;
                    $calendar[$depId]['semester'] = $academicCalendar->semester;
                    $calendar[$depId]['yearlevel'] = $yearLevel;
                    $calendar[$depId]['calendarDetail'] = $academicCalendar->toArray();
                }
            }
        }

        return $calendar;
    }

    /**
     * Retrieves academic calendar by department
     *
     * @param string $currentAcademicYear Academic year
     * @param string $departmentId Department ID
     * @return array Calendar data
     */
    public function getAcademicCalenderDepartment(string $currentAcademicYear, string $departmentId): array
    {
        $calendar = [];
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');

        $academicCalendars = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $currentAcademicYear,
                'AcademicCalendars.department_id LIKE' => '%' . serialize($departmentId) . '%'
            ])
            ->contain(['Programs', 'ProgramTypes'])
            ->toArray();

        foreach ($academicCalendars as $academicCalendar) {
            $departmentIds = unserialize($academicCalendar->department_id) ?: [];
            $yearLevelIds = unserialize($academicCalendar->year_level_id) ?: [];

            foreach ($departmentIds as $depId) {
                if (strpos($depId, 'pre_') !== false) {
                    $collegeName = $collegesTable->field('name', ['Colleges.id' => $depId]);
                    $calendar[$depId]['departmentname'] = "Pre({$collegeName})";
                    $calendar[$depId]['yearlevel'] = '1st';
                } else {
                    $deptName = $departmentsTable->field('name', ['Departments.id' => $depId]);
                    $calendar[$depId]['departmentname'] = $deptName;
                    $calendar[$depId]['yearlevel'] = $yearLevelIds;
                }
                $calendar[$depId]['calendarDetail'] = $academicCalendar->toArray();
            }
        }

        return $calendar;
    }

    /**
     * Retrieves upcoming academic calendar deadlines
     *
     * @param string $currentAcademicYear Academic year
     * @param string $departmentId Department ID
     * @return array Deadlines
     */
    public function getComingAcademicCalendarsDeadlines(string $currentAcademicYear, string $departmentId): array
    {
        $deadlines = [];
        $academicCalendars = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $currentAcademicYear,
                'AcademicCalendars.department_id LIKE' => '%' . serialize($departmentId) . '%'
            ])
            ->contain(['Programs', 'ProgramTypes'])
            ->toArray();

        foreach ($academicCalendars as $index => $calendar) {
            $today = new Time();
            $gradeSubmissionEndDate = (new Time($calendar->grade_submission_end_date))->modify('-5 days');

            if ($gradeSubmissionEndDate->modify('-5 days')->isFuture()) {
                $deadlines[$index]['GradeSubmissionDeadline'] = $gradeSubmissionEndDate->format('F j, Y, g:i a');
            } elseif ($gradeSubmissionEndDate) {
                $deadlines[$index]['GradeSubmissionDeadline'] = $gradeSubmissionEndDate->format('F j, Y, g:i a');
            }
        }

        return $deadlines;
    }

    /**
     * Retrieves minimum credit for academic status
     *
     * @param int $studentId Student ID
     * @return int Minimum credit
     */
    public function minimumCreditForStatus(int $studentId): int
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $settingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');

        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $studentId])
            ->contain(['Curriculums'])
            ->select(['id', 'program_id', 'program_type_id', 'curriculum_id'])
            ->first();

        if (!$studentDetail) {
            return DEFAULT_MINIMUM_CREDIT_FOR_STATUS;
        }

        $settings = $settingsTable->find()
            ->where([
                'GeneralSettings.program_id LIKE' => '%s:_:"' . $studentDetail->program_id . '"%',
                'GeneralSettings.program_type_id LIKE' => '%s:_:"' . $studentDetail->program_type_id . '"%'
            ])
            ->first();

        if ($settings && $studentDetail->curriculum && is_numeric($studentDetail->curriculum->id)) {
            if (stripos($studentDetail->curriculum->type_credit, 'ECTS') !== false) {
                return (int)round($settings->minimum_credit_for_status * CREDIT_TO_ECTS);
            }
            return (int)$settings->minimum_credit_for_status;
        }

        return DEFAULT_MINIMUM_CREDIT_FOR_STATUS;
    }

    /**
     * Retrieves maximum credit per semester
     *
     * @param int $studentId Student ID
     * @return int Maximum credit
     */
    public function maximumCreditPerSemester(int $studentId): int
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $settingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');

        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $studentId])
            ->contain(['Curriculums'])
            ->select(['id', 'program_id', 'program_type_id', 'curriculum_id'])
            ->first();

        if (!$studentDetail) {
            return DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;
        }

        $settings = $settingsTable->find()
            ->where([
                'GeneralSettings.program_id LIKE' => '%s:_:"' . $studentDetail->program_id . '"%',
                'GeneralSettings.program_type_id LIKE' => '%s:_:"' . $studentDetail->program_type_id . '"%'
            ])
            ->first();

        if ($settings && $studentDetail->curriculum && is_numeric($studentDetail->curriculum->id)) {
            if ($settings->maximum_credit_per_semester != 0) {
                if (stripos($studentDetail->curriculum->type_credit, 'ECTS') !== false) {
                    return (int)round($settings->maximum_credit_per_semester * CREDIT_TO_ECTS);
                }
                return (int)$settings->maximum_credit_per_semester;
            }
        }

        return DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;
    }

    /**
     * Retrieves most recent academic calendar for SMS
     *
     * @param string $phoneNumber Student's phone number
     * @return string Formatted calendar message
     */
    public function getMostRecentAcademicCalenderForSMS(string $phoneNumber): string
    {
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');

        $studentDetail = $studentsTable->find()
            ->where(['Students.phone_mobile' => $phoneNumber])
            ->contain(['Users'])
            ->first();

        if (!$studentDetail) {
            return 'No registration date to be displayed for you.';
        }

        $yearAndAcademicYear = $sectionsTable->getStudentYearLevel($studentDetail->id);

        $recentAcademicCalendar = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $yearAndAcademicYear['academicyear'],
                'AcademicCalendars.program_id' => $studentDetail->program_id,
                'AcademicCalendars.department_id LIKE' => '%' . serialize($studentDetail->department_id) . '%',
                'AcademicCalendars.year_level_id LIKE' => '%' . serialize($yearAndAcademicYear['year']) . '%',
                'AcademicCalendars.program_type_id' => $studentDetail->program_type_id
            ])
            ->order(['AcademicCalendars.academic_year' => 'DESC', 'AcademicCalendars.semester' => 'DESC'])
            ->first();

        if ($recentAcademicCalendar) {
            return $this->formateAcademicCalendarForSMS($recentAcademicCalendar);
        }

        return 'No registration/add/drop deadline defined for you.';
    }

    /**
     * Formats academic calendar for SMS
     *
     * @param \App\Model\Entity\AcademicCalendar $academicCalendar Academic calendar entity
     * @return string Formatted message
     */
    public function formateAcademicCalendarForSMS($academicCalendar): string
    {
        return sprintf(
            "Academic Year: %s Semester: %s\nRegistration Start: %s\nRegistration End: %s\nAdd Start: %s\nAdd End: %s\nDrop Start: %s\nDrop End: %s",
            $academicCalendar->academic_year,
            $academicCalendar->semester,
            (new Time($academicCalendar->course_registration_start_date))->format('F j, Y, g:i a'),
            (new Time($academicCalendar->course_registration_end_date))->format('F j, Y, g:i a'),
            (new Time($academicCalendar->course_add_start_date))->format('F j, Y, g:i a'),
            (new Time($academicCalendar->course_add_end_date))->format('F j, Y, g:i a'),
            (new Time($academicCalendar->course_drop_start_date))->format('F j, Y, g:i a'),
            (new Time($academicCalendar->course_drop_end_date))->format('F j, Y, g:i a')
        );
    }

    /**
     * Retrieves recent academic calendar schedule
     *
     * @param string $academicYear Academic year
     * @param string $semester Semester
     * @param int $programId Program ID
     * @param int $programTypeId Program type ID
     * @param string $departmentId Department ID
     * @param string $year Year level
     * @param int $freshman Freshman flag
     * @param int|null $collegeId College ID
     * @return array|null Calendar data or null
     */
    public function recentAcademicYearSchedule(string $academicYear, string $semester, int $programId, int $programTypeId, string $departmentId, string $year, int $freshman = 0, ?int $collegeId = null): ?array
    {
        if ($freshman) {
            $year = '1st';
            $departmentId = 'pre_' . $collegeId;
        }

        $calendar = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $academicYear,
                'AcademicCalendars.semester' => $semester,
                'AcademicCalendars.program_id' => $programId,
                'AcademicCalendars.department_id LIKE' => '%' . serialize($departmentId) . '%',
                'AcademicCalendars.year_level_id LIKE' => '%' . serialize($year) . '%',
                'AcademicCalendars.program_type_id' => $programTypeId
            ])
            ->order(['AcademicCalendars.academic_year' => 'DESC', 'AcademicCalendars.semester' => 'DESC'])
            ->first();

        return $calendar ? $calendar->toArray() : null;
    }

    /**
     * Retrieves grade submission date for a published course
     *
     * @param int $pid Published course ID
     * @return string Grade submission end date
     */
    public function getPublishedCourseGradeSubmissionDate(int $pid): string
    {
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $publishedCourseDetail = $publishedCoursesTable->find()
            ->where(['PublishedCourses.id' => $pid])
            ->contain(['YearLevels', 'Courses'])
            ->first();

        if (!$publishedCourseDetail) {
            $startDate = date('Y-m-d', strtotime('-4 months')); // Approximate current semester start
            return date('Y-m-d', strtotime($startDate . ' + ' . (DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER * 7) . ' days'));
        }

        if ($publishedCourseDetail->course && $publishedCourseDetail->course->thesis == 1) {
            return date('Y-m-d', strtotime('+5 days'));
        }

        $yearLevel = $publishedCourseDetail->year_level->name ?? '1st';
        $departmentId = $publishedCourseDetail->department_id ?: 'pre_' . $publishedCourseDetail->college_id;

        $gradeSubmissionDate = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $publishedCourseDetail->academic_year,
                'AcademicCalendars.semester' => $publishedCourseDetail->semester,
                'AcademicCalendars.program_id' => $publishedCourseDetail->program_id,
                'AcademicCalendars.department_id LIKE' => '%' . serialize($departmentId) . '%',
                'AcademicCalendars.year_level_id LIKE' => '%' . serialize($yearLevel) . '%',
                'AcademicCalendars.program_type_id' => $publishedCourseDetail->program_type_id
            ])
            ->order(['AcademicCalendars.created' => 'DESC'])
            ->first();

        if ($gradeSubmissionDate) {
            return $gradeSubmissionDate->grade_submission_end_date;
        }

        $startDate = date('Y-m-d', strtotime('-4 months')); // Approximate current semester start
        return date('Y-m-d', strtotime($startDate . ' + ' . (DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER * 7) . ' days'));
    }

    /**
     * Retrieves FX grade submission date for a published course
     *
     * @param int $pid Published course ID
     * @return string FX grade submission end date
     */
    public function getFxPublishedCourseGradeSubmissionDate(int $pid): string
    {
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $publishedCourseDetail = $publishedCoursesTable->find()
            ->where(['PublishedCourses.id' => $pid])
            ->contain(['YearLevels', 'Courses'])
            ->first();

        if (!$publishedCourseDetail) {
            $startDate = date('Y-m-d', strtotime('-4 months')); // Approximate current semester start
            return date('Y-m-d', strtotime($startDate . ' + 4 months'));
        }

        if ($publishedCourseDetail->course && $publishedCourseDetail->course->thesis == 1) {
            return date('Y-m-d', strtotime('+5 days'));
        }

        $yearLevel = $publishedCourseDetail->year_level->name ?? '1st';
        $departmentId = $publishedCourseDetail->department_id ?: 'pre_' . $publishedCourseDetail->college_id;

        $gradeSubmissionDate = $this->find()
            ->where([
                'AcademicCalendars.academic_year' => $publishedCourseDetail->academic_year,
                'AcademicCalendars.semester' => $publishedCourseDetail->semester,
                'AcademicCalendars.program_id' => $publishedCourseDetail->program_id,
                'AcademicCalendars.department_id LIKE' => '%' . serialize($departmentId) . '%',
                'AcademicCalendars.year_level_id LIKE' => '%' . serialize($yearLevel) . '%',
                'AcademicCalendars.program_type_id' => $publishedCourseDetail->program_type_id
            ])
            ->order(['AcademicCalendars.created' => 'DESC'])
            ->first();

        if ($gradeSubmissionDate && $gradeSubmissionDate->grade_fx_submission_end_date) {
            return $gradeSubmissionDate->grade_fx_submission_end_date;
        }

        return date('Y-m-d', strtotime('+2 days'));
    }

    /**
     * Retrieves grade submission date
     *
     * @param string $academicYear Academic year
     * @param string $semester Semester
     * @param int $programId Program ID
     * @param int $programTypeId Program type ID
     * @param string $departmentId Department ID
     * @param string $year Year level
     * @return string Grade submission end date
     */
    public function getGradeSubmissionDate(string $academicYear, string $semester, int $programId, int $programTypeId, string $departmentId, string $year): string
    {
        $programIds = explode('~', (string)$programId);
        $programID = count($programIds) > 1 ? (int)$programIds[1] : $programId;

        $programTypeIds = explode('~', (string)$programTypeId);
        $programTypeID = count($programTypeIds) > 1 ? (int)$programTypeIds[1] : $programTypeId;

        $collegeIds = explode('~', $departmentId);
        $collegeId = count($collegeIds) > 1 ? (int)$collegeIds[1] : null;

        $departments = [];
        $colleges = [];
        if ($collegeId) {
            $departments = TableRegistry::getTableLocator()->get('Departments')->find()
                ->where(['Departments.college_id' => $collegeId, 'Departments.active' => 1])
                ->contain(['Colleges', 'YearLevels'])
                ->toArray();

            $colleges = TableRegistry::getTableLocator()->get('Colleges')->find()
                ->where(['Colleges.id' => $collegeId, 'Colleges.active' => 1])
                ->toArray();
        } else {
            $departments = TableRegistry::getTableLocator()->get('Departments')->find()
                ->where(['Departments.id' => $departmentId])
                ->contain(['Colleges', 'YearLevels'])
                ->toArray();

            $colleges = TableRegistry::getTableLocator()->get('Colleges')->find()
                ->where(['Colleges.id' => $departments[0]->college->id])
                ->toArray();
        }

        foreach ($departments as $department) {
            $yearLevels = $year ? array_filter($department->year_levels, fn($yl) => strcasecmp($yl->name, $year) == 0) : $department->year_levels;

            foreach ($yearLevels as $yvalue) {
                $gradeSubmissionDate = $this->find()
                    ->where([
                        'AcademicCalendars.academic_year' => $academicYear,
                        'AcademicCalendars.semester' => $semester,
                        'AcademicCalendars.program_id' => $programID,
                        'AcademicCalendars.department_id LIKE' => '%' . serialize($department->id) . '%',
                        'AcademicCalendars.year_level_id LIKE' => '%' . serialize($yvalue->name) . '%',
                        'AcademicCalendars.program_type_id' => $programTypeID
                    ])
                    ->order(['AcademicCalendars.academic_year' => 'DESC', 'AcademicCalendars.semester' => 'DESC'])
                    ->first();

                if ($gradeSubmissionDate) {
                    $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                        $gradeSubmissionDate->id,
                        $yvalue->name,
                        $department->id,
                        $programID,
                        $programTypeID,
                        'grade_submission'
                    );

                    return $daysAdded
                        ? (new Time($gradeSubmissionDate->grade_submission_end_date))->modify("+$daysAdded days")->format('Y-m-d')
                        : $gradeSubmissionDate->grade_submission_end_date;
                }
            }
        }

        foreach ($colleges as $college) {
            $yvalueFresh = '1st';
            $fresh = 'pre_' . $college->id;

            $gradeSubmissionDate = $this->find()
                ->where([
                    'AcademicCalendars.academic_year' => $academicYear,
                    'AcademicCalendars.semester' => $semester,
                    'AcademicCalendars.program_id' => $programID,
                    'AcademicCalendars.department_id LIKE' => '%' . serialize($fresh) . '%',
                    'AcademicCalendars.year_level_id LIKE' => '%' . serialize($yvalueFresh) . '%',
                    'AcademicCalendars.program_type_id' => $programTypeID
                ])
                ->order(['AcademicCalendars.academic_year' => 'DESC', 'AcademicCalendars.semester' => 'DESC'])
                ->first();

            if ($gradeSubmissionDate) {
                $daysAdded = $this->ExtendingAcademicCalendars->getExtendedDays(
                    $gradeSubmissionDate->id,
                    $yvalueFresh,
                    $fresh,
                    $programID,
                    $programTypeID,
                    'grade_submission'
                );

                return $daysAdded
                    ? (new Time($gradeSubmissionDate->grade_submission_end_date))->modify("+$daysAdded days")->format('Y-m-d')
                    : $gradeSubmissionDate->grade_submission_end_date;
            }
        }

        $startDate = date('Y-m-d', strtotime('-4 months')); // Approximate current semester start
        return date('Y-m-d', strtotime($startDate . ' + 4 months'));
    }

    /**
     * Retrieves last grade change date for a published course
     *
     * @param int $pid Published course ID
     * @return string Last grade change date
     */
    public function getLastGradeChangeDate(int $pid): string
    {
        $publishedCoursesTable = TableRegistry::getTableLocator()->get('PublishedCourses');
        $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');

        $publishedCourseDetail = $publishedCoursesTable->find()
            ->where(['PublishedCourses.id' => $pid])
            ->contain(['YearLevels', 'Courses'])
            ->first();

        if (!$publishedCourseDetail) {
            $startDate = date('Y-m-d', strtotime('-4 months')); // Approximate current semester start
            return date('Y-m-d', strtotime($startDate . ' + 4 months'));
        }

        if ($publishedCourseDetail->course && $publishedCourseDetail->course->thesis == 1) {
            return date('Y-m-d', strtotime('+5 days'));
        }

        $nextAcademicYear = $studentExamStatusesTable->getNextSemester(
            $publishedCourseDetail->academic_year,
            $publishedCourseDetail->semester
        );

        $academicYearsToCheck = [
            $nextAcademicYear,
            $studentExamStatusesTable->getNextSemester($nextAcademicYear['academic_year'], $nextAcademicYear['semester']),
            ['academic_year' => $publishedCourseDetail->academic_year, 'semester' => $publishedCourseDetail->semester]
        ];

        $yearLevel = $publishedCourseDetail->year_level->name ?? '1st';
        $departmentId = $publishedCourseDetail->department_id ?: 'pre_' . $publishedCourseDetail->college_id;

        foreach ($academicYearsToCheck as $year) {
            $gradeSubmissionDate = $this->find()
                ->where([
                    'AcademicCalendars.academic_year' => $year['academic_year'],
                    'AcademicCalendars.semester' => $year['semester'],
                    'AcademicCalendars.program_id' => $publishedCourseDetail->program_id,
                    'AcademicCalendars.department_id LIKE' => '%' . serialize($departmentId) . '%',
                    'AcademicCalendars.year_level_id LIKE' => '%' . serialize($yearLevel) . '%',
                    'AcademicCalendars.program_type_id' => $publishedCourseDetail->program_type_id
                ])
                ->order(['AcademicCalendars.created' => 'DESC'])
                ->first();

            if ($gradeSubmissionDate) {
                return $gradeSubmissionDate->course_registration_start_date;
            }
        }

        $startDate = date('Y-m-d', strtotime('-4 months')); // Approximate current semester start
        return date('Y-m-d', strtotime($startDate . ' + 4 months'));
    }
}
