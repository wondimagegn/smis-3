<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\I18n\FrozenDate;
use Cake\ORM\TableRegistry;

class AcademicCalendarsTable extends Table
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

        $this->setTable('academic_calendars');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');


        $this->belongsTo('Programs', [
            'foreignKey' => 'program_id',
            'joinType' => 'LEFT',
            'propertyName'=>'Program'
        ]);

        $this->belongsTo('ProgramTypes', [
            'foreignKey' => 'program_type_id',
            'joinType' => 'LEFT',
            'propertyName'=>'ProgramType'
        ]);

        $this->hasMany('ExtendingAcademicCalendars', [
            'foreignKey' => 'academic_calendar_id',
            'propertyName'=>'ExtendingAcademicCalendar'
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param \Cake\Validation\Validator $validator Validator instance.
     * @return \Cake\Validation\Validator
     */
    public function validationDefault(Validator $validator)
    {

        $validator
            ->integer('id')
            ->allowEmptyString('id', null, 'create');

        $validator
            ->scalar('academic_year')
            ->maxLength('academic_year', 30)
            ->requirePresence('academic_year', 'create')
            ->notEmptyString('academic_year');

        $validator
            ->scalar('semester')
            ->maxLength('semester', 3)
            ->requirePresence('semester', 'create')
            ->notEmptyString('semester');

        $validator
            ->date('course_registration_start_date')
            ->requirePresence('course_registration_start_date', 'create')
            ->notEmptyDate('course_registration_start_date');

        $validator
            ->date('course_registration_end_date')
            ->requirePresence('course_registration_end_date', 'create')
            ->notEmptyDate('course_registration_end_date');

        $validator
            ->date('course_add_start_date')
            ->requirePresence('course_add_start_date', 'create')
            ->notEmptyDate('course_add_start_date');

        $validator
            ->date('course_add_end_date')
            ->requirePresence('course_add_end_date', 'create')
            ->notEmptyDate('course_add_end_date');

        $validator
            ->date('course_drop_start_date')
            ->requirePresence('course_drop_start_date', 'create')
            ->notEmptyDate('course_drop_start_date');

        $validator
            ->date('course_drop_end_date')
            ->requirePresence('course_drop_end_date', 'create')
            ->notEmptyDate('course_drop_end_date');

        $validator
            ->date('grade_submission_start_date')
            ->requirePresence('grade_submission_start_date', 'create')
            ->notEmptyDate('grade_submission_start_date');

        $validator
            ->date('grade_submission_end_date')
            ->requirePresence('grade_submission_end_date', 'create')
            ->notEmptyDate('grade_submission_end_date');

        $validator
            ->date('grade_fx_submission_end_date')
            ->allowEmptyDate('grade_fx_submission_end_date');

        $validator
            ->date('senate_meeting_date')
            ->allowEmptyDate('senate_meeting_date');

        $validator
            ->date('graduation_date')
            ->allowEmptyDate('graduation_date');

        $validator
            ->date('online_admission_start_date')
            ->allowEmptyDate('online_admission_start_date');

        $validator
            ->date('online_admission_end_date')
            ->allowEmptyDate('online_admission_end_date');

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

        $rules->add($rules->existsIn(['college_id'], 'Colleges'));
        $rules->add($rules->existsIn(['department_id'], 'Departments'));
        $rules->add($rules->existsIn(['program_id'], 'Programs'));
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'));
        $rules->add($rules->existsIn(['year_level_id'], 'YearLevels'));

        return $rules;
    }

    function check_registration(
        $academicYear = null,
        $semester = null,
        $departmentCollegeId = null,
        $yearLevelId = null,
        $programId = null,
        $programTypeId = null
    ) {

        $courseRegistrationStartDate = null;
        $yearLevelForSearch = ($yearLevelId == 0 ? '1st' : $yearLevelId);

        $query = $this->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->order(['AcademicCalendars.id' => 'DESC'])
            ->enableHydration(false);

        if (!empty($programId) && !empty($programTypeId)) {
            $query->where([
                'program_id' => $programId,
                'program_type_id' => $programTypeId
            ]);

            if (!empty($departmentCollegeId)) {
                $query->where([
                    "JSON_CONTAINS(department_id, '\"$departmentCollegeId\"')" => true,
                    "JSON_CONTAINS(year_level_id, '\"$yearLevelForSearch\"')" => true
                ]);
            }
        }

        $academicCalendar = $query->toArray();

        if (!empty($academicCalendar)) {
            foreach ($academicCalendar as &$acv) {
                $daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
                    $acv['id'],
                    $yearLevelForSearch,
                    $departmentCollegeId,
                    $acv['program_id'],
                    $acv['program_type_id'],
                    'registration'
                );

                $courseRegistrationStartDate = $acv['course_registration_start_date'];

                if (
                    in_array($departmentCollegeId, json_decode($acv['department_id'], true)) &&
                    in_array($yearLevelForSearch, json_decode($acv['year_level_id'], true))
                ) {
                    if (
                        (date('Y-m-d') >= $acv['course_registration_start_date']) &&
                        (date('Y-m-d') <= date(
                                'Y-m-d',
                                strtotime($acv['course_registration_end_date'] . " +$daysAdded days")
                            ))
                    ) {
                        return 1; // Registration is allowed
                    }
                }
            }
        }

        if (!empty($courseRegistrationStartDate)) {
            return (date('Y-m-d') < $courseRegistrationStartDate) ? $courseRegistrationStartDate : 2;
        }

        return false;
    }

    public function check_add_date_end(
        $academicYear = null,
        $semester = null,
        $departmentCollegeId = null,
        $yearLevelId = null,
        $programId = null,
        $programTypeId = null
    ) {

        $yearLevelForSearch = ($yearLevelId == 0 ? '1st' : $yearLevelId);

        $query = $this->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->order(['AcademicCalendars.id' => 'DESC'])
            ->enableHydration(false);

        if (!empty($programId) && !empty($programTypeId)) {
            $query->where([
                'program_id' => $programId,
                'program_type_id' => $programTypeId
            ]);

            if (!empty($departmentCollegeId)) {
                $query->where([
                    "JSON_CONTAINS(department_id, '\"$departmentCollegeId\"')" => true,
                    "JSON_CONTAINS(year_level_id, '\"$yearLevelForSearch\"')" => true
                ]);
            }
        }

        $academicCalendar = $query->toArray();

        $courseAddStartDate = null;
        $courseAddEndDate = null;
        $courseRegistrationEndDate = null;

        if (!empty($academicCalendar)) {
            foreach ($academicCalendar as &$acv) {
                $daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
                    $acv['id'],
                    $yearLevelForSearch,
                    $departmentCollegeId,
                    $acv['program_id'],
                    $acv['program_type_id'],
                    'add'
                );

                $courseAddStartDate = $acv['course_add_start_date'];
                $courseAddEndDate = $acv['course_add_end_date'];
                $courseRegistrationEndDate = $acv['course_registration_end_date'];

                if (
                    in_array($departmentCollegeId, json_decode($acv['department_id'], true)) &&
                    in_array($yearLevelForSearch, json_decode($acv['year_level_id'], true))
                ) {
                    if (
                        (date('Y-m-d') >= $acv['course_add_start_date']) &&
                        (date('Y-m-d') <= date('Y-m-d', strtotime($acv['course_add_end_date'] . " +$daysAdded days")))
                    ) {
                        return 1; // Course add is possible
                    }
                }
            }
        }

        if (!empty($courseAddStartDate)) {
            return (date('Y-m-d') < $courseAddStartDate) ? $courseAddStartDate : 2;
        }

        return false;
    }

    function check_add_date_start(
        $academicYear = null,
        $semester = null,
        $departmentCollegeId = null,
        $yearLevelId = null
    ) {

        $academicCalendar = $this->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->order(['id' => 'DESC'])
            ->enableHydration(false)
            ->toArray();

        if (!empty($academicCalendar)) {
            foreach ($academicCalendar as $acv) {
                $collegeIds = json_decode($acv['college_id'], true);
                $departmentIds = json_decode($acv['department_id'], true);
                $yearLevelIds = json_decode($acv['year_level_id'], true);

                if (in_array($departmentCollegeId, $departmentIds) && in_array($yearLevelId, $yearLevelIds)) {
                    if ($acv['course_add_start_date'] >= date('Y-m-d')) {
                        return $acv['id']; // Return the Academic Calendar ID
                    }
                }
            }
        }

        return false;
    }


    function check_add_drop_end(
        $academicYear = null,
        $semester = null,
        $departmentCollegeId = null,
        $yearLevelId = null,
        $programId = null,
        $programTypeId = null
    ) {

        $courseDropStartDate = null;

        // Build query conditions dynamically
        $conditions = [
            'academic_year' => $academicYear,
            'semester' => $semester
        ];

        if (!empty($programId) && !empty($programTypeId)) {
            $conditions['program_id'] = $programId;
            $conditions['program_type_id'] = $programTypeId;
        }

        // Fetch academic calendar data
        $academicCalendar = $this->find()
            ->where($conditions)
            ->order(['id' => 'DESC'])
            ->enableHydration(false)
            ->toArray();

        if (!empty($academicCalendar)) {
            foreach ($academicCalendar as &$acv) {
                $collegeIds = json_decode($acv['college_id'], true);
                $departmentIds = json_decode($acv['department_id'], true);
                $yearLevelIds = json_decode($acv['year_level_id'], true);

                $daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
                    $acv['id'],
                    $yearLevelId,
                    $departmentCollegeId,
                    $acv['program_id'],
                    $acv['program_type_id'],
                    'drop'
                );

                $courseDropStartDate = $acv['course_drop_start_date'];

                // Check if course drop period is within the deadline
                if (in_array($departmentCollegeId, $departmentIds) && in_array($yearLevelId, $yearLevelIds)) {
                    if ((date('Y-m-d') >= $acv['course_drop_start_date']) && (date('Y-m-d') <= date(
                                'Y-m-d',
                                strtotime($acv['course_drop_end_date'] . ' +' . $daysAdded . ' days ')
                            ))) {
                        return 1;
                    }
                }

                // Freshman check
                if (empty($yearLevelId) || $yearLevelId == 0) {
                    if (in_array('pre_' . $departmentCollegeId, $departmentIds) && in_array('1st', $yearLevelIds)) {
                        if ((date('Y-m-d') >= $acv['course_drop_start_date']) && (date('Y-m-d') <= date(
                                    'Y-m-d',
                                    strtotime($acv['course_drop_end_date'] . ' +' . $daysAdded . ' days ')
                                ))) {
                            return 1;
                        }
                    }
                }
            }
        }

        return (!empty($courseDropStartDate) && date('Y-m-d') < $courseDropStartDate) ? $courseDropStartDate : false;
    }

    function check_registration_add_drop_start_end(
        $academicYear = null,
        $semester = null,
        $departmentCollegeId = null,
        $yearLevelId = null,
        $programId = null,
        $programTypeId = null,
        $type = ''
    ) {

        $activityStartDate = null;
        $activityEndDate = null;

        if (!empty($type)) {
            // Build query conditions dynamically
            $conditions = [
                'academic_year' => $academicYear,
                'semester' => $semester
            ];

            if (!empty($programId) && !empty($programTypeId)) {
                $conditions['program_id'] = $programId;
                $conditions['program_type_id'] = $programTypeId;
            }

            // Fetch academic calendar data
            $academicCalendar = $this->find()
                ->where($conditions)
                ->order(['id' => 'DESC'])
                ->enableHydration(false)
                ->toArray();

            if (!empty($academicCalendar)) {
                foreach ($academicCalendar as &$acv) {
                    $collegeIds = json_decode($acv['college_id'], true);
                    $departmentIds = json_decode($acv['department_id'], true);
                    $yearLevelIds = json_decode($acv['year_level_id'], true);

                    // Determine the type of activity
                    switch ($type) {
                        case 'registration':
                            $daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
                                $acv['id'],
                                $yearLevelId,
                                $departmentCollegeId,
                                $acv['program_id'],
                                $acv['program_type_id'],
                                'registration'
                            );
                            $activityStartDate = $acv['course_registration_start_date'];
                            $activityEndDate = $acv['course_registration_end_date'];
                            break;

                        case 'add':
                            $daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
                                $acv['id'],
                                $yearLevelId,
                                $departmentCollegeId,
                                $acv['program_id'],
                                $acv['program_type_id'],
                                'add'
                            );
                            $activityStartDate = $acv['course_add_start_date'];
                            $activityEndDate = $acv['course_add_end_date'];
                            break;

                        case 'drop':
                            $daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
                                $acv['id'],
                                $yearLevelId,
                                $departmentCollegeId,
                                $acv['program_id'],
                                $acv['program_type_id'],
                                'drop'
                            );
                            $activityStartDate = $acv['course_drop_start_date'];
                            $activityEndDate = $acv['course_drop_end_date'];
                            break;
                    }

                    // Check if activity is within allowed dates
                    if (in_array($departmentCollegeId, $departmentIds) && in_array($yearLevelId, $yearLevelIds)) {
                        if (!empty($activityStartDate) && !empty($activityEndDate)) {
                            if ((date('Y-m-d') >= $activityStartDate) && (date('Y-m-d') <= date(
                                        'Y-m-d',
                                        strtotime($activityEndDate . ' +' . $daysAdded . ' days ')
                                    ))) {
                                return 1;
                            }
                        }
                    }

                    // Freshman check
                    if (empty($yearLevelId) || $yearLevelId == 0) {
                        if (in_array('pre_' . $departmentCollegeId, $departmentIds) && in_array('1st', $yearLevelIds)) {
                            if ((date('Y-m-d') >= $activityStartDate) && (date('Y-m-d') <= date(
                                        'Y-m-d',
                                        strtotime($activityEndDate . ' +' . $daysAdded . ' days ')
                                    ))) {
                                return 1;
                            }
                        }
                    }
                }
            }

            return (!empty($activityStartDate) && date('Y-m-d') < $activityStartDate) ? $activityStartDate : false;
        }

        return false;
    }

    function check_grade_submission_end(
        $academicYear = null,
        $semester = null,
        $departmentCollegeId = null,
        $yearLevelId = null
    ) {

        // Fetch academic calendar records
        $academicCalendar = $this->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->order(['id' => 'DESC'])
            ->enableHydration(false)
            ->toArray();

        if (!empty($academicCalendar)) {
            foreach ($academicCalendar as &$acv) {
                // Decode JSON fields (previously stored as serialized data)
                $collegeIds = json_decode($acv['college_id'], true);
                $departmentIds = json_decode($acv['department_id'], true);
                $yearLevelIds = json_decode($acv['year_level_id'], true);

                // Get any extended grade submission deadlines
                $daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
                    $acv['id'],
                    $yearLevelId,
                    $departmentCollegeId,
                    $acv['program_id'],
                    $acv['program_type_id'],
                    'grade_submission'
                );

                // Check if department & year level match
                if (in_array($departmentCollegeId, $departmentIds) && in_array($yearLevelId, $yearLevelIds)) {
                    // Validate if the grade submission period is still open
                    $extendedDeadline = date(
                        'Y-m-d',
                        strtotime($acv['grade_submission_end_date'] . ' +' . $daysAdded . ' days ')
                    );

                    if ($extendedDeadline >= date('Y-m-d')) {
                        return $acv['id'];
                    }
                }
            }
        }

        return false;
    }

    function check_duplicate_entry($data = null)
    {

        $existedDept = [];

        if (empty($data['AcademicCalendar'])) {
            return true;
        }

        $conditions = [
            'academic_year' => $data['AcademicCalendar']['academic_year'],
            'semester' => $data['AcademicCalendar']['semester'],
            'program_id' => $data['AcademicCalendar']['program_id'],
            'program_type_id' => $data['AcademicCalendar']['program_type_id'],
            'year_level_id' => json_encode($data['AcademicCalendar']['year_level_id']),
        ];

        if (!empty($data['AcademicCalendar']['id'])) {
            $conditions['id !='] = $data['AcademicCalendar']['id'];
        }

        $academicCalendars = $this->find()
            ->where($conditions)
            ->order(['id' => 'DESC'])
            ->enableHydration(false)
            ->toArray();

        if (!empty($academicCalendars)) {
            foreach ($academicCalendars as $academicCalendar) {
                $departmentIds = json_decode($academicCalendar['department_id'], true);
                $yearLevelIds = json_decode($academicCalendar['year_level_id'], true);

                if (!empty($departmentIds) && !empty($yearLevelIds)) {
                    foreach ($departmentIds as $depId) {
                        foreach ($yearLevelIds as $yearId) {
                            $existedDept[$yearId][] = $depId;
                        }
                    }
                }
            }
        }

        if (!empty($existedDept)) {
            $alreadyExistedYearLevel = [];
            $departments = [];

            foreach ($existedDept as $year => $deptIds) {
                foreach ($deptIds as $deptId) {
                    if (!in_array($deptId, $departments)) {
                        $departments[] = $deptId;
                    }

                    $departmentName = $this->Departments->find()
                        ->where(['id' => $deptId])
                        ->select(['name'])
                        ->first();

                    $deptName = $departmentName ? $departmentName->name : "Unknown Department";

                    $alreadyExistedYearLevel[] = "You have already setup an academic calendar for {$year} year in {$deptName}.";
                }
            }

            if (!empty($alreadyExistedYearLevel)) {
                $this->invalidate(
                    'duplicate',
                    $alreadyExistedYearLevel[0] . ' and ' . (count($departments) - 1) . ' others.'
                );
                $this->invalidate('departmentduplicate', $departments);
                $this->invalidate('yearlevelduplicate', $alreadyExistedYearLevel);
                return false;
            }
        }

        return true;
    }

    public function daysAvaiableForGradeChange($programId = null, $programTypeId = null)
    {

        if (!empty($programId) && !empty($programTypeId)) {
            $settings = $this->GeneralSettings
                ->find()
                ->where([
                    "JSON_CONTAINS(GeneralSettings.program_id, :programId)" => ['programId' => json_encode($programId)],
                    "JSON_CONTAINS(GeneralSettings.program_type_id, :programTypeId)" => [
                        'programTypeId' => json_encode(
                            $programTypeId
                        )
                    ]
                ])
                ->select(['daysAvailableForGradeChange'])
                ->first();

            return $settings->daysAvailableForGradeChange ?? DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE;
        }

        return DEFAULT_DAYS_AVAILABLE_FOR_GRADE_CHANGE;
    }

    public function daysAvaiableForNgToF($programId = null, $programTypeId = null)
    {

        $maxAllowedDays = 120; // Maximum 4 months

        if (!empty($programId) && !empty($programTypeId)) {
            $settings = $this->GeneralSettings
                ->find()
                ->where([
                    "JSON_CONTAINS(GeneralSettings.program_id, :programId)" => ['programId' => json_encode($programId)],
                    "JSON_CONTAINS(GeneralSettings.program_type_id, :programTypeId)" => [
                        'programTypeId' => json_encode(
                            $programTypeId
                        )
                    ]
                ])
                ->select(['daysAvailableForNgToF'])
                ->first();

            $days = $settings->daysAvailableForNgToF ?? DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F;

            return min($days, $maxAllowedDays); // Ensure max 120 days
        }

        return min(DEFAULT_DAYS_AVAILABLE_FOR_NG_TO_F, $maxAllowedDays);
    }


    public function daysAvaiableForDoToF($programId = null, $programTypeId = null)
    {

        $maxAllowedDays = 120; // Maximum 4 months

        if (!empty($programId) && !empty($programTypeId)) {
            $settings = $this->GeneralSettings
                ->find()
                ->where([
                    "JSON_CONTAINS(GeneralSettings.program_id, :programId)" => ['programId' => json_encode($programId)],
                    "JSON_CONTAINS(GeneralSettings.program_type_id, :programTypeId)" => [
                        'programTypeId' => json_encode(
                            $programTypeId
                        )
                    ]
                ])
                ->select(['daysAvailableForDoToF'])
                ->first();

            $days = $settings->daysAvailableForDoToF ?? DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F;

            return min($days, $maxAllowedDays); // Ensure max 120 days
        }

        return min(DEFAULT_DAYS_AVAILABLE_FOR_DO_TO_F, $maxAllowedDays);
    }

    function daysAvailableForFxToF($programId = null, $programTypeId = null)
    {

        $maxAllowedDays = 120; // Maximum 4 months

        if (!empty($programId) && !empty($programTypeId)) {
            $settings = $this->GeneralSettings
                ->find()
                ->where([
                    "JSON_CONTAINS(GeneralSettings.program_id, :programId)" => ['programId' => json_encode($programId)],
                    "JSON_CONTAINS(GeneralSettings.program_type_id, :programTypeId)" => [
                        'programTypeId' => json_encode(
                            $programTypeId
                        )
                    ]
                ])
                ->select(['daysAvailableForFxToF'])
                ->first();

            $days = $settings->daysAvailableForFxToF ?? DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F;

            return min($days, $maxAllowedDays); // Ensure max 120 days
        }

        return min(DEFAULT_DAYS_AVAILABLE_FOR_FX_TO_F, $maxAllowedDays);
    }

    function isFxConversionDate($academicCalendar, $departmentId, $publishedDetail)
    {

        $calendar = $this->getAcademicCalenderDepartment($academicCalendar, $departmentId);

        // Fetch Year Level Name safely using CakePHP ORM
        $yearLevelName = $this->YearLevels
            ->find()
            ->where(['id' => $publishedDetail['year_level_id']])
            ->select(['name'])
            ->first()
            ->name ?? null;

        if (!empty($calendar)) {
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


                    // Convert date using FrozenDate for comparison
                    $fxSubmissionEndDate = FrozenDate::parse($entry['calendarDetail']['grade_fx_submission_end_date'])
                        ->addDays($daysAdded);

                    return FrozenDate::now()->greaterThan($fxSubmissionEndDate);
                }
            }
        }

        return false;
    }

    function weekCountForAcademicYearAndSemester()
    {

        return DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER;
    }

    function weekCountForAcademicYear($programId = null, $programTypeId = null)
    {

        if (!empty($programId) && !empty($programTypeId)) {
            $generalSettingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');

            $settings = $generalSettingsTable->find()
                ->where([
                    'program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            if (!empty($settings) && !empty($settings->weekCountForAcademicYear)) {
                return $settings->weekCountForAcademicYear;
            }
        }

        return DEFAULT_WEEK_COUNT_FOR_ACADEMIC_YEAR;
    }

    public function weekCountForOneSemester($programId = null, $programTypeId = null)
    {

        if (!empty($programId) && !empty($programTypeId)) {
            // Fetch GeneralSettingsTable manually
            $generalSettingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');

            $settings = $generalSettingsTable->find()
                ->where([
                    'program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            if (!empty($settings) && !empty($settings->weekCountForOneSemester)) {
                return $settings->weekCountForOneSemester;
            }
        }

        return DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER;
    }

    public function semesterCountForAcademicYear($programId = null, $programTypeId = null)
    {

        if (!empty($programId) && !empty($programTypeId)) {
            // Fetch GeneralSettingsTable using TableRegistry
            $generalSettingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');

            $settings = $generalSettingsTable->find()
                ->where([
                    'program_id LIKE' => '%s:_:"' . $programId . '"%',
                    'program_type_id LIKE' => '%s:_:"' . $programTypeId . '"%'
                ])
                ->first();

            if (!empty($settings) && !empty($settings->semesterCountForAcademicYear)) {
                return $settings->semesterCountForAcademicYear;
            }
        }

        return DEFAULT_SEMESTER_COUNT_FOR_ACADEMIC_YEAR;
    }

    public function currentSemesterInTheDefinedAcademicCalender($academicYear)
    {

        // Fetch AcademicCalendarsTable
        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');

        $currentAcademicCalendar = $academicCalendarsTable->find()
            ->where(['academic_year' => $academicYear])
            ->all();

        if (!$currentAcademicCalendar->isEmpty()) {
            foreach ($currentAcademicCalendar as $calendar) {
                $now = time(); // Current timestamp
                $startDate = strtotime($calendar->course_registration_start_date);
                $dateDiff = floor(($now - $startDate) / (60 * 60 * 24));

                if ($dateDiff < 130) {
                    return $calendar->semester;
                }
            }
        }

        return 'I';
    }

    public function semesterStartAndEndMonth($semester, $academicYear)
    {

        // Initialize months array with zero values
        $months = [
            'Jan' => 0,
            'Feb' => 0,
            'Mar' => 0,
            'Apr' => 0,
            'May' => 0,
            'Jun' => 0,
            'Jul' => 0,
            'Aug' => 0,
            'Sep' => 0,
            'Oct' => 0,
            'Nov' => 0,
            'Dec' => 0
        ];

        // Fetch AcademicCalendarsTable instance
        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');

        // Query academic calendar for the given semester and academic year
        $currentAcademicCalendar = $academicCalendarsTable->find()
            ->where([
                'academic_year' => $academicYear,
                'semester' => $semester
            ])
            ->all();

        // Process the retrieved academic calendar data
        if (!$currentAcademicCalendar->isEmpty()) {
            foreach ($currentAcademicCalendar as $calendar) {
                if (!empty($calendar->course_registration_start_date)) {
                    $registrationMonth = date("M", strtotime($calendar->course_registration_start_date));
                    $months[$registrationMonth] = 0;
                }

                if (!empty($calendar->grade_submission_end_date)) {
                    $gradeSubmissionMonth = date("M", strtotime($calendar->grade_submission_end_date));
                    $months[$gradeSubmissionMonth] = 0;
                }
            }
        }

        return $months;
    }

    public function getAcademicCalendar($currentAcademicYear)
    {

        $calendar = [];

        // Fetch AcademicCalendarsTable instance
        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');

        // Query academic calendar for the given academic year
        $currentAcademicCalendar = $academicCalendarsTable->find()
            ->where(['academic_year' => $currentAcademicYear])
            ->contain(['Programs', 'ProgramTypes'])
            ->all();

        // Process the retrieved academic calendar data
        if (!$currentAcademicCalendar->isEmpty()) {
            foreach ($currentAcademicCalendar as $academicCalendar) {
                $departmentIds = unserialize($academicCalendar->department_id);
                $yearLevelIds = unserialize($academicCalendar->year_level_id);

                foreach ($departmentIds as $departmentId) {
                    if (strpos($departmentId, 'pre_') !== false) {
                        $collegeId = str_replace('pre_', '', $departmentId);
                        $collegeName = $collegesTable->find()
                            ->where(['id' => $collegeId])
                            ->select(['name'])
                            ->first();

                        $calendar[$departmentId]['departmentname'] = 'Pre(' . ($collegeName->name ?? 'Unknown') . ')';
                        $calendar[$departmentId]['yearlevel'] = '1st';
                    } else {
                        $departmentName = $departmentsTable->find()
                            ->where(['id' => $departmentId])
                            ->select(['name'])
                            ->first();

                        $calendar[$departmentId]['departmentname'] = $departmentName->name ?? 'Unknown';
                        $calendar[$departmentId]['yearlevel'] = $yearLevelIds;
                    }
                    $calendar[$departmentId]['calendarDetail'] = $academicCalendar;
                }
            }
        }

        return $calendar;
    }

    public function getAcademicCalendarDepartment($currentAcademicYear, $departmentId)
    {

        $calendar = [];

        // Fetch Table Instances
        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');
        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');

        // Query academic calendar for the given academic year & department
        $currentAcademicCalendar = $academicCalendarsTable->find()
            ->where([
                'academic_year' => $currentAcademicYear,
                'department_id LIKE' => '%s:_:"' . $departmentId . '"%',
            ])
            ->contain(['Programs', 'ProgramTypes'])
            ->all();

        // Process the retrieved academic calendar data
        if (!$currentAcademicCalendar->isEmpty()) {
            foreach ($currentAcademicCalendar as $academicCalendar) {
                $departmentIds = unserialize($academicCalendar->department_id);
                $yearLevelIds = unserialize($academicCalendar->year_level_id);

                foreach ($departmentIds as $department) {
                    if (strpos($department, 'pre_') !== false) {
                        $collegeId = str_replace('pre_', '', $department);
                        $collegeName = $collegesTable->find()
                            ->where(['id' => $collegeId])
                            ->select(['name'])
                            ->first();

                        $calendar[$department]['departmentname'] = 'Pre(' . ($collegeName->name ?? 'Unknown') . ')';
                        $calendar[$department]['yearlevel'] = '1st';
                    } else {
                        $departmentName = $departmentsTable->find()
                            ->where(['id' => $department])
                            ->select(['name'])
                            ->first();

                        $calendar[$department]['departmentname'] = $departmentName->name ?? 'Unknown';
                        $calendar[$department]['yearlevel'] = $yearLevelIds;
                    }
                    $calendar[$department]['calendarDetail'] = $academicCalendar;
                }
            }
        }

        return $calendar;
    }

    public function getComingAcademicCalendarsDeadlines($currentAcademicYear, $departmentId)
    {

        // Fetch Table Instance
        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');

        // Query academic calendar for the given academic year & department
        $currentAcademicCalendar = $academicCalendarsTable->find()
            ->where([
                'academic_year' => $currentAcademicYear,
                'department_id LIKE' => '%s:_:"' . $departmentId . '"%',
            ])
            ->contain(['Programs', 'ProgramTypes'])
            ->all();

        $deadlines = [];

        if (!$currentAcademicCalendar->isEmpty()) {
            $today = FrozenTime::now()->format('Y-m-d');
            $count = 0;

            foreach ($currentAcademicCalendar as $academicCalendar) {
                $gradeSubmissionEndDate = FrozenTime::parse($academicCalendar->grade_submission_end_date)->subDays(5);

                if ($gradeSubmissionEndDate->gt($today)) {
                    $deadlines[$count]['GradeSubmissionDeadline'] = $gradeSubmissionEndDate->i18nFormat(
                        'MMMM d, Y, h:mm a'
                    );
                } elseif (!empty($gradeSubmissionEndDate)) {
                    $deadlines[$count]['GradeSubmissionDeadline'] = $gradeSubmissionEndDate->i18nFormat(
                        'MMMM d, Y, h:mm a'
                    );
                }

                $count++;
            }
        }

        return $deadlines;
    }

    public function minimumCreditForStatus($studentId)
    {

        // Load required tables
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $generalSettingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');

        // Fetch student details with associated Curriculum
        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $studentId])
            ->contain(['Curriculums'])
            ->first();

        if (!$studentDetail) {
            return DEFAULT_MINIMUM_CREDIT_FOR_STATUS;
        }

        // Fetch general settings for the student’s program
        $settings = $generalSettingsTable->find()
            ->where([
                'program_id LIKE' => '%s:_:"' . $studentDetail->program_id . '"%',
                'program_type_id LIKE' => '%s:_:"' . $studentDetail->program_type_id . '"%',
            ])
            ->first();

        if (!$settings) {
            return DEFAULT_MINIMUM_CREDIT_FOR_STATUS;
        }

        // Determine credit system
        if (!empty($studentDetail->curriculum) && is_numeric($studentDetail->curriculum->id)) {
            if (strpos($studentDetail->curriculum->type_credit, 'ECTS') !== false) {
                return (int)round($settings->minimumCreditForStatus * CREDIT_TO_ECTS);
            } else {
                return $settings->minimumCreditForStatus;
            }
        }

        return DEFAULT_MINIMUM_CREDIT_FOR_STATUS;
    }

    public function maximumCreditPerSemester($studentId)
    {

        // Load required tables
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $generalSettingsTable = TableRegistry::getTableLocator()->get('GeneralSettings');

        // Fetch student details with associated Curriculum
        $studentDetail = $studentsTable->find()
            ->where(['Students.id' => $studentId])
            ->contain([
                'Curriculums' => function ($q) {

                    return $q->select(['id', 'name', 'type_credit']);
                }
            ])
            ->select(
                ['id', 'full_name', 'program_id', 'program_type_id', 'curriculum_id', 'department_id', 'college_id']
            )
            ->first();

        if (!$studentDetail) {
            return DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;
        }

        // Fetch general settings for the student’s program
        $settings = $generalSettingsTable->find()
            ->where([
                'program_id LIKE' => '%s:_:"' . $studentDetail->program_id . '"%',
                'program_type_id LIKE' => '%s:_:"' . $studentDetail->program_type_id . '"%',
            ])
            ->first();

        if (!$settings) {
            return DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;
        }

        // Determine credit system
        if (!empty($studentDetail->curriculum) && is_numeric($studentDetail->curriculum->id)) {
            if ($settings->maximumCreditPerSemester != 0) {
                if (strpos($studentDetail->curriculum->type_credit, 'ECTS') !== false) {
                    return (int)round($settings->maximumCreditPerSemester * CREDIT_TO_ECTS);
                } else {
                    return $settings->maximumCreditPerSemester;
                }
            } else {
                return DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;
            }
        }

        return DEFAULT_MAXIMUM_CREDIT_PER_SEMESTER;
    }


    public function getMostRecentAcademicCalenderForSMS($phonenumber)
    {

        // Load required tables
        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
        $academicCalendarsTable = TableRegistry::getTableLocator()->get('AcademicCalendars');

        // Fetch student details based on phone number
        $studentDetail = $studentsTable->find()
            ->where(['Students.phone_mobile' => $phonenumber])
            ->contain(['User'])
            ->first();

        if (!$studentDetail) {
            return "No registration date to be displayed for you.";
        }

        // Fetch year and academic year using student's id
        $yearAndAcademicYear = $sectionsTable->getStudentYearLevel($studentDetail->id);

        // Query to get the most recent academic calendar based on the student's program, department, and year level
        $recentAcademicCalendar = $academicCalendarsTable->find()
            ->where([
                'AcademicCalendars.academic_year' => $yearAndAcademicYear['academicyear'],
                'AcademicCalendars.program_id' => $studentDetail->program_id,
                'AcademicCalendars.department_id LIKE' => '%s:_:"' . $studentDetail->department_id . '"%',
                'AcademicCalendars.year_level_id LIKE' => '%s:_:"' . $yearAndAcademicYear['year'] . '"%',
                'AcademicCalendars.program_type_id' => $studentDetail->program_type_id,
            ])
            ->order(['AcademicCalendars.academic_year' => 'DESC', 'AcademicCalendars.semester' => 'DESC'])
            ->first();

        if ($recentAcademicCalendar) {
            return $this->formateAcademicCalendarForSMS($recentAcademicCalendar);
        }

        return "No registration/add/drop deadline defined for you.";
    }

    public function formateAcademicCalendarForSMS($academicCalender)
    {

        // Format the SMS message with correct academic calendar details
        $message =
            "Academic Year: " . $academicCalender->academic_year .
            " Semester: " . $academicCalender->semester .
            "\nRegistration Start: " . date(
                "F j,Y,g:i a",
                strtotime($academicCalender->course_registration_start_date)
            ) .
            "\nRegistration End: " . date("F j,Y,g:i a", strtotime($academicCalender->course_registration_end_date)) .
            "\nAdd Start: " . date("F j,Y,g:i a", strtotime($academicCalender->course_add_start_date)) .
            "\nAdd End: " . date(
                "F j,Y,g:i a",
                strtotime($academicCalender->course_add_end_date)
            ) . // Fixed the end date
            "\nDrop Start: " . date("F j,Y,g:i a", strtotime($academicCalender->course_drop_start_date)) .
            "\nDrop End: " . date("F j,Y,g:i a", strtotime($academicCalender->course_drop_end_date));

        return $message;
    }

    public function recentAcademicYearSchedule(
        $academicyear,
        $semester,
        $program_id,
        $program_type_id,
        $department_id,
        $year,
        $freshman = 0,
        $college_id = null
    ) {

        // Handle for freshman students
        if ($freshman == 0) {
            $conditions = array(
                'AcademicCalendar.academic_year' => $academicyear,
                'AcademicCalendar.semester' => $semester,
                'AcademicCalendar.program_id' => $program_id,
                'AcademicCalendar.department_id LIKE' => sprintf('%%s:_:"%s"%%', $department_id),
                'AcademicCalendar.year_level_id LIKE' => sprintf('%%s:_:"%s"%%', $year),
                'AcademicCalendar.program_type_id' => $program_type_id
            );
        } else {
            // For freshman, adjust the department and year level accordingly
            $department_id = 'pre_' . $college_id;  // Freshman department ID
            $year = '1st';  // Freshman year level

            $conditions = array(
                'AcademicCalendar.academic_year' => $academicyear,
                'AcademicCalendar.semester' => $semester,
                'AcademicCalendar.program_id' => $program_id,
                'AcademicCalendar.department_id LIKE' => sprintf('%%s:_:"%s"%%', $department_id),
                'AcademicCalendar.year_level_id LIKE' => sprintf('%%s:_:"%s"%%', $year),
                'AcademicCalendar.program_type_id' => $program_type_id
            );
        }

        // Fetch the recent academic calendar based on the conditions
        $recentAcademicCalendar = $this->find('first', array(
            'conditions' => $conditions,
            'order' => array('AcademicCalendar.academic_year DESC', 'AcademicCalendar.semester DESC'),
            'recursive' => -1
        ));

        return $recentAcademicCalendar;
    }

    public function getPublishedCourseGradeSubmissionDate($pid)
    {

        // Get PublishedCourse details
        $publishedCourseDetail = ClassRegistry::init('PublishedCourse')->find('first', array(
            'conditions' => array('PublishedCourse.id' => $pid),
            'contain' => array('YearLevel', 'Course')
        ));

        if (isset($publishedCourseDetail['PublishedCourse']) && !empty($publishedCourseDetail['PublishedCourse']) && $publishedCourseDetail['Course']['thesis'] == 1) {
            return date('Y-m-d', strtotime("+5 days"));
        }

        // Check if PublishedCourse exists
        if (!empty($publishedCourseDetail['PublishedCourse'])) {
            // Default values for department and year level if not set
            $year_level = isset($publishedCourseDetail['YearLevel']['name']) ? $publishedCourseDetail['YearLevel']['name'] : '1st';
            $department_id = isset($publishedCourseDetail['PublishedCourse']['department_id'])
                ? $publishedCourseDetail['PublishedCourse']['department_id']
                : 'pre_' . $publishedCourseDetail['PublishedCourse']['college_id'];

            // Fetch the academic calendar details based on the department and year level
            $gradeSubmissionDate = $this->find('first', array(
                'conditions' => array(
                    'AcademicCalendar.academic_year' => $publishedCourseDetail['PublishedCourse']['academic_year'],
                    'AcademicCalendar.semester' => $publishedCourseDetail['PublishedCourse']['semester'],
                    'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
                    'AcademicCalendar.department_id LIKE' => sprintf('%%s:_:"%s"%%', $department_id),
                    'AcademicCalendar.year_level_id LIKE' => sprintf('%%s:_:"%s"%%', $year_level),
                    'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
                ),
                'order' => array('AcademicCalendar.created DESC'),
                'recursive' => -1
            ));

            // If grade submission date is found, return it
            if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
                return $gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'];
            }
        }

        // If no grade submission date found, compute from the current academic year and semester
        App::import('Component', 'AcademicYear');
        $AcademicYear = new AcademicYearComponent(new ComponentRegistry());
        $current_acy_and_semester = $AcademicYear->current_acy_and_semester();

        $gradeSubmissionEnd = $AcademicYear->getAcademicYearBegainingDate(
            $current_acy_and_semester['academic_year'],
            $current_acy_and_semester['semester']
        );
        $days_to_add = DEFAULT_WEEK_COUNT_FOR_ONE_SEMESTER * 7;
        $deadlineConverted = date('Y-m-d', strtotime($gradeSubmissionEnd . ' + ' . $days_to_add . ' days'));

        return $deadlineConverted;
    }

    public function getFxPublishedCourseGradeSubmissionDate($pid)
    {

        // Get PublishedCourse details
        $publishedCourseDetail = ClassRegistry::init('PublishedCourse')->find('first', array(
            'conditions' => array('PublishedCourse.id' => $pid),
            'contain' => array('YearLevel', 'Course')
        ));

        // If course is thesis, return a default date
        if (isset($publishedCourseDetail['PublishedCourse']) && !empty($publishedCourseDetail['PublishedCourse']) && $publishedCourseDetail['Course']['thesis'] == 1) {
            return date('Y-m-d', strtotime("+5 days"));
        }

        // If PublishedCourse data is found
        if (!empty($publishedCourseDetail['PublishedCourse'])) {
            // Set default year_level and department_id if not available
            $year_level = isset($publishedCourseDetail['YearLevel']['name']) ? $publishedCourseDetail['YearLevel']['name'] : '1st';
            $department_id = isset($publishedCourseDetail['PublishedCourse']['department_id'])
                ? $publishedCourseDetail['PublishedCourse']['department_id']
                : 'pre_' . $publishedCourseDetail['PublishedCourse']['college_id'];

            // Fetch the grade submission date from AcademicCalendar
            $gradeSubmissionDate = $this->find('first', array(
                'conditions' => array(
                    'AcademicCalendar.academic_year' => $publishedCourseDetail['PublishedCourse']['academic_year'],
                    'AcademicCalendar.semester' => $publishedCourseDetail['PublishedCourse']['semester'],
                    'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
                    'AcademicCalendar.department_id LIKE' => sprintf('%%s:_:"%s"%%', $department_id),
                    'AcademicCalendar.year_level_id LIKE' => sprintf('%%s:_:"%s"%%', $year_level),
                    'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
                ),
                'order' => array('AcademicCalendar.created DESC'),
                'recursive' => -1
            ));

            // If grade submission date exists, return it
            if (!empty($gradeSubmissionDate['AcademicCalendar']['grade_fx_submission_end_date'])) {
                return $gradeSubmissionDate['AcademicCalendar']['grade_fx_submission_end_date'];
            } else {
                // If no date is found, return a fallback date (2 days later)
                return date('Y-m-d', strtotime("+2 days"));
            }
        } else {
            // If PublishedCourse not found, compute fallback deadline using current academic year
            App::import('Component', 'AcademicYear');
            $AcademicYear = new AcademicYearComponent(new ComponentRegistry());
            $current_acy_and_semester = $AcademicYear->current_acy_and_semester();

            // Calculate default grade submission date
            $gradeSubmissionEnd = $AcademicYear->getAcademicYearBegainingDate(
                $current_acy_and_semester['academic_year'],
                $current_acy_and_semester['semester']
            );
            $deadlineConverted = date('Y-m-d', strtotime($gradeSubmissionEnd . ' + 4 months'));

            return $deadlineConverted;
        }
    }

    // the below two function not converted to CakePHP 3.8
    public function getGradeSubmissionDate(
        $academicyear,
        $semester,
        $program_id,
        $program_type_id,
        $department_id,
        $year
    ) {

        $programID = null;
        $programTypeID = null;
        $departments = array();
        $colleges = array();
        $yearLevelName = null;
        $gradeSubmissionEndDate = null;

        if (isset($program_id) && !empty($program_id)) {
            $program_ids = explode('~', $program_id);
            if (count($program_ids) > 1) {
                $programID = $program_ids[1];
            } else {
                $programID = $program_id;
            }
        } else {
            $programID = $this->Program->find('list', array('fields' => array('id', 'id')));
        }

        if (isset($program_type_id) && !empty($program_type_id)) {
            $program_type_ids = explode('~', $program_type_id);
            if (count($program_type_ids) > 1) {
                $programTypeID = $program_type_ids[1];
            } else {
                $programTypeID = $program_type_id;
            }
        } else {
            $programTypeID = $this->ProgramType->find('list', array('fields' => array('id', 'id')));
        }

        if (isset($department_id) && !empty($department_id)) {
            $college_id = explode('~', $department_id);
            if (count($college_id) > 1) {
                $departments = ClassRegistry::init('Department')->find('all', array(
                    'conditions' => array(
                        'Department.college_id' => $college_id[1],
                        'Department.active' => 1,
                    ),
                    'contain' => array('College', 'YearLevel')
                ));

                $colleges = ClassRegistry::init('College')->find('all', array(
                    'conditions' => array(
                        'College.id' => $college_id[1],
                        'College.active' => 1
                    ),
                    'recursive' => -1
                ));
            } else {
                $departments = ClassRegistry::init('Department')->find('all', array(
                    'conditions' => array(
                        'Department.id' => $department_id
                    ),
                    'contain' => array('College', 'YearLevel')
                ));

                $colleges = ClassRegistry::init('College')->find('all', array(
                    'conditions' => array(
                        'College.id' => $departments[0]['College']['id']
                    ),
                    'recursive' => -1
                ));
            }
        }

        if (!empty($departments)) {
            foreach ($departments as $key => $value) {
                $yearLevel = array();

                if (!empty($year)) {
                    foreach ($value['YearLevel'] as $yykey => $yyvalue) {
                        if (!empty($year) && strcasecmp($year, $yyvalue['name']) == 0) {
                            $yearLevel[$yykey] = $yyvalue;
                        }
                    }
                } elseif (empty($year)) {
                    $yearLevel = $value['YearLevel'];
                }

                if (!empty($yearLevel)) {
                    foreach ($yearLevel as $key => $yvalue) {
                        $gradeSubmissionDate = $this->find('first', array(
                            'conditions' => array(
                                'AcademicCalendar.academic_year' => $academicyear,
                                'AcademicCalendar.semester' => $semester,
                                'AcademicCalendar.program_id' => $programID,
                                'AcademicCalendar.department_id like' => '%s:_:"' . $value['Department']['id'] . '"%',
                                'AcademicCalendar.year_level_id like' => '%s:_:"' . $yvalue['name'] . '"%',
                                'AcademicCalendar.program_type_id' => $programTypeID
                            ),
                            'order' => array(
                                'AcademicCalendar.academic_year DESC',
                                'AcademicCalendar.semester DESC'
                            ),
                            'recursive' => -1
                        ));

                        if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
                            $daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
                                $gradeSubmissionDate['AcademicCalendar']['id'],
                                $yvalue['name'],
                                $value['Department']['id'],
                                $programID,
                                $programTypeID,
                                'grade_submission'
                            );

                            if ($daysAdded) {
                                return date(
                                    'Y-m-d',
                                    strtotime(
                                        $gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'] . ' +' . $daysAdded . ' days '
                                    )
                                );
                            }
                            return $gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'];
                        }
                    }
                }
            }
        }

        if (!empty($colleges)) {
            $yvalue_fresh = '1st';
            foreach ($colleges as $key => $value) {
                $fresh = 'pre_' . $value['College']['id'];

                $gradeSubmissionDate = $this->find('first', array(
                    'conditions' => array(
                        'AcademicCalendar.academic_year' => $academicyear,
                        'AcademicCalendar.semester' => $semester,
                        'AcademicCalendar.program_id' => $programID,
                        //'AcademicCalendar.department_id like' => '%s:_:"pre_' . $value['College']['id'] . '"%',
                        //'AcademicCalendar.year_level_id like' => '%s:_:"1st"%',
                        'AcademicCalendar.department_id like' => '%s:_:"' . $fresh . '"%',
                        'AcademicCalendar.year_level_id like' => '%s:_:"' . $yvalue_fresh . '"%',
                        'AcademicCalendar.program_type_id' => $programTypeID
                    ),
                    'order' => array(
                        'AcademicCalendar.academic_year DESC',
                        'AcademicCalendar.semester DESC'
                    ),
                    'recursive' => -1
                ));

                if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
                    $daysAdded = $this->ExtendingAcademicCalendar->getExtendedDays(
                        $gradeSubmissionDate['AcademicCalendar']['id'],
                        $yvalue_fresh,
                        $fresh,
                        $programID,
                        $programTypeID,
                        'grade_submission'
                    );

                    if ($daysAdded) {
                        return date(
                            'Y-m-d',
                            strtotime(
                                $gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'] . ' +' . $daysAdded . ' days '
                            )
                        );
                    }

                    return $gradeSubmissionDate['AcademicCalendar']['grade_submission_end_date'];
                }
            }
        }

        App::import('Component', 'AcademicYear');
        $AcademicYear = new AcademicYearComponent(new ComponentRegistry());

        $gradeSumissionEnd = $AcademicYear->getAcademicYearBegainingDate($academicyear, $semester);
        $deadlineConverted = date('Y-m-d', strtotime($gradeSumissionEnd . ' + 4 months'));
        return $deadlineConverted;
    }

    public function getLastGradeChangeDate($pid)
    {

        $gradeSubmissionEndDate = null;

        $publishedCourseDetail = ClassRegistry::init('PublishedCourse')->find(
            'first',
            array('conditions' => array('PublishedCourse.id' => $pid), 'contain' => array('YearLevel', 'Course'))
        );
        $nextAcademicYear = ClassRegistry::init('StudentExamStatus')->getNextSemster(
            $publishedCourseDetail['PublishedCourse']['academic_year'],
            $publishedCourseDetail['PublishedCourse']['semester']
        );

        $publishedCourseAcademicYear['academic_year'] = $publishedCourseDetail['PublishedCourse']['academic_year'];
        $publishedCourseAcademicYear['semester'] = $publishedCourseDetail['PublishedCourse']['semester'];

        $listofAcademicYearToCheck[] = $nextAcademicYear;

        $listofAcademicYearToCheck[] = ClassRegistry::init('StudentExamStatus')->getNextSemster(
            $nextAcademicYear['academic_year'],
            $nextAcademicYear['semester']
        );


        if (isset($publishedCourseDetail['PublishedCourse']) && !empty($publishedCourseDetail['PublishedCourse']) && $publishedCourseDetail['Course']['thesis'] == 1) {
            return date('Y-m-d', strtotime("+5 days"));
            //return date('Y-m-d H:i:s');
        }

        $deadlineConverted = null;

        if (!empty($listofAcademicYearToCheck)) {
            foreach ($listofAcademicYearToCheck as $kk => $kpv) {
                if (!empty($publishedCourseDetail['PublishedCourse'])) {
                    if (isset($publishedCourseDetail['YearLevel']['name']) && !empty($publishedCourseDetail['YearLevel']['name'])) {
                        $gradeSubmissionDate = $this->find('first', array(
                            'conditions' => array(
                                'AcademicCalendar.academic_year' => $kpv['academic_year'],
                                'AcademicCalendar.semester' => $kpv['semester'],
                                'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
                                'AcademicCalendar.department_id like ' => '%s:_:"' . $publishedCourseDetail['PublishedCourse']['department_id'] . '"%',
                                'AcademicCalendar.year_level_id like' => '%s:_:"' . $publishedCourseDetail['YearLevel']['name'] . '"%',
                                'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
                            ),
                            'order' => array(
                                'AcademicCalendar.created DESC'
                            ),
                            'recursive' => -1
                        ));
                    } else {
                        if (!isset($publishedCourseDetail['YearLevel']['name'])) {
                            $year_level = '1st';
                            $department_id = 'pre_' . $publishedCourseDetail['PublishedCourse']['college_id'];
                        }

                        $gradeSubmissionDate = $this->find('first', array(
                            'conditions' => array(
                                'AcademicCalendar.academic_year' => $kpv['academic_year'],
                                'AcademicCalendar.semester' => $kpv['semester'],
                                'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
                                'AcademicCalendar.department_id like ' => '%s:_:"' . $department_id . '"%',
                                'AcademicCalendar.year_level_id like' => '%s:_:"' . $year_level . '"%',
                                'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
                            ),
                            'order' => array(
                                'AcademicCalendar.created DESC'
                            ),
                            'recursive' => -1
                        ));
                    }

                    if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
                        return $gradeSubmissionDate['AcademicCalendar']['course_registration_start_date'];
                    }
                } else {
                    App::import('Component', 'AcademicYear');
                    $AcademicYear = new AcademicYearComponent(new ComponentRegistry());

                    $gradeSumissionEnd = $AcademicYear->getAcademicYearBegainingDate(
                        $kpv['academic_year'],
                        $kpv['semester']
                    );
                    $deadlineConverted = date('Y-m-d', strtotime($gradeSumissionEnd . ' + 4 months'));
                    //return $deadlineConverted;
                }
            }
        }
        // nothing is defined for next academic year, please check published course academic year
        $listofAcademicYearToCheckk[] = $publishedCourseAcademicYear;

        if (!empty($listofAcademicYearToCheckk)) {
            foreach ($listofAcademicYearToCheckk as $kk => $kpv) {
                if (!empty($publishedCourseDetail['PublishedCourse'])) {
                    if (isset($publishedCourseDetail['YearLevel']['name']) && !empty($publishedCourseDetail['YearLevel']['name'])) {
                        $gradeSubmissionDate = $this->find('first', array(
                            'conditions' => array(
                                'AcademicCalendar.academic_year' => $kpv['academic_year'],
                                'AcademicCalendar.semester' => $kpv['semester'],
                                'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
                                'AcademicCalendar.department_id like ' => '%s:_:"' . $publishedCourseDetail['PublishedCourse']['department_id'] . '"%',
                                'AcademicCalendar.year_level_id like' => '%s:_:"' . $publishedCourseDetail['YearLevel']['name'] . '"%',
                                'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
                            ),
                            'order' => array(
                                'AcademicCalendar.created DESC'
                            ),
                            'recursive' => -1
                        ));
                    } else {
                        if (!isset($publishedCourseDetail['YearLevel']['name'])) {
                            $year_level = '1st';
                            $department_id = 'pre_' . $publishedCourseDetail['PublishedCourse']['college_id'];
                        }

                        $gradeSubmissionDate = $this->find('first', array(
                            'conditions' => array(
                                'AcademicCalendar.academic_year' => $kpv['academic_year'],
                                'AcademicCalendar.semester' => $kpv['semester'],
                                'AcademicCalendar.program_id' => $publishedCourseDetail['PublishedCourse']['program_id'],
                                'AcademicCalendar.department_id like ' => '%s:_:"' . $department_id . '"%',
                                'AcademicCalendar.year_level_id like' => '%s:_:"' . $year_level . '"%',
                                'AcademicCalendar.program_type_id' => $publishedCourseDetail['PublishedCourse']['program_type_id']
                            ),
                            'order' => array(
                                'AcademicCalendar.created DESC'
                            ),
                            'recursive' => -1
                        ));
                    }

                    if (!empty($gradeSubmissionDate['AcademicCalendar'])) {
                        return $gradeSubmissionDate['AcademicCalendar']['course_registration_start_date'];
                    }
                }
            }
        }

        return $deadlineConverted;
    }
}
