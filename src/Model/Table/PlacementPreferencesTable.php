<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class PlacementPreferencesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('placement_preferences');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('AcceptedStudents', [
            'foreignKey' => 'accepted_student_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Students', [
            'foreignKey' => 'student_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('PlacementRoundParticipants', [
            'foreignKey' => 'placement_round_participant_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
        ]);
    }

    public function getPreferenceStat(array $placementRoundParticipant, ?string $type2 = null): array
    {
        $stat = [];
        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $participatingDepartments = $placementRoundParticipantsTable->find('all')
            ->where(['PlacementRoundParticipants.group_identifier' => $placementRoundParticipant['PlacementRoundParticipant']['group_identifier']])
            ->disableHydration()
            ->all()
            ->toArray();

        if (!empty($participatingDepartments)) {
            foreach ($participatingDepartments as $participatingDepartment) {
                $index = count($stat);
                $type = $placementRoundParticipant['PlacementRoundParticipant']['type'];

                if ($type === 'College') {
                    $name = TableRegistry::getTableLocator()->get('Colleges')->find()
                        ->where(['Colleges.id' => $participatingDepartment['PlacementRoundParticipant']['foreign_key']])
                        ->disableHydration()
                        ->first();
                } elseif ($type === 'Department') {
                    $name = TableRegistry::getTableLocator()->get('Departments')->find()
                        ->where(['Departments.id' => $participatingDepartment['PlacementRoundParticipant']['foreign_key']])
                        ->disableHydration()
                        ->first();
                }

                $stat[$index]['foreign_key'] = $participatingDepartment['PlacementRoundParticipant']['foreign_key'];
                $stat[$index]['department_name'] = !empty($participatingDepartment['PlacementRoundParticipant']['name']) ?
                    $participatingDepartment['PlacementRoundParticipant']['name'] :
                    ($name[$type]['name'] ?? '');

                for ($i = 1; $i <= count($participatingDepartments); $i++) {
                    $options = [
                        'conditions' => [
                            'PlacementPreferences.placement_round_participant_id' => $participatingDepartment['PlacementRoundParticipant']['id'],
                            'PlacementPreferences.preference_order' => $i
                        ]
                    ];

                    $forcollege = explode('c~', $placementRoundParticipant['PlacementRoundParticipant']['applied_for']);
                    $fordepartment = explode('d~', $placementRoundParticipant['PlacementRoundParticipant']['applied_for']);

                    if (!empty($forcollege[1])) {
                        $options['conditions'][0] = "PlacementPreferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE department_id IS NULL AND academicyear = '{$placementRoundParticipant['PlacementRoundParticipant']['academic_year']}' AND college_id = '{$forcollege[1]}'";
                    } elseif (!empty($fordepartment[1])) {
                        $options['conditions'][0] = "PlacementPreferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE academicyear = '{$placementRoundParticipant['PlacementRoundParticipant']['academic_year']}' AND department_id = '{$fordepartment[1]}'";
                    }

                    if (strcasecmp($type2, 'female') === 0) {
                        $options['conditions'][0] .= " AND (sex LIKE '%female%' OR sex LIKE '%f%'))";
                    } elseif (strcasecmp($type2, 'disable') === 0) {
                        $options['conditions'][0] .= " AND disability IS NOT NULL AND disability <> '')";
                    } elseif (!empty($participatingDepartment['PlacementRoundParticipant']['developing_region']) && strcasecmp($type2, 'region') === 0) {
                        $options['conditions'][0] .= " AND region_id IN ('{$participatingDepartment['PlacementRoundParticipant']['developing_region']}'))";
                    } else {
                        $options['conditions'][0] .= ')';
                    }

                    $stat[$index]['count'][$i]['~total~'] = $this->find('count', $options);
                }
            }
        }

        return $stat;
    }

    public function getPreparedPreferenceStat(array $placementRoundParticipant, ?string $type = null): array
    {
        $stat = [];
        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $participatingDepartments = $placementRoundParticipantsTable->find('all')
            ->where(['PlacementRoundParticipants.group_identifier' => $placementRoundParticipant['PlacementRoundParticipant']['group_identifier']])
            ->disableHydration()
            ->all()
            ->toArray();

        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
        $accepted_student_ids = $placementParticipatingStudentsTable->find('list')
            ->where([
                'PlacementParticipatingStudents.round' => $placementRoundParticipant['PlacementRoundParticipant']['placement_round'],
                'PlacementParticipatingStudents.academic_year' => $placementRoundParticipant['PlacementRoundParticipant']['academic_year'],
                'PlacementParticipatingStudents.applied_for' => $placementRoundParticipant['PlacementRoundParticipant']['applied_for']
            ])
            ->select(['accepted_student_id'])
            ->disableHydration()
            ->toArray();

        if (!empty($participatingDepartments) && !empty($accepted_student_ids)) {
            foreach ($participatingDepartments as $participatingDepartment) {
                $index = count($stat);
                $stat[$index]['foreign_key'] = $participatingDepartment['PlacementRoundParticipant']['foreign_key'];
                $stat[$index]['department_name'] = $participatingDepartment['PlacementRoundParticipant']['name'] ?? '';

                for ($i = 1; $i <= count($participatingDepartments); $i++) {
                    $options = [
                        'conditions' => [
                            'PlacementPreferences.placement_round_participant_id' => $participatingDepartment['PlacementRoundParticipant']['id'],
                            'PlacementPreferences.preference_order' => $i
                        ]
                    ];

                    if (strcasecmp($type, 'female') === 0) {
                        $options['conditions'][] = "PlacementPreferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE id IN (" . implode(',', $accepted_student_ids) . ") AND (sex LIKE 'female%' OR sex LIKE 'f%'))";
                    } elseif (strcasecmp($type, 'disable') === 0) {
                        $options['conditions'][] = "PlacementPreferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE id IN (" . implode(',', $accepted_student_ids) . ") AND disability IS NOT NULL AND disability <> '')";
                    } elseif (!empty($participatingDepartment['PlacementRoundParticipant']['developing_region']) && strcasecmp($type, 'region') === 0) {
                        $options['conditions'][] = "PlacementPreferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE id IN (" . implode(',', $accepted_student_ids) . ") AND region_id IN ('{$participatingDepartment['PlacementRoundParticipant']['developing_region']}'))";
                    } else {
                        $options['conditions'][] = 'PlacementPreferences.accepted_student_id IN (' . implode(',', $accepted_student_ids) . ')';
                    }

                    $stat[$index]['count'][$i]['~total~'] = $this->find('count', $options);
                }
            }
        }

        return $stat;
    }

    public function isAlreadyEnteredPreference(?array $data = null): bool
    {
        $countUser = $this->find('count')
            ->where([
                'PlacementPreferences.accepted_student_id' => $data['accepted_student_id'],
                'PlacementPreferences.academic_year' => $data['academic_year'],
                'PlacementPreferences.round' => $data['round']
            ])
            ->disableHydration()
            ->count();

        $isEditing = !empty($data['id']);

        if ($countUser && !$isEditing) {
            $this->validationErrors['alreadypreferencerecorded'] = 'Validation Error: You have already recorded preference for selected student.';
            return true;
        }

        return false;
    }

    public function isAllPreferenceSelectedDifferent(?array $data = null, int $require_all_selected_switch = 0): bool
    {
        $array = [];

        if ($require_all_selected_switch) {
            if (!empty($data)) {
                foreach ($data as $value) {
                    if (!empty($value['placement_round_participant_id'])) {
                        $array[] = $value['placement_round_participant_id'];
                    } else {
                        $this->validationErrors['department'] = 'Validation Error: Please select program preference for each preference order.';
                        return false;
                    }
                }
            }
        } else {
            if (!empty($data)) {
                foreach ($data as $value) {
                    if (!empty($value['placement_round_participant_id'])) {
                        $array[] = $value['placement_round_participant_id'];
                    }
                }
            }
        }

        if (!empty($array)) {
            $arrayvaluecount = array_count_values($array);
            foreach ($arrayvaluecount as $v) {
                if ($v > 1) {
                    $this->validationErrors['preference'] = 'Validation Error: Please select different program preference for each preference order.';
                    return false;
                }
            }
        } else {
            $this->validationErrors['preference'] = 'Validation Error: Empty Preference. You did not selected any preference. Please select program preference for each preference order.';
            return false;
        }

        return true;
    }

    public function getStudentWhoFilledPreference(array $data = [], string $searchAble = ''): array
    {
        if (!empty($data)) {
            $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
            $firstData = $placementRoundParticipantsTable->find('first')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                    'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
                ])
                ->disableHydration()
                ->first();

            $allRoundParticipants = $placementRoundParticipantsTable->find('list')
                ->where(['PlacementRoundParticipants.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']])
                ->select(['id'])
                ->disableHydration()
                ->toArray();

            $conditions = [
                'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                'PlacementPreferences.placement_round_participant_id IN' => array_keys($allRoundParticipants)
            ];

            if (!empty($searchAble)) {
                $conditions['Students.first_name LIKE'] = $searchAble . '%';
            }

            $allStudentsWhoFilledPreference = $this->find()
                ->where($conditions)
                ->group(['PlacementPreferences.student_id'])
                ->contain(['Students'])
                ->limit(10)
                ->disableHydration()
                ->all()
                ->toArray();

            return $allStudentsWhoFilledPreference;
        }

        return [];
    }

    public function getPlacementCriteriaSummary(array $data = []): array
    {
        $placementSummary = [];

        if (!empty($data)) {
            $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
            $firstData = $placementRoundParticipantsTable->find('first')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                    'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
                ])
                ->disableHydration()
                ->first();

            if (!empty($firstData)) {
                $allRoundParticipants = $placementRoundParticipantsTable->find()
                    ->where(['PlacementRoundParticipants.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']])
                    ->disableHydration()
                    ->all()
                    ->toArray();

                $resultSettings = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find()
                    ->where(['PlacementResultSettings.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']])
                    ->disableHydration()
                    ->all()
                    ->toArray();

                $targetUnit = explode('~', $data['PlacementSetting']['applied_for']);
                $targetUnitName = '';

                if ($targetUnit[0] === 'c') {
                    $name = TableRegistry::getTableLocator()->get('Colleges')->find()
                        ->where(['Colleges.id' => $targetUnit[1]])
                        ->disableHydration()
                        ->first();
                    $targetUnitName = $name['name'] ?? '';
                } elseif ($targetUnit[0] === 'd') {
                    $name = TableRegistry::getTableLocator()->get('Departments')->find()
                        ->where(['Departments.id' => $targetUnit[1]])
                        ->disableHydration()
                        ->first();
                    $targetUnitName = $name['name'] ?? '';
                } elseif ($targetUnit[0] === 's') {
                    $name = TableRegistry::getTableLocator()->get('Specializations')->find()
                        ->where(['Specializations.id' => $targetUnit[1]])
                        ->disableHydration()
                        ->first();
                    $targetUnitName = $name['name'] ?? '';
                }

                $placementSummary['targetStudentInUnit'] = $targetUnitName;
                $placementSummary['round'] = $data['PlacementSetting']['round'];

                $placementSummary['placementAlreadyRun'] = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents')->find('count')
                    ->where([
                        'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                        'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                        'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                        'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                        'PlacementParticipatingStudents.placement_round_participant_id IS NOT NULL'
                    ])
                    ->disableHydration()
                    ->count();

                $placementSummary['academic_year'] = $data['PlacementSetting']['academic_year'];
                $placementSummary['totalStudentReadyForPlacement'] = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents')->find('count')
                    ->where([
                        'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                        'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                        'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                        'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round']
                    ])
                    ->group([
                        'PlacementParticipatingStudents.academic_year',
                        'PlacementParticipatingStudents.round',
                        'PlacementParticipatingStudents.student_id',
                        'PlacementParticipatingStudents.accepted_student_id'
                    ])
                    ->disableHydration()
                    ->count();

                $placementSummary['PlacementRoundParticipant'] = $allRoundParticipants;

                $allRoundParticipantsList = $placementRoundParticipantsTable->find('list')
                    ->where(['PlacementRoundParticipants.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']])
                    ->select(['id'])
                    ->disableHydration()
                    ->toArray();

                $developingRegions = $firstData['PlacementRoundParticipant']['developing_region'];

                $preferenceOrders = $this->find('list')
                    ->where(['PlacementPreferences.placement_round_participant_id IN' => array_keys($allRoundParticipantsList)])
                    ->select(['preference_order'])
                    ->group(['preference_order'])
                    ->order(['preference_order' => 'ASC'])
                    ->disableHydration()
                    ->toArray();

                $preference = [];
                $count = 0;

                if (!empty($preferenceOrders) && !empty($allRoundParticipants)) {
                    foreach ($preferenceOrders as $pv) {
                        foreach ($allRoundParticipants as $pvv) {
                            $preference[$count]['unit'] = $pvv['PlacementRoundParticipant']['name'];
                            $preference[$count]['preference_order'] = $pv;

                            $preference[$count]['male'] = 0;
                            $preference[$count]['female'] = 0;
                            $preference[$count]['disability'] = 0;
                            $preference[$count]['developing_region'] = 0;

                            $preference[$count]['total'] = $this->find('count')
                                ->where([
                                    'PlacementPreferences.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
                                    'PlacementPreferences.round' => $pvv['PlacementRoundParticipant']['placement_round'],
                                    'PlacementPreferences.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
                                    'PlacementPreferences.preference_order' => $pv
                                ])
                                ->disableHydration()
                                ->count();

                            $preference[$count]['female'] += $this->find('count')
                                ->where([
                                    'PlacementPreferences.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
                                    'PlacementPreferences.round' => $pvv['PlacementRoundParticipant']['placement_round'],
                                    'PlacementPreferences.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
                                    'PlacementPreferences.preference_order' => $pv,
                                    'PlacementPreferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE (sex = \'female\' OR sex = \'f\'))'
                                ])
                                ->disableHydration()
                                ->count();

                            $preference[$count]['male'] += $this->find('count')
                                ->where([
                                    'PlacementPreferences.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
                                    'PlacementPreferences.round' => $pvv['PlacementRoundParticipant']['placement_round'],
                                    'PlacementPreferences.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
                                    'PlacementPreferences.preference_order' => $pv,
                                    'PlacementPreferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE (sex = \'male\' OR sex = \'m\'))'
                                ])
                                ->disableHydration()
                                ->count();

                            $preference[$count]['disability'] += $this->find('count')
                                ->where([
                                    'PlacementPreferences.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
                                    'PlacementPreferences.round' => $pvv['PlacementRoundParticipant']['placement_round'],
                                    'PlacementPreferences.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
                                    'PlacementPreferences.preference_order' => $pv,
                                    'PlacementPreferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE disability IS NOT NULL)'
                                ])
                                ->disableHydration()
                                ->count();

                            if (!empty($developingRegions)) {
                                $preference[$count]['developing_region'] += $this->find('count')
                                    ->where([
                                        'PlacementPreferences.placement_round_participant_id' => $pvv['PlacementRoundParticipant']['id'],
                                        'PlacementPreferences.round' => $pvv['PlacementRoundParticipant']['placement_round'],
                                        'PlacementPreferences.academic_year' => $pvv['PlacementRoundParticipant']['academic_year'],
                                        'PlacementPreferences.preference_order' => $pv,
                                        'PlacementPreferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE region_id IN (' . $developingRegions . '))'
                                    ])
                                    ->disableHydration()
                                    ->count();
                            } else {
                                $preference[$count]['developing_region'] = 0;
                            }

                            $count++;
                        }
                    }
                }

                $placementSummary['ResultWeight'] = $resultSettings;
                $placementSummary['Preference'] = $preference;
            }
        }

        return $placementSummary;
    }

    public function getStudentWhoTookEntranceExam(array $data = []): array
    {
        if (!empty($data)) {
            $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
            $firstData = $placementRoundParticipantsTable->find('first')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                    'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
                ])
                ->disableHydration()
                ->first();

            $additionalPoints = TableRegistry::getTableLocator()->get('PlacementAdditionalPoints')->find()
                ->where([
                    'PlacementAdditionalPoints.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementAdditionalPoints.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementAdditionalPoints.program_type_id' => $data['PlacementSetting']['program_type_id'],
                    'PlacementAdditionalPoints.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementAdditionalPoints.round' => $data['PlacementSetting']['round']
                ])
                ->disableHydration()
                ->all()
                ->toArray();

            $points = [];
            foreach ($additionalPoints as $pv) {
                $points[$pv['type']] = $pv['point'];
            }

            $allRoundParticipants = $placementRoundParticipantsTable->find('list')
                ->where(['PlacementRoundParticipants.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']])
                ->select(['id'])
                ->disableHydration()
                ->toArray();

            $limit = !empty($data['PlacementSetting']['limit']) ? $data['PlacementSetting']['limit'] : 5000;

            $placementEntranceExamResultEntriesTable = TableRegistry::getTableLocator()->get('PlacementEntranceExamResultEntries');
            $conditions = ['PlacementEntranceExamResultEntries.placement_round_participant_id IN' => array_keys($allRoundParticipants)];

            if ($data['PlacementSetting']['include'] == 0) {
                $conditions[] = "PlacementEntranceExamResultEntries.accepted_student_id NOT IN (SELECT accepted_student_id FROM placement_participating_students WHERE applied_for = '{$data['PlacementSetting']['applied_for']}' AND academic_year = '{$data['PlacementSetting']['academic_year']}' AND round = '{$data['PlacementSetting']['round']}')";
            }

            $allStudentsWhoEntranceExam = $placementEntranceExamResultEntriesTable->find()
                ->where($conditions)
                ->contain(['Students', 'AcceptedStudents'])
                ->group(['PlacementEntranceExamResultEntries.student_id'])
                ->order([
                    'Students.id' => 'ASC',
                    'Students.program_id' => 'ASC',
                    'Students.program_type_id' => 'ASC'
                ])
                ->limit($limit)
                ->disableHydration()
                ->all()
                ->toArray();

            if (empty($allStudentsWhoEntranceExam)) {
                $allStudentsWhoEntranceExam = $this->find()
                    ->where(['PlacementPreferences.placement_round_participant_id IN' => array_keys($allRoundParticipants)])
                    ->contain(['PlacementRoundParticipants', 'Students', 'AcceptedStudents'])
                    ->group(['PlacementPreferences.student_id'])
                    ->order([
                        'Students.id' => 'ASC',
                        'Students.program_id' => 'ASC',
                        'Students.program_type_id' => 'ASC'
                    ])
                    ->limit($limit)
                    ->disableHydration()
                    ->all()
                    ->toArray();
            }

            if (!empty($allStudentsWhoEntranceExam)) {
                $programsTable = TableRegistry::getTableLocator()->get('Programs');
                $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
                $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
                $departmentsTable = TableRegistry::getTableLocator()->get('Departments');

                $selected_program_name = $programsTable->find()
                    ->select(['name'])
                    ->where(['Programs.id' => $allStudentsWhoEntranceExam[0]['Student']['program_id']])
                    ->disableHydration()
                    ->first()['name'] ?? '';

                $selected_program_type_name = $programTypesTable->find()
                    ->select(['name'])
                    ->where(['ProgramTypes.id' => $allStudentsWhoEntranceExam[0]['Student']['program_type_id']])
                    ->disableHydration()
                    ->first()['name'] ?? '';

                $selected_applied_unit_name = empty($allStudentsWhoEntranceExam[0]['Student']['department_id']) ?
                    $collegesTable->find()
                        ->select(['name'])
                        ->where(['Colleges.id' => $allStudentsWhoEntranceExam[0]['Student']['college_id']])
                        ->disableHydration()
                        ->first()['name'] ?? '' :
                    $departmentsTable->find()
                        ->select(['name'])
                        ->where(['Departments.id' => $allStudentsWhoEntranceExam[0]['Student']['department_id']])
                        ->disableHydration()
                        ->first()['name'] ?? '';

                foreach ($allStudentsWhoEntranceExam as $p => &$v) {
                    $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
                    $alreadyPrepared = $placementParticipatingStudentsTable->find('first')
                        ->where([
                            'PlacementParticipatingStudents.accepted_student_id' => $v['AcceptedStudent']['id'],
                            'PlacementParticipatingStudents.student_id' => $v['Student']['id'],
                            'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                            'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                            'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                            'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round']
                        ])
                        ->disableHydration()
                        ->first();

                    if ($data['PlacementSetting']['include'] == 0 && !empty($alreadyPrepared)) {
                        unset($allStudentsWhoEntranceExam[$p]);
                        continue;
                    }

                    $prep = 0;
                    $fresh = 0;
                    $entrance = 0;
                    $female_placement_weight = 0;
                    $disability_weight = 0;
                    $developing_region_weight = 0;
                    $freshmanResult = 0.0;

                    $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                    $freshManresult = $studentExamStatusesTable->find('first')
                        ->where([
                            'StudentExamStatuses.student_id' => $v['Student']['id'],
                            'StudentExamStatuses.academic_year' => $data['PlacementSetting']['academic_year'],
                            'StudentExamStatuses.semester' => $data['PlacementSetting']['round'] == 1 ? 'I' : 'II'
                        ])
                        ->contain(['AcademicStatuses' => ['fields' => ['id', 'name']]])
                        ->select(['StudentExamStatuses.academic_status_id', 'StudentExamStatuses.sgpa', 'StudentExamStatuses.cgpa'])
                        ->group(['StudentExamStatuses.student_id', 'StudentExamStatuses.semester', 'StudentExamStatuses.academic_year'])
                        ->order(['StudentExamStatuses.created' => 'DESC'])
                        ->disableHydration()
                        ->first();

                    if (!empty($freshManresult['AcademicStatus']['name'])) {
                        $v['Student']['academic_status'] = $freshManresult['AcademicStatus']['name'];
                    } else {
                        $v['Student']['academic_status'] = null;
                    }

                    if (!empty($freshManresult['StudentExamStatus']['cgpa'])) {
                        $v['Student']['cgpa'] = $freshManresult['StudentExamStatus']['cgpa'];
                    } else {
                        $v['Student']['cgpa'] = null;
                    }

                    if (!empty($freshManresult['StudentExamStatus']['academic_status_id']) && $freshManresult['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
                        unset($allStudentsWhoEntranceExam[$p]);
                        continue;
                    } elseif (empty($freshManresult['StudentExamStatus']['academic_status_id'])) {
                        unset($allStudentsWhoEntranceExam[$p]);
                        continue;
                    }

                    $placementResultSettings = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find()
                        ->where(['PlacementResultSettings.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']])
                        ->disableHydration()
                        ->all()
                        ->toArray();

                    $allPlacementResultSetting = [];
                    $allMaxPlacementResultSetting = [];

                    if (!empty($placementResultSettings)) {
                        foreach ($placementResultSettings as $pv) {
                            $allPlacementResultSetting[$pv['result_type']] = $pv['percent'];
                            $allMaxPlacementResultSetting[$pv['result_type']] = $pv['max_result'];
                        }
                    } else {
                        $error1 = "No placement setting is defined for round {$data['PlacementSetting']['round']} of {$selected_program_name} - {$selected_program_type_name} in {$selected_applied_unit_name}. This is for view only and it is generated with default placement settings(" . DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT . "% for Freshman CGPA out of 4.00, " . DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT . "% for Preparatory EHEECE total results out of 700 and " . DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT . "% for Department Entrance Exam out of 30). Please define Placement Setting first and try to Prepare.";
                        $this->validationErrors['NO_PLACEMENT_SETTING_FOUND'] = $error1;
                    }

                    if (!empty($freshManresult['StudentExamStatus']['cgpa'])) {
                        $freshmanResult = $freshManresult['StudentExamStatus']['cgpa'];
                    }

                    $other_settings = $placementRoundParticipantsTable->find('first')
                        ->where(['PlacementRoundParticipants.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']])
                        ->disableHydration()
                        ->first();

                    $region_ids = !empty($other_settings['PlacementRoundParticipant']['developing_region']) ?
                        explode(',', $other_settings['PlacementRoundParticipant']['developing_region']) :
                        [];

                    $freshamnResltSetting = $allPlacementResultSetting['freshman_result'] ?? '';
                    $entranceResltSetting = $allPlacementResultSetting['entrance_result'] ?? '';
                    $preparatoryResltSetting = $allPlacementResultSetting['EHEECE_total_results'] ?? '';

                    $entrance_settings = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find('first')
                        ->where([
                            'PlacementResultSettings.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
                            'PlacementResultSettings.result_type' => 'entrance_result'
                        ])
                        ->disableHydration()
                        ->first();

                    if (!empty($entrance_settings)) {
                        $entrance = !empty($allMaxPlacementResultSetting[$entrance_settings['result_type']]) ?
                            ($entrance_settings['percent'] * $v['PlacementEntranceExamResultEntry']['result']) / $allMaxPlacementResultSetting[$entrance_settings['result_type']] :
                            ($entrance_settings['percent'] * $v['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
                    }

                    $prepartory_settings = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find('first')
                        ->where([
                            'PlacementResultSettings.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
                            'PlacementResultSettings.result_type' => 'EHEECE_total_results'
                        ])
                        ->disableHydration()
                        ->first();

                    if (!empty($prepartory_settings)) {
                        if ($data['PlacementSetting']['academic_year'] == $v['AcceptedStudent']['academicyear']) {
                            $prep = !empty($allMaxPlacementResultSetting[$prepartory_settings['result_type']]) ?
                                ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / $allMaxPlacementResultSetting[$prepartory_settings['result_type']] :
                                ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
                        } else {
                            $prep = ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / 700;
                        }
                    }

                    $freshman_settings = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find('first')
                        ->where([
                            'PlacementResultSettings.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
                            'PlacementResultSettings.result_type' => 'freshman_result'
                        ])
                        ->disableHydration()
                        ->first();

                    if (!empty($freshman_settings)) {
                        $fresh = !empty($allMaxPlacementResultSetting[$freshman_settings['result_type']]) ?
                            ($freshman_settings['percent'] * $freshmanResult) / $allMaxPlacementResultSetting[$freshman_settings['result_type']] :
                            ($freshman_settings['percent'] * $freshmanResult) / FRESHMANMAXIMUM;
                    } else {
                        if (!empty($v['Student']['cgpa'])) {
                            $fresh = (DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * $v['Student']['cgpa']) / FRESHMANMAXIMUM;
                        }
                        if (!empty($v['AcceptedStudent']['EHEECE_total_results']) && $v['AcceptedStudent']['EHEECE_total_results'] > 100) {
                            $prep = (DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
                        }
                        if (!empty($v['PlacementEntranceExamResultEntry']['result']) && $v['PlacementEntranceExamResultEntry']['result'] >= 0) {
                            $entrance = (DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * $v['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
                        }
                    }

                    if (!empty($v['AcceptedStudent']['sex']) && (strcasecmp($v['AcceptedStudent']['sex'], 'female') === 0 || strcasecmp($v['AcceptedStudent']['sex'], 'f') === 0)) {
                        $female_placement_weight = !empty($points['female']) ? $points['female'] :
                            (is_numeric(INCLUDE_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT_BY_DEFAULT) && INCLUDE_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT_BY_DEFAULT == 1 ?
                                DEFAULT_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT : 0);
                    }

                    $v['PlacementEntranceExamResultEntry']['female_placement_weight'] = $female_placement_weight;

                    if (!empty($v['AcceptedStudent']['disability'])) {
                        $disability_weight += 5;
                    }

                    $v['PlacementEntranceExamResultEntry']['disability_weight'] = $disability_weight;

                    if (!empty($v['AcceptedStudent']['region_id']) && in_array($v['AcceptedStudent']['region_id'], $region_ids)) {
                        $developing_region_weight = (strcasecmp($v['AcceptedStudent']['sex'], 'female') === 0 || strcasecmp($v['AcceptedStudent']['sex'], 'f') === 0) ? 5 :
                            (!empty($v['AcceptedStudent']['disability']) ? 10 : 0);
                    }

                    $v['PlacementEntranceExamResultEntry']['developing_region_weight'] = $developing_region_weight;
                    $v['PlacementEntranceExamResultEntry']['result_weight'] = round(($prep + $fresh + $entrance), 2);
                    $v['PlacementEntranceExamResultEntry']['prepartory'] = round($prep, 2);
                    $v['PlacementEntranceExamResultEntry']['entrance'] = $entrance;
                    $v['PlacementEntranceExamResultEntry']['gpa'] = round($fresh, 2);
                    $v['PlacementEntranceExamResultEntry']['academic_year'] = $data['PlacementSetting']['academic_year'];
                    $v['PlacementEntranceExamResultEntry']['applied_for'] = $data['PlacementSetting']['applied_for'];
                    $v['PlacementEntranceExamResultEntry']['round'] = $data['PlacementSetting']['round'];
                    $v['PlacementEntranceExamResultEntry']['program_id'] = $data['PlacementSetting']['program_id'];
                    $v['PlacementEntranceExamResultEntry']['program_type_id'] = $data['PlacementSetting']['program_type_id'];
                    $v['PlacementEntranceExamResultEntry']['total_weight'] = round(
                        ($v['PlacementEntranceExamResultEntry']['developing_region_weight'] +
                            $v['PlacementEntranceExamResultEntry']['disability_weight'] +
                            $v['PlacementEntranceExamResultEntry']['female_placement_weight'] +
                            $v['PlacementEntranceExamResultEntry']['result_weight']),
                        2
                    );
                    $v['PlacementEntranceExamResultEntry']['total_placement_weight'] = $v['PlacementEntranceExamResultEntry']['total_weight'];

                    $v['PlacementParticipatingStudent'] = $alreadyPrepared ?: [];

                    usort($allStudentsWhoEntranceExam, [$this, 'cmp']);
                }

                return $allStudentsWhoEntranceExam;
            }
        }

        return [];
    }

    public function auto_placement_algorithm(array $data = []): array
    {
        $newly_placed_student = [];

        if (!empty($data)) {
            $units = $this->getListOfUnitNeedByPrivilageStudentMost($data);

            if (!empty($units)) {
                foreach ($units as $department_id => $weight) {
                    $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
                    $detail_of_participating_unit = $placementRoundParticipantsTable->find('first')
                        ->where([
                            'PlacementRoundParticipants.id' => $department_id,
                            'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                            'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                            'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round'],
                            'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                            'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id']
                        ])
                        ->disableHydration()
                        ->first();

                    $intake_capacity = $detail_of_participating_unit['PlacementRoundParticipant']['intake_capacity'];
                    $adjusted_privilaged_quota = [
                        'female' => $detail_of_participating_unit['PlacementRoundParticipant']['female_quota'],
                        'regions' => $detail_of_participating_unit['PlacementRoundParticipant']['region_quota'],
                        'disability' => $detail_of_participating_unit['PlacementRoundParticipant']['disability_quota']
                    ];

                    $preReadyNormalPrivilagedDepartmentAllocation = $this->checkAndAdjustPrivilagedQuota(
                        $data,
                        $department_id,
                        $adjusted_privilaged_quota,
                        $intake_capacity
                    );

                    $placedStudents = [];
                    $sortedStudentByPreferenceAndGrade = $this->sortOutStudentByPreference($data, $department_id);

                    if (!empty($sortedStudentByPreferenceAndGrade)) {
                        $n = ($intake_capacity <= count($sortedStudentByPreferenceAndGrade)) ? $intake_capacity : count($sortedStudentByPreferenceAndGrade);
                        for ($i = 0; $i < $n; $i++) {
                            $placedStudents['C'][] = $sortedStudentByPreferenceAndGrade[$i]['PlacementPreference']['accepted_student_id'];
                        }
                        unset($sortedStudentByPreferenceAndGrade);
                    }

                    $quotaBalanceForCompetitive = 0;
                    foreach ($preReadyNormalPrivilagedDepartmentAllocation[0] as $privilage_type => &$quota) {
                        if ($quota > 0) {
                            $privilaged_selected = $this->privilagedStudentsFilterOut(
                                $data,
                                $department_id,
                                $preReadyNormalPrivilagedDepartmentAllocation[0],
                                $placedStudents,
                                $privilage_type
                            );

                            if (!empty($privilaged_selected) && $quota <= count($privilaged_selected[$privilage_type])) {
                                $n = $quota;
                                for ($i = 0; $i < $n; $i++) {
                                    $placedStudents['Q'][] = $privilaged_selected[$privilage_type][$i];
                                }
                            } else {
                                $quotaBalanceForCompetitive = $quota - count($privilaged_selected[$privilage_type]);
                            }
                        }
                    }

                    $lastCA = count($placedStudents['C']);
                    if ($quotaBalanceForCompetitive > 0) {
                        $remain = $quotaBalanceForCompetitive;
                        for ($i = $lastCA; $remain > 0; $i++) {
                            $placedStudents['C'][] = $sortedStudentByPreferenceAndGrade[$i]['PlacementPreference']['accepted_student_id'];
                            $remain--;
                        }
                    } elseif ($lastCA < $intake_capacity) {
                        $remain = ($intake_capacity - $lastCA);
                        for ($i = $lastCA; $remain > 0; $i++) {
                            $placedStudents['C'][] = $sortedStudentByPreferenceAndGrade[$i]['PlacementPreference']['accepted_student_id'];
                            $remain--;
                        }
                    }
                    unset($sortedStudentByPreferenceAndGrade);

                    if (!empty($placedStudents)) {
                        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
                        $count = 0;
                        $failedC = 0;
                        $ittCount = 0;
                        $failedCArr = [];

                        foreach ($placedStudents as $key => $value) {
                            foreach ($value as $student_id) {
                                $ittCount++;
                                $preparedStudent = $placementParticipatingStudentsTable->find('first')
                                    ->where([
                                        'PlacementParticipatingStudents.accepted_student_id' => $student_id,
                                        'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                                        'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                                        'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                                        'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id']
                                    ])
                                    ->disableHydration()
                                    ->first();

                                if (!empty($preparedStudent['PlacementParticipatingStudent']['id'])) {
                                    $entity = $placementParticipatingStudentsTable->get($preparedStudent['PlacementParticipatingStudent']['id']);
                                    $entity->set([
                                        'placementtype' => AUTO_PLACEMENT,
                                        'placement_based' => $key,
                                        'placement_round_participant_id' => $department_id
                                    ]);
                                    $placementParticipatingStudentsTable->save($entity);
                                    $count++;
                                } else {
                                    $failedC++;
                                }
                            }
                            $failedCArr[$department_id] = $failedC;
                        }
                    }
                }

                $unitUnderQuota = $this->getListOfUnitsNotFullyAssignedQuota($data);
                $groupLength = count($unitUnderQuota) - 1;
                $count = 0;

                if (!empty($unitUnderQuota)) {
                    foreach ($unitUnderQuota as $department_id => $weight) {
                        $placedStudentss = [];
                        $notassignedList = $count == $groupLength ?
                            $this->sortOutForRandomAssignmentForNonAssigned($data, $department_id, true) :
                            $this->sortOutForRandomAssignmentForNonAssigned($data, $department_id);

                        $count++;

                        if (!empty($notassignedList)) {
                            $n = ($weight['weight'] <= count($notassignedList)) ? $weight['weight'] : count($notassignedList);
                            for ($i = 0; $i < $n; $i++) {
                                $placedStudentss['C'][] = $notassignedList[$i]['PlacementParticipatingStudent']['accepted_student_id'];
                            }
                            unset($notassignedList);

                            if (!empty($placedStudentss)) {
                                $failed = 0;
                                $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
                                foreach ($placedStudentss as $key => $value) {
                                    foreach ($value as $student_id) {
                                        $preparedStudent = $placementParticipatingStudentsTable->find('first')
                                            ->where([
                                                'PlacementParticipatingStudents.accepted_student_id' => $student_id,
                                                'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                                                'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                                                'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                                                'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                                                'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id']
                                            ])
                                            ->disableHydration()
                                            ->first();

                                        if (!empty($preparedStudent['PlacementParticipatingStudent']['id']) && empty($preparedStudent['PlacementParticipatingStudent']['placement_round_participant_id'])) {
                                            $entity = $placementParticipatingStudentsTable->get($preparedStudent['PlacementParticipatingStudent']['id']);
                                            $entity->set([
                                                'placementtype' => AUTO_PLACEMENT,
                                                'placement_based' => $key,
                                                'placement_round_participant_id' => $department_id
                                            ]);
                                            $placementParticipatingStudentsTable->save($entity);
                                        } else {
                                            $failed++;
                                        }
                                    }
                                }
                            }
                        }
                    }
                }
            }

            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
            $placedstudent = $placementParticipatingStudentsTable->find()
                ->where([
                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementParticipatingStudents.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                    'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id']
                ])
                ->contain([
                    'AcceptedStudents' => ['PlacementPreferences'],
                    'Students',
                    'Programs',
                    'ProgramTypes',
                    'PlacementRoundParticipants'
                ])
                ->order([
                    'PlacementParticipatingStudents.placement_round_participant_id' => 'ASC',
                    'PlacementParticipatingStudents.total_placement_weight' => 'DESC'
                ])
                ->limit(10000)
                ->disableHydration()
                ->all()
                ->toArray();

            $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
            $units = $placementRoundParticipantsTable->find('list')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                    'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
                ])
                ->select(['id'])
                ->disableHydration()
                ->toArray();

            $dep_id = array_keys($units);
            $dep_name = $placementRoundParticipantsTable->find('list')
                ->where(['PlacementRoundParticipants.id IN' => $dep_id])
                ->disableHydration()
                ->toArray();

            if (!empty($dep_name)) {
                foreach ($dep_name as $dk => $dv) {
                    if (!empty($placedstudent)) {
                        foreach ($placedstudent as $k => $v) {
                            if ($dk == $v['PlacementRoundParticipant']['id']) {
                                $newly_placed_student[$dv][$k] = $v;
                            }
                        }
                    }

                    $newly_placed_student['auto_summery'][$dv]['C'] = $placementParticipatingStudentsTable->find('count')
                        ->where([
                            'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                            'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                            'PlacementParticipatingStudents.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                            'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                            'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                            'PlacementParticipatingStudents.placement_round_participant_id' => $dk,
                            'PlacementParticipatingStudents.placement_based' => 'C'
                        ])
                        ->disableHydration()
                        ->count();

                    $newly_placed_student['auto_summery'][$dv]['Q'] = $placementParticipatingStudentsTable->find('count')
                        ->where([
                            'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                            'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                            'PlacementParticipatingStudents.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                            'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                            'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                            'PlacementParticipatingStudents.placement_round_participant_id' => $dk,
                            'PlacementParticipatingStudents.placement_based' => 'Q'
                        ])
                        ->disableHydration()
                        ->count();
                }
            }
        }

        return $newly_placed_student;
    }

    public function getAssignedStudentsForDirectPlacement(array $data): array
    {
        $options = [];

        if (!empty($data['PlacementSetting']['assigned_to'])) {
            $options['PlacementParticipatingStudents.placement_round_participant_id'] = $data['PlacementSetting']['assigned_to'];
        }

        if (!empty($data['PlacementSetting']['placement_based']) && $data['PlacementSetting']['placement_based'] !== 'all') {
            $options['PlacementParticipatingStudents.placement_based'] = $data['PlacementSetting']['placement_based'];
        }

        if (!empty($data['PlacementSetting']['placementtype']) && $data['PlacementSetting']['placementtype'] !== 'all') {
            $options['PlacementParticipatingStudents.placementtype'] = $data['PlacementSetting']['placementtype'];
        }

        if (!empty($data['PlacementSetting']['gender']) && $data['PlacementSetting']['gender'] !== 'All') {
            $options['AcceptedStudents.sex'] = $data['PlacementSetting']['gender'];
        }

        if (!empty($data['PlacementSetting']['round'])) {
            $options['PlacementParticipatingStudents.round'] = $data['PlacementSetting']['round'];
        }

        if (!empty($data['PlacementSetting']['academic_year'])) {
            $options['PlacementParticipatingStudents.academic_year LIKE'] = $data['PlacementSetting']['academic_year'] . '%';
        }

        if (!empty($data['PlacementSetting']['program_id'])) {
            $options['PlacementParticipatingStudents.program_id'] = $data['PlacementSetting']['program_id'];
        }

        if (!empty($data['PlacementSetting']['program_type_id'])) {
            $options['PlacementParticipatingStudents.program_type_id'] = $data['PlacementSetting']['program_type_id'];
        }

        $options[] = 'PlacementParticipatingStudents.accepted_student_id IN (SELECT id FROM accepted_students WHERE curriculum_id = 0 OR curriculum_id IS NULL)';

        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
        $placedstudent = $placementParticipatingStudentsTable->find()
            ->where($options)
            ->contain([
                'AcceptedStudents' => ['PlacementPreferences'],
                'Students',
                'Programs',
                'ProgramTypes',
                'PlacementRoundParticipants'
            ])
            ->order([
                'PlacementParticipatingStudents.placement_round_participant_id' => 'ASC',
                'PlacementParticipatingStudents.total_placement_weight' => 'DESC'
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        return $placedstudent;
    }



    public function getAssignedStudents(array $data): array
    {
        $newly_placed_student = [];
        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $units = $placementRoundParticipantsTable->find('list')
            ->where([
                'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
            ])
            ->select(['id'])
            ->disableHydration()
            ->toArray();

        $options = [];

        if (!empty($data['PlacementSetting']['assigned_to'])) {
            $options['PlacementParticipatingStudents.placement_round_participant_id'] = $data['PlacementSetting']['assigned_to'];
        }

        if (!empty($data['PlacementSetting']['placement_based']) && $data['PlacementSetting']['placement_based'] !== 'all') {
            $options['PlacementParticipatingStudents.placement_based'] = $data['PlacementSetting']['placement_based'];
        }

        if (!empty($data['PlacementSetting']['placementtype']) && $data['PlacementSetting']['placementtype'] !== 'all') {
            $options['PlacementParticipatingStudents.placementtype'] = $data['PlacementSetting']['placementtype'];
        }

        if (!empty($data['PlacementSetting']['gender']) && $data['PlacementSetting']['gender'] !== 'All') {
            $options['AcceptedStudents.sex'] = $data['PlacementSetting']['gender'];
        }

        if (!empty($data['PlacementSetting']['round'])) {
            $options['PlacementParticipatingStudents.round'] = $data['PlacementSetting']['round'];
        }

        if (!empty($data['PlacementSetting']['academic_year'])) {
            $options['PlacementParticipatingStudents.academic_year'] = $data['PlacementSetting']['academic_year'];
        }

        if (!empty($data['PlacementSetting']['program_id'])) {
            $options['PlacementParticipatingStudents.program_id'] = $data['PlacementSetting']['program_id'];
        }

        if (!empty($data['PlacementSetting']['program_type_id'])) {
            $options['PlacementParticipatingStudents.program_type_id'] = $data['PlacementSetting']['program_type_id'];
        }

        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
        $placedstudent = $placementParticipatingStudentsTable->find()
            ->where($options)
            ->contain([
                'AcceptedStudents' => ['PlacementPreferences'],
                'Students',
                'Programs',
                'ProgramTypes',
                'PlacementRoundParticipants'
            ])
            ->order([
                'PlacementParticipatingStudents.placement_round_participant_id' => 'ASC',
                'PlacementParticipatingStudents.total_placement_weight' => 'DESC'
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $dep_id = array_keys($units);
        $dep_name = $placementRoundParticipantsTable->find('list')
            ->where(['PlacementRoundParticipants.id IN' => $dep_id])
            ->disableHydration()
            ->toArray();

        if (!empty($dep_name)) {
            foreach ($dep_name as $dk => $dv) {
                if (!empty($placedstudent)) {
                    foreach ($placedstudent as $k => $v) {
                        if ($dk == $v['PlacementRoundParticipant']['id']) {
                            $newly_placed_student[$dv][$k] = $v;
                        }
                    }
                }

                $newly_placed_student['auto_summery'][$dv]['C'] = $placementParticipatingStudentsTable->find('count')
                    ->where([
                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                        'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                        'PlacementParticipatingStudents.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                        'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                        'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                        'PlacementParticipatingStudents.placement_round_participant_id' => $dk,
                        'PlacementParticipatingStudents.placement_based' => 'C'
                    ])
                    ->disableHydration()
                    ->count();

                $newly_placed_student['auto_summery'][$dv]['Q'] = $placementParticipatingStudentsTable->find('count')
                    ->where([
                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                        'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                        'PlacementParticipatingStudents.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                        'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                        'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                        'PlacementParticipatingStudents.placement_round_participant_id' => $dk,
                        'PlacementParticipatingStudents.placement_based' => 'Q'
                    ])
                    ->disableHydration()
                    ->count();
            }
        }

        return $newly_placed_student;
    }

    public function cancel_placement_algorithm(array $data = []): bool
    {
        if (!empty($data)) {
            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
            $update = $placementParticipatingStudentsTable->updateAll(
                [
                    'placementtype' => null,
                    'placement_based' => null,
                    'placement_round_participant_id' => null
                ],
                [
                    'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                    'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id']
                ]
            );

            return true;
        }

        return false;
    }

    public function approve_placement(array $data = [], int $type = 1): int
    {
        if (!empty($data)) {
            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
            $alreadyApproved = $placementParticipatingStudentsTable->find('count')
                ->where([
                    'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                    'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                    'PlacementParticipatingStudents.status' => 1
                ])
                ->disableHydration()
                ->count();

            $update = $placementParticipatingStudentsTable->updateAll(
                [
                    'status' => $type,
                    'remark' => $data['PlacementSetting']['remark']
                ],
                [
                    'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                    'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id']
                ]
            );

            $allPlacedStudents = $placementParticipatingStudentsTable->find()
                ->where([
                    'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                    'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id']
                ])
                ->contain([
                    'AcceptedStudents',
                    'Students',
                    'Programs',
                    'ProgramTypes',
                    'PlacementRoundParticipants'
                ])
                ->disableHydration()
                ->all()
                ->toArray();

            $clgexp = explode('~', $data['PlacementSetting']['applied_for']);
            $campusesTable = TableRegistry::getTableLocator()->get('Campuses');
            $campuses = $campusesTable->find('list')
                ->where(['Campuses.available_for_college' => $clgexp[1]])
                ->select(['id'])
                ->disableHydration()
                ->toArray();

            $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
            $collegeIds = $collegesTable->find('list')
                ->where(['Colleges.campus_id IN' => array_keys($campuses)])
                ->select(['id'])
                ->disableHydration()
                ->toArray();

            $sectionAttendedInStudent = [];
            $sectionArchived = [];
            $collegePlacement = false;

            if (!empty($allPlacedStudents)) {
                $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
                $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
                $studentsTable = TableRegistry::getTableLocator()->get('Students');
                $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
                $specializationsTable = TableRegistry::getTableLocator()->get('Specializations');

                foreach ($allPlacedStudents as $pv) {
                    if ($pv['PlacementRoundParticipant']['type'] === 'College') {
                        $collegePlacement = true;
                        $sectionAttended = $studentsSectionsTable->find('list')
                            ->where([
                                'StudentsSections.student_id' => $pv['PlacementParticipatingStudent']['student_id'],
                                'StudentsSections.section_id IN (SELECT id FROM sections WHERE academicyear = :academicyear AND program_id = :program_id AND program_type_id = :program_type_id)'
                            ])
                            ->bind(':academicyear', $pv['PlacementParticipatingStudent']['academic_year'])
                            ->bind(':program_id', $pv['PlacementParticipatingStudent']['program_id'])
                            ->bind(':program_type_id', $pv['PlacementParticipatingStudent']['program_type_id'])
                            ->select(['section_id'])
                            ->disableHydration()
                            ->toArray();

                        $entity = $acceptedStudentsTable->get($pv['AcceptedStudent']['id']);
                        $entity->set([
                            'college_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                            'original_college_id' => $pv['PlacementRoundParticipant']['foreign_key']
                        ]);
                        $acceptedStudentsTable->save($entity);

                        $entity = $studentsTable->get($pv['Student']['id']);
                        $entity->set([
                            'college_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                            'original_college_id' => $pv['PlacementRoundParticipant']['foreign_key']
                        ]);
                        $studentsTable->save($entity);
                    } elseif ($pv['PlacementRoundParticipant']['type'] === 'Department') {
                        $collegePlacement = false;
                        $collegeID = $departmentsTable->find('first')
                            ->where(['Departments.id' => $pv['PlacementRoundParticipant']['foreign_key']])
                            ->contain(['Colleges'])
                            ->disableHydration()
                            ->first();

                        $entity = $acceptedStudentsTable->get($pv['AcceptedStudent']['id']);
                        $entity->set([
                            'department_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                            'college_id' => $collegeID['College']['id'] ?? null,
                            'original_college_id' => $collegeID['College']['id'] ?? null
                        ]);
                        $acceptedStudentsTable->save($entity);

                        $entity = $studentsTable->get($pv['Student']['id']);
                        $entity->set([
                            'department_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                            'college_id' => $collegeID['College']['id'] ?? null,
                            'original_college_id' => $collegeID['College']['id'] ?? null
                        ]);
                        $studentsTable->save($entity);
                    } elseif ($pv['PlacementRoundParticipant']['type'] === 'Specialization') {
                        $collegePlacement = false;
                        $departmentID = $specializationsTable->find('first')
                            ->where(['Specializations.department_id' => $pv['PlacementRoundParticipant']['foreign_key']])
                            ->contain(['Departments'])
                            ->disableHydration()
                            ->first();

                        $entity = $acceptedStudentsTable->get($pv['AcceptedStudent']['id']);
                        $entity->set([
                            'specialization_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                            'department_id' => $departmentID['Department']['id'] ?? null,
                            'college_id' => $departmentID['Department']['college_id'] ?? null
                        ]);
                        $acceptedStudentsTable->save($entity);

                        $entity = $studentsTable->get($pv['Student']['id']);
                        $entity->set([
                            'specialization_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                            'department_id' => $departmentID['Department']['id'] ?? null,
                            'college_id' => $departmentID['Department']['college_id'] ?? null
                        ]);
                        $studentsTable->save($entity);
                    }

                    if ($pv['PlacementRoundParticipant']['type'] === 'College' && !empty($collegeIds)) {
                        $sectionAttended = $studentsSectionsTable->find('list')
                            ->where([
                                'StudentsSections.student_id' => $pv['Student']['id'],
                                'StudentsSections.section_id IN (SELECT id FROM sections WHERE academicyear = :academicyear AND program_id = :program_id AND program_type_id = :program_type_id AND college_id IN (:college_ids))'
                            ])
                            ->bind(':academicyear', $pv['PlacementParticipatingStudent']['academic_year'])
                            ->bind(':program_id', $pv['PlacementParticipatingStudent']['program_id'])
                            ->bind(':program_type_id', $pv['PlacementParticipatingStudent']['program_type_id'])
                            ->bind(':college_ids', implode(',', $collegeIds))
                            ->select(['section_id'])
                            ->disableHydration()
                            ->toArray();
                    } else {
                        $sectionAttended = $studentsSectionsTable->find('list')
                            ->where([
                                'StudentsSections.student_id' => $pv['Student']['id'],
                                'StudentsSections.archive' => 0,
                                'StudentsSections.section_id IN (SELECT id FROM sections WHERE academicyear = :academicyear AND program_id = :program_id AND program_type_id = :program_type_id)'
                            ])
                            ->bind(':academicyear', $pv['PlacementRoundParticipant']['academic_year'])
                            ->bind(':program_id', $pv['PlacementRoundParticipant']['program_id'])
                            ->bind(':program_type_id', $pv['PlacementRoundParticipant']['program_type_id'])
                            ->select(['section_id'])
                            ->disableHydration()
                            ->toArray();
                    }

                    if (!empty($sectionAttended)) {
                        if ($pv['PlacementRoundParticipant']['type'] === 'College') {
                            $intoconsideration = [];
                            if ($clgexp[1] == 2) {
                                $intoconsideration = [1, 11];
                            } elseif ($clgexp[1] == 6) {
                                $intoconsideration = [6, 5];
                            }

                            $sectionsTable = TableRegistry::getTableLocator()->get('Sections');
                            $sectionsDetails = $sectionsTable->find()
                                ->where(['Sections.id IN' => $sectionAttended])
                                ->disableHydration()
                                ->all()
                                ->toArray();

                            foreach ($sectionsDetails as $secv) {
                                $sectionArchived[$secv['id']] = $secv['id'];
                                if ($pv['PlacementRoundParticipant']['foreign_key'] == $secv['college_id'] || in_array($secv['college_id'], $intoconsideration)) {
                                    $studentsSectionsTable->updateAll(
                                        ['archive' => 0],
                                        [
                                            'StudentsSections.student_id' => $pv['Student']['id'],
                                            'StudentsSections.section_id' => $secv['id']
                                        ]
                                    );
                                } else {
                                    $studentsSectionsTable->updateAll(
                                        ['archive' => 1],
                                        [
                                            'StudentsSections.student_id' => $pv['Student']['id'],
                                            'StudentsSections.section_id' => $secv['id']
                                        ]
                                    );
                                }
                            }
                        } else {
                            $studentsSectionsTable->updateAll(
                                ['archive' => 1],
                                [
                                    'StudentsSections.student_id' => $pv['Student']['id'],
                                    'StudentsSections.section_id IN' => $sectionAttended
                                ]
                            );
                        }

                        $sectionAttendedInStudent[] = $pv['Student']['id'];
                    }
                }

                if (!empty($sectionAttendedInStudent) && !empty($sectionArchived) && $collegePlacement) {
                    $studentsSectionsTable->updateAll(
                        ['archive' => 1],
                        [
                            'StudentsSections.student_id NOT IN' => $sectionAttendedInStudent,
                            'StudentsSections.section_id IN' => $sectionArchived
                        ]
                    );

                    $studentsNotAssignedToEngineeringScience = $studentsSectionsTable->find('list')
                        ->where([
                            'StudentsSections.student_id NOT IN' => $sectionAttendedInStudent,
                            'StudentsSections.student_id IN (SELECT id FROM students WHERE department_id IS NULL OR department_id = "" OR department_id = 0)',
                            'StudentsSections.section_id IN' => $sectionArchived
                        ])
                        ->select(['student_id'])
                        ->disableHydration()
                        ->toArray();

                    if (!empty($studentsNotAssignedToEngineeringScience)) {
                        $acceptedStudentNotAssignedToEngineeringScience = $studentsTable->find('list')
                            ->where(['Students.id IN' => $studentsNotAssignedToEngineeringScience])
                            ->select(['accepted_student_id'])
                            ->disableHydration()
                            ->toArray();

                        if (!empty($acceptedStudentNotAssignedToEngineeringScience) && !empty($clgexp[1])) {
                            $studentsTable->updateAll(
                                [
                                    'college_id' => $clgexp[1],
                                    'original_college_id' => $clgexp[1]
                                ],
                                ['Students.id IN' => $studentsNotAssignedToEngineeringScience]
                            );

                            $acceptedStudentsTable->updateAll(
                                [
                                    'college_id' => $clgexp[1],
                                    'original_college_id' => $clgexp[1]
                                ],
                                ['AcceptedStudents.id IN' => $acceptedStudentNotAssignedToEngineeringScience]
                            );

                            $collegeConsideration = [$clgexp[1]];
                            if ($clgexp[1] == 2) {
                                $collegeConsideration = [2, 4, 3];
                            } elseif ($clgexp[1] == 6) {
                                $collegeConsideration = [6, 9];
                            }

                            $targetCollegeSectionIds = $sectionsTable->find('list')
                                ->where([
                                    'Sections.academicyear' => $data['PlacementSetting']['academic_year'],
                                    'Sections.program_id' => $data['PlacementSetting']['program_id'],
                                    'Sections.program_type_id' => $data['PlacementSetting']['program_type_id'],
                                    'Sections.college_id IN' => $collegeConsideration,
                                    'Sections.department_id IS NULL'
                                ])
                                ->select(['id'])
                                ->disableHydration()
                                ->toArray();

                            if (!empty($targetCollegeSectionIds)) {
                                $studentsSectionsTable->updateAll(
                                    ['archive' => 0],
                                    [
                                        'StudentsSections.student_id IN' => $studentsNotAssignedToEngineeringScience,
                                        'StudentsSections.section_id IN' => $targetCollegeSectionIds
                                    ]
                                );

                                $targetCollegeSectionIdss = $sectionsTable->find('list')
                                    ->where([
                                        'Sections.academicyear' => $data['PlacementSetting']['academic_year'],
                                        'Sections.program_id' => $data['PlacementSetting']['program_id'],
                                        'Sections.program_type_id' => $data['PlacementSetting']['program_type_id'],
                                        'Sections.college_id IN' => $collegeIds,
                                        'Sections.department_id IS NULL'
                                    ])
                                    ->select(['id'])
                                    ->disableHydration()
                                    ->toArray();

                                foreach ($targetCollegeSectionIdss as $secv) {
                                    $count = $studentsSectionsTable->find('count')
                                        ->where([
                                            'StudentsSections.section_id' => $secv,
                                            'StudentsSections.archive' => 0
                                        ])
                                        ->disableHydration()
                                        ->count();

                                    if ($count == 0) {
                                        $sectionsTable->updateAll(
                                            ['archive' => 1],
                                            ['Sections.id' => $secv]
                                        );
                                    }
                                }
                            }
                        }

                        foreach ($studentsNotAssignedToEngineeringScience as $sv) {
                            $studentdetail = $studentsTable->find('first')
                                ->where(['Students.id' => $sv])
                                ->disableHydration()
                                ->first();

                            $studentsection = $studentsSectionsTable->find('first')
                                ->where([
                                    'StudentsSections.student_id' => $studentdetail['Student']['id'],
                                    'StudentsSections.archive' => 0
                                ])
                                ->contain(['Sections'])
                                ->disableHydration()
                                ->first();

                            if (!empty($studentdetail) && !empty($studentsection['Section']['college_id'])) {
                                $entity = $acceptedStudentsTable->get($studentdetail['Student']['accepted_student_id']);
                                $entity->set(['college_id' => $studentsection['Section']['college_id']]);
                                $acceptedStudentsTable->save($entity);

                                $entity = $studentsTable->get($studentdetail['Student']['id']);
                                $entity->set(['college_id' => $studentsection['Section']['college_id']]);
                                $studentsTable->save($entity);
                            }
                        }
                    }
                }
            }

            return 1;
        }

        return 0;
    }

    public function direct_placement(array $data = [], string $type = ''): int
    {
        if (!empty($data)) {
            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
            $selectedParticipantsIds = array_keys($data['PlacementDirectly']['approve'], 1);

            $update = $placementParticipatingStudentsTable->updateAll(
                [
                    'placement_round_participant_id' => $data['PlacementDirectly']['placement_round_participant_id'],
                    'placementtype' => $type
                ],
                ['PlacementParticipatingStudents.id IN' => $selectedParticipantsIds]
            );

            $allPlacedStudents = $placementParticipatingStudentsTable->find()
                ->where(['PlacementParticipatingStudents.id IN' => $selectedParticipantsIds])
                ->contain([
                    'AcceptedStudents',
                    'Students',
                    'Programs',
                    'ProgramTypes',
                    'PlacementRoundParticipants'
                ])
                ->disableHydration()
                ->all()
                ->toArray();

            $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
            $studentsTable = TableRegistry::getTableLocator()->get('Students');
            $studentsSectionsTable = TableRegistry::getTableLocator()->get('StudentsSections');
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $specializationsTable = TableRegistry::getTableLocator()->get('Specializations');

            foreach ($allPlacedStudents as $pv) {
                if ($pv['PlacementRoundParticipant']['type'] === 'College') {
                    $sectionAttended = $studentsSectionsTable->find('list')
                        ->where([
                            'StudentsSections.student_id' => $pv['PlacementParticipatingStudent']['student_id'],
                            'StudentsSections.section_id IN (SELECT id FROM sections WHERE academicyear = :academicyear AND program_id = :program_id AND program_type_id = :program_type_id)'
                        ])
                        ->bind(':academicyear', $pv['PlacementParticipatingStudent']['academic_year'])
                        ->bind(':program_id', $pv['PlacementParticipatingStudent']['program_id'])
                        ->bind(':program_type_id', $pv['PlacementParticipatingStudent']['program_type_id'])
                        ->select(['section_id'])
                        ->disableHydration()
                        ->toArray();

                    $entity = $acceptedStudentsTable->get($pv['AcceptedStudent']['id']);
                    $entity->set([
                        'college_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                        'original_college_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                        'department_id' => null
                    ]);
                    $acceptedStudentsTable->save($entity);

                    $entity = $studentsTable->get($pv['Student']['id']);
                    $entity->set([
                        'college_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                        'original_college_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                        'department_id' => null
                    ]);
                    $studentsTable->save($entity);

                    $sectionAttended = $studentsSectionsTable->find('list')
                        ->where([
                            'StudentsSections.student_id' => $pv['Student']['id'],
                            'StudentsSections.archive' => 1,
                            'StudentsSections.section_id IN (SELECT id FROM sections WHERE academicyear = :academicyear AND program_id = :program_id AND program_type_id = :program_type_id)'
                        ])
                        ->bind(':academicyear', $pv['PlacementRoundParticipant']['academic_year'])
                        ->bind(':program_id', $pv['PlacementRoundParticipant']['program_id'])
                        ->bind(':program_type_id', $pv['PlacementRoundParticipant']['program_type_id'])
                        ->select(['section_id'])
                        ->disableHydration()
                        ->toArray();

                    if (!empty($sectionAttended)) {
                        $studentsSectionsTable->updateAll(
                            ['archive' => 1],
                            [
                                'StudentsSections.student_id' => $pv['Student']['id'],
                                'StudentsSections.section_id IN' => $sectionAttended
                            ]
                        );
                    }
                } elseif ($pv['PlacementRoundParticipant']['type'] === 'Department') {
                    $collegeID = $departmentsTable->find('first')
                        ->where(['Departments.college_id' => $pv['PlacementRoundParticipant']['foreign_key']])
                        ->disableHydration()
                        ->first();

                    $entity = $acceptedStudentsTable->get($pv['AcceptedStudent']['id']);
                    $entity->set([
                        'department_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                        'college_id' => $collegeID['College']['id'] ?? null,
                        'original_college_id' => $collegeID['College']['id'] ?? null
                    ]);
                    $acceptedStudentsTable->save($entity);

                    $entity = $studentsTable->get($pv['Student']['id']);
                    $entity->set([
                        'department_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                        'college_id' => $collegeID['College']['id'] ?? null,
                        'original_college_id' => $collegeID['College']['id'] ?? null
                    ]);
                    $studentsTable->save($entity);
                } elseif ($pv['PlacementRoundParticipant']['type'] === 'Specialization') {
                    $departmentID = $specializationsTable->find('first')
                        ->where(['Specializations.department_id' => $pv['PlacementRoundParticipant']['foreign_key']])
                        ->contain(['Departments'])
                        ->disableHydration()
                        ->first();

                    $entity = $acceptedStudentsTable->get($pv['AcceptedStudent']['id']);
                    $entity->set([
                        'specialization_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                        'department_id' => $departmentID['Department']['id'] ?? null,
                        'college_id' => $departmentID['Department']['college_id'] ?? null
                    ]);
                    $acceptedStudentsTable->save($entity);

                    $entity = $studentsTable->get($pv['Student']['id']);
                    $entity->set([
                        'specialization_id' => $pv['PlacementRoundParticipant']['foreign_key'],
                        'department_id' => $departmentID['Department']['id'] ?? null,
                        'college_id' => $departmentID['Department']['college_id'] ?? null
                    ]);
                    $studentsTable->save($entity);
                }
            }

            return 1;
        }

        return 0;
    }

    public function getAppliedUnitsId(string $appliedFor): array
    {
        $targetUnit = explode('~', $appliedFor);
        $restoringUnits = [];

        if ($targetUnit[0] === 'c') {
            $restoringUnits['college_id'] = $targetUnit[1];
        } elseif ($targetUnit[0] === 'd') {
            $restoringUnits['department_id'] = $targetUnit[1];
        } elseif ($targetUnit[0] === 's') {
            $restoringUnits['specialization_id'] = $targetUnit[1];
        }

        return $restoringUnits;
    }

    public function privilagedStudentsFilterOut(array $data, int $department_id, array $adjusted_privilaged_quota, array $placedStudents, string $privilage_type): array
    {
        $competitivly_assigned_students = $placedStudents['C'] ?? [];
        $quota_assigned_students = $placedStudents['Q'] ?? [];

        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $number_of_participating_department = $placementRoundParticipantsTable->find('count')
            ->where([
                'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round'],
                'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id']
            ])
            ->disableHydration()
            ->count();

        if (strcasecmp($privilage_type, 'female') === 0) {
            $privilagedcondition = 'AcceptedStudents.sex = \'female\'';
        } elseif (strcasecmp($privilage_type, 'disability') === 0) {
            $privilagedcondition = 'AcceptedStudents.disability IS NOT NULL';
        } else {
            $regions = $placementRoundParticipantsTable->find('first')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round'],
                    'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id']
                ])
                ->disableHydration()
                ->first();

            if (empty($regions['PlacementRoundParticipant']['developing_region'])) {
                return [];
            }

            $privilagedcondition = "AcceptedStudents.region_id IN ({$regions['PlacementRoundParticipant']['developing_region']})";
        }

        $list_students_in_x_preference = [];
        $list_of_students_selected = [];

        if ($number_of_participating_department && $adjusted_privilaged_quota[$privilage_type] > 0) {
            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');

            for ($i = 1; $i <= $number_of_participating_department; $i++) {
                $list_students_in_x_preference = $placementParticipatingStudentsTable->find()
                    ->select([
                        'PlacementPreferences.preference_order',
                        'PlacementParticipatingStudents.id',
                        'PlacementPreferences.accepted_student_id',
                        'PlacementParticipatingStudents.total_placement_weight'
                    ])
                    ->leftJoinWith('PlacementPreferences')
                    ->where([
                        'OR' => [
                            'PlacementParticipatingStudents.placement_round_participant_id IS NULL',
                            'PlacementParticipatingStudents.placement_round_participant_id = ""',
                            'PlacementParticipatingStudents.placement_round_participant_id = 0'
                        ],
                        'PlacementPreferences.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                        'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                        'PlacementPreferences.placement_round_participant_id' => $department_id,
                        'PlacementPreferences.preference_order' => $i,
                        $privilagedcondition
                    ])
                    ->contain(['AcceptedStudents'])
                    ->order([
                        'PlacementPreferences.preference_order' => 'ASC',
                        'PlacementParticipatingStudents.total_placement_weight' => 'DESC'
                    ])
                    ->group([
                        'PlacementPreferences.preference_order',
                        'PlacementPreferences.accepted_student_id'
                    ])
                    ->disableHydration()
                    ->all()
                    ->toArray();

                if ($i == 1) {
                    foreach ($list_students_in_x_preference as $student) {
                        if (!in_array($student['PlacementPreference']['accepted_student_id'], $competitivly_assigned_students) &&
                            !in_array($student['PlacementPreference']['accepted_student_id'], $quota_assigned_students)) {
                            $list_of_students_selected[] = $student['PlacementPreference']['accepted_student_id'];
                        }
                    }

                    if (count($list_of_students_selected) >= $adjusted_privilaged_quota[$privilage_type]) {
                        break;
                    }

                    continue;
                }

                $list_of_departments_id = $placementParticipatingStudentsTable->find()
                    ->select(['placement_round_participant_id'])
                    ->distinct(['placement_round_participant_id'])
                    ->where([
                        'PlacementParticipatingStudents.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                        'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                        'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                        'PlacementParticipatingStudents.placement_round_participant_id IS NOT NULL',
                        'PlacementParticipatingStudents.placement_round_participant_id != ""',
                        'PlacementParticipatingStudents.placement_round_participant_id != 0'
                    ])
                    ->disableHydration()
                    ->all()
                    ->toArray();

                $reformat_list_of_department_ids = array_column($list_of_departments_id, 'placement_round_participant_id');
                $preliminary_students_filter = [];

                foreach ($list_students_in_x_preference as &$student) {
                    $exclude_student = false;
                    for ($j = 1; $j < $i; $j++) {
                        $department_id_accepted_student = $this->find('first')
                            ->where([
                                'PlacementPreferences.accepted_student_id' => $student['PlacementPreference']['accepted_student_id'],
                                'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
                                'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                                'PlacementPreferences.preference_order' => $j
                            ])
                            ->select(['placement_round_participant_id'])
                            ->disableHydration()
                            ->first();

                        if (!empty($reformat_list_of_department_ids) &&
                            !empty($department_id_accepted_student['PlacementPreference']['placement_round_participant_id']) &&
                            is_numeric($department_id_accepted_student['PlacementPreference']['placement_round_participant_id']) &&
                            $department_id_accepted_student['PlacementPreference']['placement_round_participant_id'] > 0 &&
                            !in_array($department_id_accepted_student['PlacementPreference']['placement_round_participant_id'], $reformat_list_of_department_ids)) {
                            $exclude_student = true;
                            break;
                        }
                    }

                    if (!$exclude_student) {
                        $preliminary_students_filter[] = $student['PlacementPreference']['accepted_student_id'];
                    }
                }

                foreach ($preliminary_students_filter as $student_id) {
                    if (!in_array($student_id, $competitivly_assigned_students) &&
                        !in_array($student_id, $quota_assigned_students)) {
                        $list_of_students_selected[] = $student_id;
                    }
                }

                if (count($list_of_students_selected) >= $adjusted_privilaged_quota[$privilage_type]) {
                    break;
                }
            }

            $privilaged_selected[$privilage_type] = $list_of_students_selected;
            return $privilaged_selected;
        }

        return [];
    }



    public function getListOfUnitNeedByPrivilageStudentMost(array $data = []): array
    {
        $prefrenceMatrixOfDepartments = $this->find()
            ->select([
                'PlacementPreferences.placement_round_participant_id',
                'PlacementPreferences.preference_order',
                'student_count' => 'COUNT(PlacementPreferences.accepted_student_id)'
            ])
            ->where([
                'PlacementPreferences.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                'PlacementPreferences.preference_order IN' => [1, 2, 3],
                'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for']
            ])
            ->group([
                'PlacementPreferences.placement_round_participant_id',
                'PlacementPreferences.preference_order'
            ])
            ->order([
                'PlacementPreferences.placement_round_participant_id' => 'ASC',
                'PlacementPreferences.preference_order' => 'ASC'
            ])
            ->contain(['PlacementRoundParticipants'])
            ->limit(100000)
            ->disableHydration()
            ->all()
            ->toArray();

        $prefrenceMatrix = [];
        foreach ($prefrenceMatrixOfDepartments as $prefrenceMatrixOfDepartment) {
            $prefrenceMatrix[$prefrenceMatrixOfDepartment['placement_round_participant_id']][$prefrenceMatrixOfDepartment['preference_order']] =
                $prefrenceMatrixOfDepartment['student_count'];
        }

        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $department_capacity = $placementRoundParticipantsTable->find()
            ->select(['id', 'intake_capacity'])
            ->where([
                'PlacementRoundParticipants.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $weight = [];
        $count = count($prefrenceMatrix);
        for ($i = 1; $i <= count($prefrenceMatrix); $i++) {
            $weight[$i] = $count--;
        }

        $unitssprivilagedorder = [];
        if (!empty($prefrenceMatrix)) {
            foreach ($prefrenceMatrix as $key => $value) {
                $sum = 0;
                $total_student = array_sum($value);
                foreach ($value as $preference_key => $number_students) {
                    foreach ($weight as $weight_preference_key => $weight_preference_point) {
                        if ($preference_key == $weight_preference_key) {
                            $sum += ($weight_preference_point * $number_students);
                        }
                    }
                }

                $unit_capacity_number = 1;
                foreach ($department_capacity as $dept_value) {
                    if ($dept_value['id'] == $key) {
                        $unit_capacity_number = $dept_value['intake_capacity'];
                        break;
                    }
                }

                $unitssprivilagedorder[$key]['weight'] = $sum / $unit_capacity_number;
            }
        }

        uasort($unitssprivilagedorder, [$this, 'compareweight']);

        return $unitssprivilagedorder;
    }

    public function getListOfUnitsNotFullyAssignedQuota(array $data = []): array
    {
        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $department_capacity = $placementRoundParticipantsTable->find()
            ->select(['id', 'intake_capacity'])
            ->where([
                'PlacementRoundParticipants.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $unitssprivilagedorder = [];

        if (!empty($department_capacity)) {
            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
            foreach ($department_capacity as $v) {
                $assignedCount = $placementParticipatingStudentsTable->find('count')
                    ->where([
                        'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                        'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                        'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                        'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                        'PlacementParticipatingStudents.placement_round_participant_id' => $v['id']
                    ])
                    ->disableHydration()
                    ->count();

                $intake_capacity = $v['intake_capacity'];
                $difference = $intake_capacity - $assignedCount;

                if ($difference > 0) {
                    $unitssprivilagedorder[$v['id']]['weight'] = $difference;
                }
            }
        }

        uasort($unitssprivilagedorder, [$this, 'compareweight']);

        return $unitssprivilagedorder;
    }

    public function sortOutForRandomAssignmentForNonAssigned(array $data, ?int $department_id = null, bool $lastRound = false): array
    {
        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
        $partcipantsOrderByResult = $placementParticipatingStudentsTable->find()
            ->select([
                'id',
                'total_placement_weight',
                'accepted_student_id'
            ])
            ->where([
                'OR' => [
                    'PlacementParticipatingStudents.placement_round_participant_id IS NULL',
                    'PlacementParticipatingStudents.placement_round_participant_id = ""',
                    'PlacementParticipatingStudents.placement_round_participant_id = 0'
                ],
                'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for']
            ])
            ->order(['PlacementParticipatingStudents.total_placement_weight' => 'DESC'])
            ->limit(100000)
            ->disableHydration()
            ->all()
            ->toArray();

        $partcipantsOrderByResultNew = [];
        $withoutPreference = [];

        if (!empty($partcipantsOrderByResult)) {
            foreach ($partcipantsOrderByResult as $pk) {
                $x = $this->find('first')
                    ->where([
                        'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
                        'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                        'PlacementPreferences.placement_round_participant_id' => $department_id,
                        'PlacementPreferences.accepted_student_id' => $pk['PlacementParticipatingStudent']['accepted_student_id']
                    ])
                    ->order(['PlacementPreferences.preference_order' => 'ASC'])
                    ->disableHydration()
                    ->first();

                if (!empty($x['PlacementPreference'])) {
                    $pk['PlacementPreference'] = $x['PlacementPreference'];
                    $partcipantsOrderByResultNew[] = $pk;
                } elseif (empty($x['PlacementPreference'])) {
                    $withoutPreference[] = $pk;
                }
            }
        }

        $sortOutStudentsResultCategory = $partcipantsOrderByResultNew;
        $students_to_be_sorted = [];
        $students_to_be_removed = [];

        if (!empty($sortOutStudentsResultCategory)) {
            foreach ($sortOutStudentsResultCategory as $k => &$v) {
                if (!empty($v['PlacementPreference']['preference_order']) && is_numeric($v['PlacementPreference']['preference_order']) && $v['PlacementPreference']['preference_order'] > 1) {
                    $previous_preferences = $this->find()
                        ->where([
                            'PlacementPreferences.accepted_student_id' => $v['PlacementPreference']['accepted_student_id'],
                            'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
                            'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                            'PlacementPreferences.preference_order <' => $v['PlacementPreference']['preference_order']
                        ])
                        ->disableHydration()
                        ->all()
                        ->toArray();

                    $placement_pp_dept_done = 1;

                    if (!empty($previous_preferences)) {
                        foreach ($previous_preferences as $pp_v) {
                            $placed_students_count = $placementParticipatingStudentsTable->find('count')
                                ->where([
                                    'PlacementParticipatingStudents.applied_for' => $data['P
lacementSetting']['applied_for'],
                                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                                    'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                                    'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                                    'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                                    'PlacementParticipatingStudents.placement_round_participant_id' => $pp_v['placement_round_participant_id']
                                ])
                                ->disableHydration()
                                ->count();

                            if ($placed_students_count == 0) {
                                $placement_pp_dept_done = 0;
                                break;
                            }
                        }
                    }

                    if ($placement_pp_dept_done == 1) {
                        $students_to_be_sorted[] = $v;
                    } elseif ($placement_pp_dept_done == 0) {
                        $students_to_be_removed[] = $v['PlacementPreference']['accepted_student_id'];
                    }
                } else {
                    unset($sortOutStudentsResultCategory[$k]);
                }
            }
        }


        if (!empty($students_to_be_removed)) {
            $notAssignedDifference = $placementParticipatingStudentsTable->find('list')
                ->where([
                    'NOT' => ['PlacementParticipatingStudents.accepted_student_id IN' => $students_to_be_removed],
                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                    'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementParticipatingStudents.placement_round_participant_id IS NULL'
                ])
                ->select(['accepted_student_id'])
                ->disableHydration()
                ->toArray();
        } else {
            $notAssignedDifference = $placementParticipatingStudentsTable->find('list')
                ->where([
                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                    'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementParticipatingStudents.placement_round_participant_id IS NULL'
                ])
                ->select(['accepted_student_id'])
                ->disableHydration()
                ->toArray();
        }

        $partcipantsOrderByResultt = $placementParticipatingStudentsTable->find()
            ->select([
                'id',
                'total_placement_weight',
                'accepted_student_id'
            ])
            ->where([
                'OR' => [
                    'PlacementParticipatingStudents.placement_round_participant_id IS NULL',
                    'PlacementParticipatingStudents.placement_round_participant_id = ""',
                    'PlacementParticipatingStudents.placement_round_participant_id = 0'
                ],
                'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementParticipatingStudents.accepted_student_id IN' => array_keys($notAssignedDifference),
                'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for']
            ])
            ->order(['PlacementParticipatingStudents.total_placement_weight' => 'DESC'])
            ->limit(100000)
            ->disableHydration()
            ->all()
            ->toArray();

        $tmpSort = array_merge($sortOutStudentsResultCategory, $partcipantsOrderByResultt, $withoutPreference);
        $sortOutStudentsResultCategory = $tmpSort;

        if (!$lastRound && !empty($students_to_be_removed)) {
            $tmp = [];
            foreach ($sortOutStudentsResultCategory as $for_sort_v) {
                if (!in_array($for_sort_v['PlacementParticipatingStudent']['accepted_student_id'], $students_to_be_removed)) {
                    $tmp[] = $for_sort_v;
                }
            }
            $sortOutStudentsResultCategory = $tmp;
        }

        return $sortOutStudentsResultCategory;
    }

    public function sortOutStudentByPreference(array $data, ?int $department_id = null): array
    {
        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');

        $placementRoundParticipantsTable=TableRegistry::getTableLocator()->get('PlacementRoundParticipants');

        $partcipantsOrderByResult = $placementParticipatingStudentsTable->find()
            ->select([
                'id',
                'total_placement_weight',
                'accepted_student_id'
            ])
            ->where([
                'OR' => [
                    'PlacementParticipatingStudents.placement_round_participant_id IS NULL',
                    'PlacementParticipatingStudents.placement_round_participant_id = ""',
                    'PlacementParticipatingStudents.placement_round_participant_id = 0'
                ],
                'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for']
            ])
            ->order(['PlacementParticipatingStudents.total_placement_weight' => 'DESC'])
            ->limit(100000)
            ->disableHydration()
            ->all()
            ->toArray();

        $partcipantsOrderByResultNew = [];
        $students_to_be_removed = [];

        foreach ($partcipantsOrderByResult as $pk) {
            $x = $this->find('first')
                ->where([
                    'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                    'PlacementPreferences.placement_round_participant_id' => $department_id,
                    'PlacementPreferences.accepted_student_id' => $pk['PlacementParticipatingStudent']['accepted_student_id']
                ])
                ->order(['PlacementPreferences.preference_order' => 'ASC'])
                ->disableHydration()
                ->first();

            if (!empty($x['PlacementPreference'])) {
                $pk['PlacementPreference'] = $x['PlacementPreference'];
                $partcipantsOrderByResultNew[] = $pk;

                if (!empty($pk['PlacementPreference']['preference_order']) && is_numeric($pk['PlacementPreference']['preference_order']) && $pk['PlacementPreference']['preference_order'] > 1) {
                    $previous_preferences = $this->find()
                        ->where([
                            'PlacementPreferences.accepted_student_id' => $pk['PlacementPreference']['accepted_student_id'],
                            'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
                            'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                            'PlacementPreferences.preference_order <' => $pk['PlacementPreference']['preference_order']
                        ])
                        ->order(['PlacementPreferences.preference_order' => 'ASC'])
                        ->disableHydration()
                        ->all()
                        ->toArray();

                    $placement_pp_dept_done = 1;

                    if (!empty($previous_preferences)) {
                        foreach ($previous_preferences as $pp_v) {
                            $placed_students_count = $placementParticipatingStudentsTable->find('count')
                                ->where([
                                    'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                                    'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                                    'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                                    'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                                    'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                                    'PlacementParticipatingStudents.placement_round_participant_id' => $pp_v['placement_round_participant_id']
                                ])
                                ->disableHydration()
                                ->count();

                            if ($placed_students_count == 0) {
                                $placement_pp_dept_done = 0;
                                break;
                            }
                        }
                    }

                    if ($placement_pp_dept_done == 0) {
                        $students_to_be_removed[] = $pk['PlacementPreference']['accepted_student_id'];
                    }
                }
            }
        }

        $sortOutStudentsResultCategory = $partcipantsOrderByResultNew;

        if (!empty($students_to_be_removed)) {
            $quoteDefined = $placementRoundParticipantsTable->find('count')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                    'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round'],
                    'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                    'OR' => [
                        'PlacementRoundParticipants.female_quota >' => 0,
                        'PlacementRoundParticipants.disability_quota >' => 0,
                        'PlacementRoundParticipants.region_quota >' => 0
                    ]
                ])
                ->disableHydration()
                ->count();

            $resorted = [];
            foreach ($sortOutStudentsResultCategory as $for_sort_v) {
                if ($quoteDefined) {
                    if (!in_array($for_sort_v['PlacementParticipatingStudent']['accepted_student_id'], $students_to_be_removed)) {
                        $resorted[] = $for_sort_v;
                    }
                } else {
                    $propabilityCheck = $this->getPropabilityOfPreviousPreference($data, $department_id, $for_sort_v['PlacementPreference']['accepted_student_id']);
                    if (!in_array($for_sort_v['PlacementParticipatingStudent']['accepted_student_id'], $students_to_be_removed) || $propabilityCheck == 0) {
                        $resorted[] = $for_sort_v;
                    }
                }
            }

            $sortOutStudentsResultCategory = $resorted;
        }

        return $sortOutStudentsResultCategory;
    }

    public function getPropabilityOfPreviousPreference(array $data, int $current_department_id, int $accepted_student_id): int
    {
        $currentDepartmentPreferenceofStudent = $this->find('first')
            ->where([
                'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                'PlacementPreferences.placement_round_participant_id' => $current_department_id,
                'PlacementPreferences.accepted_student_id' => $accepted_student_id
            ])
            ->order(['PlacementPreferences.preference_order' => 'ASC'])
            ->disableHydration()
            ->first();

        $previous_preference_lists = $this->find()
            ->where([
                'PlacementPreferences.accepted_student_id' => $accepted_student_id,
                'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                'PlacementPreferences.preference_order <' => $currentDepartmentPreferenceofStudent['PlacementPreference']['preference_order'] ?? 0
            ])
            ->order(['PlacementPreferences.preference_order' => 'ASC'])
            ->disableHydration()
            ->all()
            ->toArray();

        if (!empty($previous_preference_lists)) {
            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
            foreach ($previous_preference_lists as $prrv) {
                $placed_students_count = $placementParticipatingStudentsTable->find('count')
                    ->where([
                        'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                        'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                        'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                        'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                        'PlacementParticipatingStudents.placement_round_participant_id' => $prrv['placement_round_participant_id']
                    ])
                    ->disableHydration()
                    ->count();

                if ($placed_students_count > 0) {
                    continue;
                }

                $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
                $detail_of_participating_unit = $placementRoundParticipantsTable->find('first')
                    ->where([
                        'PlacementRoundParticipants.id' => $prrv['placement_round_participant_id'],
                        'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                        'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                        'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round'],
                        'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                        'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id']
                    ])
                    ->disableHydration()
                    ->first();

                $intake_capacity = ($detail_of_participating_unit['PlacementRoundParticipant']['intake_capacity'] ?? 0) +
                    (!empty($detail_of_participating_unit['PlacementRoundParticipant']['female_quota']) ? $detail_of_participating_unit['PlacementRoundParticipant']['female_quota'] : 0) +
                    (!empty($detail_of_participating_unit['PlacementRoundParticipant']['disability_quota']) ? $detail_of_participating_unit['PlacementRoundParticipant']['disability_quota'] : 0) +
                    (!empty($detail_of_participating_unit['PlacementRoundParticipant']['region_quota']) ? $detail_of_participating_unit['PlacementRoundParticipant']['region_quota'] : 0);

                $those_who_prefered_it_as_first = $this->find('list')
                    ->where([
                        'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
                        'PlacementPreferences.round' => $data['PlacementSetting']['round'],
                        'PlacementPreferences.placement_round_participant_id' => $prrv['placement_round_participant_id'],
                        'PlacementPreferences.preference_order <=' => $prrv['preference_order']
                    ])
                    ->select(['accepted_student_id'])
                    ->disableHydration()
                    ->toArray();

                $partcipantsOrderByResult = $placementParticipatingStudentsTable->find()
                    ->select([
                        'id',
                        'total_placement_weight',
                        'accepted_student_id'
                    ])
                    ->where([
                        'OR' => [
                            'PlacementParticipatingStudents.placement_round_participant_id IS NULL',
                            'PlacementParticipatingStudents.placement_round_participant_id = ""',
                            'PlacementParticipatingStudents.placement_round_participant_id = 0'
                        ],
                        'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                        'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                        'PlacementParticipatingStudents.accepted_student_id IN' => array_keys($those_who_prefered_it_as_first)
                    ])
                    ->order(['PlacementParticipatingStudents.total_placement_weight' => 'DESC'])
                    ->limit($intake_capacity)
                    ->disableHydration()
                    ->all()
                    ->toArray();

                foreach ($partcipantsOrderByResult as $pv) {
                    if ($pv['accepted_student_id'] == $accepted_student_id) {
                        return 1;
                    }
                }
            }
        }

        return 0;
    }

    public function multi_unique(array $src): array
    {
        return array_map('unserialize', array_unique(array_map('serialize', $src)));
    }

    public function checkAndAdjustPrivilagedQuota(array $data, int $department_id, array $adjusted_privilaged_quota = [], $resevedquote): array
    {
        if (empty($data)) {
            return [];
        }

        $studentPreparednessCondition = "PlacementPreferences.accepted_student_id IN (SELECT accepted_student_id FROM placement_participating_students WHERE applied_for = '{$data['PlacementSetting']['applied_for']}' AND academic_year = '{$data['PlacementSetting']['academic_year']}' AND round = {$data['PlacementSetting']['round']})";

        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $placementParticipatingStudentsTable= TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');

        $number_of_participating_department = $placementRoundParticipantsTable->find('count')
            ->where([
                'PlacementRoundParticipants.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
            ])
            ->disableHydration()
            ->count();

        if (!empty($adjusted_privilaged_quota)) {
            foreach ($adjusted_privilaged_quota as $privilage_type => &$quota) {
                $privilagedcondition = null;

                if (strcasecmp($privilage_type, 'female') === 0) {
                    $privilagedcondition = "(AcceptedStudents.sex = 'female' OR AcceptedStudents.sex = 'f')";
                } elseif (strcasecmp($privilage_type, 'disability') === 0) {
                    $privilagedcondition = 'AcceptedStudents.disability IS NOT NULL';
                } else {
                    $regions = $placementRoundParticipantsTable->find('first')
                        ->where([
                            'PlacementRoundParticipants.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                            'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                            'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                            'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                            'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
                        ])
                        ->disableHydration()
                        ->first();

                    if (empty($regions['PlacementRoundParticipant']['developing_region'])) {
                        continue;
                    }

                    $privilagedcondition = "AcceptedStudents.region_id IN ({$regions['PlacementRoundParticipant']['developing_region']})";
                }

                $sum_available_students_privilaged = 0;
                $list_students_in_x_preference = [];

                if ($number_of_participating_department && $quota) {
                    for ($i = 1; $i <= $number_of_participating_department; $i++) {
                        $list_students_in_x_preference = $this->find()
                            ->select(['accepted_student_id', 'placement_round_participant_id'])
                            ->where([
                                'PlacementPreferences.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                                'PlacementPreferences.placement_round_participant_id' => $department_id,
                                'PlacementPreferences.preference_order' => $i,
                                $privilagedcondition,
                                $studentPreparednessCondition
                            ])
                            ->contain(['AcceptedStudents'])
                            ->disableHydration()
                            ->all()
                            ->toArray();

                        if ($i == 1) {
                            $sum_available_students_privilaged += count($list_students_in_x_preference);
                            if ($sum_available_students_privilaged >= $quota) {
                                break;
                            }
                            continue;
                        }

                        $list_of_departments_id = $placementParticipatingStudentsTable->find()
                            ->select(['placement_round_participant_id'])
                            ->distinct(['placement_round_participant_id'])
                            ->where([
                                'PlacementParticipatingStudents.academic_year LIKE' => $data['PlacementSetting']['academic_year'] . '%',
                                'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                                'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                                'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                                'OR' => [
                                    'PlacementParticipatingStudents.placement_round_participant_id IS NOT NULL',
                                    'PlacementParticipatingStudents.placement_round_participant_id != ""',
                                    'PlacementParticipatingStudents.placement_round_participant_id != 0'
                                ]
                            ])
                            ->disableHydration()
                            ->all()
                            ->toArray();

                        $reformat_list_of_department_ids = array_column($list_of_departments_id, 'placement_round_participant_id');
                        $excluded_student_count = 0;

                        foreach ($list_students_in_x_preference as &$student) {
                            for ($j = 1; $j < $i; $j++) {
                                $department_id_accepted_student = $this->find('first')
                                    ->where([
                                        'PlacementPreferences.accepted_student_id' => $student['accepted_student_id'],
                                        'PlacementPreferences.preference_order' => $j
                                    ])
                                    ->select(['placement_round_participant_id'])
                                    ->disableHydration()
                                    ->first();

                                if (!empty($reformat_list_of_department_ids) &&
                                    !empty($department_id_accepted_student['placement_round_participant_id']) &&
                                    is_numeric($department_id_accepted_student['placement_round_participant_id']) &&
                                    $department_id_accepted_student['placement_round_participant_id'] > 0 &&
                                    !in_array($department_id_accepted_student['placement_round_participant_id'], $reformat_list_of_department_ids)) {
                                    $excluded_student_count++;
                                    break;
                                }
                            }
                        }

                        $sum_available_students_privilaged += (count($list_students_in_x_preference) - $excluded_student_count);

                        if ($sum_available_students_privilaged >= $quota) {
                            break;
                        }
                    }

                    if ($sum_available_students_privilaged < $quota) {
                        $privilaged_quota_gap = ($quota - $sum_available_students_privilaged);
                        $quota -= $privilaged_quota_gap;
                    }
                }
            }
        }

        return [$adjusted_privilaged_quota];
    }

    public function getStudentWhoToPrepareForPlacement(array $data = []): array
    {
        if (empty($data)) {
            return [];
        }

        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        $firstData = $placementRoundParticipantsTable->find('first')
            ->where([
                'PlacementRoundParticipants.applied_for' => $data['PlacementSetting']['applied_for'],
                'PlacementRoundParticipants.program_id' => $data['PlacementSetting']['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['PlacementSetting']['program_type_id'],
                'PlacementRoundParticipants.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementRoundParticipants.placement_round' => $data['PlacementSetting']['round']
            ])
            ->disableHydration()
            ->first();

        $additionalPoints = TableRegistry::getTableLocator()->get('PlacementAdditionalPoints')->find()
            ->where([
                'PlacementAdditionalPoints.applied_for' => $data['PlacementSetting']['applied_for'],
                'PlacementAdditionalPoints.program_id' => $data['PlacementSetting']['program_id'],
                'PlacementAdditionalPoints.program_type_id' => $data['PlacementSetting']['program_type_id'],
                'PlacementAdditionalPoints.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementAdditionalPoints.round' => $data['PlacementSetting']['round']
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $points = [];
        foreach ($additionalPoints as $pv) {
            $points[$pv['type']] = $pv['point'];
        }

        $allRoundParticipants = $placementRoundParticipantsTable->find('list')
            ->where(['PlacementRoundParticipants.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']])
            ->select(['id'])
            ->disableHydration()
            ->toArray();

        $limit = !empty($data['PlacementSetting']['limit']) ? $data['PlacementSetting']['limit'] : 5000;

        $student_ids_that_have_exam_result_entries_for_the_round = [];
        if ($data['PlacementSetting']['with_entrance'] == 1) {
            $student_ids_that_have_exam_result_entries_for_the_round = TableRegistry::getTableLocator()->get('PlacementEntranceExamResultEntries')->find('list')
                ->where(['PlacementEntranceExamResultEntries.placement_round_participant_id IN' => array_keys($allRoundParticipants)])
                ->group(['PlacementEntranceExamResultEntries.accepted_student_id', 'PlacementEntranceExamResultEntries.student_id'])
                ->select(['accepted_student_id', 'student_id'])
                ->disableHydration()
                ->toArray();
        }

        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
        $already_prepared_students_for_the_round = $placementParticipatingStudentsTable->find('list')
            ->where([
                'PlacementParticipatingStudents.applied_for' => $data['PlacementSetting']['applied_for'],
                'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round'],
                'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id']
            ])
            ->select(['accepted_student_id', 'student_id'])
            ->disableHydration()
            ->toArray();

        $conditions = [
            'PlacementPreferences.placement_round_participant_id IN' => array_keys($allRoundParticipants),
            'PlacementPreferences.academic_year' => $data['PlacementSetting']['academic_year'],
            'PlacementPreferences.round' => $data['PlacementSetting']['round']
        ];

        if ($data['PlacementSetting']['with_entrance'] == 1 && !empty($student_ids_that_have_exam_result_entries_for_the_round)) {
            $conditions['PlacementPreferences.student_id IN'] = array_values($student_ids_that_have_exam_result_entries_for_the_round);
            if ($data['PlacementSetting']['include'] == 0) {
                $conditions['NOT'] = ['PlacementPreferences.student_id IN' => array_values($already_prepared_students_for_the_round)];
            }
        } elseif ($data['PlacementSetting']['include'] == 0) {
            $conditions['NOT'] = ['PlacementPreferences.student_id IN' => array_values($already_prepared_students_for_the_round)];
        }

        $allStudentsWhoEntranceExam = $this->find()
            ->where($conditions)
            ->contain([
                'Students' => ['fields' => ['id', 'gender', 'full_name', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'college_id', 'department_id']],
                'AcceptedStudents' => ['fields' => ['id', 'sex', 'full_name', 'EHEECE_total_results', 'studentnumber', 'program_id', 'program_type_id', 'academicyear', 'region_id', 'college_id', 'department_id', 'disability']]
            ])
            ->group(['PlacementPreferences.accepted_student_id', 'PlacementPreferences.student_id', 'PlacementPreferences.academic_year', 'PlacementPreferences.round'])
            ->order([
                'Students.id' => 'ASC',
                'Students.program_id' => 'ASC',
                'Students.program_type_id' => 'ASC',
                'PlacementPreferences.preference_order' => 'ASC'
            ])
            ->limit($limit)
            ->disableHydration()
            ->all()
            ->toArray();

        if (!empty($allStudentsWhoEntranceExam)) {
            $programsTable = TableRegistry::getTableLocator()->get('Programs');
            $programTypesTable = TableRegistry::getTableLocator()->get('ProgramTypes');
            $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');

            $selected_program_name = $programsTable->find()
                ->select(['name'])
                ->where(['Programs.id' => $allStudentsWhoEntranceExam[0]['Student']['program_id']])
                ->disableHydration()
                ->first()['name'] ?? '';

            $selected_program_type_name = $programTypesTable->find()
                ->select(['name'])
                ->where(['ProgramTypes.id' => $allStudentsWhoEntranceExam[0]['Student']['program_type_id']])
                ->disableHydration()
                ->first()['name'] ?? '';

            $selected_applied_unit_name = empty($allStudentsWhoEntranceExam[0]['Student']['department_id']) ?
                $collegesTable->find()
                    ->select(['name'])
                    ->where(['Colleges.id' => $allStudentsWhoEntranceExam[0]['Student']['college_id']])
                    ->disableHydration()
                    ->first()['name'] ?? '' :
                $departmentsTable->find()
                    ->select(['name'])
                    ->where(['Departments.id' => $allStudentsWhoEntranceExam[0]['Student']['department_id']])
                    ->disableHydration()
                    ->first()['name'] ?? '';

            $placementResultSettings = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find()
                ->where(['PlacementResultSettings.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier']])
                ->disableHydration()
                ->all()
                ->toArray();

            $freshman_settings = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find('first')
                ->where([
                    'PlacementResultSettings.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
                    'PlacementResultSettings.result_type' => 'freshman_result'
                ])
                ->disableHydration()
                ->first();

            $prepartory_settings = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find('first')
                ->where([
                    'PlacementResultSettings.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
                    'PlacementResultSettings.result_type' => 'EHEECE_total_results'
                ])
                ->disableHydration()
                ->first();

            $entrance_settings = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find('first')
                ->where([
                    'PlacementResultSettings.group_identifier' => $firstData['PlacementRoundParticipant']['group_identifier'],
                    'PlacementResultSettings.result_type' => 'entrance_result'
                ])
                ->disableHydration()
                ->first();

            $region_ids = !empty($firstData['PlacementRoundParticipant']['developing_region']) ?
                explode(',', $firstData['PlacementRoundParticipant']['developing_region']) : [];

            if (empty($freshman_settings) && empty($prepartory_settings) && empty($entrance_settings)) {
                $error1 = "No placement setting is defined for round {$data['PlacementSetting']['round']} of {$selected_program_name} - {$selected_program_type_name} in {$selected_applied_unit_name}. This is for view only and it is generated with default placement settings(" . DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT . "% for Freshman CGPA out of 4.00, " . DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT . "% for Preparatory EHEECE total results out of 700 and " . DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT . "% for Department Entrance Exam out of 30). Please define Placement Setting first and try to Prepare.";
                $this->validationErrors['NO_PLACEMENT_SETTING_FOUND'] = $error1;
            }

            foreach ($allStudentsWhoEntranceExam as $p => &$v) {
                $alreadyPrepared = $placementParticipatingStudentsTable->find('first')
                    ->where([
                        'PlacementParticipatingStudents.accepted_student_id' => $v['AcceptedStudent']['id'],
                        'PlacementParticipatingStudents.student_id' => $v['Student']['id'],
                        'PlacementParticipatingStudents.program_id' => $data['PlacementSetting']['program_id'],
                        'PlacementParticipatingStudents.program_type_id' => $data['PlacementSetting']['program_type_id'],
                        'PlacementParticipatingStudents.academic_year' => $data['PlacementSetting']['academic_year'],
                        'PlacementParticipatingStudents.round' => $data['PlacementSetting']['round']
                    ])
                    ->disableHydration()
                    ->first();

                $prep = 0;
                $fresh = 0;
                $entrance = 0;
                $female_placement_weight = 0;
                $disability_weight = 0;
                $developing_region_weight = 0;
                $freshmanResult = 0.0;

                $studentExamStatusesTable = TableRegistry::getTableLocator()->get('StudentExamStatuses');
                $freshManresult = $studentExamStatusesTable->find('first')
                    ->where([
                        'StudentExamStatuses.student_id' => $v['Student']['id'],
                        'StudentExamStatuses.academic_year' => $data['PlacementSetting']['academic_year'],
                        'StudentExamStatuses.semester' => !empty($firstData['PlacementRoundParticipant']['semester']) ? $firstData['PlacementRoundParticipant']['semester'] : ($data['PlacementSetting']['round'] == 1 ? 'I' : 'II')
                    ])
                    ->contain(['AcademicStatuses' => ['fields' => ['id', 'name']]])
                    ->select(['StudentExamStatuses.academic_status_id', 'StudentExamStatuses.sgpa', 'StudentExamStatuses.cgpa'])
                    ->group(['StudentExamStatuses.student_id', 'StudentExamStatuses.semester', 'StudentExamStatuses.academic_year'])
                    ->order(['StudentExamStatuses.id' => 'DESC', 'StudentExamStatuses.created' => 'DESC'])
                    ->disableHydration()
                    ->first();

                if (!empty($freshManresult['AcademicStatus']['name'])) {
                    $v['Student']['academic_status'] = $freshManresult['AcademicStatus']['name'];
                    $v['Student']['academic_status_id'] = $freshManresult['AcademicStatus']['id'];
                } else {
                    $v['Student']['academic_status'] = null;
                }

                if (!empty($freshManresult['StudentExamStatus']['cgpa'])) {
                    $v['Student']['cgpa'] = $freshManresult['StudentExamStatus']['cgpa'];
                } else {
                    $v['Student']['cgpa'] = null;
                }

                if ($firstData['PlacementRoundParticipant']['require_cgpa'] == 1) {
                    if (empty($freshManresult['StudentExamStatus']['cgpa']) || !is_numeric($freshManresult['StudentExamStatus']['cgpa'])) {
                        unset($allStudentsWhoEntranceExam[$p]);
                        continue;
                    } elseif (!empty($freshManresult['StudentExamStatus']['academic_status_id']) && $freshManresult['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
                        unset($allStudentsWhoEntranceExam[$p]);
                        continue;
                    } elseif (is_numeric($freshManresult['StudentExamStatus']['cgpa']) && $freshManresult['StudentExamStatus']['cgpa'] < DEFAULT_MINIMUM_CGPA_FOR_PLACEMENT) {
                        unset($allStudentsWhoEntranceExam[$p]);
                        continue;
                    } elseif (!empty($firstData['PlacementRoundParticipant']['minimum_cgpa']) && empty($firstData['PlacementRoundParticipant']['maximum_cgpa'])) {
                        if ($freshManresult['StudentExamStatus']['cgpa'] < $firstData['PlacementRoundParticipant']['minimum_cgpa']) {
                            unset($allStudentsWhoEntranceExam[$p]);
                            continue;
                        }
                    } elseif (!empty($firstData['PlacementRoundParticipant']['minimum_cgpa']) && !empty($firstData['PlacementRoundParticipant']['maximum_cgpa'])) {
                        if ($freshManresult['StudentExamStatus']['cgpa'] < $firstData['PlacementRoundParticipant']['minimum_cgpa'] || $freshManresult['StudentExamStatus']['cgpa'] > $firstData['PlacementRoundParticipant']['maximum_cgpa']) {
                            unset($allStudentsWhoEntranceExam[$p]);
                            continue;
                        }
                    }
                }

                if (!empty($freshManresult['StudentExamStatus']['academic_status_id']) && $freshManresult['StudentExamStatus']['academic_status_id'] == DISMISSED_ACADEMIC_STATUS_ID) {
                    unset($allStudentsWhoEntranceExam[$p]);
                    continue;
                } elseif (empty($freshManresult['StudentExamStatus']['academic_status_id'])) {
                    unset($allStudentsWhoEntranceExam[$p]);
                    continue;
                }

                if (!empty($freshManresult['StudentExamStatus']['cgpa'])) {
                    $freshmanResult = $freshManresult['StudentExamStatus']['cgpa'];
                }

                $placementEntranceExamResultEntriesTable = TableRegistry::getTableLocator()->get('PlacementEntranceExamResultEntries');
                if (!empty($entrance_settings['percent']) && !empty($v['PlacementEntranceExamResultEntry']['result'])) {
                    $entranceExamResult = $placementEntranceExamResultEntriesTable->find('first')
                        ->where([
                            'OR' => [
                                'PlacementEntranceExamResultEntries.accepted_student_id' => $v['AcceptedStudent']['id'],
                                'PlacementEntranceExamResultEntries.student_id' => $v['Student']['id']
                            ],
                            'PlacementEntranceExamResultEntries.placement_round_participant_id IN' => array_keys($allRoundParticipants)
                        ])
                        ->group(['PlacementEntranceExamResultEntries.accepted_student_id', 'PlacementEntranceExamResultEntries.student_id'])
                        ->order(['PlacementEntranceExamResultEntries.result' => 'DESC'])
                        ->disableHydration()
                        ->first();

                    if (!empty($entranceExamResult['PlacementEntranceExamResultEntry']['result'])) {
                        $entrance = (!empty($entrance_settings['max_result']) && $entrance_settings['max_result'] <= ENTRANCEMAXIMUM && $entrance_settings['max_result'] >= 0) ?
                            ($entrance_settings['percent'] * $v['PlacementEntranceExamResultEntry']['result']) / $entrance_settings['max_result'] :
                            ($entrance_settings['percent'] * $v['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
                    }
                }

                if (!empty($prepartory_settings['percent'])) {
                    if ($data['PlacementSetting']['academic_year'] == $v['AcceptedStudent']['academicyear']) {
                        if (!empty($prepartory_settings['max_result']) && $prepartory_settings['max_result'] <= PREPARATORYMAXIMUM && $prepartory_settings['max_result'] >= 0) {
                            $prep = ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / $prepartory_settings['max_result'];
                        } else {
                            if (in_array($v['AcceptedStudent']['college_id'], \Cake\Core\Configure::read('social_stream_college_ids'))) {
                                $prep = ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / SOCIAL_STREAM_PREPARATORY_MAXIMUM;
                            } elseif (in_array($v['AcceptedStudent']['college_id'], \Cake\Core\Configure::read('natural_stream_college_ids'))) {
                                $prep = ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / NATURAL_STREAM_PREPARATORY_MAXIMUM;
                            } else {
                                $prep = ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
                            }
                        }
                    } else {
                        if (in_array($v['AcceptedStudent']['college_id'], \Cake\Core\Configure::read('social_stream_college_ids'))) {
                            $prep = ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / SOCIAL_STREAM_PREPARATORY_MAXIMUM;
                        } elseif (in_array($v['AcceptedStudent']['college_id'], \Cake\Core\Configure::read('natural_stream_college_ids'))) {
                            $prep = ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / NATURAL_STREAM_PREPARATORY_MAXIMUM;
                        } else {
                            $prep = ($prepartory_settings['percent'] * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
                        }
                    }
                }

                if (!empty($freshman_settings['percent'])) {
                    $fresh = (!empty($freshman_settings['max_result']) && $freshman_settings['max_result'] <= FRESHMANMAXIMUM && $freshman_settings['max_result'] >= 0) ?
                        ($freshman_settings['percent'] * $freshmanResult) / $freshman_settings['max_result'] :
                        ($freshman_settings['percent'] * $freshmanResult) / FRESHMANMAXIMUM;
                }

                if (empty($freshman_settings) && empty($prepartory_settings) && empty($entrance_settings)) {
                    if (!empty($v['Student']['cgpa']) && $v['Student']['cgpa'] > 0) {
                        $fresh = (DEFAULT_FRESHMAN_RESULT_PERCENT_FOR_PLACEMENT * $v['Student']['cgpa']) / FRESHMANMAXIMUM;
                    }

                    if (!empty($v['AcceptedStudent']['EHEECE_total_results']) && $v['AcceptedStudent']['EHEECE_total_results'] > 100) {
                        if (in_array($v['AcceptedStudent']['college_id'], \Cake\Core\Configure::read('social_stream_college_ids'))) {
                            $prep = (DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * $v['AcceptedStudent']['EHEECE_total_results']) / SOCIAL_STREAM_PREPARATORY_MAXIMUM;
                        } elseif (in_array($v['AcceptedStudent']['college_id'], \Cake\Core\Configure::read('natural_stream_college_ids'))) {
                            $prep = (DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * $v['AcceptedStudent']['EHEECE_total_results']) / NATURAL_STREAM_PREPARATORY_MAXIMUM;
                        } else {
                            $prep = (DEFAULT_PREPARATORY_RESULT_PERCENT_FOR_PLACEMENT * $v['AcceptedStudent']['EHEECE_total_results']) / PREPARATORYMAXIMUM;
                        }
                    }

                    if (!empty($v['PlacementEntranceExamResultEntry']['result']) && $v['PlacementEntranceExamResultEntry']['result'] >= 0) {
                        $entrance = (DEFAULT_DEPARTMENT_ENTRANCE_RESULT_PERCENT_FOR_PLACEMENT * $v['PlacementEntranceExamResultEntry']['result']) / ENTRANCEMAXIMUM;
                    }
                }

                if (!empty($v['AcceptedStudent']['sex']) && (strcasecmp($v['AcceptedStudent']['sex'], 'female') === 0 || strcasecmp($v['AcceptedStudent']['sex'], 'f') === 0)) {
                    $female_placement_weight = !empty($points['female']) ? $points['female'] :
                        (is_numeric(INCLUDE_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT_BY_DEFAULT) && INCLUDE_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT_BY_DEFAULT == 1 ?
                            DEFAULT_FEMALE_AFFIRMATIVE_POINTS_FOR_PLACEMENT : 0);
                }

                $v['PlacementParticipatingStudent']['female_placement_weight'] = $female_placement_weight;

                if (!empty($v['AcceptedStudent']['disability'])) {
                    $disability_weight = 5;
                }

                $v['PlacementParticipatingStudent']['disability_weight'] = $disability_weight;

                if (!empty($v['AcceptedStudent']['region_id']) && in_array($v['AcceptedStudent']['region_id'], $region_ids)) {
                    $developing_region_weight = (strcasecmp($v['AcceptedStudent']['sex'], 'female') === 0 || strcasecmp($v['AcceptedStudent']['sex'], 'f') === 0) ? 5 :
                        (!empty($v['AcceptedStudent']['disability']) ? 10 : 0);
                }

                $v['PlacementParticipatingStudent']['developing_region_weight'] = $developing_region_weight;
                $v['PlacementParticipatingStudent']['result_weight'] = round(($prep + $fresh + $entrance), 2);
                $v['PlacementParticipatingStudent']['prepartory'] = round($prep, 2);
                $v['PlacementParticipatingStudent']['entrance'] = $entrance;
                $v['PlacementParticipatingStudent']['gpa'] = round($fresh, 2);
                $v['PlacementParticipatingStudent']['academic_year'] = $data['PlacementSetting']['academic_year'];
                $v['PlacementParticipatingStudent']['applied_for'] = $data['PlacementSetting']['applied_for'];
                $v['PlacementParticipatingStudent']['round'] = $data['PlacementSetting']['round'];
                $v['PlacementParticipatingStudent']['program_id'] = $data['PlacementSetting']['program_id'];
                $v['PlacementParticipatingStudent']['program_type_id'] = $data['PlacementSetting']['program_type_id'];
                $v['PlacementParticipatingStudent']['total_weight'] = round(
                    ($v['PlacementParticipatingStudent']['developing_region_weight'] +
                        $v['PlacementParticipatingStudent']['disability_weight'] +
                        $v['PlacementParticipatingStudent']['female_placement_weight'] +
                        $v['PlacementParticipatingStudent']['result_weight']),
                    2
                );
                $v['PlacementParticipatingStudent']['total_placement_weight'] = $v['PlacementParticipatingStudent']['total_weight'];

                if (!empty($alreadyPrepared)) {
                    $v['PlacementParticipatingStudent']['id'] = $alreadyPrepared['PlacementParticipatingStudent']['id'];
                }
            }

            usort($allStudentsWhoEntranceExam, [$this, 'cmp']);

            return $allStudentsWhoEntranceExam;
        }

        return [];
    }

    public function cmp($a, $b): int
    {
        if (!isset($a['PlacementParticipatingStudent']['total_weight']) || !isset($b['PlacementParticipatingStudent']['total_weight'])) {
            return 0;
        }
        return $a['PlacementParticipatingStudent']['total_weight'] == $b['PlacementParticipatingStudent']['total_weight'] ? 0 :
            ($a['PlacementParticipatingStudent']['total_weight'] < $b['PlacementParticipatingStudent']['total_weight'] ? 1 : -1);
    }

    public function compareweight($x, $y): int
    {
        return $x['weight'] < $y['weight'] ? 1 : -1;
    }

    public function cmpTotalPlacementWeight($a, $b): int
    {
        return 0;
    }

    public function get_defined_list_of_applied_for($data = null, $curr_acy = null): array
    {
        $placementRoundParticipantsTable = TableRegistry::getTableLocator()->get('PlacementRoundParticipants');
        if (!empty($curr_acy)) {
            $placementRoundParticipants = $placementRoundParticipantsTable->find('list')
                ->where([
                    'PlacementRoundParticipants.program_id' => 1,
                    'PlacementRoundParticipants.program_type_id' => 1,
                    'PlacementRoundParticipants.academic_year' => $curr_acy,
                    'PlacementRoundParticipants.placement_round' => 1
                ])
                ->select(['applied_for'])
                ->disableHydration()
                ->toArray();
        } elseif (!empty($data)) {
            $placementRoundParticipants = $placementRoundParticipantsTable->find('list')
                ->where([
                    'PlacementRoundParticipants.program_id' => $data['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $data['program_type_id'],
                    'PlacementRoundParticipants.academic_year' => $data['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['round'] ?? $data['placement_round'] ?? 1
                ])
                ->select(['applied_for'])
                ->disableHydration()
                ->toArray();
        } else {
            $placementRoundParticipants = $placementRoundParticipantsTable->find('list')
                ->select(['applied_for'])
                ->disableHydration()
                ->toArray();
        }

        $dept_ids = [];
        $coll_ids = [];
        $appliedForList = [];

        if (!empty($placementRoundParticipants)) {
            $prtpnt = array_values(array_unique($placementRoundParticipants));
            foreach ($prtpnt as $prval) {
                $parts = explode('~', $prval);
                if ($parts[0] === 'd') {
                    $dept_ids[] = $parts[1];
                } elseif ($parts[0] === 'c') {
                    $coll_ids[] = $parts[1];
                }
            }
        }

        if (!empty($coll_ids)) {
            $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
            $colls = $collegesTable->find('list')
                ->where(['Colleges.id IN' => $coll_ids, 'Colleges.active' => 1])
                ->disableHydration()
                ->toArray();
            foreach ($colls as $colkey => $colval) {
                $appliedForList[$colval]['c~' . $colkey] = 'All ' . $colval;
            }
        }

        if (!empty($dept_ids)) {
            $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
            $depts = $departmentsTable->find()
                ->where(['Departments.id IN' => $dept_ids, 'Departments.active' => 1])
                ->contain(['Colleges' => ['fields' => ['name']]])
                ->disableHydration()
                ->all()
                ->toArray();
            foreach ($depts as $deptval) {
                $appliedForList[$deptval['College']['name']]['d~' . $deptval['Department']['id']] = $deptval['Department']['name'];
            }
        }

        return $appliedForList;
    }
}
