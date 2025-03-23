<?php

namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class YearLevelsTable extends Table
{
    public function initialize(array $config): void
    {
        parent::initialize($config);

        $this->setTable('year_levels');
        $this->setPrimaryKey('id');

        $this->belongsTo('Departments', [
            'foreignKey' => 'department_id',
            'joinType' => 'INNER',
        ]);

        $this->hasMany('Sections', [
            'foreignKey' => 'year_level_id',
        ]);

        $this->hasMany('Courses', [
            'foreignKey' => 'year_level_id',
        ]);

        $this->hasMany('ExamPeriods', [
            'foreignKey' => 'year_level_id',
        ]);
    }

    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('department_id', 'Please select department.')
            ->numeric('department_id', 'Department ID must be numeric.');

        return $validator;
    }

    public function buildRules(RulesChecker $rules)
    {
        return $rules;
    }

    /**
     * Returns distinct year levels.
     */
    public function distinct_year_level()
    {
        return $this->find()
            ->select(['name'])
            ->distinct(['name'])
            ->order(['name' => 'ASC'])
            ->all()
            ->extract('name')
            ->toArray();
    }

    /**
     * Returns distinct year levels based on user role.
     */
    public function distinct_year_level_based_on_role($roleId = null, $collegeIds = null, $departmentIds = null, $programIds = [])
    {
        $conditions = [];

        if ($roleId == ROLE_COLLEGE) {
            $deptIds = $this->Departments->find('list', [
                'conditions' => ['Departments.college_id IN' => $collegeIds, 'Departments.active' => 1],
            ])->toArray();

            if (!empty($deptIds)) {
                $conditions['YearLevels.department_id IN'] = array_keys($deptIds);
            }
        } elseif ($roleId == ROLE_DEPARTMENT) {
            $conditions['YearLevels.department_id'] = $departmentIds;
        }

        return $this->find()
            ->select(['name'])
            ->where($conditions)
            ->distinct(['name'])
            ->order(['name' => 'ASC'])
            ->all()
            ->extract('name')
            ->toArray();
    }

    /**
     * Get maximum year level for a department.
     */
    public function get_department_max_year_level($departmentIds = null)
    {
        $maxYearLevel = 0;

        $yearLevels = $this->find('list', [
            'conditions' => ['YearLevels.department_id' => $departmentIds],
        ])->toArray();

        foreach ($yearLevels as $yearLevel) {
            $yearLevelNumber = intval(substr($yearLevel, 0, -2));
            if ($yearLevelNumber > $maxYearLevel) {
                $maxYearLevel = $yearLevelNumber;
            }
        }

        return $maxYearLevel;
    }

    /**
     * Get the next year level after a given year level.
     */
    public function getNextYearLevel($yearLevelId, $departmentId)
    {
        $yearLevel = $this->get($yearLevelId);
        return $this->find()
            ->where(['YearLevels.department_id' => $departmentId, 'YearLevels.name >' => $yearLevel->name])
            ->order(['YearLevels.name' => 'ASC'])
            ->first()
            ->id ?? null;
    }
}
