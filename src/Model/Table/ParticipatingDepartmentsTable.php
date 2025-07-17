<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\ORM\TableRegistry;
use Cake\Validation\Validator;

class ParticipatingDepartmentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('participating_departments');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('ReservedPlaces', [
            'foreignKey' => 'participating_department_id',
            'dependent' => false,
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('academic_year', 'Select academic year')
            ->numeric('number', 'Enter number required')
            ->numeric('female', 'Enter number required')
            ->numeric('regions', 'Enter number required');

        return $validator;
    }

    public function canEditOwn(?int $college_id = null, ?int $id = null): bool
    {
        $canEditOwn = $this->find()
            ->where(['ParticipatingDepartments.college_id' => $college_id])
            ->disableHydration()
            ->all()
            ->toArray();

        foreach ($canEditOwn as $value) {
            if ($value['id'] == $id) {
                return true;
            }
        }

        return false;
    }

    public function checkIfOtherCollege(?int $department_id = null, ?int $college_id = null): bool
    {
        $count = $this->Departments->find('count')
            ->where([
                'Departments.college_id' => $college_id,
                'Departments.id' => $department_id
            ])
            ->disableHydration()
            ->count();

        return $count > 0;
    }

    public function isAlreadyRecordedParticipationgDepartments(?int $college_id = null, ?string $academicyear = null, ?array $reformatparticipatingdepartments = null)
    {
        if ($academicyear && $college_id) {
            $check = TableRegistry::getTableLocator()->get('Preferences')->find('count')
                ->where([
                    'Preferences.college_id' => $college_id,
                    'Preferences.academicyear' => $academicyear
                ])
                ->disableHydration()
                ->count();

            if ($check) {
                $this->validationErrors['alreadyrecorded'] = "Validation Error: Student has started to fill their preference. You cannot add more participating departments for {$academicyear} academic year.";
                return false;
            }
        }

        if (!empty($reformatparticipatingdepartments) && !empty($academicyear) && !empty($college_id)) {
            foreach ($reformatparticipatingdepartments['ParticipatingDepartment'] as $key => &$value) {
                $check = $this->find('count')
                    ->where([
                        'ParticipatingDepartments.college_id' => $college_id,
                        'ParticipatingDepartments.academic_year LIKE' => $academicyear . '%',
                        'ParticipatingDepartments.department_id' => $value['department_id']
                    ])
                    ->disableHydration()
                    ->count();

                if ($check == 1) {
                    unset($reformatparticipatingdepartments['ParticipatingDepartment'][$key]);
                }
            }

            if (!empty($reformatparticipatingdepartments['ParticipatingDepartment'])) {
                return $reformatparticipatingdepartments;
            }

            $this->validationErrors['alreadyrecorded'] = "Validation Error: The participating department for placement has already been recorded for {$academicyear} academic year.";
            return false;
        }

        return false;
    }

    public function checkAgainstAvailableStudentFromOtherCollege(?array $data = null): bool
    {
        $array = [];
        $number = 0;
        $count = 0;
        $academicyear = null;

        if (!empty($data)) {
            foreach ($data as $key => $value) {
                if (!empty($value['other_college_department'])) {
                    $array[] = $value['department_id'];
                    $number += $value['number'];
                    $academicyear = $value['academic_year'];
                }
                if ($key == 'other_college_department') {
                    $count++;
                }
            }
        }

        if ($count == 0) {
            return true;
        }

        if (!empty($array)) {
            $findcollege = $this->Departments->find('first')
                ->where(['Departments.id IN' => $array])
                ->contain(['Colleges' => ['fields' => ['id', 'name']]])
                ->disableHydration()
                ->first();

            if (!empty($findcollege['College']['id'])) {
                $total_accepted_students_unsigned_to_department = $this->ReservedPlaces->total_accepted_students_unsigned_to_department(
                    $findcollege['College']['id'],
                    $academicyear
                );

                return $number <= $total_accepted_students_unsigned_to_department;
            }
        }

        return false;
    }

    public function checkAvailableFemaleInTheGivenAcademicYear(array $data, ?int $college_id = null, ?string $academicyear = null): bool
    {
        $female = $this->Colleges->AcceptedStudents->find('count')
            ->where([
                'AcceptedStudents.sex' => 'female',
                'AcceptedStudents.college_id' => $college_id,
                'AcceptedStudents.academicyear' => $academicyear
            ])
            ->disableHydration()
            ->count();

        if ($this->sumQuota($data, 'female') <= $female) {
            return true;
        }

        $this->validationErrors['female'] = "Validation Error: The female quota should be less than or equal to the number of female students. The total female students in your college is {$female}. Please adjust number again.";
        return false;
    }

    public function checkAvailableRegionStudentInTheGivenAcademicYear(array $data, ?int $college_id = null, array $region_ids, ?string $academicyear = null): bool
    {
        if (empty($region_ids)) {
            return true;
        }

        $regions = $this->Colleges->AcceptedStudents->find('count')
            ->where([
                'AcceptedStudents.region_id IN' => $region_ids,
                'AcceptedStudents.college_id' => $college_id,
                'AcceptedStudents.academicyear' => $academicyear
            ])
            ->disableHydration()
            ->count();

        if ($this->sumQuota($data, 'regions') <= $regions) {
            return true;
        }

        $this->validationErrors['regions'] = "Validation Error: The region quota should be less than or equal to the number of students in the given regions. The total students in selected regions is {$regions}. Please adjust number.";
        return false;
    }

    public function checkAvailableDisableStudentInTheGivenAcademicYear(array $data, ?int $college_id = null, ?string $academicyear = null): bool
    {
        $disable = $this->Colleges->AcceptedStudents->find('count')
            ->where([
                'AcceptedStudents.disability IS NOT NULL',
                'AcceptedStudents.college_id' => $college_id,
                'AcceptedStudents.academicyear' => $academicyear
            ])
            ->disableHydration()
            ->count();

        if ($this->sumQuota($data, 'disability') <= $disable) {
            return true;
        }

        $this->validationErrors['regions'] = "Validation Error: The disability quota should be less than or equal to the number of students in the given college. The total students who are disabled in your college is {$disable}. Please adjust number.";
        return false;
    }

    public function sumQuota(?array $data = null, ?string $field = null): int
    {
        $sumquota = 0;
        if (!empty($data)) {
            foreach ($data as $v) {
                if ($field == 'female') {
                    $sumquota += $v['female'];
                } elseif ($field == 'regions') {
                    $sumquota += $v['regions'];
                } elseif ($field == 'disability') {
                    $sumquota += $v['disability'];
                } elseif ($field == 'number') {
                    $sumquota += $v['number'];
                }
            }
        }
        return $sumquota;
    }

    public function checkAvailableNumberOfStudentAgainstGivenQuotaOfDepartment(?array $data = null, ?int $college_id = null, ?string $academicyear = null): bool
    {
        $isPrepartory = TableRegistry::getTableLocator()->get('PlacementsResultsCriteria')->isPrepartoryResult($academicyear, $college_id);
        $conditions = [
            'OR' => [
                ['AcceptedStudents.department_id IN' => ['', 0]],
                ['AcceptedStudents.department_id IS NULL'],
                ['AcceptedStudents.placementtype IN' => [null, CANCELLED_PLACEMENT]]
            ],
            'AND' => [
                ['AcceptedStudents.academicyear LIKE' => $academicyear . '%'],
                ['AcceptedStudents.college_id' => $college_id],
                ['AcceptedStudents.Placement_Approved_By_Department IS NULL'],
                ['AcceptedStudents.college_id' => $college_id]
            ]
        ];

        if ($isPrepartory == 0) {
            $conditions['AND'][] = ['AcceptedStudents.freshman_result IS NOT NULL'];
        }

        $total = $this->Colleges->AcceptedStudents->find('count')
            ->where($conditions)
            ->disableHydration()
            ->count();

        $female = $this->sumQuota($data, 'female');
        $totaldepartmentsnumber = $this->sumQuota($data, 'number');
        $disability = $this->sumQuota($data, 'disability');
        $regions = $this->sumQuota($data, 'regions');
        $privilaged_quota = $female + $disability + $regions;

        foreach ($data as $v) {
            $privilaged_quota_sum = $v['female'] + $v['regions'] + $v['disability'];
            if ($v['number'] == 0) {
                $this->validationErrors['DepartmentCapacity'] = 'Department capacity of the participating department should be greater than zero, or you have to remove/delete from the participating department list before adding quota. Please adjust number.';
                return false;
            }

            $dep_name = $this->Departments->find()
                ->select(['name'])
                ->where(['Departments.id' => $v['department_id']])
                ->disableHydration()
                ->first()['name'] ?? '';

            if ($v['number'] < $v['female']) {
                $this->validationErrors['DepartmentCapacity'] = "The total quota for female students should be less than or equal to the department capacity for {$dep_name} department.";
                return false;
            }

            if ($v['number'] < $v['regions']) {
                $this->validationErrors['DepartmentCapacity'] = "The total quota for region students should be less than or equal to the department capacity for {$dep_name} department.";
                return false;
            }

            if ($v['number'] < $v['disability']) {
                $this->validationErrors['DepartmentCapacity'] = "The total quota for disability students should be less than or equal to the department capacity for {$dep_name} department.";
                return false;
            }

            if ($v['number'] < $privilaged_quota_sum) {
                $this->validationErrors['DepartmentCapacity'] = "The sum of the privileged quota should be less than or equal to the department capacity for {$dep_name} department.";
                return false;
            }
        }

        if ($totaldepartmentsnumber == $total) {
            return true;
        }

        $this->validationErrors['DepartmentCapacity'] = "The sum of all department capacity should be equal to the total number of students who can participate in your college/institute for placement. The total number of students in your college who are eligible for placement are {$total}. Please adjust department capacity accordingly.";
        return false;
    }

    public function quotaNameAndValue(?string $academicyear = null, ?int $college_id = null): array
    {
        if ($college_id) {
            return $this->find()
                ->select([
                    'ParticipatingDepartments.department_id',
                    'ParticipatingDepartments.female',
                    'ParticipatingDepartments.disability',
                    'ParticipatingDepartments.regions'
                ])
                ->where([
                    'ParticipatingDepartments.college_id' => $college_id,
                    'ParticipatingDepartments.academic_year LIKE' => $academicyear . '%'
                ])
                ->disableHydration()
                ->all()
                ->toArray();
        }

        return [];
    }

    public function checkDepartmentCapacityBeforeEditing(?array $data = null, ?int $college_id = null, ?string $academicyear = null): bool
    {
        $conditions = [
            'OR' => [
                ['AcceptedStudents.department_id IN' => ['', 0]],
                ['AcceptedStudents.department_id IS NULL'],
                ['AcceptedStudents.placementtype IN' => [null, CANCELLED_PLACEMENT]]
            ],
            'AND' => [
                ['AcceptedStudents.academicyear LIKE' => $academicyear . '%'],
                ['AcceptedStudents.college_id' => $college_id],
                ['AcceptedStudents.Placement_Approved_By_Department IS NULL'],
                ['AcceptedStudents.college_id' => $college_id]
            ]
        ];

        $total = $this->Colleges->AcceptedStudents->find('count')
            ->where($conditions)
            ->disableHydration()
            ->count();

        if ($total > 0) {
            $others_capacity = $this->find()
                ->where([
                    'ParticipatingDepartments.college_id' => $college_id,
                    'ParticipatingDepartments.academic_year LIKE' => $academicyear . '%',
                    'ParticipatingDepartments.department_id <>' => $data['department_id']
                ])
                ->disableHydration()
                ->all()
                ->toArray();

            $other_sum = 0;
            foreach ($others_capacity as $v) {
                $other_sum += $v['number'];
            }

            if ($other_sum != ($total - $data['number'])) {
                $this->validationErrors['DepartmentCapacity'] = "The department capacity should be equal to the total number of students in your college minus other department capacities. The total number of students in your college eligible for placement is {$total} and other department capacity is {$other_sum}. Please adjust number.";
                return false;
            }

            return true;
        }

        return false;
    }

    public function getParticipatingDepartment(int $collegeId, string $academicYear): array
    {
        $departments = $this->find()
            ->where([
                'ParticipatingDepartments.college_id' => $collegeId,
                'ParticipatingDepartments.academic_year' => $academicYear
            ])
            ->contain(['Departments'])
            ->disableHydration()
            ->all()
            ->toArray();

        $departmentList = [];
        foreach ($departments as $v) {
            $departmentList[$v['Department']['id']] = $v['Department']['name'];
        }

        return $departmentList;
    }
}
