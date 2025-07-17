<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class PreferencesTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('preferences');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('AcceptedStudents', [
            'foreignKey' => 'accepted_student_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'LEFT',
        ]);

        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'LEFT',
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('academicyear', 'Select Academic Year.');

        return $validator;
    }

    public function isAlreadyEnteredPreference(?int $acceptedStudentId = null): bool
    {
        $usersTable = TableRegistry::getTableLocator()->get('Users');
        $user = $usersTable->find('first')
            ->where(['Users.id' => $acceptedStudentId])
            ->disableHydration()
            ->first();

        $count = 0;
        if (empty($user)) {
            $count = $this->find('count')
                ->where(['Preferences.accepted_student_id' => $acceptedStudentId])
                ->disableHydration()
                ->count();
        } else {
            $count = $this->find('count')
                ->where(['Preferences.user_id' => $acceptedStudentId])
                ->disableHydration()
                ->count();

            if (!$count) {
                $acceptedStudentsTable = TableRegistry::getTableLocator()->get('AcceptedStudents');
                $acceptedStudent = $acceptedStudentsTable->find('first')
                    ->where(['AcceptedStudents.user_id' => $user['User']['id']])
                    ->disableHydration()
                    ->first();

                if (!empty($acceptedStudent)) {
                    $count = $this->find('count')
                        ->where(['Preferences.accepted_student_id' => $acceptedStudent['AcceptedStudent']['id']])
                        ->disableHydration()
                        ->count();
                }
            }
        }

        if ($count) {
            $this->validationErrors['alreadypreferencerecorded'] = 'Validation Error: You have already recorded preference for selected student.';
            return true;
        }

        return false;
    }

    public function isDepartmentSelected(array $data): array
    {
        $arrayselected = [];
        foreach ($data as $value) {
            if (!empty($value['department_id'])) {
                $arrayselected[$value['department_id']] = $value['department_id'];
            }
        }
        return $arrayselected;
    }

    public function isAllPreferenceDepartmentSelectedDifferent(?array $data = null): bool
    {
        $array = [];
        if (!empty($data)) {
            foreach ($data as $value) {
                if (!empty($value['department_id'])) {
                    $array[] = $value['department_id'];
                } else {
                    $this->validationErrors['department'] = 'Validation Error: Please select department preference for each preference order.';
                    return false;
                }
            }
        }

        $arrayvaluecount = array_count_values($array);
        foreach ($arrayvaluecount as $v) {
            if ($v > 1) {
                $this->validationErrors['preference'] = 'Validation Error: Please select different department preference for each preference order.';
                return false;
            }
        }

        return true;
    }

    public function getPreferenceStat(?int $college_id = null, ?string $academic_year = null, ?string $type = null): array
    {
        $stat = [];
        $participatingDepartmentsTable = TableRegistry::getTableLocator()->get('ParticipatingDepartments');
        $participatingDepartments = $participatingDepartmentsTable->find()
            ->select([
                'ParticipatingDepartments.id',
                'ParticipatingDepartments.department_id',
                'ParticipatingDepartments.developing_regions_id',
                'Departments.name'
            ])
            ->where([
                'ParticipatingDepartments.college_id' => $college_id,
                'ParticipatingDepartments.academic_year' => $academic_year
            ])
            ->contain(['Departments' => ['fields' => ['name']]])
            ->disableHydration()
            ->all()
            ->toArray();

        $placementsResultsCriteriasTable = TableRegistry::getTableLocator()->get('PlacementsResultsCriteria');
        $placementsResultsCriterias = $placementsResultsCriteriasTable->find()
            ->where([
                'PlacementsResultsCriterias.college_id' => $college_id,
                'PlacementsResultsCriterias.admissionyear' => $academic_year
            ])
            ->disableHydration()
            ->all()
            ->toArray();

        $isPrepartory = $placementsResultsCriteriasTable->isPrepartoryResult($academic_year, $college_id);

        foreach ($participatingDepartments as $participatingDepartment) {
            $index = count($stat);
            $stat[$index]['department_id'] = $participatingDepartment['department_id'];
            $stat[$index]['department_name'] = $participatingDepartment['Department']['name'];

            for ($i = 1; $i <= count($participatingDepartments); $i++) {
                $options = [
                    'conditions' => [
                        'Preferences.department_id' => $participatingDepartment['department_id'],
                        'Preferences.college_id' => $college_id,
                        'Preferences.academicyear' => $academic_year,
                        'Preferences.preferences_order' => $i
                    ]
                ];
                $options['conditions'][0] = "Preferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE academicyear = '{$academic_year}' AND college_id = '{$college_id}'";
                if (strcasecmp($type, 'female') == 0) {
                    $options['conditions'][0] .= " AND (sex = 'female' OR sex = 'f'))";
                } elseif (strcasecmp($type, 'disable') == 0) {
                    $options['conditions'][0] .= " AND disability IS NOT NULL AND disability <> '')";
                } elseif (!empty($participatingDepartment['developing_regions_id']) && strcasecmp($type, 'region') == 0) {
                    $options['conditions'][0] .= " AND region_id IN ('{$participatingDepartment['developing_regions_id']}'))";
                } else {
                    $options['conditions'][0] .= ')';
                }
                $stat[$index]['count'][$i]['~total~'] = $this->find('count', $options)
                    ->disableHydration()
                    ->count();

                foreach ($placementsResultsCriterias as $placementsResultsCriteria) {
                    $options = [
                        'conditions' => [
                            'Preferences.department_id' => $participatingDepartment['department_id'],
                            'Preferences.college_id' => $college_id,
                            'Preferences.academicyear' => $academic_year,
                            'Preferences.preferences_order' => $i
                        ]
                    ];
                    $resultField = $isPrepartory ? 'EHEECE_total_results' : 'freshman_result';
                    $options['conditions'][0] = "Preferences.accepted_student_id IN (SELECT id FROM accepted_students WHERE academicyear = '{$academic_year}' AND college_id = '{$college_id}' AND {$resultField} >= {$placementsResultsCriteria['result_from']} AND {$resultField} <= {$placementsResultsCriteria['result_to']}";
                    if (strcasecmp($type, 'female') == 0) {
                        $options['conditions'][0] .= " AND (sex = 'female' OR sex = 'f'))";
                    } elseif (strcasecmp($type, 'disable') == 0) {
                        $options['conditions'][0] .= " AND disability IS NOT NULL AND disability <> '')";
                    } elseif (!empty($participatingDepartment['developing_regions_id']) && strcasecmp($type, 'region') == 0) {
                        $options['conditions'][0] .= " AND region_id IN ('{$participatingDepartment['developing_regions_id']}'))";
                    } else {
                        $options['conditions'][0] .= ')';
                    }
                    $stat[$index]['count'][$i][$placementsResultsCriteria['name']] = $this->find('count', $options)
                        ->disableHydration()
                        ->count();
                }
            }
        }

        return $stat;
    }
}
