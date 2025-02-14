<?php
namespace App\Model\Table;

use Cake\ORM\Query;
use Cake\ORM\RulesChecker;
use Cake\ORM\Table;
use Cake\Validation\Validator;

class MealTypesTable extends Table
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

        $this->setTable('meal_types');
        $this->setDisplayField('id');
        $this->setPrimaryKey('id');

        $this->addBehavior('Timestamp');

        $this->hasMany('MealAttendances', [
            'foreignKey' => 'meal_type_id',
        ]);
    }
    public function validationDefault(Validator $validator)
    {
        $validator
            ->notEmptyString('meal_name', 'Meal name should not be empty. Please provide a valid meal name.')
            ->add('meal_name', 'unique', [
                'rule' => 'validateUnique',
                'provider' => 'table',
                'message' => 'You have already entered this meal type name. Please provide a unique meal name.'
            ]);

        return $validator;
    }

    function checkUnique ($data, $fieldName) {
        $valid=true;
        if(!isset($this->data['MealType']['id'])){
            if(isset($fieldName) && $this->hasField($fieldName)) {

                $check=$this->find('count',array('conditions'=>array('MealType.meal_name'=>$this->data['MealType']['meal_name'])));
                if($check>0) {
                    $valid=false;
                }
            }
        }
        return $valid;
    }
}
