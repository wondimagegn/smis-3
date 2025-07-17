<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;

class DepartmentsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('departments');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Colleges', [
            'foreignKey' => 'college_id'
        ]);

        $this->hasMany('AcceptedStudents', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('DepartmentTransfers', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('Specializations', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('Curriculums', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('Courses', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('PublishedCourses', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('Offers', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('Preferences', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('Staffs', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('Students', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('Sections', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('YearLevels', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('GradeScales', [
            'foreignKey' => 'foreign_key',
            'conditions' => ['GradeScales.model' => 'Department'],
            'dependent' => true
        ]);

        $this->hasMany('AcademicCalendars', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('TakenProperties', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);

        $this->hasMany('DepartmentNameChanges', [
            'foreignKey' => 'department_id',
            'dependent' => false
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->notEmptyString('name', 'Name is required')
            ->add('name', 'isUniqueDepartmentInCollege', [
                'rule' => function ($value, $context) {
                    $conditions = [
                        'Departments.college_id' => $context['data']['college_id'],
                        'Departments.name' => trim($value)
                    ];

                    if (!empty($context['data']['id'])) {
                        $conditions['Departments.id !='] = $context['data']['id'];
                    }

                    $count = $this->find()
                        ->where($conditions)
                        ->count();

                    return $count === 0;
                },
                'message' => 'The department name should be unique in the college. The name is already taken. Use another one.'
            ]);

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('college_id', 'Colleges'), [
            'errorField' => 'college_id',
            'message' => 'The specified college does not exist.'
        ]);

        return $rules;
    }

    public function canItBeDeleted($department_id = null): bool
    {
        if ($this->YearLevels->find()->where(['YearLevels.department_id' => $department_id])->count() > 0) {
            return false;
        }

        if ($this->Students->find()->where(['Students.department_id' => $department_id])->count() > 0) {
            return false;
        }

        if ($this->Sections->find()->where(['Sections.department_id' => $department_id])->count() > 0) {
            return false;
        }

        if ($this->GradeScales->find()->where(['GradeScales.model' => 'Department', 'GradeScales.foreign_key' => $department_id])->count() > 0) {
            return false;
        }

        if ($this->PublishedCourses->find()->where(['PublishedCourses.department_id' => $department_id])->count() > 0) {
            return false;
        }

        if ($this->Curriculums->find()->where(['Curriculums.department_id' => $department_id])->count() > 0) {
            return false;
        }

        if ($this->AcceptedStudents->find()->where(['AcceptedStudents.department_id' => $department_id])->count() > 0) {
            return false;
        }

        if ($this->Staffs->find()->where(['Staffs.department_id' => $department_id])->count() > 0) {
            return false;
        }

        return true;
    }

    public function allDepartmentsByCollege($include_freshman_program = 0, $only_active = 0): array
    {
        $departments_organized = [];

        $active = $only_active ? 1 : [0, 1];

        $departments_data = $this->Colleges->find()
            ->contain([
                'Departments' => [
                    'conditions' => ['Departments.active IN' => $active]
                ]
            ])
            ->toArray();

        debug($departments_data);

        if (!empty($departments_data)) {
            foreach ($departments_data as $college_and_department) {
                $departments_organized[$college_and_department->name] = [];
                if ($include_freshman_program == 1) {
                    $departments_organized[$college_and_department->name]['c~' . $college_and_department->id] = $college_and_department->shortname . ' - Pre/Freshman/Remedial';
                }
                foreach ($college_and_department->departments as $department) {
                    $departments_organized[$college_and_department->name][$department->id] = $department->name;
                }
            }
        }

        return $departments_organized;
    }

    public function allDepartmentsByCollege2($include_all_department = 0, $privileged_department_ids = [], $privileged_college_ids = [], $only_active = 0): array
    {
        $departments_organized = [];

        $active = $only_active ? 1 : [0, 1];

        if (!empty($privileged_department_ids)) {
            $departments_data = $this->Colleges->find()
                ->contain([
                    'Departments' => [
                        'conditions' => [
                            'Departments.id IN' => $privileged_department_ids,
                            'Departments.active IN' => $active
                        ],
                        'sort' => ['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC']
                    ]
                ])
                ->toArray();
        } elseif (!empty($privileged_college_ids)) {
            $departments_data = $this->Colleges->find()
                ->where(['Colleges.id IN' => $privileged_college_ids, 'Colleges.active IN' => $active])
                ->contain([
                    'Departments' => [
                        'conditions' => ['Departments.active IN' => $active],
                        'sort' => ['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC']
                    ]
                ])
                ->toArray();
        } else {
            $departments_data = $this->Colleges->find()
                ->where(['Colleges.active IN' => $active])
                ->contain([
                    'Departments' => [
                        'conditions' => ['Departments.active IN' => $active],
                        'sort' => ['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC']
                    ]
                ])
                ->toArray();
        }

        if (!empty($departments_data)) {
            foreach ($departments_data as $college_and_department) {
                $departments_organized[$college_and_department->name] = [];
                if ($include_all_department == 1) {
                    if (
                        (!empty($privileged_college_ids) && is_numeric($privileged_college_ids) && $college_and_department->id == $privileged_college_ids) ||
                        (!empty($privileged_department_ids) && !empty($privileged_college_ids) && is_array($privileged_college_ids) && in_array($college_and_department->id, $privileged_college_ids)) ||
                        (empty($privileged_department_ids) && empty($privileged_college_ids)) ||
                        (!empty($privileged_department_ids) && empty($privileged_college_ids)) ||
                        (!empty($privileged_college_ids) && is_array($privileged_college_ids) && in_array($college_and_department->id, $privileged_college_ids))
                    ) {
                        $departments_organized[$college_and_department->name]['c~' . $college_and_department->id] = 'All ' . $college_and_department->shortname;
                    }
                }
                foreach ($college_and_department->departments as $department) {
                    if (!empty($privileged_department_ids) && in_array($department->id, $privileged_department_ids)) {
                        $departments_organized[$college_and_department->name][$department->id] = $department->name;
                    } elseif (empty($privileged_department_ids)) {
                        $departments_organized[$college_and_department->name][$department->id] = $department->name;
                    }
                }
            }
        }

        return $departments_organized;
    }

    public function onlyFreshmanInAllColleges($college_ids = null, $only_active = 0): array
    {
        $departments = [];

        $active = $only_active ? 1 : [0, 1];

        $colleges_query = $this->Colleges->find()
            ->where(['Colleges.active IN' => $active]);

        if (!empty($college_ids)) {
            $colleges_query->where(['Colleges.id IN' => $college_ids]);
        }

        $colleges = $colleges_query->toArray();

        if (!empty($colleges)) {
            foreach ($colleges as $v) {
                $departments[$v->name]['c~' . $v->id] = 'Pre/Freshman/Remedial - ' . $v->shortname;
            }
        }

        return $departments;
    }

    public function allCollegeDepartments($college_id = null, $only_active = 0, $include_freshman_program = 0): array
    {
        $departments_organized = [];

        $active = $only_active ? 1 : [0, 1];

        if (!empty($college_id)) {
            $departments_data = $this->find()
                ->where([
                    'Departments.college_id' => $college_id,
                    'Departments.active IN' => $active
                ])
                ->contain(['Colleges' => ['fields' => ['id', 'name', 'shortname']]])
                ->order(['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC'])
                ->toArray();

            if (!empty($departments_data)) {
                foreach ($departments_data as $department) {
                    if ($include_freshman_program) {
                        $departments_organized['c~' . $department->college->id] = 'Pre/Freshman/Remedial - ' . $department->college->shortname;
                    }
                    $departments_organized[$department->id] = $department->name;
                }
            }
        }

        return $departments_organized;
    }

    public function allDepartmentsByCollege3($include_all_department = 0, $privileged_department_ids = [], $privileged_college_ids = [], $only_active = 0): array
    {
        $departments_organized = [];

        $active = $only_active ? 1 : [0, 1];

        $departments_data = $this->Colleges->find()
            ->where(['Colleges.active IN' => $active])
            ->contain([
                'Departments' => [
                    'conditions' => ['Departments.active IN' => $active],
                    'sort' => ['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC']
                ]
            ])
            ->toArray();

        if (!empty($departments_data)) {
            foreach ($departments_data as $college_and_department) {
                $departments_organized[$college_and_department->name] = [];
                if ($include_all_department == 1) {
                    $departments_organized[$college_and_department->name]['c~' . $college_and_department->id] = 'All ' . $college_and_department->shortname . ' Students';
                }
                foreach ($college_and_department->departments as $department) {
                    $departments_organized[$college_and_department->name][$department->id] = $department->name;
                }
            }
        }

        return $departments_organized;
    }

    public function allDepartmentInCollegeIncludingPre($department_ids = null, $college_ids = null, $includePre = 0, $only_active = 0): array
    {
        $departments = [];

        $active = $only_active ? 1 : [0, 1];

        if (!empty($department_ids)) {
            $college_s = $this->find()
                ->where([
                    'Departments.id IN' => $department_ids,
                    'Departments.active IN' => $active
                ])
                ->contain([
                    'Colleges' => [
                        'conditions' => ['Colleges.active IN' => $active]
                    ]
                ])
                ->order(['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC'])
                ->toArray();

            if (!empty($college_s)) {
                foreach ($college_s as $v) {
                    if ($includePre) {
                        $departments[$v->college->name]['c~' . $v->college->id] = 'Pre/Freshman/Remedial - ' . $v->college->shortname;
                    }
                    $departments[$v->college->name][$v->id] = $v->name;
                }
            }
        }

        if (!empty($college_ids)) {
            $college_s = $this->find()
                ->where([
                    'Departments.college_id IN' => $college_ids,
                    'Departments.active IN' => $active
                ])
                ->contain([
                    'Colleges' => [
                        'conditions' => ['Colleges.active IN' => $active]
                    ]
                ])
                ->order(['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC'])
                ->toArray();

            if (!empty($college_s)) {
                foreach ($college_s as $v) {
                    if ($includePre) {
                        $departments[$v->college->name]['c~' . $v->college->id] = 'Pre/Freshman/Remedial - ' . $v->college->shortname;
                    }
                    $departments[$v->college->name][$v->id] = $v->name;
                }
            }
        }

        return $departments;
    }

    public function allUnits($role_id = null, $unit_id = null, $allUnit = 0): array
    {
        $departments_organized = [];

        if ($role_id == ROLE_COLLEGE && $allUnit == 0) {
            $departments_data = $this->Colleges->find()
                ->where(['Colleges.id' => $unit_id])
                ->contain([
                    'Departments' => [
                        'conditions' => ['Departments.active' => 1],
                        'Specializations',
                        'sort' => ['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC']
                    ]
                ])
                ->toArray();

            if (!empty($departments_data)) {
                foreach ($departments_data as $college_and_department) {
                    $departments_organized[$college_and_department->name]['c~' . $college_and_department->id] = 'All ' . $college_and_department->name;
                }
            }
        } elseif ($role_id == ROLE_DEPARTMENT && $allUnit == 0) {
            debug($unit_id);
            $departments_data = $this->find()
                ->where(['Departments.id' => $unit_id])
                ->contain(['Colleges', 'Specializations'])
                ->toArray();

            debug($departments_data);

            if (!empty($departments_data)) {
                foreach ($departments_data as $department) {
                    $departments_organized[$department->college->name]['d~' . $department->id] = 'Department of ' . $department->name;
                    if (!empty($department->specializations)) {
                        foreach ($department->specializations as $spec) {
                            $departments_organized[$department->name][$spec->id] = $spec->name;
                        }
                    }
                }
            }
        } elseif ($role_id == ROLE_REGISTRAR && $allUnit == 0) {
            $departments_data = $this->Colleges->find()
                ->where(['Colleges.active' => 1])
                ->contain([
                    'Departments' => [
                        'conditions' => ['Departments.active' => 1],
                        'Specializations',
                        'sort' => ['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC']
                    ]
                ])
                ->toArray();

            if (!empty($departments_data)) {
                foreach ($departments_data as $college_and_department) {
                    $departments_organized[$college_and_department->name]['c~' . $college_and_department->id] = 'All ' . $college_and_department->name;
                    foreach ($college_and_department->departments as $department) {
                        $departments_organized[$college_and_department->name]['d~' . $department->id] = $department->name;
                        if (!empty($department->specializations)) {
                            foreach ($department->specializations as $spec) {
                                $departments_organized[$department->name][$spec->id] = $spec->name;
                            }
                        }
                    }
                }
            }
        } elseif ($allUnit == 1) {
            $departments_data = $this->Colleges->find()
                ->where(['Colleges.active' => 1])
                ->contain([
                    'Departments' => [
                        'conditions' => ['Departments.active' => 1],
                        'Specializations',
                        'sort' => ['Departments.college_id' => 'ASC', 'Departments.name' => 'ASC']
                    ]
                ])
                ->toArray();

            if (!empty($departments_data)) {
                foreach ($departments_data as $college_and_department) {
                    $departments_organized[$college_and_department->name]['c~' . $college_and_department->id] = 'All ' . $college_and_department->name;
                    foreach ($college_and_department->departments as $department) {
                        $departments_organized[$college_and_department->name]['d~' . $department->id] = 'Department of ' . $department->name;
                        if (!empty($department->specializations)) {
                            foreach ($department->specializations as $spec) {
                                $departments_organized[$department->name][$spec->id] = $spec->name;
                            }
                        }
                    }
                }
            }
        }

        return $departments_organized;
    }

    public function allDepartmentsByCampus($department_id = null, $include_freshman_program = 0, $only_active = 1): array
    {
        $departments_organized = [];

        $active = $only_active ? 1 : [0, 1];

        $department_college_id = $this->find('list')
            ->where(['Departments.id' => $department_id])
            ->select(['college_id'])
            ->toArray();

        $college_campus_ids = $this->Colleges->find('list')
            ->where(['Colleges.id IN' => $department_college_id])
            ->select(['campus_id'])
            ->toArray();

        $departments_data = $this->Colleges->find()
            ->where(['Colleges.campus_id IN' => $college_campus_ids])
            ->contain([
                'Departments' => [
                    'conditions' => ['Departments.active IN' => $active]
                ]
            ])
            ->toArray();

        debug($departments_data);

        if (!empty($departments_data)) {
            foreach ($departments_data as $college_and_department) {
                $departments_organized[$college_and_department->name] = [];
                if ($include_freshman_program == 1) {
                    $departments_organized[$college_and_department->name]['c~' . $college_and_department->id] = $college_and_department->shortname . ' - Pre/Freshman/Remedial';
                }
                foreach ($college_and_department->departments as $department) {
                    $departments_organized[$college_and_department->name][$department->id] = $department->name;
                }
            }
        }

        return $departments_organized;
    }
}
