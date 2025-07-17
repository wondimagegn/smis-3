<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class PlacementRoundParticipantsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('placement_round_participants');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->hasMany('PlacementParticipatingStudents', [
            'foreignKey' => 'placement_round_participant_id',
            'dependent' => false,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->numeric('foreign_key')
            ->notEmptyString('foreign_key', 'Please select participating unit')
            ->notEmptyString('academic_year', 'Please select academic year')
            ->numeric('placement_round')
            ->notEmptyString('placement_round', 'Please select placement round')
            ->notEmptyString('applied_for', 'Please select placement round')
            ->notEmptyString('name', 'Please select placement round')
            ->notEmptyString('type');

        return $validator;
    }

    public function reformat(array $data = [])
    {
        $reformatedData = [];
        $group_identifier = strtotime(date('Y-m-d H:i:s'));

        if (!empty($data)) {
            $firstData = $data['PlacementRoundParticipant'][1];
            foreach ($data['PlacementRoundParticipant'] as $dk => $dv) {
                $reformatedData['PlacementRoundParticipant'][$dk] = $dv;
                $reformatedData['PlacementRoundParticipant'][$dk]['group_identifier'] = !empty($firstData['group_identifier']) ? $firstData['group_identifier'] : $group_identifier;
                $reformatedData['PlacementRoundParticipant'][$dk]['applied_for'] = $firstData['applied_for'];
                $reformatedData['PlacementRoundParticipant'][$dk]['program_id'] = $firstData['program_id'];
                $reformatedData['PlacementRoundParticipant'][$dk]['program_type_id'] = $firstData['program_type_id'];
                $reformatedData['PlacementRoundParticipant'][$dk]['academic_year'] = $firstData['academic_year'];
                $reformatedData['PlacementRoundParticipant'][$dk]['placement_round'] = $firstData['placement_round'];
                $reformatedData['PlacementRoundParticipant'][$dk]['semester'] = $firstData['semester'];
                $reformatedData['PlacementRoundParticipant'][$dk]['require_all_selected'] = $firstData['require_all_selected'];
            }
        }

        $reformatedDataDuplicateRemoved['PlacementRoundParticipant'] = array_unique($reformatedData['PlacementRoundParticipant'], SORT_REGULAR);

        if (count($reformatedData['PlacementRoundParticipant']) > count($reformatedDataDuplicateRemoved['PlacementRoundParticipant'])) {
            $this->validationErrors['foreign_key'] = 'Please remove the duplicated rows, and try again.';
            return false;
        }

        return $reformatedData;
    }

    public function isDuplicated(array $data = [], int $edit = 0)
    {
        if (!empty($data)) {
            $firstData = $data['PlacementRoundParticipant'][1];
            $conditions = [
                'PlacementRoundParticipants.type' => $firstData['type'],
                'PlacementRoundParticipants.applied_for' => $firstData['applied_for'],
                'PlacementRoundParticipants.program_id' => $firstData['program_id'],
                'PlacementRoundParticipants.program_type_id' => $firstData['program_type_id'],
                'PlacementRoundParticipants.foreign_key' => $firstData['foreign_key'],
                'PlacementRoundParticipants.academic_year' => $firstData['academic_year'],
                'PlacementRoundParticipants.placement_round' => $firstData['placement_round']
            ];

            if ($edit) {
                $conditions['PlacementRoundParticipants.group_identifier <>'] = $firstData['group_identifier'];
            }

            $count = $this->find('first')
                ->where($conditions)
                ->disableHydration()
                ->first();

            if (!empty($count)) {
                return $count['PlacementRoundParticipant']['group_identifier'];
            }
        }

        return false;
    }

    public function isPossibleToDefineDeadline(array $data = [])
    {
        if (!empty($data)) {
            $row = $this->find('first')
                ->where([
                    'PlacementRoundParticipants.applied_for' => $data['PlacementDeadline']['applied_for'],
                    'PlacementRoundParticipants.program_id' => $data['PlacementDeadline']['program_id'],
                    'PlacementRoundParticipants.program_type_id' => $data['PlacementDeadline']['program_type_id'],
                    'PlacementRoundParticipants.academic_year' => $data['PlacementDeadline']['academic_year'],
                    'PlacementRoundParticipants.placement_round' => $data['PlacementDeadline']['placement_round']
                ])
                ->disableHydration()
                ->first();

            if (!empty($row)) {
                return $row['PlacementRoundParticipant']['group_identifier'];
            }
        }

        return false;
    }

    public function participating_unit_name(array $acceptedStudentdetail = [], string $selectedAcademicYear, ?string $apply_for_college = null, string $type = 'd'): array
    {
        $participatingdepartmentname = [];
        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');

        if (!empty($acceptedStudentdetail)) {
            $placementRound = $placementParticipatingStudentsTable->getNextRound($selectedAcademicYear, $acceptedStudentdetail['AcceptedStudent']['id']);
            $applied_for = empty($acceptedStudentdetail['AcceptedStudent']['department_id']) ?
                'c~' . $acceptedStudentdetail['AcceptedStudent']['college_id'] :
                (!empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && !empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['specialization_id']) ?
                    'd~' . $acceptedStudentdetail['AcceptedStudent']['department_id'] : '');

            $rows = $this->find()
                ->where([
                    'PlacementRoundParticipants.applied_for' => $applied_for,
                    'PlacementRoundParticipants.placement_round' => $placementRound,
                    'PlacementRoundParticipants.program_id IN' => \Cake\Core\Configure::read('programs_available_for_placement_preference'),
                    'PlacementRoundParticipants.program_type_id IN' => \Cake\Core\Configure::read('program_types_available_for_placement_preference'),
                    'PlacementRoundParticipants.academic_year' => $selectedAcademicYear
                ])
                ->disableHydration()
                ->all()
                ->toArray();
        } elseif (!empty($apply_for_college)) {
            $applied_for = $type == 'c' ? 'c~' . $apply_for_college : ($type == 'd' ? 'd~' . $apply_for_college : '');
            $rows = $this->find()
                ->where([
                    'PlacementRoundParticipants.applied_for' => $applied_for,
                    'PlacementRoundParticipants.program_id IN' => \Cake\Core\Configure::read('programs_available_for_placement_preference'),
                    'PlacementRoundParticipants.program_type_id IN' => \Cake\Core\Configure::read('program_types_available_for_placement_preference'),
                    'PlacementRoundParticipants.academic_year' => $selectedAcademicYear
                ])
                ->disableHydration()
                ->all()
                ->toArray();
        } else {
            $rows = [];
        }

        foreach ($rows as $v) {
            if (!empty($v['name'])) {
                $participatingdepartmentname[$v['id']] = $v['name'];
            }
        }

        return $participatingdepartmentname;
    }

    public function reformatDevRegion(array $data = []): array
    {
        if (!empty($data['PlacementSetting']) && !empty($data['PlacementSetting'][0]['developing_region'])) {
            $developingRegions = implode(',', $data['PlacementSetting'][0]['developing_region']);
            if (!empty($developingRegions)) {
                $reformatedData = [];
                foreach ($data['PlacementSetting'] as $k => $v) {
                    $v['developing_region'] = $developingRegions;
                    $reformatedData['PlacementSetting'][$k] = $v;
                }
                return $reformatedData;
            }
        }

        return $data;
    }

    public function get_selected_participating_unit_name(array $data): array
    {
        $rows = $this->find()
            ->where([
                'PlacementRoundParticipants.applied_for' => $data['PlacementPreference']['applied_for'],
                'PlacementRoundParticipants.academic_year' => $data['PlacementPreference']['academic_year'],
                'PlacementRoundParticipants.placement_round' => $data['PlacementPreference']['round'],
                'PlacementRoundParticipants.program_id' => $data['PlacementPreference']['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['PlacementPreference']['program_type_id']
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $participatingdepartmentname = [];
        foreach ($rows as $v) {
            if (!empty($v['name'])) {
                $participatingdepartmentname[$v['id']] = $v['name'];
            }
        }

        return $participatingdepartmentname;
    }

    public function get_participating_unit_name(array $data): array
    {
        $rows = $this->find()
            ->where([
                'PlacementRoundParticipants.applied_for' => $data['applied_for'],
                'PlacementRoundParticipants.academic_year' => $data['academic_year'],
                'PlacementRoundParticipants.placement_round' => $data['round'],
                'PlacementRoundParticipants.program_id' => $data['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['program_type_id']
            ])
            ->limit($data['limit'] ?? 100)
            ->disableHydration()
            ->all()
            ->toArray();

        $participatingdepartmentname = [];
        foreach ($rows as $v) {
            if (!empty($v['name'])) {
                $participatingdepartmentname[$v['id']] = $v['name'];
            }
        }

        return $participatingdepartmentname;
    }

    public function allParticipatingUnitsDefined(array $data): array
    {
        if (empty($data['PlacementRoundParticipant']['applied_for'])) {
            return [];
        }

        $collegesTable = TableRegistry::getTableLocator()->get('Colleges');
        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $colleges = $collegesTable->find('list')->disableHydration()->toArray();
        $departments = $departmentsTable->find('list')->disableHydration()->toArray();

        $rows = $this->find()
            ->where([
                'PlacementRoundParticipants.applied_for' => $data['PlacementRoundParticipant']['applied_for'],
                'PlacementRoundParticipants.academic_year' => $data['PlacementRoundParticipant']['academic_year'],
                'PlacementRoundParticipants.placement_round' => $data['PlacementRoundParticipant']['placement_round'],
                'PlacementRoundParticipants.program_id' => $data['PlacementRoundParticipant']['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['PlacementRoundParticipant']['program_type_id']
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $participatingUnitName = [];
        $collegeID = null;
        $departmentID = null;
        $collegeName = null;
        $departmentName = null;

        $appliedUnitClg = explode('c~', $data['PlacementRoundParticipant']['applied_for']);
        if (!empty($appliedUnitClg[1])) {
            $collegeID = $appliedUnitClg[1];
        } else {
            $appliedUnitDept = explode('d~', $data['PlacementRoundParticipant']['applied_for']);
            if (!empty($appliedUnitDept[1])) {
                $departmentID = $appliedUnitDept[1];
            }
        }

        if (!empty($collegeID)) {
            $collegeName = $colleges[$collegeID] ?? '';
        } elseif (!empty($departmentID)) {
            $departmentName = $departments[$departmentID] ?? '';
            $deptCollID = $departmentsTable->find()
                ->select(['college_id'])
                ->where(['Departments.id' => $departmentID])
                ->disableHydration()
                ->first()['college_id'] ?? null;
            $collegeName = $colleges[$deptCollID] ?? '';
        }

        foreach ($rows as $v) {
            $targetName = empty($departmentName) ? $collegeName : $departmentName;
            if ($v['type'] === 'College') {
                $participatingUnitName[$targetName]['c~' . $v['foreign_key']] = ($colleges[$v['foreign_key']] ?? '') . ' Freshman (' . $v['name'] . ')';
            } elseif ($v['type'] === 'Department') {
                $participatingUnitName[$targetName]['d~' . $v['foreign_key']] = $departments[$v['foreign_key']] ?? '';
            }
        }

        return $participatingUnitName;
    }

    public function getParticipatingUnitForDirectPlacement(array $data): array
    {
        $rows = $this->find()
            ->where([
                'PlacementRoundParticipants.applied_for' => $data['applied_for'],
                'PlacementRoundParticipants.academic_year' => $data['academic_year'],
                'PlacementRoundParticipants.program_id' => $data['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['program_type_id']
            ])
            ->limit($data['limit'] ?? 100)
            ->disableHydration()
            ->all()
            ->toArray();

        $participatingdepartmentname = [];
        foreach ($rows as $v) {
            if (!empty($v['name'])) {
                $participatingdepartmentname[$v['id']] = $v['name'];
            }
        }

        return $participatingdepartmentname;
    }

    public function get_participating_unit_for_edit(int $placement_round_participant_id): array
    {
        if (!empty($placement_round_participant_id)) {
            $groupId = $this->find('first')
                ->where(['PlacementRoundParticipants.id' => $placement_round_participant_id])
                ->disableHydration()
                ->first();

            $rows = $this->find()
                ->where(['PlacementRoundParticipants.group_identifier' => $groupId['PlacementRoundParticipant']['group_identifier']])
                ->disableHydration()
                ->all()
                ->toArray();

            $participatingdepartmentname = [];
            foreach ($rows as $v) {
                if (!empty($v['name'])) {
                    $participatingdepartmentname[$v['id']] = $v['name'];
                }
            }

            return $participatingdepartmentname;
        }

        return [];
    }

    public function placementSettingDefined(array $data = []): bool
    {
        $participatinListCapacity = $this->find()
            ->where([
                'PlacementRoundParticipants.applied_for' => $data['applied_for'],
                'PlacementRoundParticipants.academic_year' => $data['academic_year'],
                'PlacementRoundParticipants.placement_round' => $data['round'],
                'PlacementRoundParticipants.program_id' => $data['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['program_type_id']
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $participatingSettings = $this->find('count')
            ->where([
                'PlacementRoundParticipants.applied_for' => $data['applied_for'],
                'PlacementRoundParticipants.academic_year' => $data['academic_year'],
                'PlacementRoundParticipants.placement_round' => $data['round'],
                'PlacementRoundParticipants.program_id' => $data['program_id'],
                'PlacementRoundParticipants.program_type_id' => $data['program_type_id']
            ])
            ->disableHydration()
            ->count();

        $placementResultSetting = TableRegistry::getTableLocator()->get('PlacementResultSettings')->find('count')
            ->where([
                'PlacementResultSettings.applied_for' => $data['applied_for'],
                'PlacementResultSettings.academic_year' => $data['academic_year'],
                'PlacementResultSettings.round' => $data['round'],
                'PlacementResultSettings.program_id' => $data['program_id'],
                'PlacementResultSettings.program_type_id' => $data['program_type_id']
            ])
            ->disableHydration()
            ->count();

        $placementReadyStudent = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents')->find('count')
            ->where([
                'PlacementParticipatingStudents.academic_year' => $data['academic_year'],
                'PlacementParticipatingStudents.round' => $data['round'],
                'PlacementParticipatingStudents.program_id' => $data['program_id'],
                'PlacementParticipatingStudents.program_type_id' => $data['program_type_id']
            ])
            ->disableHydration()
            ->count();

        $intake_capacity_defined_for_all_participants = true;
        $defined_intake_capacies = [];

        foreach ($participatinListCapacity as $participants) {
            if (!is_null($participants['intake_capacity']) && is_numeric($participants['intake_capacity'])) {
                $defined_intake_capacies[$participants['name']] = $participants['intake_capacity'];
            }
        }

        if (count($defined_intake_capacies) != count($participatinListCapacity)) {
            $intake_capacity_defined_for_all_participants = false;
        }

        if ($participatingSettings == 0) {
            $this->validationErrors['placement_round_participant'] = 'Please record placement round participant before running auto placement.';
            return false;
        } elseif ($placementResultSetting == 0) {
            $this->validationErrors['placement_result_setting'] = 'Please record result settings in auto placement before running the auto placement.';
            return false;
        } elseif ($placementReadyStudent == 0) {
            $this->validationErrors['placement_participating_student'] = 'Please prepare the students for auto placement before running the auto placement.';
            return false;
        } elseif (!$intake_capacity_defined_for_all_participants) {
            $this->validationErrors['placement_round_participant'] = 'Please define all intake capacities for all participating units for auto placement before running the auto placement.';
            return false;
        }

        return true;
    }

    public function roundLabel(int $round): string
    {
        if ($round == 1) {
            return $round . 'st';
        } elseif ($round == 2) {
            return $round . 'nd';
        } elseif ($round == 3) {
            return $round . 'rd';
        }
        return $round . 'th';
    }

    public function appliedFor(array $acceptedStudentdetail, string $selectedAcademicYear): string
    {
        $applied_for = '';
        if (!empty($acceptedStudentdetail)) {
            $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
            $placementRound = $placementParticipatingStudentsTable->getNextRound($selectedAcademicYear, $acceptedStudentdetail['AcceptedStudent']['id']);

            if (!empty($placementRound)) {
                $roundAppliedFor = $placementParticipatingStudentsTable->find('first')
                    ->where([
                        'PlacementParticipatingStudents.academic_year LIKE' => $selectedAcademicYear . '%',
                        'PlacementParticipatingStudents.round' => $placementRound,
                        'PlacementParticipatingStudents.accepted_student_id' => $acceptedStudentdetail['AcceptedStudent']['id']
                    ])
                    ->order(['PlacementParticipatingStudents.round' => 'DESC'])
                    ->disableHydration()
                    ->first();

                if (!empty($roundAppliedFor)) {
                    $applied_for = $roundAppliedFor['PlacementParticipatingStudent']['applied_for'];
                } else {
                    $applied_for = empty($acceptedStudentdetail['AcceptedStudent']['department_id']) ?
                        'c~' . $acceptedStudentdetail['AcceptedStudent']['college_id'] :
                        (!empty($acceptedStudentdetail['AcceptedStudent']['college_id']) && !empty($acceptedStudentdetail['AcceptedStudent']['department_id']) && empty($acceptedStudentdetail['AcceptedStudent']['specialization_id']) ?
                            'd~' . $acceptedStudentdetail['AcceptedStudent']['department_id'] : '');
                }
            }
        }

        return $applied_for;
    }

    public function canItBeDeleted(?int $round_participant_id = null): bool
    {
        $placementParticipatingStudentsTable = TableRegistry::getTableLocator()->get('PlacementParticipatingStudents');
        $placementPreferencesTable = TableRegistry::getTableLocator()->get('PlacementPreferences');

        if ($placementParticipatingStudentsTable->find('count')
                ->where(['PlacementParticipatingStudents.placement_round_participant_id' => $round_participant_id])
                ->disableHydration()
                ->count() > 0) {
            return false;
        } elseif ($placementPreferencesTable->find('count')
                ->where(['PlacementPreferences.placement_round_participant_id' => $round_participant_id])
                ->disableHydration()
                ->count() > 0) {
            return false;
        }

        return true;
    }

    public function latest_defined_academic_year_and_round(?string $applied_for = null): array
    {
        $latestAcyRnd = [];

        $query = $this->find('first')
            ->order([
                'PlacementRoundParticipants.academic_year' => 'DESC',
                'PlacementRoundParticipants.placement_round' => 'DESC'
            ])
            ->group([
                'PlacementRoundParticipants.academic_year',
                'PlacementRoundParticipants.placement_round',
                'PlacementRoundParticipants.applied_for'
            ])
            ->disableHydration();

        if (!empty($applied_for)) {
            $query->where(['PlacementRoundParticipants.applied_for' => $applied_for]);
        }

        $latestACYDefined = $query->first();

        if (!empty($latestACYDefined)) {
            $latestAcyRnd['applied_for'] = $latestACYDefined['applied_for'];
            $latestAcyRnd['academic_year'] = $latestACYDefined['academic_year'];
            $latestAcyRnd['round'] = $latestACYDefined['placement_round'];
        }

        return $latestAcyRnd;
    }

    public function get_placement_participant_ids_by_group_identifier(?string $group_identifier = null): array
    {
        if (!empty($group_identifier)) {
            $participantIDs = $this->find('list')
                ->where(['PlacementRoundParticipants.group_identifier' => $group_identifier])
                ->select(['id'])
                ->disableHydration()
                ->toArray();
            return $participantIDs;
        }

        return [];
    }
}
