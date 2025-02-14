<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class UserMealAssignmentsTable extends Table
{

    public function initialize(array $config)
    {
        parent::initialize($config);

        $this->setTable('user_meal_assignments');
        $this->setPrimaryKey('id');

        // Define associations
        $this->belongsTo('Users', [
            'foreignKey' => 'user_id',
            'joinType' => 'INNER',
        ]);

        $this->belongsTo('MealHalls', [
            'foreignKey' => 'meal_hall_id',
            'joinType' => 'INNER',
        ]);
    }

    /**
     * Validation rules.
     */
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('user_id', 'User ID is required')
            ->notEmptyString('meal_hall_id', 'Meal Hall ID is required');

        return $validator;
    }

    /**
     * Check for duplicate meal hall assignments.
     */
    public function checkDuplicationAssignment($data = null)
    {
        if (empty($data['UserMealAssignment'])) {
            return true;
        }

        foreach ($data['UserMealAssignment'] as $id => $value) {
            $meal = $this->MealHalls->find()
                ->select(['name'])
                ->where(['id' => $value['meal_hall_id']])
                ->first();

            $check = $this->find()
                ->where([
                    'user_id' => $value['user_id'],
                    'meal_hall_id' => $value['meal_hall_id']
                ])
                ->count();

            if ($check > 0) {
                return __('The selected user has already been assigned to the {0} meal hall previously.', $meal->name);
            }
        }

        return true;
    }

    /**
     * Get meal hall assignments grouped by campus.
     */
    public function mealHallAssignmentOrganizedByCampus()
    {
        $assignments = $this->find()
            ->contain(['Users' => ['fields' => ['id', 'full_name']], 'MealHalls' => ['Campuses']])
            ->toArray();

        $organizedAssignments = [];

        foreach ($assignments as $assignment) {
            $campusName = $assignment->meal_hall->campus->name;
            $organizedAssignments[$campusName][] = [
                'User' => $assignment->user,
                'MealHall' => $assignment->meal_hall,
                'UserMealAssignment' => $assignment,
            ];
        }

        return $organizedAssignments;
    }

    /**
     * Get meal hall IDs assigned to a given user.
     */
    public function assigned_meal_hall($userId = null)
    {
        return $this->find()
            ->select(['meal_hall_id'])
            ->where(['user_id' => $userId])
            ->extract('meal_hall_id')
            ->toList();
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
        $rules->add($rules->existsIn(['user_id'], 'Users'));
        $rules->add($rules->existsIn(['meal_hall_id'], 'MealHalls'));

        return $rules;
    }
}
