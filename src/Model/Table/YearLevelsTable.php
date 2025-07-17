<?php
namespace App\Model\Table;

use Cake\ORM\Table;
use Cake\Validation\Validator;
use Cake\ORM\RulesChecker;
use Cake\ORM\TableRegistry;

class YearLevelsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('year_levels');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id'
        ]);

        $this->hasMany('Sections', [
            'foreignKey' => 'year_level_id',
            'dependent' => false
        ]);

        $this->hasMany('Courses', [
            'foreignKey' => 'year_level_id',
            'dependent' => false
        ]);

        $this->hasMany('ExamPeriods', [
            'foreignKey' => 'year_level_id',
            'dependent' => false
        ]);
    }

    public function validationDefault(Validator $validator): Validator
    {
        $validator
            ->integer('department_id', 'Please select department')
            ->requirePresence('department_id', 'create')
            ->notEmptyString('department_id', 'Please select department');

        return $validator;
    }

    public function buildRules(RulesChecker $rules): RulesChecker
    {
        $rules->add($rules->existsIn('department_id', 'Departments'), [
            'errorField' => 'department_id',
            'message' => 'The specified department does not exist.'
        ]);

        return $rules;
    }

    public function distinctYearLevel(): array
    {
        $yearLevels = $this->find()
            ->select(['name'])
            ->distinct(['name'])
            ->order(['name' => 'ASC'])
            ->toArray();

        $yearleveldistinct = [];
        foreach ($yearLevels as $value) {
            $yearleveldistinct[$value->name] = $value->name;
        }

        return $yearleveldistinct;
    }

    public function distinctYearLevelBasedOnRole($role_id = null, $college_ids = null, $department_ids = null, $program_ids = []): array
    {
        $year_level_find = [];

        $departmentsTable = TableRegistry::getTableLocator()->get('Departments');
        $curriculumsTable = TableRegistry::getTableLocator()->get('Curriculums');
        $coursesTable = TableRegistry::getTableLocator()->get('Courses');

        if ($role_id == ROLE_COLLEGE) {
            $dept_ids = $departmentsTable->find('list')
                ->where(['Departments.college_id IN' => $college_ids, 'Departments.active' => 1])
                ->toArray();

            if (empty($program_ids)) {
                $year_level_find = $this->find('list')
                    ->where(['YearLevels.department_id IN' => array_keys($dept_ids)])
                    ->select(['name'])
                    ->distinct(['name'])
                    ->order(['name' => 'ASC'])
                    ->toArray();
            } else {
                $curriculum_ids = $curriculumsTable->find('list')
                    ->where([
                        'Curriculums.department_id IN' => array_keys($dept_ids),
                        'Curriculums.program_id IN' => $program_ids
                    ])
                    ->select(['id'])
                    ->toArray();

                if (!empty($curriculum_ids)) {
                    $year_level_ids = $coursesTable->find('list')
                        ->where([
                            'Courses.curriculum_id IN' => array_keys($curriculum_ids),
                            'Courses.active' => 1
                        ])
                        ->select(['year_level_id'])
                        ->distinct(['year_level_id'])
                        ->toArray();

                    if (!empty($year_level_ids)) {
                        $year_level_find = $this->find('list')
                            ->where(['YearLevels.id IN' => array_keys($year_level_ids)])
                            ->select(['name'])
                            ->distinct(['name'])
                            ->order(['name' => 'ASC'])
                            ->toArray();
                    } else {
                        $year_level_find = ['1st' => '1st'];
                    }
                } else {
                    $year_level_find = ['1st' => '1st'];
                }
            }
        } elseif ($role_id == ROLE_REGISTRAR) {
            if (!empty($college_ids)) {
                $dept_ids = $departmentsTable->find('list')
                    ->where(['Departments.college_id IN' => $college_ids, 'Departments.active' => 1])
                    ->toArray();

                if (empty($program_ids)) {
                    $year_level_find = $this->find('list')
                        ->where(['YearLevels.department_id IN' => array_keys($dept_ids)])
                        ->select(['name'])
                        ->distinct(['name'])
                        ->order(['name' => 'ASC'])
                        ->toArray();
                } else {
                    $year_level_find = ['1st' => '1st'];
                }
            } elseif (!empty($department_ids)) {
                $dept_ids = $departmentsTable->find('list')
                    ->where(['Departments.id IN' => $department_ids, 'Departments.active' => 1])
                    ->toArray();

                if (empty($program_ids)) {
                    $year_level_find = $this->find('list')
                        ->where(['YearLevels.department_id IN' => array_keys($dept_ids)])
                        ->select(['name'])
                        ->distinct(['name'])
                        ->order(['name' => 'ASC'])
                        ->toArray();
                } else {
                    $curriculum_ids = $curriculumsTable->find('list')
                        ->where([
                            'Curriculums.department_id IN' => array_keys($dept_ids),
                            'Curriculums.program_id IN' => $program_ids
                        ])
                        ->select(['id'])
                        ->toArray();

                    if (!empty($curriculum_ids)) {
                        $year_level_ids = $coursesTable->find('list')
                            ->where([
                                'Courses.curriculum_id IN' => array_keys($curriculum_ids),
                                'Courses.active' => 1
                            ])
                            ->select(['year_level_id'])
                            ->distinct(['year_level_id'])
                            ->toArray();

                        if (!empty($year_level_ids)) {
                            $year_level_find = $this->find('list')
                                ->where(['YearLevels.id IN' => array_keys($year_level_ids)])
                                ->select(['name'])
                                ->distinct(['name'])
                                ->order(['name' => 'ASC'])
                                ->toArray();
                        } else {
                            $year_level_find = ['1st' => '1st'];
                        }
                    } else {
                        $year_level_find = ['1st' => '1st'];
                    }
                }
            }
        } elseif ($role_id == ROLE_DEPARTMENT) {
            if (empty($program_ids)) {
                $year_level_find = $this->find('list')
                    ->where(['YearLevels.department_id IN' => $department_ids])
                    ->select(['name'])
                    ->distinct(['name'])
                    ->order(['name' => 'ASC'])
                    ->toArray();
            } else {
                $curriculum_ids = $curriculumsTable->find('list')
                    ->where([
                        'Curriculums.department_id IN' => $department_ids,
                        'Curriculums.program_id IN' => $program_ids
                    ])
                    ->select(['id'])
                    ->toArray();

                if (!empty($curriculum_ids)) {
                    $year_level_ids = $coursesTable->find('list')
                        ->where([
                            'Courses.curriculum_id IN' => array_keys($curriculum_ids),
                            'Courses.active' => 1
                        ])
                        ->select(['year_level_id'])
                        ->distinct(['year_level_id'])
                        ->toArray();

                    if (!empty($year_level_ids)) {
                        $year_level_find = $this->find('list')
                            ->where(['YearLevels.id IN' => array_keys($year_level_ids)])
                            ->select(['name'])
                            ->distinct(['name'])
                            ->order(['name' => 'ASC'])
                            ->toArray();
                    } else {
                        $year_level_find = ['1st' => '1st'];
                    }
                } else {
                    $year_level_find = ['1st' => '1st'];
                }
            }
        } else {
            if (!empty($program_ids) && !empty($department_ids)) {
                $curriculum_ids = $curriculumsTable->find('list')
                    ->where([
                        'Curriculums.department_id IN' => $department_ids,
                        'Curriculums.program_id IN' => $program_ids
                    ])
                    ->select(['id'])
                    ->toArray();

                if (!empty($curriculum_ids)) {
                    $year_level_ids = $coursesTable->find('list')
                        ->where([
                            'Courses.curriculum_id IN' => array_keys($curriculum_ids),
                            'Courses.active' => 1
                        ])
                        ->select(['year_level_id'])
                        ->distinct(['year_level_id'])
                        ->toArray();

                    if (!empty($year_level_ids)) {
                        $year_level_find = $this->find('list')
                            ->where(['YearLevels.id IN' => array_keys($year_level_ids)])
                            ->select(['name'])
                            ->distinct(['name'])
                            ->order(['name' => 'ASC'])
                            ->toArray();
                    } else {
                        $year_level_find = ['1st' => '1st'];
                    }
                } else {
                    $year_level_find = ['1st' => '1st'];
                }
            } elseif (!empty($department_ids)) {
                $year_level_find = $this->find('list')
                    ->where(['YearLevels.department_id IN' => $department_ids])
                    ->select(['name'])
                    ->distinct(['name'])
                    ->order(['name' => 'ASC'])
                    ->toArray();
            } else {
                $year_level_find = $this->find('list')
                    ->select(['name'])
                    ->distinct(['name'])
                    ->order(['name' => 'ASC'])
                    ->toArray();
            }
        }

        if (isset($year_level_find['10th'])) {
            unset($year_level_find['10th']);
            $year_level_find['10th'] = '10th';
        }

        return $year_level_find;
    }

    public function getDepartmentMaxYearLevel($department_ids = null): int
    {
        $max_year_level = 0;

        $conditions = [];
        if (is_array($department_ids)) {
            $conditions['YearLevels.department_id IN'] = $department_ids;
        } elseif ($department_ids !== null) {
            $conditions['YearLevels.department_id'] = $department_ids;
        }

        $yearLevels = $this->find('list')
            ->where($conditions)
            ->toArray();

        if (!empty($yearLevels)) {
            foreach ($yearLevels as $yearLevel) {
                $year_level_number = (int) substr($yearLevel, 0, -2);
                if ($year_level_number > $max_year_level) {
                    $max_year_level = $year_level_number;
                }
            }
        }

        return $max_year_level;
    }

    public function getNextYearLevel($yearLevelId, $departmentId): ?int
    {
        $yearLevel = $this->find()
            ->where(['YearLevels.id' => $yearLevelId])
            ->first();

        if (!$yearLevel) {
            return null;
        }

        $yearLevels = $this->find()
            ->where(['YearLevels.department_id' => $departmentId])
            ->order(['YearLevels.name' => 'ASC'])
            ->toArray();

        if (!empty($yearLevels)) {
            foreach ($yearLevels as $v) {
                if ($v->name > $yearLevel->name) {
                    return $v->id;
                }
            }
        }

        return null;
    }
}
