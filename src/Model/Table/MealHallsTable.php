<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MealHallsTable extends Table
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

        $this->setTable('meal_halls');
        $this->setDisplayField('name');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->belongsTo('Campuses', [
            'foreignKey' => 'campus_id',
            'joinType' => 'INNER',
        ]);
        $this->hasMany('ExceptionMealAssignments', [
            'foreignKey' => 'meal_hall_id',
        ]);
        $this->hasMany('MealHallAssignments', [
            'foreignKey' => 'meal_hall_id',
        ]);

    }
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('name', 'Block name should not be empty. Please provide a valid block name.')
            ->add('name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'You have already entered this meal hall name. Please provide a unique name.'
            ]);

        return $validator;
    }
}
