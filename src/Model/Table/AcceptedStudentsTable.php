<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;
use Cake\I18n\Time;

/**
 * AcceptedStudents Table
 */
class AcceptedStudentsTable extends Table
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

        $this->setTable('accepted_students');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        // Placeholder for Logable behavior (replace with actual implementation if available)
        // $this->addBehavior('Logable', ['change' => 'full', 'foreignKey' => 'foreign_key']);

        // BelongsTo Associations
        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Curriculums', [
            'foreignKey' => 'curriculum_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
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
        $this->belongsTo('Regions', [
            'foreignKey' => 'region_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Zones', [
            'foreignKey' => 'zone_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Woredas', [
            'foreignKey' => 'woreda_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Campuses', [
            'foreignKey' => 'campus_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('Disabilities', [
            'foreignKey' => 'disability_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('ForeignPrograms', [
            'foreignKey' => 'foreign_program_id',
            'joinType' => 'LEFT',
        ]);
        $this->belongsTo('PlacementTypes', [
            'foreignKey' => 'placement_type_id',
            'joinType' => 'LEFT',
        ]);

        // HasMany Associations
        $this->hasMany('Preferences', [
            'foreignKey' => 'accepted_student_id',
            'dependent' => false,
        ]);
        $this->hasMany('PlacementEntranceExamResultEntries', [
            'foreignKey' => 'accepted_student_id',
            'dependent' => false,
        ]);
        $this->hasMany('PlacementParticipatingStudents', [
            'foreignKey' => 'accepted_student_id',
            'dependent' => false,
        ]);
        $this->hasMany('PlacementPreferences', [
            'foreignKey' => 'accepted_student_id',
            'dependent' => false,
        ]);
        $this->hasMany('DormitoryAssignments', [
            'foreignKey' => 'accepted_student_id',
            'dependent' => false,
        ]);
        $this->hasMany('MealHallAssignments', [
            'foreignKey' => 'accepted_student_id',
            'dependent' => false,
        ]);

        // HasOne Association
        $this->hasOne('Students', [
            'foreignKey' => 'accepted_student_id',
            'dependent' => true,
        ]);
    }

    /**
     * Default validation rules.
     *
     * @param Validator $validator Validator instance.
     * @return Validator
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->allowEmptyString('id', null, 'create')
            ->scalar('first_name')
            ->requirePresence('first_name', 'create')
            ->notEmptyString('first_name', 'Please enter first name.')
            ->scalar('middle_name')
            ->requirePresence('middle_name', 'create')
            ->notEmptyString('middle_name', 'Please enter middle name.')
            ->scalar('last_name')
            ->requirePresence('last_name', 'create')
            ->notEmptyString('last_name', 'Please enter last name.')
            ->numeric('college_id')
            ->requirePresence('college_id', 'create')
            ->notEmptyString('college_id', 'Please select college.')
            ->scalar('academicyear')
            ->requirePresence('academicyear', 'create')
            ->notEmptyString('academicyear', 'Please select academic year.')
            ->numeric('program_id')
            ->requirePresence('program_id', 'create')
            ->notEmptyString('program_id', 'Please select program.')
            ->numeric('program_type_id')
            ->requirePresence('program_type_id', 'create')
            ->notEmptyString('program_type_id', 'Program type is required field.')
            ->numeric('EHEECE_total_results')
            ->greaterThanOrEqual('EHEECE_total_results', 0, 'Must be at least 0.')
            ->requirePresence('EHEECE_total_results', 'create')
            ->notEmptyString('EHEECE_total_results', 'EHEECE is required field.')
            ->scalar('studentnumber')
            ->requirePresence('studentnumber', INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE ? 'create' : 'update')
            ->notEmptyString('studentnumber', 'Student ID Number is required.', INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE ? 'create' : 'update')
            ->add('studentnumber', 'isUniqueStudentNumber', [
                'rule' => function ($value, $context) {
                    $conditions = ['AcceptedStudents.studentnumber LIKE' => trim($value) . '%'];
                    if (!empty($context['data']['id'])) {
                        $conditions['AcceptedStudents.id !='] = $context['data']['id'];
                    }
                    return $this->find()->where($conditions)->count() === 0;
                },
                'message' => 'The provided student number is taken. Please use another one.',
                'on' => INCLUDE_STUDENT_NUMBER_IN_IMPORT_TEMPLATE_FILE ? 'create' : 'update'
            ])
            ->scalar('sex')
            ->requirePresence('sex', 'create')
            ->notEmptyString('sex', 'Please select sex.')
            ->numeric('region_id')
            ->requirePresence('region_id', 'create')
            ->notEmptyString('region_id', 'Region is required field.')
            ->numeric('zone_id')
            ->requirePresence('zone_id', 'update')
            ->notEmptyString('zone_id', 'Zone is required field.', 'update')
            ->numeric('woreda_id')
            ->requirePresence('woreda_id', 'update')
            ->notEmptyString('woreda_id', 'Woreda is required field.', 'update');

        return $validator;
    }

    /**
     * Returns a rules checker object that will be used for validating
     * application integrity.
     *
     * @param RulesChecker $rules The rules object to be modified.
     * @return RulesChecker
     */
    public function buildRules(RulesChecker $rules)
    {
        $rules->add($rules->existsIn(['college_id'], 'Colleges'), [
            'errorField' => 'college_id',
            'message' => 'The specified college does not exist.'
        ]);
        $rules->add($rules->existsIn(['program_id'], 'Programs'), [
            'errorField' => 'program_id',
            'message' => 'The specified program does not exist.'
        ]);
        $rules->add($rules->existsIn(['program_type_id'], 'ProgramTypes'), [
            'errorField' => 'program_type_id',
            'message' => 'The specified program type does not exist.'
        ]);
        $rules->add($rules->existsIn(['region_id'], 'Regions'), [
            'errorField' => 'region_id',
            'message' => 'The specified region does not exist.'
        ]);
        return $rules;
    }

    /**
     * Updates accepted student college based on section college
     *
     * @param int $collegeId College ID
     * @param string $academicYear Academic year
     * @param int $programId Program ID
     * @param int $programTypeId Program type ID
     * @return void
     */
    public function updateAcceptedStudentCollege($collegeId, $academicYear, $programId = 1, $programTypeId = 1)
    {
        $acceptedStudents = $this->find()
            ->where([
                'AcceptedStudents.academicyear' => $academicYear,
                'AcceptedStudents.original_college_id' => $collegeId,
                'AcceptedStudents.program_id' => $programId,
                'AcceptedStudents.program_type_id' => $programTypeId,
                'AcceptedStudents.campus_id !=' => 0
            ])
            ->contain(['Students'])
            ->toArray();

        $studentsTable = TableRegistry::getTableLocator()->get('Students');
        $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');

        foreach ($acceptedStudents as $acceptedStudent) {
            if (!empty($acceptedStudent->student->id)) {
                $sectionCollege = $studentsSectionsTable->find()
                    ->where(['StudentsSections.student_id' => $acceptedStudent->student->id])
                    ->contain(['Sections'])
                    ->first();

                if (!empty($sectionCollege->section->college_id)) {
                    $acceptedStudent->college_id = $sectionCollege->section->college_id;
                    $this->save($acceptedStudent);

                    $student = $studentsTable->get($acceptedStudent->student->id);
                    $student->college_id = $sectionCollege->section->college_id;
                    $studentsTable->save($student);
                }
            }
        }
    }

    /**
     * Retrieves recent accepted students
     *
     * @param int|null $collegeId College ID
     * @param string|null $academicYear Academic year
     * @return array|null Accepted students or null if invalid
     */
    public function getRecentAcceptedStudent($collegeId = null, $academicYear = null)
    {
        if ($collegeId && $academicYear) {
            return $this->find()
                ->where([
                    'AcceptedStudents.college_id' => $collegeId,
                    'AcceptedStudents.academicyear' => $academicYear
                ])
                ->limit(100)
                ->toArray();
        }
        return null;
    }

    /**
     * Retrieves accepted student by ID with related data
     *
     * @param int|null $id Accepted student ID
     * @return array|null Student data or null if not found
     */
    public function readAllById($id = null)
    {
        if ($id) {
            $data = $this->find()
                ->where(['AcceptedStudents.id' => $id])
                ->contain(['Programs', 'ProgramTypes', 'Colleges', 'Departments'])
                ->first();
            return $data ? $data->toArray() : null;
        }
        return null;
    }

    /**
     * Counts accepted students with valid student numbers
     *
     * @param int|null $collegeId College ID
     * @param string|null $year Academic year
     * @param int|null $programId Program ID
     * @param int|null $programTypeId Program type ID
     * @return int Count of students
     */
    public function countId($collegeId = null, $year = null, $programId = null, $programTypeId = null)
    {
        if ($collegeId && $year && $programId && $programTypeId) {
            return $this->find()
                ->where([
                    'AcceptedStudents.academicyear LIKE' => $year . '%',
                    'AcceptedStudents.college_id' => $collegeId,
                    'AcceptedStudents.program_id' => $programId,
                    'AcceptedStudents.program_type_id' => $programTypeId,
                    'AcceptedStudents.studentnumber NOT IN' => ['', '0', null]
                ])
                ->count();
        }
        return 0;
    }

    /**
     * Retrieves summary of students without IDs
     *
     * @param string|null $academicYear Academic year
     * @return array|null Summary data or null if invalid
     */
    public function getIdlessStudentSummary($academicYear = null)
    {
        if (!$academicYear) {
            return null;
        }

        $colleges = $this->Colleges->find('list')->where(['Colleges.active' => 1])->toArray();
        $programs = $this->Programs->find('list')->where(['Programs.active' => 1])->toArray();
        $programTypes = $this->ProgramTypes->find('list')->where(['ProgramTypes.active' => 1])->toArray();

        $data = [];
        foreach ($colleges as $collegeId => $collegeName) {
            foreach ($programs as $programId => $programName) {
                foreach ($programTypes as $programTypeId => $programTypeName) {
                    $data[$collegeName][$programName][$programTypeName] = $this->find()
                        ->where([
                            'AcceptedStudents.academicyear' => $academicYear,
                            'AcceptedStudents.college_id' => $collegeId,
                            'AcceptedStudents.program_id' => $programId,
                            'AcceptedStudents.program_type_id' => $programTypeId,
                            'OR' => [
                                'AcceptedStudents.studentnumber IS NULL',
                                'AcceptedStudents.studentnumber' => ''
                            ]
                        ])
                        ->count();
                }
            }
        }

        return $data;
    }

    /**
     * Checks if an accepted student is approved
     *
     * @param int|null $acceptedStudentId Accepted student ID
     * @return bool True if approved, false otherwise
     */
    public function isApproved($acceptedStudentId = null)
    {
        if ($acceptedStudentId) {
            $student = $this->find()
                ->where(['AcceptedStudents.id' => $acceptedStudentId])
                ->first();
            return !empty($student) && $student->Placement_Approved_By_Department;
        }
        return false;
    }

    /**
     * Checks if placement settings are recorded
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @return bool True if recorded, false otherwise
     */
    public function checkPlacementSettingIsRecorded($academicYear = null, $collegeId = null)
    {
        $placementsResultsCriteriaTable = TableRegistry::getTableLocator()->get('PlacementsResultsCriteria');

        $settings = [
            'placement_result_criteria' => $placementsResultsCriteriaTable->isPlacementResultRecorded($academicYear, $collegeId),
            'ReservedPlace' => $placementsResultsCriteriaTable->isReservedPlaceRecorded($academicYear, $collegeId),
            'participating_department' => $placementsResultsCriteriaTable->isParticipationgDepartmentRecorded($academicYear, $collegeId)
        ];

        if (empty($settings['placement_result_criteria'])) {
            $this->validationErrors['placement_result_criteria'] = ['Please record placement result criteria before running auto placement.'];
            return false;
        }
        if (empty($settings['ReservedPlace'])) {
            $this->validationErrors['reserved_place'] = ['Please record reserved place for each department you want to participate in auto placement before running the auto placement.'];
            return false;
        }
        if (empty($settings['participating_department'])) {
            $this->validationErrors['participating_department'] = ['Please record participating department you want in auto placement before running the auto placement.'];
            return false;
        }

        return true;
    }

    /**
     * Checks if preference deadline has passed
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @return bool True if passed, false otherwise
     */
    public function isPreferenceDeadlinePassed($academicYear = null, $collegeId = null)
    {
        if (!$academicYear || !$collegeId) {
            return false;
        }

        $preferenceDeadlinesTable = TableRegistry::getTableLocator()->get('PreferenceDeadlines');
        $count = $preferenceDeadlinesTable->find()
            ->where([
                'PreferenceDeadlines.academicyear LIKE' => $academicYear . '%',
                'PreferenceDeadlines.college_id' => $collegeId,
                'PreferenceDeadlines.deadline >' => Time::now()->format('Y-m-d H:i:s')
            ])
            ->count();

        if ($count > 0) {
            $this->validationErrors['preferencedeadline'] = ['The deadline for filling the preference has not passed. Please wait until the deadline is passed to run the auto placement.'];
            return false;
        }

        return true;
    }

    /**
     * Counts students not assigned to a department
     *
     * @param int|null $collegeId College ID
     * @param string|null $academicYear Academic year
     * @return int Count of students
     */
    public function totalNoAssignedToDepartment($collegeId = null, $academicYear = null)
    {
        if (!$collegeId || !$academicYear) {
            return 0;
        }

        return $this->find()
            ->where([
                'OR' => [
                    ['AcceptedStudents.department_id' => ['', 0]],
                    ['AcceptedStudents.department_id IS NULL'],
                    ['AcceptedStudents.placementtype' => [null, CANCELLED_PLACEMENT]]
                ],
                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                'AcceptedStudents.college_id' => $collegeId,
                'AcceptedStudents.placementtype IS NULL',
                'AcceptedStudents.Placement_Approved_By_Department' => 0
            ])
            ->count();
    }

    /**
     * Retrieves departments requested by privileged students (old version)
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @return array Department order by weight
     */
    public function getListOfDepartmentRequestedByPrivilegedStudentMostOld($academicYear = null, $collegeId = null)
    {
        if (!$academicYear || !$collegeId) {
            return [];
        }

        $participatingDepartmentsTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        $region = $participatingDepartmentsTable->find()
            ->where([
                'ParticipatingDepartments.academic_year LIKE' => $academicYear . '%',
                'ParticipatingDepartments.college_id' => $collegeId
            ])
            ->first();

        $privilegedConditions = empty($region->developing_regions_id) ? [
            'OR' => [
                'AcceptedStudents.sex' => 'female',
                'AcceptedStudents.disability IS NOT NULL',
                'AcceptedStudents.disability !=' => ''
            ]
        ] : [
            'OR' => [
                'AcceptedStudents.region_id IN' => [$region->developing_regions_id],
                'AcceptedStudents.sex' => 'female',
                'AcceptedStudents.disability IS NOT NULL',
                'AcceptedStudents.disability !=' => ''
            ]
        ];

        $preferenceMatrix = $this->Preferences->find()
            ->select([
                'Preferences.department_id',
                'Preferences.preferences_order',
                'student_count' => 'COUNT(Preferences.accepted_student_id)'
            ])
            ->where([
                'Preferences.academicyear LIKE' => $academicYear . '%',
                'Preferences.college_id' => $collegeId,
                'OR' => [
                    'AcceptedStudents.department_id IS NULL',
                    'AcceptedStudents.department_id' => ['', 0]
                ],
                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                'AcceptedStudents.college_id' => $collegeId,
                'OR' => [
                    'AcceptedStudents.placementtype IS NULL',
                    'AcceptedStudents.placementtype' => CANCELLED_PLACEMENT
                ],
                $privilegedConditions
            ])
            ->group(['Preferences.department_id', 'Preferences.preferences_order'])
            ->order(['Preferences.department_id', 'Preferences.preferences_order'])
            ->toArray();

        $matrix = [];
        foreach ($preferenceMatrix as $pref) {
            $matrix[$pref->department_id][$pref->preferences_order] = $pref->student_count;
        }

        $weight = [];
        $count = count($matrix);
        for ($i = 1; $i <= $count; $i++) {
            $weight[$i] = $count--;
        }

        $departmentsPrivilegedOrder = [];
        foreach ($matrix as $deptId => $prefs) {
            $sum = 0;
            $totalStudents = array_sum($prefs);
            foreach ($prefs as $prefKey => $numStudents) {
                if (isset($weight[$prefKey])) {
                    $sum += $weight[$prefKey] * $numStudents;
                }
            }
            $departmentsPrivilegedOrder[$deptId]['weight'] = $sum / ($totalStudents ?: 1);
        }

        uasort($departmentsPrivilegedOrder, [$this, 'compare']);

        $departmentsWithoutPrivilegedQuota = $participatingDepartmentsTable->find()
            ->select(['ParticipatingDepartments.department_id'])
            ->where([
                'ParticipatingDepartments.academic_year LIKE' => $academicYear . '%',
                'ParticipatingDepartments.college_id' => $collegeId,
                'OR' => [
                    ['ParticipatingDepartments.female' => 0],
                    ['ParticipatingDepartments.female IS NULL'],
                    ['ParticipatingDepartments.female' => '']
                ],
                'OR' => [
                    ['ParticipatingDepartments.disability' => 0],
                    ['ParticipatingDepartments.disability IS NULL'],
                    ['ParticipatingDepartments.disability' => '']
                ],
                'OR' => [
                    ['ParticipatingDepartments.regions' => 0],
                    ['ParticipatingDepartments.regions IS NULL'],
                    ['ParticipatingDepartments.regions' => '']
                ]
            ])
            ->toArray();

        $mergedDepartmentOrder = [];
        foreach ($departmentsWithoutPrivilegedQuota as $dept) {
            $mergedDepartmentOrder[$dept->department_id]['weight'] = 10000000;
        }

        foreach ($departmentsPrivilegedOrder as $deptId => $value) {
            if (!array_key_exists($deptId, $mergedDepartmentOrder)) {
                $mergedDepartmentOrder[$deptId] = $value;
            }
        }

        $participatingDepartments = $participatingDepartmentsTable->find()
            ->select(['ParticipatingDepartments.department_id'])
            ->where([
                'ParticipatingDepartments.academic_year LIKE' => $academicYear . '%',
                'ParticipatingDepartments.college_id' => $collegeId
            ])
            ->toArray();

        foreach ($participatingDepartments as $dept) {
            if (!isset($mergedDepartmentOrder[$dept->department_id])) {
                $mergedDepartmentOrder[$dept->department_id]['weight'] = 10000;
            }
        }

        return $mergedDepartmentOrder;
    }

    /**
     * Retrieves departments requested by privileged students
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @return array Department order by weight
     */
    public function getListOfDepartmentRequestedByPrivilegedStudentMost($academicYear = null, $collegeId = null)
    {
        if (!$academicYear || !$collegeId) {
            return [];
        }

        $preferenceMatrix = $this->Preferences->find()
            ->select([
                'Preferences.department_id',
                'Preferences.preferences_order',
                'student_count' => 'COUNT(Preferences.accepted_student_id)'
            ])
            ->where([
                'Preferences.academicyear LIKE' => $academicYear . '%',
                'Preferences.college_id' => $collegeId,
                'OR' => [
                    'AcceptedStudents.department_id IS NULL',
                    'AcceptedStudents.department_id' => ['', 0]
                ],
                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                'AcceptedStudents.college_id' => $collegeId,
                'OR' => [
                    'AcceptedStudents.placementtype IS NULL',
                    'AcceptedStudents.placementtype' => CANCELLED_PLACEMENT
                ]
            ])
            ->group(['Preferences.department_id', 'Preferences.preferences_order'])
            ->order(['Preferences.department_id', 'Preferences.preferences_order'])
            ->toArray();

        $matrix = [];
        foreach ($preferenceMatrix as $pref) {
            $matrix[$pref->department_id][$pref->preferences_order] = $pref->student_count;
        }

        $departmentCapacity = TableRegistry::getTableLocator()->get('ParticipatingDepartments')->find()
            ->select(['ParticipatingDepartments.department_id', 'ParticipatingDepartments.number'])
            ->where([
                'ParticipatingDepartments.academic_year LIKE' => $academicYear . '%',
                'ParticipatingDepartments.college_id' => $collegeId
            ])
            ->toArray();

        $weight = [];
        $count = count($matrix);
        for ($i = 1; $i <= $count; $i++) {
            $weight[$i] = $count--;
        }

        $departmentsPrivilegedOrder = [];
        foreach ($matrix as $deptId => $prefs) {
            $sum = 0;
            $totalStudents = array_sum($prefs);
            foreach ($prefs as $prefKey => $numStudents) {
                if (isset($weight[$prefKey])) {
                    $sum += $weight[$prefKey] * $numStudents;
                }
            }
            $capacity = 1;
            foreach ($departmentCapacity as $dept) {
                if ($dept->department_id == $deptId) {
                    $capacity = $dept->number;
                    break;
                }
            }
            $departmentsPrivilegedOrder[$deptId]['weight'] = $sum / $capacity;
        }

        uasort($departmentsPrivilegedOrder, [$this, 'compare']);
        return $departmentsPrivilegedOrder;
    }

    /**
     * Comparison function for sorting
     *
     * @param array $x First element
     * @param array $y Second element
     * @return bool True if x < y
     */
    public function compare($x, $y)
    {
        return $x['weight'] < $y['weight'];
    }

    /**
     * Adjusts allocation based on availability
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @param string|null $resultType Result type
     * @param int|null $departmentId Department ID
     * @param array $reservedQuotaNumber Reserved quota data
     * @return array Adjusted quota
     */
    public function checkAndAdjustAllocationWithAvailability($academicYear = null, $collegeId = null, $resultType = null, $departmentId = null, $reservedQuotaNumber = [])
    {
        if (empty($reservedQuotaNumber)) {
            return [];
        }

        do {
            $recheck = false;
            foreach ($reservedQuotaNumber as $resultCriteriaId => &$allocation) {
                if ($allocation['reservedquota'] > $allocation['available']) {
                    $gap = $allocation['reservedquota'] - $allocation['available'];
                    $allocation['reservedquota'] -= $gap;
                    $recheck = true;
                    $allocation['adjusted'] = 1;

                    $reservedSum = array_sum(array_column(array_filter($reservedQuotaNumber, function ($v) {
                        return !$v['adjusted'];
                    }), 'reservedquota'));

                    $gapDistributionSum = 0;
                    $maxReservedQuota = ['max_quota' => 0, 'max_index' => 0];

                    if ($reservedSum > 0) {
                        foreach ($reservedQuotaNumber as $id => &$alloc) {
                            if (!$alloc['adjusted']) {
                                $increment = round($gap * ($alloc['reservedquota'] / $reservedSum));
                                $gapDistributionSum += $increment;
                                $alloc['reservedquota'] += $increment;
                                if ($alloc['reservedquota'] >= $maxReservedQuota['max_quota']) {
                                    $maxReservedQuota = ['max_quota' => $alloc['reservedquota'], 'max_index' => $id];
                                }
                            }
                        }
                    }

                    if ($gapDistributionSum != $gap && $maxReservedQuota['max_index']) {
                        $reservedQuotaNumber[$maxReservedQuota['max_index']]['reservedquota'] += ($gap - $gapDistributionSum);
                    } elseif ($gap - $gapDistributionSum > 0) {
                        foreach ($reservedQuotaNumber as $id => &$alloc) {
                            if ($alloc['reservedquota'] == 0 && !$alloc['adjusted'] && $alloc['available'] > 0) {
                                $alloc['reservedquota'] = min($alloc['available'], $gap - $gapDistributionSum);
                                $gapDistributionSum += $alloc['reservedquota'];
                                if ($gap - $gapDistributionSum <= 0) {
                                    break;
                                }
                            }
                        }
                    }
                }
            }
        } while ($recheck);

        return $reservedQuotaNumber;
    }

    /**
     * Adjusts privileged quota
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @param string|null $resultType Result type
     * @param int|null $departmentId Department ID
     * @param array $adjustedPrivilegedQuota Adjusted privileged quota
     * @param array $reservedQuotaNumber Reserved quota data
     * @return array Merged quota data
     */
    public function checkAndAdjustPrivilegedQuota($academicYear = null, $collegeId = null, $resultType = null, $departmentId = null, $adjustedPrivilegedQuota = [], $reservedQuotaNumber = [])
    {
        if (!$academicYear || !$collegeId || empty($adjustedPrivilegedQuota)) {
            return [[], []];
        }

        $participatingDepartmentsTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        $numParticipatingDepartments = $participatingDepartmentsTable->find()
            ->where([
                'ParticipatingDepartments.college_id' => $collegeId,
                'ParticipatingDepartments.academic_year' => $academicYear
            ])
            ->count();

        foreach ($adjustedPrivilegedQuota as $privilegeType => &$quota) {
            $privilegedCondition = null;
            switch (strtolower($privilegeType)) {
                case 'female':
                    $privilegedCondition = 'AcceptedStudents.sex = :sex';
                    break;
                case 'disability':
                    $privilegedCondition = 'AcceptedStudents.disability IS NOT NULL';
                    break;
                case 'regions':
                    $region = $participatingDepartmentsTable->find()
                        ->where([
                            'ParticipatingDepartments.college_id' => $collegeId,
                            'ParticipatingDepartments.academic_year' => $academicYear
                        ])
                        ->first();
                    if (empty($region->developing_regions_id)) {
                        continue 2; // Skip to next privilege type
                    }
                    $privilegedCondition = 'AcceptedStudents.region_id IN (:regions)';
                    break;
                default:
                    continue 2;
            }

            $sumAvailableStudents = 0;
            if ($numParticipatingDepartments && $quota) {
                for ($i = 1; $i <= $numParticipatingDepartments; $i++) {
                    $query = $this->Preferences->find()
                        ->select(['Preferences.accepted_student_id', 'Preferences.department_id'])
                        ->where([
                            'Preferences.academicyear LIKE' => $academicYear . '%',
                            'Preferences.college_id' => $collegeId,
                            'Preferences.department_id' => $departmentId,
                            'Preferences.preferences_order' => $i,
                            'AcceptedStudents.college_id' => $collegeId,
                            'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                            'OR' => [
                                'AcceptedStudents.department_id IS NULL',
                                'AcceptedStudents.department_id' => ['', 0]
                            ]
                        ]);

                    if ($privilegedCondition) {
                        $query->andWhere($privilegedCondition);
                        if ($privilegeType === 'female') {
                            $query->bind(':sex', 'female', 'string');
                        } elseif ($privilegeType === 'regions') {
                            $query->bind(':regions', $region->developing_regions_id, 'string');
                        }
                    }

                    $students = $query->toArray();

                    if ($i == 1) {
                        $sumAvailableStudents += count($students);
                        if ($sumAvailableStudents >= $quota) {
                            break;
                        }
                        continue;
                    }

                    $allocatedDeptIds = $this->find()
                        ->select(['department_id' => 'DISTINCT AcceptedStudents.department_id'])
                        ->where([
                            'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                            'AcceptedStudents.college_id' => $collegeId,
                            'AcceptedStudents.department_id IS NOT NULL',
                            'AcceptedStudents.department_id NOT IN' => ['', 0]
                        ])
                        ->toArray();
                    $allocatedDeptIds = array_column($allocatedDeptIds, 'department_id');

                    $excludedCount = 0;
                    foreach ($students as $student) {
                        for ($j = 1; $j < $i; $j++) {
                            $prevPref = $this->Preferences->find()
                                ->select(['Preferences.department_id'])
                                ->where([
                                    'Preferences.accepted_student_id' => $student->accepted_student_id,
                                    'Preferences.preferences_order' => $j
                                ])
                                ->first();
                            if ($prevPref && !in_array($prevPref->department_id, $allocatedDeptIds)) {
                                $excludedCount++;
                                break;
                            }
                        }
                    }

                    $sumAvailableStudents += (count($students) - $excludedCount);
                    if ($sumAvailableStudents >= $quota) {
                        break;
                    }
                }

                if ($sumAvailableStudents < $quota) {
                    $gap = $quota - $sumAvailableStudents;
                    $quota -= $gap;

                    $reservedSum = array_sum(array_column(array_filter($reservedQuotaNumber, function ($v) {
                        return !$v['adjusted'];
                    }), 'reservedquota'));

                    $gapDistributionSum = 0;
                    $maxReservedQuota = ['max_quota' => 0, 'max_index' => 0];

                    if ($reservedSum > 0) {
                        foreach ($reservedQuotaNumber as $id => &$alloc) {
                            if (!$alloc['adjusted']) {
                                $increment = round($gap * ($alloc['reservedquota'] / $reservedSum));
                                $gapDistributionSum += $increment;
                                $alloc['reservedquota'] += $increment;
                                if ($alloc['reservedquota'] >= $maxReservedQuota['max_quota']) {
                                    $maxReservedQuota = ['max_quota' => $alloc['reservedquota'], 'max_index' => $id];
                                }
                            }
                        }
                    }

                    if ($gapDistributionSum != $gap && $maxReservedQuota['max_index']) {
                        $reservedQuotaNumber[$maxReservedQuota['max_index']]['reservedquota'] += ($gap - $gapDistributionSum);
                    }
                }
            }
        }

        return [$reservedQuotaNumber, $adjustedPrivilegedQuota];
    }

    /**
     * Filters out privileged students
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @param string|null $resultType Result type
     * @param int|null $departmentId Department ID
     * @param array $adjustedPrivilegedQuota Adjusted privileged quota
     * @param array $reservedQuotaNumber Reserved quota data
     * @param array $placedStudents Placed students
     * @param string|null $privilegeType Privilege type
     * @return array Privileged selected students
     */
    public function privilegedStudentsFilterOut($academicYear = null, $collegeId = null, $resultType = null, $departmentId = null, $adjustedPrivilegedQuota = [], $reservedQuotaNumber = [], $placedStudents = [], $privilegeType = null)
    {
        if (!$academicYear || !$collegeId || !$privilegeType || empty($adjustedPrivilegedQuota[$privilegeType])) {
            return [];
        }

        $competitivelyAssigned = $placedStudents['C'] ?? [];
        $quotaAssigned = $placedStudents['Q'] ?? [];

        $participatingDepartmentsTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        $numParticipatingDepartments = $participatingDepartmentsTable->find()
            ->where([
                'ParticipatingDepartments.college_id' => $collegeId,
                'ParticipatingDepartments.academic_year' => $academicYear
            ])
            ->count();

        $privilegedCondition = null;
        switch (strtolower($privilegeType)) {
            case 'female':
                $privilegedCondition = 'AcceptedStudents.sex = :sex';
                break;
            case 'disability':
                $privilegedCondition = 'AcceptedStudents.disability IS NOT NULL';
                break;
            case 'regions':
                $region = $participatingDepartmentsTable->find()
                    ->where([
                        'ParticipatingDepartments.college_id' => $collegeId,
                        'ParticipatingDepartments.academic_year' => $academicYear
                    ])
                    ->first();
                if (empty($region->developing_regions_id)) {
                    return [];
                }
                $privilegedCondition = 'AcceptedStudents.region_id IN (:regions)';
                break;
            default:
                return [];
        }

        $resultOrderBy = $resultType ? 'AcceptedStudents.EHEECE_total_results DESC' : 'AcceptedStudents.freshman_result DESC';
        $selectedStudents = [];

        if ($numParticipatingDepartments && $adjustedPrivilegedQuota[$privilegeType] > 0) {
            for ($i = 1; $i <= $numParticipatingDepartments; $i++) {
                $query = $this->Preferences->find()
                    ->select(['Preferences.accepted_student_id'])
                    ->where([
                        'Preferences.academicyear LIKE' => $academicYear . '%',
                        'Preferences.college_id' => $collegeId,
                        'Preferences.department_id' => $departmentId,
                        'Preferences.preferences_order' => $i,
                        'AcceptedStudents.college_id' => $collegeId,
                        'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                        'OR' => [
                            'AcceptedStudents.department_id IS NULL',
                            'AcceptedStudents.department_id' => ['', 0]
                        ]
                    ])
                    ->order([$resultOrderBy]);

                if ($privilegedCondition) {
                    $query->andWhere($privilegedCondition);
                    if ($privilegeType === 'female') {
                        $query->bind(':sex', 'female', 'string');
                    } elseif ($privilegeType === 'regions') {
                        $query->bind(':regions', $region->developing_regions_id, 'string');
                    }
                }

                $students = $query->toArray();

                if ($i == 1) {
                    foreach ($students as $student) {
                        if (!in_array($student->accepted_student_id, $competitivelyAssigned) && !in_array($student->accepted_student_id, $quotaAssigned)) {
                            $selectedStudents[] = $student->accepted_student_id;
                        }
                    }
                    if (count($selectedStudents) >= $adjustedPrivilegedQuota[$privilegeType]) {
                        break;
                    }
                    continue;
                }

                $allocatedDeptIds = $this->find()
                    ->select(['department_id' => 'DISTINCT AcceptedStudents.department_id'])
                    ->where([
                        'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                        'AcceptedStudents.college_id' => $collegeId,
                        'AcceptedStudents.department_id IS NOT NULL',
                        'AcceptedStudents.department_id NOT IN' => ['', 0]
                    ])
                    ->toArray();
                $allocatedDeptIds = array_column($allocatedDeptIds, 'department_id');

                $preliminaryStudents = [];
                foreach ($students as $student) {
                    $exclude = false;
                    for ($j = 1; $j < $i; $j++) {
                        $prevPref = $this->Preferences->find()
                            ->select(['Preferences.department_id'])
                            ->where([
                                'Preferences.accepted_student_id' => $student->accepted_student_id,
                                'Preferences.preferences_order' => $j
                            ])
                            ->first();
                        if ($prevPref && !in_array($prevPref->department_id, $allocatedDeptIds)) {
                            $exclude = true;
                            break;
                        }
                    }
                    if (!$exclude) {
                        $preliminaryStudents[] = $student->accepted_student_id;
                    }
                }

                foreach ($preliminaryStudents as $studentId) {
                    if (!in_array($studentId, $competitivelyAssigned) && !in_array($studentId, $quotaAssigned)) {
                        $selectedStudents[] = $studentId;
                    }
                }

                if (count($selectedStudents) >= $adjustedPrivilegedQuota[$privilegeType]) {
                    break;
                }
            }
        }

        return [$privilegeType => $selectedStudents];
    }

    /**
     * Runs parallel assignment after sequential placement
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @param string|null $resultType Result type
     * @return array Placed students
     */
    public function runAutoParallelAssignmentAfterSeq($academicYear = null, $collegeId = null, $resultType = null)
    {
        if (!$academicYear || !$collegeId) {
            return [];
        }

        $studentsPlacedByQuota = $this->find()
            ->where([
                'AcceptedStudents.college_id' => $collegeId,
                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                'AcceptedStudents.placement_based' => 'Q',
                'AcceptedStudents.placementtype' => 'AUTO PLACED',
                'AcceptedStudents.Placement_Approved_By_Department IS NULL'
            ])
            ->contain(['Departments'])
            ->toArray();

        $acceptedStudents = $this->find()
            ->where([
                'AcceptedStudents.college_id' => $collegeId,
                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                'AcceptedStudents.placementtype' => 'AUTO PLACED',
                'AcceptedStudents.Placement_Approved_By_Department IS NULL',
                'AcceptedStudents.program_id' => PROGRAM_UNDEGRADUATE,
                'AcceptedStudents.program_type_id IN' => [PROGRAM_TYPE_REGULAR, PROGRAM_TYPE_ADVANCE_STANDING, PROGRAM_TYPE_DAY_TIME_EXTENSION]
            ])
            ->limit(1000000)
            ->toArray();

        $cancellationList = [];
        foreach ($acceptedStudents as $index => $student) {
            $cancellationList[$index] = [
                'id' => $student->id,
                'placementtype' => CANCELLED_PLACEMENT,
                'minute_number' => null,
                'department_id' => null
            ];
        }

        $autoPlacedStudents = [];
        if ($cancellationList && $this->saveAll($cancellationList)) {
            $autoPlacedStudents = $this->autoParallelAssignment($academicYear, $collegeId, $resultType);

            if (!empty($autoPlacedStudents)) {
                $placedStudentsSave = [];
                $count = 0;
                foreach ($studentsPlacedByQuota as $student) {
                    $placedStudentsSave[$count] = [
                        'id' => $student->id,
                        'placementtype' => AUTO_PLACEMENT,
                        'placement_based' => 'Q',
                        'department_id' => $student->department_id
                    ];
                    $count++;
                }

                if ($placedStudentsSave) {
                    $this->saveAll($placedStudentsSave);
                }
            }
        }

        return $autoPlacedStudents;
    }

    /**
     * Runs auto placement algorithm
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @param string|null $resultType Result type
     * @param bool|null $highPriorityForHighResult High priority flag
     * @param bool|null $firstConsiderFirst First consider flag
     * @return array Placed students
     */
    public function autoPlacementAlgorithm($academicYear = null, $collegeId = null, $resultType = null, $highPriorityForHighResult = null, $firstConsiderFirst = null)
    {
        if (!$academicYear || !$collegeId) {
            return [];
        }

        $departments = $this->getListOfDepartmentRequestedByPrivilegedStudentMost($academicYear, $collegeId);
        $placedStudentsSave = [];
        $count = 0;

        $participatingDepartmentsTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        $placementsResultsCriteriaTable = TableRegistry::getTableLocator()->get('PlacementsResultsCriteria');

        foreach ($departments as $departmentId => $weight) {
            $deptDetails = $participatingDepartmentsTable->find()
                ->where([
                    'ParticipatingDepartments.college_id' => $collegeId,
                    'ParticipatingDepartments.academic_year' => $academicYear,
                    'ParticipatingDepartments.department_id' => $departmentId
                ])
                ->first();

            $adjustedPrivilegedQuota = [
                'female' => $deptDetails->female ?? 0,
                'regions' => $deptDetails->regions ?? 0,
                'disability' => $deptDetails->disability ?? 0
            ];

            $reservedPlace = $placementsResultsCriteriaTable->reservedPlaceForDepartmentByGradeRange($academicYear, $collegeId, $departmentId);
            $reservedQuotaNumber = [];
            foreach ($reservedPlace as $quota) {
                $reservedQuotaNumber[$quota['PlacementsResultsCriteria']['id']] = [
                    'reservedquota' => $quota['ReservedPlace']['number'],
                    'available' => $this->availableStudentInGivenRangeAndQuota($academicYear, $collegeId, $resultType, $quota),
                    'adjusted' => 0
                ];
            }

            $preAdjusted = $this->checkAndAdjustPrivilegedQuota($academicYear, $collegeId, $resultType, $departmentId, $adjustedPrivilegedQuota, $reservedQuotaNumber);
            $adjustedQuota = $this->checkAndAdjustAllocationWithAvailability($academicYear, $collegeId, $resultType, $departmentId, $preAdjusted[0]);

            $placedStudents = [];
            do {
                $completed = false;

                foreach ($adjustedQuota as $resultCategoryId => $quota) {
                    if ($quota['reservedquota'] > 0) {
                        $sortedStudents = $this->sortOutStudentByPreference($collegeId, $academicYear, $resultCategoryId, $resultType, $departmentId, $highPriorityForHighResult, $firstConsiderFirst);
                        $n = min($quota['reservedquota'], count($sortedStudents));
                        for ($i = 0; $i < $n; $i++) {
                            $placedStudents['C'][] = $sortedStudents[$i]['AcceptedStudent']['id'];
                        }
                    }
                }

                if ($preAdjusted[1]['female'] == 0 && $preAdjusted[1]['regions'] == 0 && $preAdjusted[1]['disability'] == 0) {
                    $completed = true;
                }

                foreach ($preAdjusted[1] as $privilegeType => &$quota) {
                    if ($quota > 0) {
                        $completed = false;
                        $privilegedSelected = $this->privilegedStudentsFilterOut($academicYear, $collegeId, $resultType, $departmentId, $preAdjusted[1], $adjustedQuota, $placedStudents, $privilegeType);
                        if (!empty($privilegedSelected[$privilegeType]) && $quota <= count($privilegedSelected[$privilegeType])) {
                            for ($i = 0; $i < $quota; $i++) {
                                $placedStudents['Q'][] = $privilegedSelected[$privilegeType][$i];
                            }
                            $completed = true;
                        } else {
                            $gap = $quota - count($privilegedSelected[$privilegeType] ?? []);
                            $quota -= $gap;
                            $reservedSum = array_sum(array_column($adjustedQuota, 'reservedquota'));
                            $gapDistributionSum = 0;
                            $maxReservedQuota = ['max_quota' => 0, 'max_index' => 0];

                            foreach ($adjustedQuota as $id => &$alloc) {
                                $alloc['adjusted'] = 0;
                            }

                            if ($reservedSum > 0) {
                                foreach ($adjustedQuota as $id => &$alloc) {
                                    if (!$alloc['adjusted']) {
                                        $increment = round($gap * ($alloc['reservedquota'] / $reservedSum));
                                        $gapDistributionSum += $increment;
                                        $alloc['reservedquota'] += $increment;
                                        if ($alloc['reservedquota'] >= $maxReservedQuota['max_quota']) {
                                            $maxReservedQuota = ['max_quota' => $alloc['reservedquota'], 'max_index' => $id];
                                        }
                                    }
                                }
                            }

                            if ($gapDistributionSum != $gap && $maxReservedQuota['max_index']) {
                                $adjustedQuota[$maxReservedQuota['max_index']]['reservedquota'] += ($gap - $gapDistributionSum);
                            } elseif ($gap - $gapDistributionSum > 0) {
                                foreach ($adjustedQuota as $id => &$alloc) {
                                    if ($alloc['reservedquota'] == 0 && !$alloc['adjusted'] && $alloc['available'] > 0) {
                                        $alloc['reservedquota'] = min($alloc['available'], $gap - $gapDistributionSum);
                                        $gapDistributionSum += $alloc['reservedquota'];
                                        if ($gap - $gapDistributionSum <= 0) {
                                            break;
                                        }
                                    }
                                }
                            }

                            $adjustedQuota = $this->checkAndAdjustAllocationWithAvailability($academicYear, $collegeId, $resultType, $departmentId, $adjustedQuota);
                            break;
                        }
                    }
                }
            } while (!$completed);

            foreach ($placedStudents as $type => $students) {
                foreach ($students as $studentId) {
                    $placedStudentsSave[$count] = [
                        'id' => $studentId,
                        'placementtype' => AUTO_PLACEMENT,
                        'placement_based' => $type,
                        'department_id' => $departmentId
                    ];
                    $count++;
                }
            }

            if ($placedStudentsSave) {
                $this->saveAll($placedStudentsSave);
            }
        }

        $resultOrderBy = $resultType ? 'AcceptedStudents.EHEECE_total_results DESC' : 'AcceptedStudents.freshman_result DESC';
        $placedStudents = $this->find()
            ->where([
                'AcceptedStudents.college_id' => $collegeId,
                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                'AcceptedStudents.placementtype' => AUTO_PLACEMENT
            ])
            ->order(['AcceptedStudents.department_id ASC', $resultOrderBy])
            ->toArray();

        $deptIds = array_keys($departments);
        $deptNames = $this->Departments->find('list')->where(['Departments.id IN' => $deptIds])->toArray();
        $newlyPlacedStudents = [];

        foreach ($deptNames as $deptId => $deptName) {
            foreach ($placedStudents as $k => $student) {
                if ($deptId == $student->department_id) {
                    $newlyPlacedStudents[$deptName][$k] = $student->toArray();
                }
            }

            $newlyPlacedStudents['auto_summery'][$deptName]['C'] = $this->find()
                ->where([
                    'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                    'AcceptedStudents.department_id' => $deptId,
                    'AcceptedStudents.college_id' => $collegeId,
                    'AcceptedStudents.placement_based' => 'C'
                ])
                ->count();

            $newlyPlacedStudents['auto_summery'][$deptName]['Q'] = $this->find()
                ->where([
                    'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                    'AcceptedStudents.department_id' => $deptId,
                    'AcceptedStudents.college_id' => $collegeId,
                    'AcceptedStudents.placement_based' => 'Q'
                ])
                ->count();
        }

        return $newlyPlacedStudents;
    }

    /**
     * Sorts students by preference
     *
     * @param int|null $collegeId College ID
     * @param string|null $academicYear Academic year
     * @param int|null $resultCategoryId Result category ID
     * @param string|null $resultType Result type
     * @param int|null $departmentId Department ID
     * @param bool|null $highPriorityForHighResult High priority flag
     * @param bool|null $firstConsiderFirst First consider flag
     * @return array Sorted students
     */
    public function sortOutStudentByPreference($collegeId = null, $academicYear = null, $resultCategoryId = null, $resultType = null, $departmentId = null, $highPriorityForHighResult = null, $firstConsiderFirst = null)
    {
        if (!$collegeId || !$academicYear || !$resultCategoryId || !$departmentId) {
            return [];
        }

        $placementsResultsCriteriaTable = TableRegistry::getTableLocator()->get('PlacementsResultsCriteria');
        $resultCategory = $placementsResultsCriteriaTable->find()
            ->where([
                'PlacementsResultsCriteria.id' => $resultCategoryId,
                'PlacementsResultsCriteria.admissionyear' => $academicYear,
                'PlacementsResultsCriteria.college_id' => $collegeId
            ])
            ->first();

        if (!$resultCategory) {
            return [];
        }

        $resultCondition = $resultType
            ? ['AcceptedStudents.EHEECE_total_results >=' => $resultCategory->result_from, 'AcceptedStudents.EHEECE_total_results <=' => $resultCategory->result_to]
            : ['AcceptedStudents.freshman_result >=' => $resultCategory->result_from, 'AcceptedStudents.freshman_result <=' => $resultCategory->result_to];

        $resultOrderBy = $resultType ? 'AcceptedStudents.EHEECE_total_results DESC' : 'AcceptedStudents.freshman_result DESC';

        $studentsWithPreference = $this->find()
            ->leftJoinWith('Preferences', function ($q) use ($departmentId) {
                return $q->where(['Preferences.department_id' => $departmentId]);
            })
            ->select([
                'AcceptedStudents.id',
                'AcceptedStudents.EHEECE_total_results',
                'AcceptedStudents.freshman_result',
                'Preferences.preferences_order'
            ])
            ->where([
                'OR' => [
                    'AcceptedStudents.department_id IS NULL',
                    'AcceptedStudents.department_id' => ['', 0]
                ],
                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                'AcceptedStudents.college_id' => $collegeId,
                $resultCondition
            ])
            ->order(['Preferences.preferences_order ASC', $resultOrderBy])
            ->toArray();

        $studentsWithoutPreference = $this->find()
            ->leftJoinWith('Preferences', function ($q) use ($departmentId) {
                return $q->where(['Preferences.department_id' => $departmentId]);
            })
            ->select([
                'AcceptedStudents.id',
                'AcceptedStudents.EHEECE_total_results',
                'AcceptedStudents.freshman_result',
                'Preferences.preferences_order'
            ])
            ->where([
                'OR' => [
                    'AcceptedStudents.department_id IS NULL',
                    'AcceptedStudents.department_id' => ['', 0]
                ],
                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                'AcceptedStudents.college_id' => $collegeId,
                'AcceptedStudents.id NOT IN' => $this->Preferences->find()->select(['Preferences.accepted_student_id'])->where(['Preferences.department_id' => $departmentId]),
                $resultCondition
            ])
            ->order(['Preferences.preferences_order ASC', $resultOrderBy])
            ->toArray();

        $sortedStudents = array_merge($studentsWithPreference, $studentsWithoutPreference);

        if ($highPriorityForHighResult || $firstConsiderFirst) {
            $studentsToSort = [];
            $studentsToRemove = [];

            foreach ($sortedStudents as $student) {
                if (!empty($student->preference->preferences_order) && $student->preference->preferences_order > 1) {
                    $prevPrefs = $this->Preferences->find()
                        ->where([
                            'Preferences.accepted_student_id' => $student->id,
                            'Preferences.academicyear' => $academicYear,
                            'Preferences.preferences_order <' => $student->preference->preferences_order
                        ])
                        ->toArray();

                    $allPrefsPlaced = true;
                    foreach ($prevPrefs as $pref) {
                        $placedCount = $this->find()
                            ->where([
                                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                                'AcceptedStudents.department_id' => $pref->department_id
                            ])
                            ->count();
                        if ($placedCount <= 0) {
                            $allPrefsPlaced = false;
                            break;
                        }
                    }

                    if ($allPrefsPlaced) {
                        $studentsToSort[] = $student->toArray();
                    } else {
                        $studentsToRemove[] = $student->id;
                    }
                }
            }

            if ($highPriorityForHighResult && $studentsToSort) {
                $tmp = [];
                foreach ($sortedStudents as $student) {
                    $insertPos = 0;
                    foreach ($studentsToSort as $toSort) {
                        if (
                            ($resultType && $toSort['AcceptedStudent']['EHEECE_total_results'] > $student['AcceptedStudent']['EHEECE_total_results']) ||
                            (!$resultType && $toSort['AcceptedStudent']['freshman_result'] > $student['AcceptedStudent']['freshman_result']) ||
                            empty($student['Preference']['preferences_order'])
                        ) {
                            $insertPos = array_search($student, $sortedStudents);
                            break;
                        }
                    }
                    if ($insertPos && $student['AcceptedStudent']['id'] != ($toSort['AcceptedStudent']['id'] ?? 0)) {
                        $tmp[] = $toSort;
                    }
                    if ($student['AcceptedStudent']['id'] != ($toSort['AcceptedStudent']['id'] ?? 0)) {
                        $tmp[] = $student;
                    }
                }
                $sortedStudents = $tmp;
            }

            if ($firstConsiderFirst && $studentsToRemove) {
                $sortedStudents = array_filter($sortedStudents, function ($s) use ($studentsToRemove) {
                    return !in_array($s['AcceptedStudent']['id'], $studentsToRemove);
                });
            }
        }

        return $sortedStudents;
    }

    /**
     * Retrieves reserved quota
     *
     * @param array|null $departments Departments
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @param string|null $resultType Result type
     * @return array|string Reserved quota or error message
     */
    public function getReservedQuota($departments = null, $academicYear = null, $collegeId = null, $resultType = null)
    {
        if (!$departments || !$academicYear || !$collegeId) {
            return 'NO DEPARTMENT FOUND';
        }

        $placementsResultsCriteriaTable = TableRegistry::getTableLocator()->get('PlacementsResultsCriteria');
        $reservedQuotaNumber = [];

        foreach ($departments as $departmentId => $weight) {
            $reservedCategories = $placementsResultsCriteriaTable->reservedPlaceCategory($academicYear, $collegeId, $departmentId);
            foreach ($reservedCategories as $category) {
                $reservedQuotaNumber[$departmentId][$category['PlacementsResultsCriteria']['id']] = [
                    'reservedquota' => $category['ReservedPlace']['number'],
                    'available' => $this->availableStudentInGivenRangeAndQuota($academicYear, $collegeId, $resultType, $category)
                ];
            }
        }

        return $reservedQuotaNumber;
    }

    /**
     * Retrieves privileged quota
     *
     * @param array|null $departments Departments
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @param string|null $resultCategory Result category
     * @return array Privileged quota
     */
    public function getPrivilegedQuota($departments = null, $academicYear = null, $collegeId = null, $resultCategory = null)
    {
        if (!$academicYear || !$collegeId) {
            return [];
        }

        $participatingDepartmentsTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        return $participatingDepartmentsTable->quotaNameAndValue($academicYear, $collegeId);
    }

    /**
     * Shrinks or enlarges quota
     *
     * @param array|null $reservedQuotaNumber Reserved quota data
     * @return array Adjusted quota
     */
    public function shrinkAndEnlarge($reservedQuotaNumber = null)
    {
        $adjustedReservedQuota = [];
        foreach ($reservedQuotaNumber as $deptId => $categories) {
            foreach ($categories as $catId => $value) {
                if ($value['available'] < $value['reservedquota']) {
                    $gap = $value['reservedquota'] - $value['available'];
                    $adjustedReservedQuota[$deptId][$catId] = $value['reservedquota'] - $gap;
                } else {
                    $adjustedReservedQuota[$deptId][$catId] = $value['reservedquota'];
                }
            }
        }
        return $adjustedReservedQuota;
    }

    /**
     * Counts available students in a given range
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @param string|null $resultType Result type
     * @param array|null $categoryValue Category value
     * @return int Count of students
     */
    public function availableStudentInGivenRangeAndQuota($academicYear = null, $collegeId = null, $resultType = null, $categoryValue = null)
    {
        if (!$academicYear || !$collegeId || !$categoryValue) {
            return 0;
        }

        $conditions = [
            'OR' => [
                'AcceptedStudents.department_id IS NULL',
                'AcceptedStudents.department_id' => ['', 0]
            ],
            'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
            'AcceptedStudents.college_id' => $collegeId
        ];

        if ($resultType) {
            $conditions['AcceptedStudents.EHEECE_total_results >='] = $categoryValue['PlacementsResultsCriteria']['result_from'];
            $conditions['AcceptedStudents.EHEECE_total_results <='] = $categoryValue['PlacementsResultsCriteria']['result_to'];
        } else {
            $conditions['AcceptedStudents.freshman_result >='] = $categoryValue['PlacementsResultsCriteria']['result_from'];
            $conditions['AcceptedStudents.freshman_result <='] = $categoryValue['PlacementsResultsCriteria']['result_to'];
        }

        return $this->find()->where($conditions)->count();
    }

    /**
     * Detects privileged quota presence
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @return int Sum of quotas
     */
    public function detectPrivilegedQuotaPresence($academicYear = null, $collegeId = null)
    {
        if (!$academicYear || !$collegeId) {
            return 0;
        }

        $participatingDepartmentsTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        $departments = $participatingDepartmentsTable->find()
            ->where([
                'ParticipatingDepartments.academic_year LIKE' => $academicYear . '%',
                'ParticipatingDepartments.college_id' => $collegeId
            ])
            ->toArray();

        $quotaSum = 0;
        foreach ($departments as $dept) {
            $quotaSum += ($dept->female ?? 0) + ($dept->regions ?? 0) + ($dept->disability ?? 0);
        }

        return $quotaSum;
    }

    /**
     * Runs parallel assignment
     *
     * @param string|null $academicYear Academic year
     * @param int|null $collegeId College ID
     * @param string|null $resultType Result type
     * @return array Placed students
     */
    public function autoParallelAssignment($academicYear = null, $collegeId = null, $resultType = null)
    {
        if (!$academicYear || !$collegeId) {
            return [];
        }

        $placementsResultsCriteriaTable = TableRegistry::getTableLocator()->get('PlacementsResultsCriteria');
        $resultCategories = $placementsResultsCriteriaTable->getListOfGradeCategory($academicYear, $collegeId);

        if (empty($resultCategories)) {
            throw new \Exception('No result criteria recorded. Technical Detail: Model: AcceptedStudent, Function: auto_parallel_assignment');
        }

        $placedStudentsSave = [];
        $count = 0;

        foreach ($resultCategories as $category) {
            $reservedPlaces = TableRegistry::getTableLocator()->get('ReservedPlaces')->find()
                ->select(['ReservedPlaces.id', 'ReservedPlaces.participating_department_id', 'ReservedPlaces.number'])
                ->where([
                    'ReservedPlaces.college_id' => $collegeId,
                    'ReservedPlaces.academicyear LIKE' => $academicYear . '%',
                    'ReservedPlaces.placements_results_criteria_id' => $category['PlacementsResultsCriteria']['id']
                ])
                ->toArray();

            $reservedQuotaByDept = [];
            foreach ($reservedPlaces as $place) {
                $reservedQuotaByDept[$place->participating_department_id] = [
                    'quota' => $place->number,
                    'assigned' => 0
                ];
            }

            $resultCondition = $resultType
                ? [
                    'AcceptedStudents.EHEECE_total_results >=' => $category['PlacementsResultsCriteria']['result_from'],
                    'AcceptedStudents.EHEECE_total_results <=' => $category['PlacementsResultsCriteria']['result_to']
                ]
                : [
                    'AcceptedStudents.freshman_result >=' => $category['PlacementsResultsCriteria']['result_from'],
                    'AcceptedStudents.freshman_result <=' => $category['PlacementsResultsCriteria']['result_to']
                ];

            $resultOrder = $resultType ? 'AcceptedStudents.EHEECE_total_results DESC' : 'AcceptedStudents.freshman_result DESC';

            $students = $this->find()
                ->select(['AcceptedStudents.id'])
                ->where([
                    'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                    'AcceptedStudents.placementtype !=' => 'DIRECT PLACED',
                    'AcceptedStudents.college_id' => $collegeId,
                    $resultCondition
                ])
                ->order([$resultOrder])
                ->toArray();

            $studentsWithoutPreference = [];
            foreach ($students as $student) {
                $preferences = $this->Preferences->find()
                    ->select(['Preferences.department_id', 'Preferences.preferences_order'])
                    ->where(['Preferences.accepted_student_id' => $student->id])
                    ->order(['Preferences.preferences_order ASC'])
                    ->toArray();

                if ($preferences) {
                    foreach ($preferences as $pref) {
                        if ($reservedQuotaByDept[$pref->department_id]['assigned'] < $reservedQuotaByDept[$pref->department_id]['quota']) {
                            $placedStudentsSave[$count] = [
                                'id' => $student->id,
                                'placementtype' => AUTO_PLACEMENT,
                                'placement_based' => 'C',
                                'department_id' => $pref->department_id
                            ];
                            $reservedQuotaByDept[$pref->department_id]['assigned']++;
                            $count++;
                            break;
                        }
                    }
                } else {
                    $studentsWithoutPreference[] = $student->id;
                }
            }

            foreach ($studentsWithoutPreference as $studentId) {
                foreach ($reservedQuotaByDept as $deptId => &$quota) {
                    if ($quota['assigned'] < $quota['quota']) {
                        $placedStudentsSave[$count] = [
                            'id' => $studentId,
                            'placementtype' => AUTO_PLACEMENT,
                            'placement_based' => 'C',
                            'department_id' => $deptId
                        ];
                        $quota['assigned']++;
                        $count++;
                        break;
                    }
                }
            }
        }

        if ($placedStudentsSave) {
            $this->saveAll($placedStudentsSave);
        }

        $resultOrderBy = $resultType ? 'AcceptedStudents.EHEECE_total_results DESC' : 'AcceptedStudents.freshman_result DESC';
        $placedStudents = $this->find()
            ->where([
                'AcceptedStudents.college_id' => $collegeId,
                'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                'AcceptedStudents.placementtype' => AUTO_PLACEMENT
            ])
            ->order(['AcceptedStudents.department_id ASC', $resultOrderBy])
            ->toArray();

        $participatingDepartmentsTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        $deptIds = $participatingDepartmentsTable->find()
            ->select(['ParticipatingDepartments.department_id'])
            ->where([
                'ParticipatingDepartments.academic_year LIKE' => $academicYear . '%',
                'ParticipatingDepartments.college_id' => $collegeId
            ])
            ->toArray();
        $deptIds = array_column($deptIds, 'department_id');

        $deptNames = $this->Departments->find('list')->where(['Departments.id IN' => $deptIds])->toArray();
        $newlyPlacedStudents = [];

        foreach ($deptNames as $deptId => $deptName) {
            foreach ($placedStudents as $k => $student) {
                if ($deptId == $student->department_id) {
                    $newlyPlacedStudents[$deptName][$k] = $student->toArray();
                }
            }

            $newlyPlacedStudents['auto_summery'][$deptName]['C'] = $this->find()
                ->where([
                    'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                    'AcceptedStudents.department_id' => $deptId,
                    'AcceptedStudents.college_id' => $collegeId,
                    'AcceptedStudents.placement_based' => 'C'
                ])
                ->count();

            $newlyPlacedStudents['auto_summery'][$deptName]['Q'] = $this->find()
                ->where([
                    'AcceptedStudents.academicyear LIKE' => $academicYear . '%',
                    'AcceptedStudents.department_id' => $deptId,
                    'AcceptedStudents.college_id' => $collegeId,
                    'AcceptedStudents.placement_based' => 'Q'
                ])
                ->count();
        }

        return $newlyPlacedStudents;
    }

    /**
     * Checks program type validity
     *
     * @param array|null $data Accepted student data
     * @param int|null $roleId Role ID
     * @return bool True if valid, false otherwise
     */
    public function checkProgramType($data = null, $roleId = null)
    {
        if ($roleId == ROLE_COLLEGE) {
            return true;
        }

        if (!empty($data['AcceptedStudent'])) {
            if ($data['AcceptedStudent']['program_id'] != PROGRAM_UNDEGRADUATE) {
                if (empty($data['AcceptedStudent']['department_id']) || $data['AcceptedStudent']['department_id'] == 0) {
                    $this->validationErrors['program'] = ['For postgraduate student, you need to select department.'];
                    return false;
                }
            }

            if ($data['AcceptedStudent']['program_type_id'] != PROGRAM_TYPE_REGULAR && empty($data['AcceptedStudent']['department_id'])) {
                $this->validationErrors['program'] = ['For non-regular student, you need to select department.'];
                return false;
            }
        }

        return true;
    }

    /**
     * Copies freshman results from student exam statuses
     *
     * @param string|null $admissionYear Admission year
     * @param int|null $collegeId College ID
     * @return void
     */
    public function copyFreshmanResult($admissionYear = null, $collegeId = null)
    {
        if (!$admissionYear || !$collegeId) {
            return;
        }

        $studentResults = $this->find()
            ->select(['AcceptedStudents.id'])
            ->where([
                'AcceptedStudents.academicyear' => $admissionYear,
                'AcceptedStudents.department_id IS NULL',
                'AcceptedStudents.program_id' => PROGRAM_UNDEGRADUATE,
                'AcceptedStudents.program_type_id IN' => [
                    PROGRAM_TYPE_REGULAR,
                    PROGRAM_TYPE_ADVANCE_STANDING,
                    PROGRAM_TYPE_DAY_TIME_EXTENSION
                ],
                'AcceptedStudents.college_id' => $collegeId
            ])
            ->contain([
                'Students.StudentExamStatuses' => function ($q) {
                    return $q
                        ->select(['StudentExamStatuses.student_id', 'StudentExamStatuses.sgpa'])
                        ->where(['StudentExamStatuses.academic_status_id !=' => DISMISSED_ACADEMIC_STATUS_ID])
                        ->order(['StudentExamStatuses.created' => 'ASC']);
                }
            ])
            ->toArray();

        $acceptedStudentsResult = [];
        $acceptedStudentsEmptyFreshResult = [];

        foreach ($studentResults as $studentResult) {
            if (
                !empty($studentResult->student) &&
                !empty($studentResult->student->student_exam_statuses) &&
                !empty($studentResult->student->student_exam_statuses[0]->sgpa)
            ) {
                $acceptedStudentsResult[] = [
                    'id' => $studentResult->id,
                    'freshman_result' => $studentResult->student->student_exam_statuses[0]->sgpa
                ];
            }
            $acceptedStudentsEmptyFreshResult[] = [
                'id' => $studentResult->id,
                'freshman_result' => null
            ];
        }

        if (!empty($acceptedStudentsEmptyFreshResult)) {
            $entities = $this->patchEntities(
                $this->find()->where(['AcceptedStudents.id IN' => array_column($acceptedStudentsEmptyFreshResult, 'id')])->toArray(),
                $acceptedStudentsEmptyFreshResult
            );
            $this->saveMany($entities);
        }

        if (!empty($acceptedStudentsResult)) {
            $entities = $this->patchEntities(
                $this->find()->where(['AcceptedStudents.id IN' => array_column($acceptedStudentsResult, 'id')])->toArray(),
                $acceptedStudentsResult
            );
            $this->saveMany($entities);
        }
    }
}
